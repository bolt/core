<?php

declare(strict_types=1);

namespace Bolt\Twig;

use Bolt\Canonical;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Entity\Field;
use Bolt\Entity\Field\Excerptable;
use Bolt\Entity\Field\ImageField;
use Bolt\Entity\Field\ImagelistField;
use Bolt\Entity\Field\TemplateselectField;
use Bolt\Entity\Taxonomy;
use Bolt\Enum\Statuses;
use Bolt\Log\LoggerTrait;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\TaxonomyRepository;
use Bolt\Storage\Query;
use Bolt\Utils\ContentHelper;
use Bolt\Utils\Excerpt;
use Bolt\Utils\Html;
use Bolt\Utils\Sanitiser;
use Pagerfanta\Pagerfanta;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Exception\InvalidParameterException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Contracts\Translation\TranslatorInterface;
use Tightenco\Collect\Support\Collection;
use Twig\Environment;
use Twig\Extension\AbstractExtension;
use Twig\Markup;
use Twig\TwigFilter;
use Twig\TwigFunction;

class ContentExtension extends AbstractExtension
{
    use LoggerTrait;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var CsrfTokenManagerInterface */
    private $csrfTokenManager;

    /** @var Security */
    private $security;

    /** @var Request */
    private $request;

    /** @var Config */
    private $config;

    /** @var Query */
    private $query;

    /** @var TaxonomyRepository */
    private $taxonomyRepository;

    /** @var TranslatorInterface */
    private $translator;

    /** @var Canonical */
    private $canonical;

    /** @var ContentHelper */
    private $contentHelper;

    /** @var Notifications */
    private $notifications;

    /** @var Sanitiser */
    private $sanitiser;

    public function __construct(
        UrlGeneratorInterface $urlGenerator,
        ContentRepository $contentRepository,
        CsrfTokenManagerInterface $csrfTokenManager,
        Security $security,
        RequestStack $requestStack,
        Config $config,
        Query $query,
        TaxonomyRepository $taxonomyRepository,
        TranslatorInterface $translator,
        Canonical $canonical,
        ContentHelper $contentHelper,
        Notifications $notifications,
        Sanitiser $sanitiser
    ) {
        $this->urlGenerator = $urlGenerator;
        $this->contentRepository = $contentRepository;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->security = $security;
        $this->request = $requestStack->getCurrentRequest() ?? Request::createFromGlobals();
        $this->config = $config;
        $this->query = $query;
        $this->taxonomyRepository = $taxonomyRepository;
        $this->translator = $translator;
        $this->canonical = $canonical;
        $this->contentHelper = $contentHelper;
        $this->notifications = $notifications;
        $this->sanitiser = $sanitiser;
    }

    /**
     * {@inheritdoc}
     */
    public function getFilters(): array
    {
        $safe = [
            'is_safe' => ['html'],
        ];
        $env = ['needs_environment' => true];

        return [
            new TwigFilter('title', [$this, 'getTitle'], $safe),
            new TwigFilter('title_fields_names', [$this, 'getTitleFields']),
            new TwigFilter('image', [$this, 'getImage']),
            new TwigFilter('excerpt', [$this, 'getExcerpt'], $safe),
            new TwigFilter('previous', [$this, 'getPreviousContent']),
            new TwigFilter('next', [$this, 'getNextContent']),
            new TwigFilter('link', [$this, 'getLink']),
            new TwigFilter('edit_link', [$this, 'getEditLink']),
            new TwigFilter('taxonomies', [$this, 'getTaxonomies']),
            new TwigFilter('has_path', [$this, 'hasPath']),
            new TwigFilter('allow_twig', [$this, 'allowTwig'], $env),
            new TwigFilter('status_options', [$this, 'statusOptions']),
            new TwigFilter('feature', [$this, 'getSpecialFeature']),
            new TwigFilter('sanitise', [$this, 'sanitise']),
        ];
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
            new TwigFunction('excerpt', [$this, 'getExcerpt'], $safe),
            new TwigFunction('previous_record', [$this, 'getPreviousContent']),
            new TwigFunction('next_record', [$this, 'getNextContent']),
            new TwigFunction('list_templates', [$this, 'getListTemplates']),
            new TwigFunction('pager', [$this, 'pager'], $env + $safe),
            new TwigFunction('select_options', [$this, 'selectOptions']),
            new TwigFunction('taxonomy_options', [$this, 'taxonomyOptions']),
            new TwigFunction('taxonomy_values', [$this, 'taxonomyValues']),
            new TwigFunction('icon', [$this, 'icon'], $safe),
        ];
    }

    public function getAnyTitle(?Content $content, int $length = 120): string
    {
        $title = $this->getTitle($content, '', $length);

        if (! empty($title)) {
            return $title;
        }

        if ($content->getDefinition()->has('locales')) {
            $locales = $content->getDefinition()->get('locales');

            foreach ($locales as $locale) {
                $title = $this->getTitle($content, $locale, $length);

                if (! empty($title)) {
                    return $title;
                }
            }
        }

        return '(untitled)';
    }

    public function getTitle(?Content $content, string $locale = '', int $length = 120): string
    {
        if (! $content instanceof Content) {
            return '<mark>No content given</mark>';
        }

        if (empty($locale)) {
            $locale = $this->request->getLocale();
        }

        if (ContentHelper::isSuitable($content)) {
            $title = $this->contentHelper->get($content, $content->getDefinition()->get('title_format'), $locale);
        } else {
            $title = ContentHelper::getFieldBasedTitle($content, $locale);
        }

        return Html::trimText($title, $length);
    }

    public function getTitleFieldsNames(Content $content): array
    {
        if (ContentHelper::isSuitable($content)) {
            return ContentHelper::getFieldNames($content->getDefinition()->get('title_format'));
        }

        return ContentHelper::guessTitleFields($content);
    }

    /**
     * @return ImageField|array|null
     */
    public function getImage(?Content $content, bool $onlyValues = false)
    {
        if (! $content) {
            return null;
        }

        foreach ($content->getFields() as $field) {
            if ($field instanceof ImageField && $field->get('filename')) {
                return $onlyValues ? $field->getValue() : $field;
            }

            if ($field instanceof ImagelistField) {
                $firstImage = current($field->getValue());
                if ($firstImage && $firstImage->get('filename')) {
                    return $onlyValues ? $firstImage->getValue() : $firstImage;
                }
            }
        }

        return null;
    }

    /**
     * @param string|Markup|Content|Field $content
     */
    public function getExcerpt($content, int $length = 280, bool $includeTitle = false, ?string $focus = null, bool $wrap = false): string
    {
        if (is_string($content) || $content instanceof Markup || $content instanceof Field) {
            return Excerpt::getExcerpt((string) $content, $length, $focus);
        }

        if (! $content instanceof Content) {
            return '<mark>No content given</mark>';
        }

        if (ContentHelper::isSuitable($content, 'excerpt_format')) {
            $excerpt = $this->contentHelper->get($content, $content->getDefinition()->get('excerpt_format'));
        } else {
            $excerpt = $this->getFieldBasedExcerpt($content, $length, $includeTitle);
        }

        if ($wrap) {
            $pre = '<p>';
            $post = '</p>';
        } else {
            $pre = '';
            $post = '';
        }

        return $pre . Excerpt::getExcerpt($excerpt, $length, $focus) . $post;
    }

    private function getFieldBasedExcerpt(Content $content, int $length, bool $includeTitle = false): string
    {
        $excerptParts = [];

        if ($includeTitle) {
            $title = $this->getTitle($content);
            if ($title !== '') {
                $title = Html::trimText($title, $length);
                $excerptParts[] = $title;
            }
        }

        $skipFields = $this->getTitleFieldsNames($content);

        foreach ($content->getFields() as $field) {
            if ($field instanceof Excerptable && in_array($field->getName(), $skipFields, true) === false) {
                $excerptPart = $field->__toString();
                if ($excerptPart !== '') {
                    $excerptParts[] = $excerptPart;
                }
            }
        }

        $specialChars = ['.', ',', '!', '?'];
        $excerpt = array_reduce($excerptParts, function (string $excerpt, string $part) use ($specialChars): string {
            if (in_array(mb_substr($part, -1), $specialChars, true) === false) {
                // add period at end of string if it doesn't have sentence end
                $part .= '.';
            }

            return $excerpt . $part . ' ';
        }, '');

        return rtrim($excerpt, '. ');
    }

    public function getPreviousContent(?Content $content, string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        if (! $content instanceof Content) {
            return null;
        }

        return $this->getAdjacentContent($content, 'previous', $byColumn, $sameContentType);
    }

    public function getNextContent(?Content $content, string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        if (! $content instanceof Content) {
            return null;
        }

        return $this->getAdjacentContent($content, 'next', $byColumn, $sameContentType);
    }

    private function getAdjacentContent(Content $content, string $direction, string $byColumn = 'id', bool $sameContentType = true): ?Content
    {
        if ($byColumn !== 'id') {
            // @todo implement ordering by other columns/fields too
            throw new \RuntimeException('Ordering content by column other than ID is not yet implemented');
        }

        $byColumn = filter_var($byColumn, FILTER_SANITIZE_STRING);
        $contentType = $sameContentType ? $content->getContentType() : null;

        return $this->contentRepository->findAdjacentBy($byColumn, $direction, $content->getId(), $contentType);
    }

    public function isCurrent(Environment $env, ?Content $content): bool
    {
        if (! $content instanceof Content) {
            return false;
        }

        // If we have a $record set in the Global Twig env, we can simply
        // compare that to what's passed in.
        if (array_key_exists('record', $env->getGlobals())) {
            return $env->getGlobals()['record'] === $content;
        }

        // Otherwise, we'll have to compare 'slugOrId' and 'contentTypeSlug' as
        // grabbed from the Request
        $recordParams = [
            'slugOrId' => $content->getSlug() ?: $content->getId(),
            'contentTypeSlug' => $content->getContentTypeSingularSlug(),
        ];

        $routeParams = $this->request->get('_route_params');

        return isset($routeParams['slugOrId']) &&
            isset($routeParams['contentTypeSlug']) &&
            $recordParams['slugOrId'] === $routeParams['slugOrId'] &&
            $recordParams['contentTypeSlug'] === $routeParams['contentTypeSlug'];
    }

    public function allowTwig(Environment $env, Content $content): void
    {
        $content->setTwig($env);
    }

    /**
     * @param Content|Taxonomy $contentOrTaxonomy
     */
    public function getLink($contentOrTaxonomy, bool $canonical = false, ?string $locale = null): ?string
    {
        if ($contentOrTaxonomy instanceof Content) {
            if ($contentOrTaxonomy->getId() === null) {
                return null;
            }

            if ($contentOrTaxonomy->getDefinition()->get('viewless') && $this->getSpecialFeature($contentOrTaxonomy) !== 'homepage') {
                return null;
            }

            return $this->contentHelper->getLink($contentOrTaxonomy, $canonical, $locale);
        }

        if ($contentOrTaxonomy instanceof Taxonomy) {
            return $this->urlGenerator->generate('taxonomy', [
                'taxonomyslug' => $contentOrTaxonomy->getTaxonomyTypeSingularSlug(),
                'slug' => $contentOrTaxonomy->getSlug(),
            ]);
        }

        $body = sprintf("You have called the <code>|link</code> filter with a parameter of type '%s', but <code>|link</code> accepts record (Content) or taxonomy.", gettype($contentOrTaxonomy));
        $this->notifications->warning('Incorrect use of <code>|link</code> filter', $body);

        return null;
    }

    public function getEditLink(?Content $content): ?string
    {
        if (! $content instanceof Content || $content->getId() === null || ! $this->security->getUser() || ! $this->security->isGranted('ROLE_ADMIN')) {
            return null;
        }

        return $this->generateLink('bolt_content_edit', ['id' => $content->getId()]);
    }

    public function getDeleteLink(?Content $content, bool $absolute = false): ?string
    {
        if (! $content instanceof Content || $content->getId() === null || ! $this->security->getUser() || ! $this->security->isGranted('ROLE_ADMIN')) {
            return null;
        }

        $params = [
            'id' => $content->getId(),
            'token' => (string) $this->csrfTokenManager->getToken('delete'),
        ];

        return $this->generateLink('bolt_content_delete', $params, $absolute);
    }

    public function getDuplicateLink(?Content $content, bool $absolute = false): ?string
    {
        if (! $content instanceof Content || $content->getId() === null || ! $this->security->getUser() || ! $this->security->isGranted('ROLE_ADMIN')) {
            return null;
        }

        return $this->generateLink('bolt_content_duplicate', ['id' => $content->getId()], $absolute);
    }

    public function getStatusLink(?Content $content, bool $absolute = false): ?string
    {
        if (! $content instanceof Content || $content->getId() === null || ! $this->security->getUser() || ! $this->security->isGranted('ROLE_ADMIN')) {
            return null;
        }

        $params = [
            'id' => $content->getId(),
            'token' => (string) $this->csrfTokenManager->getToken('status'),
        ];

        return $this->generateLink('bolt_content_status', $params, $absolute);
    }

    private function generateLink(string $route, array $params, $canonical = false): string
    {
        try {
            $link = $this->canonical->generateLink($route, $params, $canonical);
        } catch (InvalidParameterException $e) {
            $this->logger->notice('Could not create URL for route \'' . $route . '\'. Perhaps the ContentType was changed or removed. Try clearing the cache');
            $link = '';
        }

        return $link;
    }

    public function getTaxonomies(?Content $content): Collection
    {
        if (! $content instanceof Content) {
            $body = sprintf("You have called the <code>|taxonomies</code> filter with a parameter of type '%s', but <code>|taxonomies</code> accepts record (Content).", gettype($content));
            $this->notifications->warning('Incorrect use of <code>|taxonomies</code> filter', $body);

            return new Collection();
        }

        $taxonomies = [];

        $definition = $content->getDefinition();

        if ($definition && is_iterable($definition->get('taxonomy'))) {
            foreach ($definition->get('taxonomy') as $taxonomy) {
                $taxonomies[$taxonomy] = [];
            }
        }

        foreach ($content->getTaxonomies() as $taxonomy) {
            $taxonomies[$taxonomy->getType()][$taxonomy->getSlug()] = $taxonomy;
        }

        return new Collection($taxonomies);
    }

    public function getListTemplates(TemplateselectField $field): Collection
    {
        $definition = $field->getDefinition();
        $current = current($field->getValue());

        $finder = new Finder();
        $finder
            ->files()
            ->in($this->config->getPath('theme'))
            ->name($definition->get('filter', '/^[^_].*\.twig$/'))
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

        return new Collection($options);
    }

    public function pager(Environment $twig, ?Pagerfanta $records = null, string $template = '@bolt/helpers/_pager_basic.html.twig', string $class = 'pagination', int $surround = 3)
    {
        $params = array_merge(
            $this->request->get('_route_params'),
            $this->request->query->all()
        );

        if (! $records && array_key_exists('records', $twig->getGlobals())) {
            $records = $twig->getGlobals()['records'];
        }

        $context = [
            'records' => $records,
            'surround' => $surround,
            'class' => $class,
            'route' => $this->request->get('_route'),
            'routeParams' => $params,
        ];

        return $twig->render($template, $context);
    }

    public function selectOptions(Field $field): Collection
    {
        $values = $field->getDefinition()->get('values');

        if (is_iterable($values)) {
            return $this->selectOptionsArray($field);
        }

        return $this->selectOptionsContentType($field);
    }

    private function selectOptionsArray(Field $field): Collection
    {
        $values = $field->getDefinition()->get('values');
        $currentValues = $field->getValue();

        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the one.
        if (! $field->getDefinition()->get('required', true)) {
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

        if (! $field->getDefinition()->get('required')) {
            $options[] = [
                'key' => '',
                'value' => '',
            ];
        }

        if (! empty($field->getDefinition()->get('limit'))) {
            $maxAmount = $field->getDefinition()->get('limit');
        } else {
            $maxAmount = $this->config->get('maximum_listing_select', 200);
        }
        $order = $field->getDefinition()->get('order', '');

        $params = [
            'limit' => $maxAmount,
            'order' => $order,
        ];

        /** @var Content[] $records */
        $records = iterator_to_array($this->query->getContent($contentTypeSlug, $params)->getCurrentPageResults());

        foreach ($records as $record) {
            $options[] = [
                'key' => $record->getId(),
                'value' => $this->contentHelper->get($record, $format),
            ];
        }

        return new Collection($options);
    }

    public function taxonomyOptions(Collection $taxonomy): Collection
    {
        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the first option.
        if ($taxonomy['required'] === false) {
            $options[] = [
                'key' => '',
                'value' => '',
            ];
        }

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

        return new Collection($options);
    }

    public function taxonomyValues(\Doctrine\Common\Collections\Collection $current, Collection $taxonomy): Collection
    {
        $values = [];

        foreach ($current as $value) {
            $values[$value->getType()][] = $value->getSlug();
        }

        if ($taxonomy['slug']) {
            $values = $values[$taxonomy['slug']] ?? [];
        }

        if (empty($values) && $taxonomy['required']) {
            $values[] = key($taxonomy['options']);
        }

        return new Collection($values);
    }

    public function icon(?Content $record = null, string $icon = 'question-circle'): string
    {
        if ($record instanceof Content) {
            $icon = $record->getIcon();
        }

        $icon = str_replace('fa-', '', $icon);

        return "<i class='fas mr-2 fa-${icon}'></i>";
    }

    public function hasPath(Content $record, string $path): bool
    {
        try {
            $result = $this->query
                ->getContent($path);

            if ($result instanceof Content) {
                return $record === $result;
            }

            $pager = $result
                ->setMaxPerPage(1)
                ->setCurrentPage(1);
            $content = iterator_to_array($pager->getCurrentPageResults())[0];

            return $record === $content;
        } catch (\Throwable $e) {
        }

        return false;
    }

    public function statusOptions(Content $record)
    {
        $options = [];

        foreach (Statuses::all() as $option) {
            $options[] = [
                'key' => $option,
                'value' => $this->translator->trans('status.' . $option),
                'selected' => $option === $record->getStatus(),
            ];
        }

        return $options;
    }

    public function getSpecialFeature(Content $record): string
    {
        if ($this->isHomepage($record)) {
            return 'homepage';
        }

        if ($this->is404($record)) {
            return '404';
        }

        if ($this->is403($record)) {
            return '403';
        }

        if ($this->isMaintenance($record)) {
            return 'maintenance';
        }

        return '';
    }

    public function isHomepage(Content $content): bool
    {
        return $this->contentHelper->isHomepage($content);
    }

    public function is404(Content $content): bool
    {
        return $this->contentHelper->is404($content);
    }

    public function is403(Content $content): bool
    {
        return $this->contentHelper->is403($content);
    }

    public function isMaintenance(Content $content): bool
    {
        return $this->contentHelper->isMaintenance($content);
    }

    public function isHomepageListing(ContentType $contentType): bool
    {
        $homepageSetting = $this->config->get('general/homepage');

        if ($homepageSetting === $contentType->get('slug') || $homepageSetting === $contentType->get('singular_slug')) {
            return true;
        }

        return false;
    }

    public function sanitise(string $html)
    {
        return $this->sanitiser->clean($html);
    }
}
