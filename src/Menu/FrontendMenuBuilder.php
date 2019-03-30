<?php

namespace Bolt\Menu;

use Bolt\Configuration\Config;
use Bolt\Entity\Content;
use Bolt\Repository\ContentRepository;
use Bolt\Repository\FieldRepository;
use Knp\Menu\FactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FrontendMenuBuilder
{

    /** @var Config */
    private $config;

    /** @var UrlGeneratorInterface */
    private $urlGenerator;

    /** @var Request */
    private $request;

    /** @var ContentRepository */
    private $contentRepository;

    /** @var FieldRepository */
    private $fieldRepository;

    public function __construct(
        Config $config,
        UrlGeneratorInterface $urlGenerator,
        RequestStack $requestStack,
        ContentRepository $contentRepository,
        FieldRepository $fieldRepository)
    {
        $this->config = $config;
        $this->urlGenerator = $urlGenerator;
        $this->request = $requestStack->getCurrentRequest();
        $this->contentRepository = $contentRepository;
        $this->fieldRepository = $fieldRepository;
    }

    public function getMenu(string $name = '')
    {
        $menuConfig = $this->config->get('menu');

        if ($name === '' && is_iterable($menuConfig)) {
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

    private function updateItem($item)
    {
        $item['uri'] = $this->setUri($item['link']);

        if (is_iterable($item['submenu'])) {
            foreach ($item['submenu'] as $sub) {
                $this->updateItem($sub);
            }
        }
    }

    public function setUri($link = ''): string
    {
        $link = trim($link, '/');

        // Special case for "Homepage"
        if ($link == 'homepage') {
            return $this->urlGenerator->generate('homepage');
        }

        // If it looks like `contenttype/slug`, get the Record.
        if (strpos($link, '/') && strpos($link, 'http') === false) {
            $content = $this->getContent($link);
            if ($content) {
                return $content->getExtras()['link'];
            }
        }

        // Otherwise trust the user. ¯\_(ツ)_/¯ 
        return $link;
    }

    public function getContent(string $link): ?Content
    {
        list($contentType, $slugOrId) = explode('/', $link);

        // First, try to get it if the id is numeric.
        if (is_numeric($slugOrId)) {
            return $this->contentRepository->findOneBy(['id' => (int) $slugOrId]);
        }

        // Otherwise fetch it by getting it from the slug
        $field = $this->fieldRepository->findOneBySlug($slugOrId);
        if ($field === null) {
            return null;
        }

        return $field->getContent();
    }

}