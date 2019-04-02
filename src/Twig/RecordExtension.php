<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Menu\FrontendMenuBuilder;
use Bolt\Menu\MenuBuilder;
use Bolt\Repository\TaxonomyRepository;
use Bolt\Utils\Excerpt;
use Doctrine\Common\Collections\Collection;
use Pagerfanta\Pagerfanta;
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
    /** @var MenuBuilder */
    private $menuBuilder;

    /** @var string */
    private $sidebarMenu = null;

    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /** @var FrontendMenuBuilder */
    private $frontendMenuBuilder;

    public function __construct(MenuBuilder $menuBuilder, TaxonomyRepository $taxonomyRepository, FrontendMenuBuilder $frontendMenuBuilder)
    {
        $this->menuBuilder = $menuBuilder;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->frontendMenuBuilder = $frontendMenuBuilder;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions()
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('excerpt', [$this, 'excerpt'], $safe),
            new TwigFunction('list_templates', [$this, 'getListTemplates']),
            new TwigFunction('pager', [$this, 'pager'], $env + $safe),
            new TwigFunction('menu', [$this, 'getMenu'], $env + $safe),
            new TwigFunction('sidebar_menu', [$this, 'getSidebarMenu']),
            new TwigFunction('selectoptionsfromarray', [$this, 'selectoptionsfromarray']),
            new TwigFunction('taxonomyoptions', [$this, 'taxonomyoptions']),
            new TwigFunction('taxonomyvalues', [$this, 'taxonomyvalues']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function getListTemplates(): string
    {
        return 'list_templates placeholder';
    }

    public function pager(Environment $twig, Pagerfanta $records, string $template = '_sub_pager.twig', string $class = 'pagination', string $theme = 'default', int $surround = 3)
    {
        $context = [
            'records' => $records,
            'surround' => $surround,
            'class' => $class,
            'theme' => $theme,
        ];

        return $twig->render($template, $context);
    }

    public function getMenu(Environment $twig, ?string $name = null, string $template = '_sub_menu.twig', string $class = '', bool $withsubmenus = true): string
    {
        $context = [
            'menu' => $this->frontendMenuBuilder->getMenu($name),
            'class' => $class,
            'withsubmenus' => $withsubmenus,
        ];

        return $twig->render($template, $context);
    }

    public static function excerpt(string $text, int $length = 100): string
    {
        return Excerpt::getExcerpt($text, $length);
    }

    public function getSidebarMenu($pretty = false): string
    {
        if (! $this->sidebarMenu) {
            $menuArray = $this->menuBuilder->getMenu();
            $options = $pretty ? JSON_PRETTY_PRINT : 0;
            $this->sidebarMenu = json_encode($menuArray, $options);
        }

        return $this->sidebarMenu;
    }

    public function icon(?Content $record = null, string $icon = 'question-circle'): string
    {
        if ($record instanceof Content) {
            $icon = $record->getIcon();
        }

        $icon = str_replace('fa-', '', $icon);

        return "<i class='fas mr-2 fa-${icon}'></i>";
    }

    public function selectoptionsfromarray(Field $field): LaravelCollection
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

    public function taxonomyoptions($taxonomy): LaravelCollection
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

    public function taxonomyvalues(Collection $current, $taxonomy): LaravelCollection
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
