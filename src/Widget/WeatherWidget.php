<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class WeatherWidget extends BaseWidget implements TwigAware
{
    protected $name = 'Weather Widget';
    protected $target = AdditionalTarget::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
    protected $priority = 200;
    protected $template = '@bolt/widgets/weather.twig';
    protected $zone = RequestZone::BACKEND;

    /** @var string Open API key, don't use more than once per second */
    public const KEY = '0acbdeea56dfafe244ac87707c5fdcb2';

    public function run(array $params = []): string
    {
        $ip = $this->getIP();

        $location = $this->getLocation($ip);

        $weather = $this->getWeather($location);

        $context = [
            'location' => $location,
            'weather' => $weather,
        ];

        return parent::run($context);
    }

    private function getIP(): string
    {
        try {
            $client = new Client(['base_uri' => 'http://checkip.dyndns.com/']);
            $dnsResponse = $client->request('GET', '/')->getBody()->getContents();
        } catch (RequestException $e) {
            $dnsResponse = 'Just assume we are at 127.0.0.1';
        }

        preg_match('/(\d{1,3}\.){3}\d{1,3}/', $dnsResponse, $matches);

        return $matches[0];
    }

    private function getLocation(string $ip): array
    {
        try {
            $client = new Client(['base_uri' => "http://ipinfo.io/{$ip}"]);
            $details = json_decode($client->request('GET', '/')->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $details = [];
        }

        return $details;
    }

    private function getWeather(array $location): array
    {
        [$lat, $lon] = explode(',', $location['loc']);

        $url = sprintf(
            'https://api.openweathermap.org/data/2.5/weather?lat=%s&lon=%s&appid=%s&units=metric',
            $lat,
            $lon,
            $this::KEY
        );

        try {
            $client = new Client();
            $details = json_decode($client->request('GET', $url)->getBody()->getContents(), true);
        } catch (RequestException $e) {
            $details = [];
        }

        return $details;
    }
}
