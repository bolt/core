<?php

declare(strict_types=1);

namespace Bolt\Controller\Backend\Async;

use Bolt\Configuration\Config;
use Bolt\Twig\FieldExtension;
use Illuminate\Support\Collection;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Security("is_granted('upload')")
 */
class SelectOptionsController extends AbstractController implements AsyncZoneInterface
{
    public function __construct(
        private readonly Config $config,
        private readonly FieldExtension $fieldExtension
    ) {
    }

    /**
     * Based on Bolt\Twig\FieldExtension.
     */
    #[Route(path: '/select-options', name: 'bolt_async_select_options', methods: [Request::METHOD_GET])]
    public function handleSelectOptions(Request $request): JsonResponse
    {
        [$contentTypeSlug, $format] = explode('/', (string) $request->get('values'));

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
