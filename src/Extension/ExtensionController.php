<?php

declare(strict_types=1);

namespace Bolt\Extension;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ExtensionController extends AbstractController
{
    use ServicesTrait;
    use ConfigTrait;
}
