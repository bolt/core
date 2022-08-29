<?php

declare(strict_types=1);

namespace Bolt\Controller\Frontend;

use Bolt\Controller\TwigAwareController;
use Bolt\Repository\ContentRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomepageController extends TwigAwareController implements FrontendZoneInterface
{
    /**
     * @Route("/", methods={"GET|POST"}, name="homepage")
     * @Route(
     *     "/{_locale}/",
     *     methods={"GET|POST"},
     *     name="homepage_locale",
     *     requirements={"_locale": "%app_locales%"})
     */
    public function homepage(ContentRepository $contentRepository): Response
    {
        $homepage = $this->config->get('theme/homepage') ?: $this->config->get('general/homepage');
        $templates = $this->templateChooser->forHomepage();

        if ($homepage === null) {
            return $this->render($templates);
        }

        $homepageTokens = explode('/', $homepage);
        $contentType = $this->config->getContentType($homepageTokens[0]);

        if (! $contentType) {
            $message = sprintf('Homepage is set to `%s`, but that ContentType is not defined', $homepage);

            throw new \Exception($message);
        }

        // Perhaps we need a listing instead. If so, forward the Request there
        if (! $contentType->get('singleton') && ! isset($homepageTokens[1])) {
            $params = array_merge($this->request->query->all(), [
                'contentTypeSlug' => $homepage,
                '_locale' => $this->request->getLocale()
            ]);

            return $this->forward('Bolt\Controller\Frontend\ListingController::listing', $params);
        }

        // @todo Get $homepage content, using "setcontent"
        $record = $contentRepository->findOneBy([
            'contentType' => $contentType->get('slug'),
            'id' => $homepageTokens[1] ?? 1,
        ]);
        if (! $record) {
            $record = $contentRepository->findOneBy(['contentType' => $homepageTokens[0]]);
        }

        return $this->renderSingle($record, false, $templates);
    }
}
