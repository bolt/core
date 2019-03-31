<?php

declare(strict_types=1);

namespace Bolt\Menu;

use Bolt\Collection\DeepCollection;
use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontendMenuBuilder
{
    /** @var Config */
    private $config;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var FieldRepository */
    private $fieldRepository;

    public function __construct(
        Config $config,
        UrlGeneratorInterface $urlGenerator,
        ContentRepository $contentRepository,
        FieldRepository $fieldRepository
    ) {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->contentRepository = $contentRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function getMenu(?string $name = null): ?DeepCollection
    {
        /** @var DeepCollection $menuConfig */
        $menuConfig = $this->config->get('menu');

        if (! $name && is_iterable($menuConfig)) {
            $menu = $menuConfig->first();
        } elseif ($name !== '' && isset($menuConfig[$name])) {
            $menu = $menuConfig[$name];
        } else {
            return null;
        }

        foreach ($menu as $item) {
            $this->updateItem($item);
        }

        return $menu;
    }

    private function updateItem($item): void
    {
        $item['uri'] = $this->setUri($item['link']);

        if (is_iterable($item['submenu'])) {
            foreach ($item['submenu'] as $sub) {
                $this->updateItem($sub);
            }
        }
    }

    private function setUri($link = ''): string
    {
        $link = trim($link, '/');

        // Special case for "Homepage"
        if ($link === 'homepage') {
            return $this->urlGenerator->generate('homepage');
        }

        // If it looks like `contenttype/slug`, get the Record.
        if (mb_strpos($link, '/') && mb_strpos($link, 'http') === false) {
            $content = $this->getContent($link);
            if ($content) {
                return $content->getExtras()['link'];
            }
        }

        // Otherwise trust the user. ¯\_(ツ)_/¯
        return $link;
    }

    private function getContent(string $link): ?Content
    {
        $parts = explode('/', $link);

        // First, try to get it if the id is numeric.
        if (is_numeric($parts[1])) {
            return $this->contentRepository->findOneBy(['id' => (int) $parts[1]]);
        }

        // Otherwise fetch it by getting it from the slug
        $field = $this->fieldRepository->findOneBySlug($parts[1]);
        if ($field === null) {
            return null;
        }

        return $field->getContent();
    }
}
