<?php

use Bolt\Kernel;

require __DIR__. '/../../vendor/autoload.php';

$appKernel = new Kernel('test', false);
$appKernel->boot();

return $appKernel->getContainer();
