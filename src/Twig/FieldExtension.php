<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Repository\ContentRepository;
use Tightenco\Collect\Support\Collection;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;
use Twig\TwigFunction;

class FieldExtension extends AbstractExtension
{
    /** @var Notifications */
    private $notifications;

    /** @var ContentRepository */
    private $contentRepository;

    public function __construct(Notifications $notifications, ContentRepository $contentRepository)
    {
        $this->notifications = $notifications;
        $this->contentRepository = $contentRepository;
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
        ];
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('field_factory', [$this, 'fieldFactory']),
        ];
    }

    public function fieldFactory(string $name, ?Collection $definition = null): Field
    {
        if ($definition === null || $definition->isEmpty()) {
            $definition = new Collection(['type' => 'generic']);
        }

        return Field::factory($definition, $name);
    }

    public function getLabel(Field $field): string
    {
        return $field->getDefinition()->get('label');
    }

    public function getType(Field $field): string
    {
        return $field->getDefinition()->get('type');
    }

    /**
     * @return array|Content|null
     */
    public function getSelected(Field $field, $returnsingle = false, $returnarray = false)
    {
        $definition = $field->getDefinition();

        if ($definition->get('type') !== 'select' || ! $field->isContentSelect()) {
            return $this->notifications->warning(
                'Incorrect usage of `selected`-filter',
                'The `selected`-filter can only be applied to a field of `type: select`, and it must be used as a selector for other content.'
            );
        }

        $records = [];
        foreach ($field->getValue() as $id) {
            $record = $this->contentRepository->findOneBy(['id' => (int)$id]);

            if ($record) {
                $records[] = $record;
            }
        }

        if ($returnsingle || (! $returnarray && $definition->get('multiple') === false)) {
            return current($records);
        }

        return $records;
    }
}
