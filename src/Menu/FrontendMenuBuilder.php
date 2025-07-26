<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Bolt\Configuration\Content\ContentType;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Twig\ContentExtension;
use Bolt\Twig\LocaleExtension;
use Bolt\Utils\Html;
use RuntimeException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Twig\Environment;

final class FrontendMenuBuilder implements FrontendMenuBuilderInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly UrlGeneratorInterface $urlGenerator,
        private readonly ContentRepository $contentRepository,
        private readonly ContentExtension $contentExtension,
        private readonly LocaleExtension $localeExtension,
        private readonly string $defaultLocale
    ) {
    }

    public function buildMenu(Environment $twig, ?string $name = null): array
    {
        /** @var DeepCollection $menuConfig */
        $menuConfig = $this->config->get('menu');

        if ($name === null) {
            $menu = $menuConfig->first()->toArray();
        } elseif ($name !== '' && isset($menuConfig[$name])) {
            $menu = $menuConfig[$name]->toArray();
        } else {
            throw new RuntimeException("Tried to build non-existing menu: {$name}");
        }

        return array_map(fn ($item): array => $this->setUris($twig, $item), $menu);
    }

    private function setUris(Environment $twig, array $item): array
    {
        [$title, $item['uri']] = $this->generateUri($item['link']);

        if (empty($item['title'])) {
            $item['title'] = $title;
        }

        $currentLocale = $this->localeExtension->getHtmlLang($twig);
        if (is_iterable($item['label'])) {
            if (array_key_exists($currentLocale, $item['label'])) {
                $label = $item['label'][$currentLocale];
            } elseif (array_key_exists($this->defaultLocale, $item['label'])) {
                $label = $item['label'][$this->defaultLocale];
            } else {
                $label = $item['title'] ?? '';
            }

            $item['label'] = $label;
        }

        if (is_iterable($item['submenu'])) {
            $item['submenu'] = array_map(fn ($sub): array => $this->setUris($twig, $sub), $item['submenu']);
        }

        return $item;
    }

    private function generateUri(string $link = ''): array
    {
        $trimmedLink = mb_trim($link, '/');

        // Special case for "Homepage"
        if ($trimmedLink === 'homepage' || $trimmedLink === $this->config->get('general/homepage')) {
            return ['Home', $this->urlGenerator->generate('homepage')];
        }

        // If it looks like `contenttype/slug`, get the Record.
        if (preg_match('/^[a-zA-Z\-\_]+\/[0-9a-zA-Z\-\_]+$/', $trimmedLink)) {
            $content = $this->getContent($trimmedLink);
            if ($content) {
                return [$this->contentExtension->getTitle($content), $this->contentExtension->getLink($content)];
            }
        }

        // Otherwise trust the user. ¯\_(ツ)_/¯
        return ['', Html::makeAbsoluteLink($link)];
    }

    private function getContent(string $link): ?Content
    {
        [$contentTypeSlug, $slug] = explode('/', $link);

        // First, try to get it if the id is numeric.
        if (is_numeric($slug)) {
            return $this->contentRepository->findOneById((int) $slug);
        }

        /** @var ContentType $contentType */
        $contentType = $this->config->getContentType($contentTypeSlug);
        if (! $contentType instanceof ContentType) {
            return null;
        }

        return $this->contentRepository->findOneBySlug($slug, $contentType);
    }
}
