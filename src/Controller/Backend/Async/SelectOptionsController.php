<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Twig\FieldExtension;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tightenco\Collect\Support\Collection;

/**
 * @Security("is_granted('upload')")
 */
class SelectOptionsController extends AbstractController implements AsyncZoneInterface
{
    /** @var Config */
    private $config;

    /** @var FieldExtension */
    private $fieldExtension;

    public function __construct(Config $config, FieldExtension $fieldExtension)
    {
        $this->config = $config;
        $this->fieldExtension = $fieldExtension;
    }

    /**
     * Based on Bolt\Twig\FieldExtension.
     *
     * @Route("/select-options", name="bolt_async_select_options", methods={"GET"})
     */
    public function handleSelectOptions(Request $request): JsonResponse
    {
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

        $options = array_merge($options, $this->fieldExtension->selectOptionsHelper($contentTypeSlug, $params, $field, $format));

        return new JsonResponse(new Collection($options));
    }
}
