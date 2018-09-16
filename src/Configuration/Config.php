<?php
/**
 *
 *
 * @author Bob den Otter <bobdenotter@gmail.com>
 */

namespace Bolt\Configuration;

use Bolt\Helpers\Html;
use Bolt\Helpers\Str;
use Bolt\Collection\Arr;
use Bolt\Collection\Bag;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Webmozart\PathUtil\Path;

class Config
{
    /** @var array */
    protected $data;

    /** @var Symfony\Component\Config\FileLocator */
    private $fileLocator;

    public function __construct()
    {
        $this->initialize();
    }

    private function initialize()
    {
        $configDirectories = array(dirname(dirname(__DIR__)).'/config/bolt');
        $this->fileLocator = new FileLocator($configDirectories);

//        $this->cacheFile = $this->app['filesystem']->getFile('cache://config-cache.json');

        $data = null;

//        $data = $this->loadCache();
        if ($data === null) {
            $data = $this->getConfig();

            // If we have to reload the config, we will also want to make sure
            // the DB integrity is checked.
//            $this->app['schema.timer']->setCheckRequired();
        }

        $this->data = $data;

//        $this->loadTheme();

//        $this->setCKPath();
//        $this->parseTemplatefields();
    }

    /**
     * Load the configuration from the various YML files.
     *
     * @return array
     */
    public function getConfig()
    {
        $config = Bag::from([
            'general' => $this->parseGeneral(),
            // 'taxonomy' => $this->parseTaxonomy(),
            // 'contenttypes' => $this->parseContentTypes($config['general']),
            // 'menu' => $this->parseConfigYaml('menu.yml'),
            //'routing' => $this->parseConfigYaml('routing.yml'),
            //'permissions' => $this->parseConfigYaml('permissions.yml'),
            //'extensions' => $this->parseConfigYaml('extensions.yml'),
        ]);

        return $config;
    }

    /**
     * Read and parse the config.yaml and config_local.yaml configuration files.
     *
     * @return array
     */
    protected function parseGeneral()
    {
        $defaultconfig = $this->getDefaultConfig();
        $tempconfig = $this->parseConfigYaml('config.yaml');
        $tempconfiglocal = $this->parseConfigYaml('config_local.yaml');
        $mergedarray = Arr::replaceRecursive($defaultconfig, Arr::replaceRecursive($tempconfig, $tempconfiglocal));
        $general = Bag::fromRecursive($mergedarray);

        // Make sure old settings for 'accept_file_types' are not still picked up. Before 1.5.4 we used to store them
        // as a regex-like string, and we switched to an array. If we find the old style, fall back to the defaults.
        if (isset($general['accept_file_types']) && !is_array($general['accept_file_types'])) {
            unset($general['accept_file_types']);
        }

        // Make sure Bolt's mount point is OK:
        $general['branding']['path'] = '/' . Str::makeSafe($general['branding']['path']);

        // Set the link in branding, if provided_by is set.
        $general['branding']['provided_link'] = Html::providerLink(
            $general['branding']['provided_by']
        );

        $general['database'] = $this->parseDatabase($general['database']);

        return $general;
    }

    public function get()
    {
//        echo "joe";
//        die();
    }


    /**
     * Read and parse a YAML configuration file.
     *
     * @param string             $filename  The name of the YAML file to read
     * @param DirectoryInterface $directory The (optional) directory to the YAML file
     *
     * @return array
     */
    protected function parseConfigYaml($filename)
    {
        try {
            $filename = $this->fileLocator->locate($filename, null, true);
//            $file = $directory->get($filename);
        } catch (FileNotFoundException $e) {
            // Copy in dist files if applicable
//            $distFiles = ['config.yml', 'contenttypes.yml', 'menu.yml', 'permissions.yml', 'routing.yml', 'taxonomy.yml'];
//            if ($directory->getMountPoint() !== 'config' || !in_array($filename, $distFiles)) {
//                return [];
//            }

//            $this->app['filesystem']->copy("bolt://app/config/$filename.dist", "config://$filename");
//            $file = $directory->get($filename);
        }


//        if (!$file instanceof ParsableInterface) {
//            throw new \LogicException('File is not parsable.');
//        }

        $yaml = Yaml::parseFile($filename);

        // Unset the repeated nodes key after parse
        unset($yaml['__nodes']);

        return Bag::from($yaml);
    }



    /**
     * Assume sensible defaults for a number of options.
     */
    protected function getDefaultConfig()
    {
        return [
            'database'    => [
                'driver'         => 'sqlite',
                'host'           => 'localhost',
                'slaves'         => [],
                'dbname'         => 'bolt',
                'prefix'         => 'bolt_',
                'charset'        => 'utf8',
                'collate'        => 'utf8_unicode_ci',
                'randomfunction' => '',
            ],
            'sitename'                    => 'Default Bolt site',
            'locale'                      => null,
            'recordsperpage'              => 10,
            'recordsperdashboardwidget'   => 5,
            'systemlog'                   => [
                'enabled' => true,
            ],
            'changelog'                   => [
                'enabled' => false,
            ],
            'debuglog'                    => [
                'enabled'  => false,
                'level'    => 'DEBUG',
                'filename' => 'bolt-debug.log',
            ],
            'debug'                       => null,
            'debug_show_loggedoff'        => false,
            'debug_error_level'           => null,
            'production_error_level'      => null,
            'debug_enable_whoops'         => false, /** @deprecated. Deprecated since 3.2, to be removed in 4.0 */
            'debug_error_use_symfony'     => false,
            'debug_permission_audit_mode' => false,
            'debug_trace_argument_limit'  => 4,
            'strict_variables'            => null,
            'theme'                       => 'base-2016',
            'listing_template'            => 'listing.twig',
            'listing_records'             => '5',
            'listing_sort'                => 'datepublish DESC',
            'caching'                     => [
                'config'    => true,
                'templates' => true,
                'request'   => false,
                'duration'  => 10,
            ],
            'wysiwyg'                     => [
                'images'      => false,
                'tables'      => false,
                'fontcolor'   => false,
                'align'       => false,
                'subsuper'    => false,
                'embed'       => false,
                'anchor'      => false,
                'underline'   => false,
                'strike'      => false,
                'blockquote'  => false,
                'codesnippet' => false,
                'specialchar' => false,
                'styles'      => false,
                'ck'          => [
                    'autoParagraph'           => true,
                    'contentsCss'             => [
                        ['css/ckeditor-contents.css', 'bolt'],
                        ['css/ckeditor.css', 'bolt'],
                    ],
                    'filebrowserWindowWidth'  => 640,
                    'filebrowserWindowHeight' => 480,
                ],
            ],
            'liveeditor'                  => true,
            'canonical'                   => null,
            'developer_notices'           => false,
            'cookies_use_remoteaddr'      => true,
            'cookies_use_browseragent'    => false,
            'cookies_use_httphost'        => true,
            'cookies_lifetime'            => 14 * 24 * 3600,
            'enforce_ssl'                 => false,
            'thumbnails'                  => [
                'default_thumbnail' => [160, 120],
                'default_image'     => [1000, 750],
                'quality'           => 75,
                'cropping'          => 'crop',
                'notfound_image'    => 'bolt_assets://img/default_notfound.png',
                'error_image'       => 'bolt_assets://img/default_error.png',
                'only_aliases'      => false,
            ],
            'accept_file_types'           => explode(',', 'twig,html,js,css,scss,gif,jpg,jpeg,png,ico,zip,tgz,txt,md,doc,docx,pdf,epub,xls,xlsx,csv,ppt,pptx,mp3,ogg,wav,m4a,mp4,m4v,ogv,wmv,avi,webm,svg'),
            'hash_strength'               => 10,
            'branding'                    => [
                'name'        => 'Bolt',
                'path'        => '/bolt',
                'provided_by' => [],
            ],
            'maintenance_mode'            => false,
            'headers'                     => [
                'x_frame_options' => true,
            ],
            'htmlcleaner'                 => [
                'allowed_tags'       => explode(',', 'div,span,p,br,hr,s,u,strong,em,i,b,li,ul,ol,mark,blockquote,pre,code,tt,h1,h2,h3,h4,h5,h6,dd,dl,dh,table,tbody,thead,tfoot,th,td,tr,a,img,address,abbr,iframe'),
                'allowed_attributes' => explode(',', 'id,class,style,name,value,href,Bolt,alt,title,width,height,frameborder,allowfullscreen,scrolling'),
            ],
            'performance'                 => [
                'http_cache'    => [
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
     *
     * @param Bag $options
     *
     * @return array
     */
    protected function parseDatabase(Bag $options)
    {
        // Make sure prefix ends with underscore
        if (substr($options['prefix'], strlen($options['prefix']) - 1) !== '_') {
            $options['prefix'] .= '_';
        }

        // Parse master connection parameters
        $master = $this->parseConnectionParams($options);
        // Merge master connection into options
        $options = Bag::fromRecursive($options, $master);

        // Add platform specific random functions
        $driver = \Bolt\Common\Str::replaceFirst($options['driver'], 'pdo_', '');
        if ($driver === 'sqlite') {
            $options['driver'] = 'pdo_sqlite';
            $options['randomfunction'] = 'RANDOM()';
        } elseif (in_array($driver, ['mysql', 'mysqli'])) {
            $options['driver'] = 'pdo_mysql';
            $options['randomfunction'] = 'RAND()';
        } elseif (in_array($driver, ['pgsql', 'postgres', 'postgresql'])) {
            $options['driver'] = 'pdo_pgsql';
            $options['randomfunction'] = 'RANDOM()';
        }

        // Specify the wrapper class for the connection
        $options['wrapperClass'] = Database\Connection::class;

        // Parse SQLite separately since it has to figure out database path
        if ($driver === 'sqlite') {
            return $this->parseSqliteOptions($options);
        }

        // If no slaves return with single connection
        if (empty($options['slaves'])) {
            return $options;
        }

        // Specify we want a master slave connection
        $options['wrapperClass'] = Database\MasterSlaveConnection::class;

        // Add master connection where MasterSlaveConnection looks for it.
        $options['master'] = $master;

        // Parse each slave connection parameters
        foreach ($options['slaves'] as $name => $slave) {
            $options['slaves'][$name] = $this->parseConnectionParams($slave, $master);
        }

        return $options;
    }

    /**
     * Fine-tune Sqlite configuration parameters.
     *
     * @param Bag $config
     *
     * @return array
     */
    protected function parseSqliteOptions(Bag $config)
    {
        if (isset($config['memory']) && $config['memory']) {
            // If in-memory, no need to parse paths
            unset($config['path']);

            return $config;
        }
        // Prevent SQLite driver from trying to use in-memory connection
        unset($config['memory']);

        $pathResolver = new PathResolver(dirname(dirname(__DIR__)), []);

        // Get path from config or use database path
        $path = isset($config['path']) ? $config['path'] : $pathResolver->resolve('database');
        if (Path::isRelative($path)) {
            $path = $pathResolver->resolve($path);
        }
        dump($path);

        // If path has filename with extension, use that
        if (Path::hasExtension($path)) {
            $config['path'] = $path;

            return $config;
        }

        // Use database name for filename
        $filename = basename($config['dbname']);
        if (!Path::hasExtension($filename)) {
            $filename .= '.db';
        }

        // Join filename with database path
        $config['path'] = Path::join($path, $filename);

        return $config;
    }

    /**
     * Parses params to valid connection parameters.
     *
     * - Defaults are merged into the params
     * - Bolt keys are converted to Doctrine keys
     * - Invalid keys are filtered out
     *
     * @param Bag $params
     * @param array        $defaults
     *
     * @return array
     */
    protected function parseConnectionParams(Bag $params, $defaults = [])
    {
        // Handle host shortcut
        if (is_string($params)) {
            $params = ['host' => $params];
        }

        // Convert keys from Bolt
        $replacements = [
            'databasename' => 'dbname',
            'username'     => 'user',
        ];
        foreach ($replacements as $old => $new) {
            if (isset($params[$old])) {
                $params[$new] = $params[$old];
                unset($params[$old]);
            }
        }

        // Merge in defaults
        $params = Bag::fromRecursive($defaults, $params);

        // Filter out invalid keys
        $validKeys = [
            'user', 'password', 'host', 'port', 'dbname', 'charset',      // common
            'path', 'memory',                                             // Qqlite
            'unix_socket', 'driverOptions', 'collate',                    // MySql
            'sslmode',                                                    // PostgreSQL
            'servicename', 'service', 'pooled', 'instancename', 'server', // Oracle
            'persistent',                                                 // SQL Anywhere
        ];
        $params = $params->intersectKeys($validKeys);

        return $params;
    }
}
