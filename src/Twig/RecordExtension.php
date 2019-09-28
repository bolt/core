<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\TaxonomyRepository;
use Doctrine\Common\Collections\Collection;
use Pagerfanta\Pagerfanta;
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
    /** @var ContentRepository */
    private $contentRepository;

    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /** @var Request */
    private $request;

    /** @var Config */
    private $config;

    public function __construct(ContentRepository $contentRepository, TaxonomyRepository $taxonomyRepository, RequestStack $requestStack, Config $config)
    {
        $this->contentRepository = $contentRepository;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->request = $requestStack->getCurrentRequest();
        $this->config = $config;
    }

    /**
     * {@inheritdoc}
     */
    public function getFunctions(): array
    {
        $safe = ['is_safe' => ['html']];
        $env = ['needs_environment' => true];

        return [
            new TwigFunction('list_templates', [$this, 'getListTemplates']),
            new TwigFunction('pager', [$this, 'pager'], $env + $safe),
            new TwigFunction('selectOptions', [$this, 'selectOptions']),
            new TwigFunction('taxonomyoptions', [$this, 'taxonomyoptions']),
            new TwigFunction('taxonomyvalues', [$this, 'taxonomyvalues']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function getListTemplates(): string
    {
        return 'list_templates placeholder';
    }

    public function pager(Environment $twig, Pagerfanta $records, string $template = 'helpers/_pager_basic.html.twig', string $class = 'pagination', int $surround = 3)
    {
        $context = [
            'records' => $records,
            'surround' => $surround,
            'class' => $class,
            'route' => $this->request->get('_route'),
            'routeParams' => $this->request->get('_route_params'),
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

    public function selectOptions(Field $field): LaravelCollection
    {
        $values = $field->getDefinition()->get('values');

        if (is_array($values)) {
            return $this->selectOptionsArray($field);
        }
        return $this->selectOptionsContentType($field);
    }

    private function selectOptionsArray(Field $field): LaravelCollection
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

    private function selectOptionsContentType(Field $field): LaravelCollection
    {
        [ $contentTypeSlug, $fieldNames ] = explode('/', $field->getDefinition()->get('values'));

        // @todo Actually do something with these, instead of using a default.
        $fieldNames = explode(',', $fieldNames);

        $currentValues = $field->getValue();

        $options = [];

        if ($field->getDefinition()->get('required', false)) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }

        $contentType = ContentType::factory($contentTypeSlug, $this->config->get('contenttypes'));

        $maxAmount = $this->config->get('maximum_listing_select', 1000);

        /** @var Content[] $records */
        $records = $this->contentRepository->findForListing(1, $maxAmount, $contentType);

        foreach ($records as $record) {
            $options[] = [
                'key' => $record->getId(),
                'value' => sprintf('%s. %s (%s)', $record->getId(), $record->getExtras()['title'], $record->getStatus()),
                'selected' => in_array($record->getId(), $currentValues, true),
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
