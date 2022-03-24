<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Common\Str;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\SelectField;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Bolt\Storage\Query;
use Bolt\Utils\ContentHelper;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    /** @var Notifications */
    private $notifications;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var Config */
    private $config;

    /** @var ContentHelper */
    private $contentHelper;

    /** @var Query */
    private $query;


    public function __construct(
        Notifications $notifications,
        ContentRepository $contentRepository,
        Config $config,
        ContentHelper $contentHelper,
        Query $query)
    {
        $this->notifications = $notifications;
        $this->contentRepository = $contentRepository;
        $this->config = $config;
        $this->contentHelper = $contentHelper;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('label', [$this, 'getLabel']),
            new TwigFilter('type', [$this, 'getType']),
            new TwigFilter('selected', [$this, 'getSelected']),
            new TwigFilter('date', [$this, 'getDate'], ['needs_environment' => true]),
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field_factory', [$this, 'fieldFactory']),
            new TwigFunction('list_templates', [$this, 'getListTemplates']),
            new TwigFunction('select_options', [$this, 'selectOptions']),
        ];
    }

    public function getDate(Environment $twig, $date, $format = null, $timezone = null)
    {
        if ($format === null && ! $date instanceof \DateInterval) {
            $format = $this->config->get('general/date_format', null);
        }

        if ($timezone === null) {
            $timezone = $this->config->get('general/timezone', null);
        }

        return twig_date_format_filter($twig, $date, $format, $timezone);
    }

    public function fieldFactory(string $name, $definition = null): Field
    {
        if (is_iterable($definition)) {
            $definition = collect($definition);
        }

        if ($definition === null || $definition->isEmpty()) {
            $definition = new Collection(['type' => 'generic']);
        }

        return FieldRepository::factory($definition, $name);
    }

    public function getLabel(Field $field): string
    {
        return $field->getDefinition()->get('label');
    }

    public function getType(Field $field): string
    {
        return $field->getType();
    }

    /**
     * @return array|Content|null
     */
    public function getSelected(Field\SelectField $field, $returnsingle = false, $returnarray = false)
    {
        $definition = $field->getDefinition();

        if ($definition->get('type') !== 'select' || ! $field->isContentSelect() || $definition->get('mode') === 'format') {
            return $this->notifications->warning(
                'Incorrect usage of `selected`-filter',
                'The `selected`-filter can only be applied to a field of `type: select`, and it must be used as a selector for other content, and without `mode: format`.'
            );
        }

        $ids = $field->getValue();
        // Find records by their respective ids
        $records = collect($this->contentRepository->findBy(['id' => $ids]));

        // Sort the results in the order of the $ids.
        $order = array_flip($ids);
        $records = $records->sortBy(function (Content $record) use ($order) {
            return $order[$record->getId()];
        })->values()->toArray();

        if ($returnsingle || (! $returnarray && $definition->get('multiple') === false)) {
            return current($records);
        }

        return $records;
    }

    public function getListTemplates(TemplateselectField $field): Collection
    {
        $definition = $field->getDefinition();
        $current = current($field->getValue());

        $finder = new Finder();
        $templatesDir = $this->config->get('theme/template_directory');
        $templatesPath = $this->config->getPath('theme', true, $templatesDir);

        $filter = $definition->get('filter', '/^[^_]*\.twig$/');

        if (! Str::isValidRegex($filter)) {
            $filter = Str::isValidRegex('/' . $filter . '/') ? '/' . $filter . '/' : '/^[^_]*\.twig$/';
        }

        $finder
            ->files()
            ->in($templatesPath)
            ->path($definition->get('path'))
            ->sortByName()
            ->filter(function (SplFileInfo $file) use ($filter) {
                return preg_match($filter, $file->getRelativePathname()) === 1;
            });

        $options = [];

        if ($field->allowEmpty()) {
            $options = [[
                'key' => '',
                'value' => '(choose a template)',
                'selected' => false,
            ]];
        }

        foreach ($finder as $file) {
            $options[] = [
                'key' => $file->getRelativePathname(),
                'value' => $file->getRelativePathname(),
            ];

            if ($current === $file->getRelativePathname()) {
                $current = false;
            }
        }

        if ($current !== false) {
            $options[] = [
                'key' => $current,
                'value' => $current . ' (file seems to be missing)',
            ];
        }

        return new Collection($options);
    }

    public function selectOptions(Field $field): Collection
    {
        if (! $field instanceof SelectField) {
            return collect([]);
        }

        $values = $field->getOptions();

        if (is_iterable($values)) {
            return $this->selectOptionsArray($field);
        }

        return $this->selectOptionsContentType($field);
    }

    private function selectOptionsArray(Field $field): Collection
    {
        if (! $field instanceof SelectField) {
            return collect([]);
        }

        $values = $field->getOptions();
        $currentValues = $field->getValue();

        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the one.
        if ($field->allowEmpty()) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }

        if (! is_iterable($values)) {
            return new Collection($options);
        }

        foreach ($values as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
                'selected' => in_array($key, $currentValues, true),
            ];
        }

        return new Collection($options);
    }

    private function selectOptionsContentType(Field $field): Collection
    {
        [ $contentTypeSlug, $format ] = explode('/', $field->getDefinition()->get('values'));

        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the one.
        if ($field->allowEmpty()) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }

        if (empty($maxAmount = $field->getDefinition()->get('limit'))) {
            $maxAmount = $this->config->get('general/maximum_listing_select', 200);
        }

        $order = $field->getDefinition()->get('order', '');

        $params = [
            'limit' => $maxAmount,
            'order' => $order,
        ];

        $options = array_merge($options, $this->selectOptionsHelper($contentTypeSlug, $params, $field, $format));

        return new Collection($options);
    }

    /**
     * Decorated by `\Bolt\Cache\SelectOptionsCacher`
     */
    public function selectOptionsHelper(string $contentTypeSlug, array $params, Field $field, string $format): array
    {
        /** @var Content[] $records */
        $records = iterator_to_array($this->query->getContent($contentTypeSlug, $params)->getCurrentPageResults());

        $options = [];

        foreach ($records as $record) {
            if ($field->getDefinition()->get('mode') === 'format') {
                $formattedKey = $this->contentHelper->get($record, $field->getDefinition()->get('format'));
            }
            $options[] = [
                'key' => $formattedKey ?? $record->getId(),
                'value' => $this->contentHelper->get($record, $format),
            ];
        }

        return $options;
    }
}
