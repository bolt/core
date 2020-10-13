<?php

declare(strict_types=1);

use Bolt\Kernel;
use Symfony\Component\ErrorHandler\Debug;
use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;

require __DIR__.'/../vendor/autoload.php';

// If APP_ENV is set Dotenv is skipped (so .env / .env.local etc. are _not_ processed)
if (! isset($_SERVER['APP_ENV'])) {
    if (! class_exists(Dotenv::class)) {
        throw new \RuntimeException('APP_ENV environment variable is not defined. You need to define environment variables for configuration or add "symfony/dotenv" as a Composer dependency to load variables from a .env file.');
    }
    (new Dotenv())->bootEnv(__DIR__.'/../.env');
}

$env = $_SERVER['APP_ENV'] ?? 'dev';
$debug = (bool) ($_SERVER['APP_DEBUG'] ?? ($env !== 'prod'));

if ($debug) {
    umask(0000);

    Debug::enable();
}

if (! empty($_SERVER['TRUSTED_PROXIES'])) {
    Request::setTrustedProxies(explode(',', $_SERVER['TRUSTED_PROXIES']), Request::HEADER_X_FORWARDED_ALL ^ Request::HEADER_X_FORWARDED_HOST);
}

if (! empty($_SERVER['TRUSTED_HOSTS'])) {
    Request::setTrustedHosts(explode(',', $_SERVER['TRUSTED_HOSTS']));
}

$kernel = new Kernel($env, $debug);
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
