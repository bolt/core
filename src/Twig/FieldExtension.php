<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
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

    public function __construct(Notifications $notifications, ContentRepository $contentRepository, Config $config)
    {
        $this->notifications = $notifications;
        $this->contentRepository = $contentRepository;
        $this->config = $config;
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
        return $field->getType() ?? $field->getDefinition()->get('type');
    }

    /**
     * @return array|Content|null
     */
    public function getSelected(Field\SelectField $field, $returnsingle = false, $returnarray = false)
    {
        $definition = $field->getDefinition();

        if ($definition->get('type') !== 'select' || ! $field->isContentSelect()) {
            return $this->notifications->warning(
                'Incorrect usage of `selected`-filter',
                'The `selected`-filter can only be applied to a field of `type: select`, and it must be used as a selector for other content.'
            );
        }

        $ids = $field->getValue();
        // Find records by their respective ids
        $records = $this->contentRepository->findBy(['id' => $ids]);

        if ($returnsingle || (! $returnarray && $definition->get('multiple') === false)) {
            return current($records);
        }

        return $records;
    }
}
