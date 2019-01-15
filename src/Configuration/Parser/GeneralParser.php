<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Common\Arr;
use Bolt\Utils\Html;
use Bolt\Utils\Str;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

class GeneralParser extends BaseParser
{
    /**
     * Read and parse the config.yaml and config_local.yaml configuration files.
     */
    public function parse(): Collection
    {
        $defaultconfig = $this->getDefaultConfig();
        $tempconfig = $this->parseConfigYaml('config.yaml');
        $tempconfiglocal = $this->parseConfigYaml('config_local.yaml');
        $general = Arr::replaceRecursive($defaultconfig, Arr::replaceRecursive($tempconfig, $tempconfiglocal));

        // Make sure Bolt's mount point is OK:
        $path = $general['branding']['path'];
        if (is_string($path)) {
            $path = '/' . Str::makeSafe($path);
        } else {
            $path = '/';
        }
        $general['branding']['path'] = $path;

        // Set the link in branding, if provided_by is set.
        $general['branding']['provided_link'] = Html::providerLink(
            $general['branding']['provided_by']
        );

        $general['database'] = $this->parseDatabase($general['database']);

        return collect($general);
    }

    /**
     * Assume sensible defaults for a number of options.
     */
    protected function getDefaultConfig(): array
    {
        return [
            'database' => [
                'driver' => 'sqlite',
                'host' => 'localhost',
                'slaves' => [],
                'dbname' => 'bolt',
                'prefix' => 'bolt_',
                'charset' => 'utf8',
                'collate' => 'utf8_unicode_ci',
                'randomfunction' => '',
            ],
            'sitename' => 'Default Bolt site',
            'locale' => null,
            'recordsperpage' => 10,
            'recordsperdashboardwidget' => 5,
            'systemlog' => [
                'enabled' => true,
            ],
            'changelog' => [
                'enabled' => false,
            ],
            'debuglog' => [
                'enabled' => false,
                'level' => 'DEBUG',
                'filename' => 'bolt-debug.log',
            ],
            'debug' => null,
            'debug_show_loggedoff' => false,
            'debug_error_level' => null,
            'production_error_level' => null,
            'debug_enable_whoops' => false, /* @deprecated. Deprecated since 3.2, to be removed in 4.0 */
            'debug_error_use_symfony' => false,
            'debug_permission_audit_mode' => false,
            'debug_trace_argument_limit' => 4,
            'strict_variables' => null,
            'theme' => 'base-2016',
            'listing_template' => 'listing.html.twig',
            'listing_records' => '5',
            'listing_sort' => 'datepublish DESC',
            'caching' => [
                'config' => true,
                'templates' => true,
                'request' => false,
                'duration' => 10,
            ],
            'wysiwyg' => [
                'images' => false,
                'tables' => false,
                'fontcolor' => false,
                'align' => false,
                'subsuper' => false,
                'embed' => false,
                'anchor' => false,
                'underline' => false,
                'strike' => false,
                'blockquote' => false,
                'codesnippet' => false,
                'specialchar' => false,
                'styles' => false,
                'ck' => [
                    'autoParagraph' => true,
                    'contentsCss' => [
                        ['css/ckeditor-contents.css', 'bolt'],
                        ['css/ckeditor.css', 'bolt'],
                    ],
                    'filebrowserWindowWidth' => 640,
                    'filebrowserWindowHeight' => 480,
                ],
            ],
            'liveeditor' => true,
            'canonical' => null,
            'developer_notices' => false,
            'cookies_use_remoteaddr' => true,
            'cookies_use_browseragent' => false,
            'cookies_use_httphost' => true,
            'cookies_lifetime' => 14 * 24 * 3600,
            'enforce_ssl' => false,
            'thumbnails' => [
                'default_thumbnail' => [160, 120],
                'default_image' => [1000, 750],
                'quality' => 75,
                'cropping' => 'crop',
                'notfound_image' => 'bolt_assets://img/default_notfound.png',
                'error_image' => 'bolt_assets://img/default_error.png',
                'only_aliases' => false,
            ],
            'accept_file_types' => explode(',', 'twig,html,js,css,scss,gif,jpg,jpeg,png,ico,zip,tgz,txt,md,doc,docx,pdf,epub,xls,xlsx,csv,ppt,pptx,mp3,ogg,wav,m4a,mp4,m4v,ogv,wmv,avi,webm,svg'),
            'hash_strength' => 10,
            'branding' => [
                'name' => 'Bolt',
                'path' => '/bolt',
                'provided_by' => [],
            ],
            'maintenance_mode' => false,
            'headers' => [
                'x_frame_options' => true,
            ],
            'htmlcleaner' => [
                'allowed_tags' => explode(',', 'div,span,p,br,hr,s,u,strong,em,i,b,li,ul,ol,mark,blockquote,pre,code,tt,h1,h2,h3,h4,h5,h6,dd,dl,dh,table,tbody,thead,tfoot,th,td,tr,a,img,address,abbr,iframe'),
                'allowed_attributes' => explode(',', 'id,class,style,name,value,href,Bolt,alt,title,width,height,frameborder,allowfullscreen,scrolling'),
            ],
            'performance' => [
                'http_cache' => [
                    'options' => [],
                ],
                'timed_records' => [
                    'interval' => 3600,
                    'use_cron' => false,
                ],
            ],
        ];
    }

    /**
     * Parse and fine-tune the database configuration.
     */
    protected function parseDatabase(array $options): Collection
    {
        // Make sure prefix ends with underscore
        if (mb_substr($options['prefix'], mb_strlen($options['prefix']) - 1) !== '_') {
            $options['prefix'] .= '_';
        }

        // Parse master connection parameters
        $master = $this->parseConnectionParams($options);
        // Merge master connection into options
        $options = collect($options)->merge($master);

        // Add platform specific random functions
        $driver = \Bolt\Common\Str::replaceFirst($options['driver'], 'pdo_', '');
        if ($driver === 'sqlite') {
            $options['driver'] = 'pdo_sqlite';
            $options['randomfunction'] = 'RANDOM()';
        } elseif (in_array($driver, ['mysql', 'mysqli'], true)) {
            $options['driver'] = 'pdo_mysql';
            $options['randomfunction'] = 'RAND()';
        } elseif (in_array($driver, ['pgsql', 'postgres', 'postgresql'], true)) {
            $options['driver'] = 'pdo_pgsql';
            $options['randomfunction'] = 'RANDOM()';
        }

        // Specify the wrapper class for the connection
        // $options['wrapperClass'] = Database\Connection::class;

        // Parse SQLite separately since it has to figure out database path
        if ($driver === 'sqlite') {
            return $this->parseSqliteOptions($options);
        }

        // If no slaves return with single connection
        if (empty($options['slaves'])) {
            return $options;
        }

        // Specify we want a master slave connection
        // $options['wrapperClass'] = Database\MasterSlaveConnection::class;

        // Add master connection where MasterSlaveConnection looks for it.
        $options['master'] = $master;

        // Parse each slave connection parameters
        foreach ($options['slaves'] as $name => $slave) {
            $options['slaves'][$name] = $this->parseConnectionParams($slave, $master);
        }

        return $options;
    }

    /**
     * Parses params to valid connection parameters.
     *
     * - Defaults are merged into the params
     * - Bolt keys are converted to Doctrine keys
     * - Invalid keys are filtered out
     *
     * @param array|string $params
     */
    protected function parseConnectionParams($params, ?Collection $defaults = null): Collection
    {
        // Handle host shortcut
        if (is_string($params)) {
            $params = ['host' => $params];
        }

        $params = collect($params);

        // Convert keys from Bolt
        $replacements = [
            'databasename' => 'dbname',
            'username' => 'user',
        ];
        foreach ($replacements as $old => $new) {
            if (isset($params[$old])) {
                $params[$new] = $params[$old];
                unset($params[$old]);
            }
        }

        // Merge with defaults
        if ($defaults !== null) {
            $params = $defaults->merge($params);
        }

        // Filter out invalid keys
        $validKeys = [
            'user', 'password', 'host', 'port', 'dbname', 'charset',      // common
            'path', 'memory',                                             // Qqlite
            'unix_socket', 'driverOptions', 'collate',                    // MySql
            'sslmode',                                                    // PostgreSQL
            'servicename', 'service', 'pooled', 'instancename', 'server', // Oracle
            'persistent',                                                 // SQL Anywhere
        ];
        return $params->intersectByKeys($validKeys);
    }

    /**
     * Fine-tune Sqlite configuration parameters.
     */
    protected function parseSqliteOptions(Collection $config): Collection
    {
        if (isset($config['memory']) && $config['memory']) {
            // If in-memory, no need to parse paths
            unset($config['path']);

            return $config;
        }
        // Prevent SQLite driver from trying to use in-memory connection
        unset($config['memory']);

        // Get path from config or use database path
        $path = $config['path'] ?? $this->pathResolver->resolve('database');
        if (Path::isRelative($path)) {
            $path = $this->pathResolver->resolve($path);
        }

        // If path has filename with extension, use that
        if (Path::hasExtension($path)) {
            $config['path'] = $path;

            return $config;
        }

        // Use database name for filename
        $filename = basename($config['dbname']);
        if (! Path::hasExtension($filename)) {
            $filename .= '.db';
        }

        // Join filename with database path
        $config['path'] = Path::join($path, $filename);

        return $config;
    }
}
