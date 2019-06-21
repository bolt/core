<?php

declare(strict_types=1);

namespace Bolt\Widget;

use Bolt\Common\Exception\ParseException;
use Bolt\Common\Json;
use Bolt\Version;
use Bolt\Widget\Injector\AdditionalTarget;
use Bolt\Widget\Injector\RequestZone;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

class NewsWidget extends BaseWidget implements TwigAware, RequestAware, CacheAware, StopwatchAware
{
    use CacheTrait;
    use StopwatchTrait;

    protected $name = 'News Widget';
    protected $target = AdditionalTarget::WIDGET_BACK_DASHBOARD_ASIDE_TOP;
    protected $priority = 150;
    protected $template = '@bolt/widgets/news.twig';
    protected $zone = RequestZone::BACKEND;
    protected $cacheDuration = 3600;

    protected function run(array $params = []): ?string
    {
        $news = $this->getNews();

        if (isset($news['information'])) {
            $currentItem = $news['information'];
        } else {
            $currentItem = $news['error'];
        }

        $context = [
            'title' => $currentItem['title'],
            'news' => $currentItem['teaser'],
            'link' => $currentItem['link'],
            'datechanged' => $currentItem['datechanged'],
            'datefetched' => date('Y-m-d H:i:s'),
        ];

        return parent::run($context);
    }

    /**
     * Get the news from Bolt HQ.
     */
    private function getNews(): array
    {
        $source = 'https://news.bolt.cm/';
        $options = $this->fetchNewsOptions();

        // $this->app['logger.system']->info('Fetching from remote server: ' . $source, ['event' => 'news']);

        try {
            $client = new Client(['base_uri' => $source]);
            $fetchedNewsData = $client->request('GET', '/', $options)->getBody()->getContents();
        } catch (RequestException $e) {
            return [
                'error' => [
                    'type' => 'error',
                    'title' => 'Unable to fetch news!',
                    'teaser' => "<p>Unable to connect to ${source}</p>",
                    'link' => null,
                    'datechanged' => '0000-01-01 00:00:00'
                ],
            ];
        }

        try {
            $fetchedNewsItems = Json::parse($fetchedNewsData);
        } catch (ParseException $e) {
            // Just move on, a user-friendly notice is returned below.
            $fetchedNewsItems = [];
        }

        $news = [];

        // Iterate over the items, pick the first news-item that
        // applies and the first alert we need to show
        foreach ($fetchedNewsItems as $item) {
            $type = isset($item->type) ? $item->type : 'information';
            if (! isset($news[$type])
                && (empty($item->target_version) || Version::compare($item->target_version, '>'))
            ) {
                $news[$type] = $item;
            }
        }

        if ($news) {
            return $news;
        }

        // $this->app['logger.system']->error('Invalid JSON feed returned', ['event' => 'news']);

        return [
            'error' => [
                'type' => 'error',
                'title' => 'Unable to fetch news!',
                'teaser' => "<p>Invalid JSON feed returned by ${source}</p>",
            ],
        ];
    }

    /**
     * Get the guzzle options.
     */
    private function fetchNewsOptions(): array
    {
        // @todo Determine current database driver
        $driver = 'unknown';

        return [
            'query' => [
                'v' => Version::VERSION,
                'p' => PHP_VERSION,
                'db' => $driver,
                'name' => $this->getRequest()->getHost(),
            ],
            'connect_timeout' => 5,
            'timeout' => 10,
        ];
    }
}
