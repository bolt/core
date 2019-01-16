<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Content\MenuBuilder;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Repository\TaxonomyRepository;
use Doctrine\Common\Collections\Collection;
use Symfony\Contracts\Translation\TranslatorInterface;
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

    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /**
     * ContentHelperExtension constructor.
     */
    public function __construct(MenuBuilder $menuBuilder, TranslatorInterface $translator, TaxonomyRepository $taxonomyRepository)
    {
        $this->menuBuilder = $menuBuilder;
        $this->translator = $translator;
        $this->taxonomyRepository = $taxonomyRepository;
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
            new TwigFunction('selectoptionsfromarray', [$this, 'selectoptionsfromarray']),
            new TwigFunction('taxonomyoptions', [$this, 'taxonomyoptions']),
            new TwigFunction('taxonomyvalues', [$this, 'taxonomyvalues']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function sidebarmenu($pretty = false)
    {
        if (! $this->menu) {
            $menuArray = $this->menuBuilder->getMenu();
            $options = $pretty ? JSON_PRETTY_PRINT : 0;
            $this->menu = json_encode($menuArray, $options);
        }

        return $this->menu;
    }

    public function icon($record, $icon = 'question-circle')
    {
        if ($record instanceof Content) {
            $icon = $record->getDefinition()->get('icon_one') ?: $record->getDefinition()->get('icon_many');
        }

        $icon = str_replace('fa-', '', $icon);

        return "<i class='fas mr-2 fa-${icon}'></i>";
    }

    public function jsonlabels(array $labels, bool $pretty = false): string
    {
        $result = [];
        $options = $pretty ? JSON_PRETTY_PRINT : 0;

        foreach ($labels as $label) {
            $key = is_array($label) ? $label[0] : $label;
            $result[$key] = $this->translator->trans(...(array) $label);
        }

        return json_encode($result, $options);
    }

    public function jsonrecords($records, bool $pretty = false): string
    {
        $result = [];
        $options = $pretty ? JSON_PRETTY_PRINT : 0;

        foreach ($records as $record) {
            $result[] = $record->getSummary();
        }

        return json_encode($result, $options);
    }

    public function selectoptionsfromarray(Field $field): \Tightenco\Collect\Support\Collection
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

        if (! is_iterable($values)) {
            return collect($options);
        }

        foreach ($values as $key => $value) {
            $options[] = [
                'key' => $key,
                'value' => $value,
                'selected' => in_array($key, $currentValues, true),
            ];
        }

        return collect($options);
    }

    public function taxonomyoptions($taxonomy): \Tightenco\Collect\Support\Collection
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

        return collect($options);
    }

    public function taxonomyvalues(Collection $current, $taxonomy): \Tightenco\Collect\Support\Collection
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

        return collect($values);
    }
}
