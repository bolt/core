<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Content\MenuBuilder;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Symfony\Component\Translation\TranslatorInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class ContentHelperExtension extends AbstractExtension
{
    /** @var MenuBuilder */
    private $menuBuilder;

    /** @var TranslatorInterface */
    private $translator;

    /** @var string */
    private $menu = null;

    /**
     * ContentHelperExtension constructor.
     *
     * @param MenuBuilder         $menuBuilder
     * @param TranslatorInterface $translator
     */
    public function __construct(MenuBuilder $menuBuilder, TranslatorInterface $translator)
    {
        $this->menuBuilder = $menuBuilder;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        return [
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];

        return [
            new TwigFunction('sidebarmenu', [$this, 'sidebarmenu']),
            new TwigFunction('jsonlabels', [$this, 'jsonlabels']),
            new TwigFunction('jsonrecords', [$this, 'jsonrecords']),
            new TwigFunction('fieldfactory', [$this, 'fieldfactory']),
            new TwigFunction('selectoptionsfromarray', [$this, 'selectoptionsfromarray']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function sidebarmenu($pretty = false)
    {
        if (!$this->menu) {
            $menuArray = $this->menuBuilder->getMenu();
            $options = $pretty ? JSON_PRETTY_PRINT : 0;
            $this->menu = json_encode($menuArray, $options);
        }

        return $this->menu;
    }

    public function fieldfactory($name, $definition)
    {
        $field = Field::factory($definition['type']);
        $field->setName($name);
        $field->setDefinition($name, $definition);

        return $field;
    }

    public function icon($record, $icon = 'question-circle')
    {
        if ($record instanceof Content) {
            $icon = $record->getDefinition()->get('icon_one') ?: $record->getDefinition()->get('icon_many');
        }

        $icon = str_replace('fa-', '', $icon);

        return "<i class='fas mr-2 fa-$icon'></i>";
    }

    /**
     * @param array $labels
     * @param bool  $pretty
     *
     * @return string
     */
    public function jsonlabels(array $labels, $pretty = false): string
    {
        $result = [];
        $options = $pretty ? JSON_PRETTY_PRINT : 0;

        foreach ($labels as $label) {
            $key = is_array($label) ? $label[0] : $label;
            $result[$key] = $this->translator->trans(...(array) $label);
        }

        return json_encode($result, $options);
    }

    /**
     * @param $records
     * @param bool $pretty
     *
     * @return string
     */
    public function jsonrecords($records, $pretty = false): string
    {
        $result = [];
        $options = $pretty ? JSON_PRETTY_PRINT : 0;

        foreach ($records as $record) {
            $result[] = $record->getSummary();
        }

        return json_encode($result, $options);
    }

    public function selectoptionsfromarray(Field $field)
    {
        $values = $field->getDefinition()->get('values');
        $currentValues = $field->getValue();

        $options = [];

        if ($field->getDefinition()->get('required', false)) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }

        if (!is_iterable($values)) {
            return $options;
        }

        foreach ($values as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
                'selected' => in_array($key, $currentValues, true),
            ];
        }

        return $options;
    }
}
