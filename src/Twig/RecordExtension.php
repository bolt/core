<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Repository\TaxonomyRepository;
use Bolt\Storage\Query;
use Doctrine\Common\Collections\Collection;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Tightenco\Collect\Support\Collection as LaravelCollection;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

/**
 * Record helpers Twig extension.
 *
 * @todo merge with ContentExtension?
 */
class RecordExtension extends AbstractExtension
{
    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /** @var Request */
    private $request;

    /** @var Config */
    private $config;

    /** @var Query */
    private $query;

    public function __construct(Query $query, TaxonomyRepository $taxonomyRepository, RequestStack $requestStack, Config $config)
    {
        $this->taxonomyRepository = $taxonomyRepository;
        $this->request = $requestStack->getCurrentRequest();
        $this->config = $config;
        $this->query = $query;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('list_templates', [$this, 'getListTemplates']),
            new TwigFunction('pager', [$this, 'pager'], $env + $safe),
            new TwigFunction('select_options', [$this, 'selectOptions']),
            new TwigFunction('taxonomyoptions', [$this, 'taxonomyoptions']),
            new TwigFunction('taxonomyvalues', [$this, 'taxonomyvalues']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function pager(Environment $twig, Pagerfanta $records, string $template = '@bolt/helpers/_pager_basic.html.twig', string $class = 'pagination', int $surround = 3)
    {
        $params = array_merge(
            $this->request->get('_route_params'),
            $this->request->query->all()
        );

        $context = [
            'records' => $records,
            'surround' => $surround,
            'class' => $class,
            'route' => $this->request->get('_route'),
            'routeParams' => $params,
        ];

        return $twig->render($template, $context);
    }

    public function icon(?Content $record = null, string $icon = 'question-circle'): string
    {
        if ($record instanceof Content) {
            $icon = $record->getIcon();
        }

        $icon = str_replace('fa-', '', $icon);

        return "<i class='fas mr-2 fa-${icon}'></i>";
    }

    public function selectOptions(Field\SelectField $field): LaravelCollection
    {
        $values = $field->getDefinition()->get('values');

        if (is_iterable($values)) {
            return $this->selectOptionsArray($field);
        }
        return $this->selectOptionsContentType($field);
    }

    private function selectOptionsArray(Field\SelectField $field): LaravelCollection
    {
        $values = $field->getDefinition()->get('values');
        $currentValues = $field->getValue();

        $options = [];

        if (! $field->getDefinition()->get('required', true)) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }

        if (! is_iterable($values)) {
            return new LaravelCollection($options);
        }

        foreach ($values as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
                'selected' => in_array($key, $currentValues, true),
            ];
        }

        return new LaravelCollection($options);
    }

    private function selectOptionsContentType(Field\SelectField $field): LaravelCollection
    {
        [ $contentTypeSlug, $format ] = explode('/', $field->getDefinition()->get('values'));

        $options = [];

        if ($field->getDefinition()->get('required', false)) {
            $options[] = [
                'key' => '',
                'value' => '',
            ];
        }

        $maxAmount = $this->config->get('maximum_listing_select', $field->getDefinition('limit', 200));
        $orderBy = $field->getDefinition()->get('sort', '');

        $params = [
            'limit' => $maxAmount,
            'order' => $orderBy,
        ];

        /** @var Content[] $records */
        $records = iterator_to_array($this->query->getContent($contentTypeSlug, $params)->getCurrentPageResults());

        foreach ($records as $record) {
            $options[] = [
                'key' => $record->getId(),
                'value' => $this->composeSelectValue($format, $record),
            ];
        }

        return new LaravelCollection($options);
    }

    private function composeSelectValue(string $format, Content $record): string
    {
        if (empty($format)) {
            $format = '{title} (№ {id}, {status})';
        }

        return preg_replace_callback(
            '/{([a-z]+)}/i',
            function ($match) use ($record) {
                if ($match[1] === 'id') {
                    return $record->getId();
                }

                if ($match[1] === 'status') {
                    return $record->getStatus();
                }

                if ($record->hasField($match[1])) {
                    return $record->getField($match[1]);
                }

                if (array_key_exists($match[1], $record->getExtras())) {
                    return $record->getExtras()[$match[1]];
                }

                return '(unknown)';
            },
            $format
        );
    }

    public function getListTemplates(TemplateselectField $field): LaravelCollection
    {
        $definition = $field->getDefinition();
        $current = current($field->getValue());

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->config->getPath('theme'))
            ->name($definition->get('filter', '*.twig'))
            ->path($definition->get('path'));

        $options = [];

        if ($definition->get('required') === false) {
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

        return new LaravelCollection($options);
    }

    public function taxonomyoptions(LaravelCollection $taxonomy): LaravelCollection
    {
        $options = [];

        if ($taxonomy['behaves_like'] === 'tags') {
            $allTaxonomies = $this->taxonomyRepository->findBy(['type' => $taxonomy['slug']]);
            foreach ($allTaxonomies as $item) {
                $taxonomy['options'][$item->getSlug()] = $item->getName();
            }
        }

        foreach ($taxonomy['options'] as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
            ];
        }

        return new LaravelCollection($options);
    }

    public function taxonomyvalues(Collection $current, LaravelCollection $taxonomy): LaravelCollection
    {
        $values = [];

        foreach ($current as $value) {
            $values[$value->getType()][] = $value->getSlug();
        }

        if ($taxonomy['slug']) {
            $values = $values[$taxonomy['slug']] ?? [];
        }

        if (empty($values) && ! $taxonomy['allow_empty']) {
            $values[] = key($taxonomy['options']);
        }

        return new LaravelCollection($values);
    }
}
