<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Twig\FieldExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;
use Bolt\Cache\SelectOptionsCacher;

/**
 * @Security("is_granted('upload')")
 */
class SelectOptionsController extends AbstractController implements AsyncZoneInterface
{
    // use CsrfTrait;

    /** @var Config */
    private $config;

    /** @var Request */
    private $request;

    /** @var FieldExtension */
    private $fieldExtension;


    public function __construct(Config              $config,
                                RequestStack        $requestStack,
                                SelectOptionsCacher $selectOptionsCacher)
    {
        $this->config = $config;
        $this->request = $requestStack->getCurrentRequest();
        $this->fieldExtension = $selectOptionsCacher;
    }

    /**
     * Based on Bolt\Twig\FieldExtension.
     *
     * @Route("/select-options", name="bolt_async_select_options", methods={"GET"})
     */
    public function handleSelectOptions(Request $request): JsonResponse
    {
        // TODO: Need to get a field definition somewhere ...

        [ $contentTypeSlug, $format ] = explode('/', $request->get('values'));

        if (empty($maxAmount = $request->get('limit'))) {
            $maxAmount = $this->config->get('general/maximum_listing_select', 200);
        }

        $order = $request->get('order');

        $options = [];

        // We need to add this as a 'dummy' option for when the user is allowed
        // not to pick an option. This is needed, because otherwise the `select`
        // would default to the one.
        /*
        if ($field->allowEmpty()) {
            $options[] = [
                'key' => '',
                'value' => '',
                'selected' => false,
            ];
        }
        */

        $params = [
            'limit' => $maxAmount,
            'order' => $order,
        ];

        $field = $this->fieldExtension->fieldFactory($request->get('name'));

        $options = array_merge($options, $this->fieldExtension->selectOptionsHelper($contentTypeSlug, $params, $field, $format)); // is this part cached?

        $response = new JsonResponse(new Collection($options));
        /* -- This does NOT seem to work, we want to store responses so we won't DDOS ourselves.
        $response->setCache([
            'must_revalidate' => false,
            'no_cache' => false,
            'max_age'  => 36000,
        ]);
        */

        return $response;
    }


}
