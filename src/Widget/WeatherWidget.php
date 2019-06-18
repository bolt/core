<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WeatherWidget extends BaseWidget implements TwigAware, CacheAware, StopwatchAware
{
    use CacheTrait;
    use StopwatchTrait;

    protected $name = 'Weather Widget';
    protected $target = AdditionalTarget::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
    protected $priority = 200;
    protected $template = '@bolt/widgets/weather.twig';
    protected $zone = RequestZone::BACKEND;
    protected $cacheDuration = 3600;

    public function run(array $params = []): string
    {
//        $location = $this->getLocation($ip);

        $weather = $this->getWeather();

        $context = ['weather' => $weather];

        return parent::run($context);
    }

    private function getWeather(): array
    {
        $url = 'wttr.in/?format=%c|%C|%h|%t|%w|%l|%m|%M|%p|%P';

        try {
            $client = new Client();
            $details = explode('|', trim($client->request('GET', $url)->getBody()->getContents()));
        } catch (RequestException $e) {
            $details = [];
        }

        return $details;
    }
}
