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
    protected $cacheDuration = 1800;

    public function run(array $params = []): ?string
    {
        $weather = $this->getWeather();

        if (empty($weather)) {
            return null;
        }

        return parent::run(['weather' => $weather]);
    }

    private function getWeather(): array
    {
        $url = 'wttr.in/?format=%c|%C|%h|%t|%w|%l|%m|%M|%p|%P';

        $details = [];

        try {
            $client = new Client();
            $result = $client->request('GET', $url)->getBody()->getContents();
            if (substr_count($result, '|') === 9) {
                $details = explode('|', trim($result));
            }
        } catch (RequestException $e) {
            // Do nothing, fall through to empty array
        }

        return $details;
    }
}
