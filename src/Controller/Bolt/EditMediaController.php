<?php

declare(strict_types=1);
/**
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Controller\Bolt;

use Bolt\Configuration\Config;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class EditMediaController.
 *
 * @Route("/bolt")
 * @Security("has_role('ROLE_ADMIN')")
 */
class EditMediaController
{
    /**
     * @var Config
     */
    private $config;

    public function __construct(Config $config)
    {
        $this->config = $config;
    }

    public function edit()
    {
    }
}
