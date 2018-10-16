<?php

declare(strict_types=1);

namespace Bolt\Configuration;

use Bolt\Common\Arr;
use Bolt\Helpers\Html;
use Bolt\Helpers\Str;
use Cocur\Slugify\Slugify;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

class Config
{
    /** @var array */
    protected $data;

    /** @var FileLocator */
    private $fileLocator;

    /** @var PathResolver */
    private $pathResolver;

    public function __construct()
    {
        $this->initialize();
    }

    private function initialize()
    {
        $this->pathResolver = new PathResolver(dirname(dirname(__DIR__)), []);

        $configDirectories = [dirname(dirname(__DIR__)) . '/config/bolt'];
        $this->fileLocator = new FileLocator($configDirectories);

//        $this->cacheFile = $this->app['filesystem']->getFile('cache://config-cache.json');

        $data = null;

//        $data = $this->loadCache();
        if ($data === null) {
            $data = $this->parseConfig();

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
    public function parseConfig()
    {
        $config = collect([
            'general' => $this->parseGeneral(),
        ]);

        $this->data = $config;

        $config['contenttypes'] = $this->parseContentTypes();
        $config['taxonomies'] = $this->parseTaxonomy();

        // 'menu' => $this->parseConfigYaml('menu.yml'),
        //'routing' => $this->parseConfigYaml('routing.yml'),
        //'permissions' => $this->parseConfigYaml('permissions.yml'),
        //'extensions' => $this->parseConfigYaml('extensions.yml'),

        return $config;
    }

    /**
     * Get a config value, using a path.
     *
     * For example:
     * $var = $config->get('general/wysiwyg/ck/contentsCss');
     *
     * @param string            $path
     * @param string|array|bool $default
     *
     * @return mixed
     */
    public function get(string $path, $default = null)
    {
        return Arr::get($this->data, $path, $default);
    }

    /**
     * @param string $path
     * @param bool   $absolute
     *
     * @return string
     */
    public function getPath(string $path, bool $absolute = true, string $additional = ''): string
    {
        return $this->pathResolver->resolve($path, $absolute, $additional);
    }

    /**
     * @return Collection
     */
    public function getPaths(): Collection
    {
        return $this->pathResolver->resolveAll();
    }

    /**
     * @return Collection
     */
    public function getMediaTypes(): Collection
    {
        return collect(['png', 'jpg', 'jpeg', 'gif', 'svg', 'pdf', 'mp3', 'tiff']);
    }

    /**
     * Read and parse the config.yaml and config_local.yaml configuration files.
     *
     * @return Collection
     */
    protected function parseGeneral(): Collection
    {
        $defaultconfig = $this->getDefaultConfig();
        $tempconfig = $this->parseConfigYaml('config.yaml');
        $tempconfiglocal = $this->parseConfigYaml('config_local.yaml');
        $general = Arr::replaceRecursive($defaultconfig, Arr::replaceRecursive($tempconfig, $tempconfiglocal));

        // Make sure Bolt's mount point is OK:
        $general['branding']['path'] = '/' . Str::makeSafe($general['branding']['path']);

        // Set the link in branding, if provided_by is set.
        $general['branding']['provided_link'] = Html::providerLink(
            $general['branding']['provided_by']
        );

        $general['database'] = $this->parseDatabase($general['database']);

        return collect($general);
    }

    /**
     * Read and parse the taxonomy.yml configuration file.
     *
     * @param array|null $taxonomies
     *
     * @return array
     */
    protected function parseTaxonomy()
    {
        $taxonomies = $this->parseConfigYaml('taxonomy.yml');

        $slugify = Slugify::create();

        foreach ($taxonomies as $key => $taxonomy) {
            if (!isset($taxonomy['name'])) {
                $taxonomy['name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['slug'])));
            }
            if (!isset($taxonomy['singular_name'])) {
                if (isset($taxonomy['singular_slug'])) {
                    $taxonomy['singular_name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['singular_slug'])));
                } else {
                    $taxonomy['singular_name'] = ucwords(str_replace('-', ' ', Str::humanize($taxonomy['slug'])));
                }
            }
            if (!isset($taxonomy['slug'])) {
                $taxonomy['slug'] = $slugify->slugify($taxonomy['name']);
            }
            if (!isset($taxonomy['singular_slug'])) {
                $taxonomy['singular_slug'] = $slugify->slugify($taxonomy['singular_name']);
            }
            if (!isset($taxonomy['has_sortorder'])) {
                $taxonomy['has_sortorder'] = false;
            }
            if (!isset($taxonomy['allow_spaces'])) {
                $taxonomy['allow_spaces'] = false;
            }

            // Make sure the options are $key => $value pairs, and not have implied integers for keys.
            if (!empty($taxonomy['options']) && is_array($taxonomy['options'])) {
                $options = [];
                foreach ($taxonomy['options'] as $optionKey => $optionValue) {
                    if (is_numeric($optionKey)) {
                        $optionKey = $optionValue;
                    }
                    $optionKey = $slugify->slugify($optionKey);
                    $options[$optionKey] = $optionValue;
                }
                $taxonomy['options'] = $options;
            }

            if (!isset($taxonomy['behaves_like'])) {
                $taxonomy['behaves_like'] = 'tags';
            }
            // If taxonomy is like tags, set 'tagcloud' to true by default.
            if (($taxonomy['behaves_like'] === 'tags') && (!isset($taxonomy['tagcloud']))) {
                $taxonomy['tagcloud'] = true;
            } else {
                $taxonomy += ['tagcloud' => false];
            }

            $taxonomies[$key] = $taxonomy;
        }

        return $taxonomies;
    }

    /**
     * Read and parse the contenttypes.yml configuration file.
     *
     *
     * @return Collection
     */
    protected function parseContentTypes(): Collection
    {
        $contentTypes = new Collection();
        $tempContentTypes = $this->parseConfigYaml('contenttypes.yml');
        foreach ($tempContentTypes as $key => $contentType) {
            try {
                $contentType = $this->parseContentType($key, $contentType);
                $contentTypes[$key] = $contentType;
            } catch (InvalidArgumentException $e) {
                $this->exceptions[] = $e->getMessage();
            }
        }

        return $contentTypes;
    }

    /**
     * Read and parse a YAML configuration file.
     *
     * @param string $filename The name of the YAML file to read
     *
     * @return Collection
     */
    protected function parseConfigYaml($filename): Collection
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

        return collect($yaml);
    }

    /**
     * Assume sensible defaults for a number of options.
     */
    protected function getDefaultConfig()
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
            'listing_template' => 'listing.twig',
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
     * Parse a single Contenttype configuration array.
     *
     * @param string $key
     * @param array  $contentType
     *
     * @throws InvalidArgumentException
     *
     * @return array
     */
    protected function parseContentType($key, $contentType)
    {
        // If the slug isn't set, and the 'key' isn't numeric, use that as the slug.
        if (!isset($contentType['slug']) && !is_numeric($key)) {
            $contentType['slug'] = Slugify::create()->slugify($key);
        }

        // If neither 'name' nor 'slug' is set, we need to warn the user. Same goes for when
        // neither 'singular_name' nor 'singular_slug' is set.
        if (!isset($contentType['name']) && !isset($contentType['slug'])) {
            $error = sprintf("In contenttype <code>%s</code>, neither 'name' nor 'slug' is set. Please edit <code>contenttypes.yml</code>, and correct this.", $key);
            throw new InvalidArgumentException($error);
        }
        if (!isset($contentType['singular_name']) && !isset($contentType['singular_slug'])) {
            $error = sprintf("In contenttype <code>%s</code>, neither 'singular_name' nor 'singular_slug' is set. Please edit <code>contenttypes.yml</code>, and correct this.", $key);
            throw new InvalidArgumentException($error);
        }

        // Contenttypes without fields make no sense.
        if (!isset($contentType['fields'])) {
            $error = sprintf("In contenttype <code>%s</code>, no 'fields' are set. Please edit <code>contenttypes.yml</code>, and correct this.", $key);
            throw new InvalidArgumentException($error);
        }

        if (!isset($contentType['slug'])) {
            $contentType['slug'] = Slugify::create()->slugify($contentType['name']);
        }
        if (!isset($contentType['name'])) {
            $contentType['name'] = ucwords(preg_replace('/[^a-z0-9]/i', ' ', $contentType['slug']));
        }
        if (!isset($contentType['singular_slug'])) {
            $contentType['singular_slug'] = Slugify::create()->slugify($contentType['singular_name']);
        }
        if (!isset($contentType['singular_name'])) {
            $contentType['singular_name'] = ucwords(preg_replace('/[^a-z0-9]/i', ' ', $contentType['singular_slug']));
        }
        if (!isset($contentType['show_on_dashboard'])) {
            $contentType['show_on_dashboard'] = true;
        }
        if (!isset($contentType['show_in_menu'])) {
            $contentType['show_in_menu'] = true;
        }
        if (!isset($contentType['sort'])) {
            $contentType['sort'] = false;
        }
        if (!isset($contentType['default_status'])) {
            $contentType['default_status'] = 'published';
        }
        if (!isset($contentType['viewless'])) {
            $contentType['viewless'] = false;
        }
        if (!isset($contentType['icon_one'])) {
            $contentType['icon_one'] = 'fa-file';
        } else {
            $contentType['icon_one'] = str_replace('fa:', 'fa-', $contentType['icon_one']);
        }
        if (!isset($contentType['icon_many'])) {
            $contentType['icon_many'] = 'fa-copy';
        } else {
            $contentType['icon_many'] = str_replace('fa:', 'fa-', $contentType['icon_many']);
        }

        // Allow explicit setting of a Contenttype's table name suffix. We default
        // to slug if not present as it has been this way since Bolt v1.2.1
        if (!isset($contentType['tablename'])) {
            $contentType['tablename'] = Slugify::create()->slugify($contentType['slug'], '_');
        } else {
            $contentType['tablename'] = Slugify::create()->slugify($contentType['tablename'], '_');
        }
        if (!isset($contentType['allow_numeric_slugs'])) {
            $contentType['allow_numeric_slugs'] = false;
        }
        if (!isset($contentType['singleton'])) {
            $contentType['singleton'] = false;
        }

        list($fields, $groups) = $this->parseFieldsAndGroups($contentType['fields']);
        $contentType['fields'] = $fields;
        $contentType['groups'] = $groups;

        // Make sure taxonomy is an array.
        if (isset($contentType['taxonomy'])) {
            $contentType['taxonomy'] = (array) $contentType['taxonomy'];
        }

        // when adding relations, make sure they're added by their slug. Not their 'name' or 'singular name'.
        if (!empty($contentType['relations']) && is_array($contentType['relations'])) {
            foreach (array_keys($contentType['relations']) as $relkey) {
                if ($relkey !== Slugify::create()->slugify($relkey)) {
                    $contentType['relations'][Slugify::create()->slugify($relkey)] = $contentType['relations'][$relkey];
                    unset($contentType['relations'][$relkey]);
                }
            }
        }

        return $contentType;
    }

    /**
     * Parse and fine-tune the database configuration.
     *
     *
     * @return array
     */
    protected function parseDatabase(array $options)
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
     * Parse a Contenttype's field and determine the grouping.
     *
     *
     * @return array
     */
    protected function parseFieldsAndGroups(array $fields)
    {
        $acceptableFileTypes = $this->get('general')['accept_file_types'];

        $currentGroup = 'ungrouped';
        $groups = [];
        $hasGroups = false;

        foreach ($fields as $key => $field) {
            unset($fields[$key]);
            $key = str_replace('-', '_', mb_strtolower(Str::makeSafe($key, true)));
            if (!isset($field['type']) || empty($field['type'])) {
                $error = sprintf('Field "%s" has no "type" set.', $key);

                throw new InvalidArgumentException($error);
            }

            // If field is a "file" type, make sure the 'extensions' are set, and it's an array.
            if ($field['type'] === 'file' || $field['type'] === 'filelist') {
                if (empty($field['extensions'])) {
                    $field['extensions'] = $acceptableFileTypes;
                }

                $field['extensions'] = (array) $field['extensions'];
            }

            // If field is an "image" type, make sure the 'extensions' are set, and it's an array.
            if ($field['type'] === 'image' || $field['type'] === 'imagelist') {
                if (empty($field['extensions'])) {
                    $field['extensions'] = collect(['gif', 'jpg', 'jpeg', 'png', 'svg'])
                        ->intersect($acceptableFileTypes);
                }

                $field['extensions'] = (array) $field['extensions'];
            }

            // Make indexed arrays into associative for select fields
            // e.g.: [ 'yes', 'no' ] => { 'yes': 'yes', 'no': 'no' }
            if ($field['type'] === 'select' && isset($field['values']) && Arr::isIndexed($field['values'])) {
                $field['values'] = array_combine($field['values'], $field['values']);
            }

            if (!empty($field['group'])) {
                $hasGroups = true;
            }

            // Make sure we have these keys and every field has a group set.
            $field = array_replace(
                [
                    'class' => '',
                    'default' => '',
                    'group' => $currentGroup,
                    'label' => '',
                    'variant' => '',
                ],
                $field
            );

            // Collect group data for rendering.
            // Make sure that once you started with group all following have that group, too.
            $currentGroup = $field['group'];
            $groups[$currentGroup] = 1;

            $fields[$key] = $field;

            // Repeating fields checks
            if ($field['type'] === 'repeater') {
                $fields[$key] = $this->parseFieldRepeaters($fields, $key);
                if ($fields[$key] === null) {
                    unset($fields[$key]);
                }
            }
        }

        // Make sure the 'uses' of the slug is an array.
        if (isset($fields['slug']) && isset($fields['slug']['uses'])) {
            $fields['slug']['uses'] = (array) $fields['slug']['uses'];
        }

        return [$fields, $hasGroups ? array_keys($groups) : []];
    }

    /**
     * Basic validation of repeater fields.
     *
     * @param string $key
     *
     * @return array
     */
    private function parseFieldRepeaters(array $fields, $key)
    {
        $blacklist = ['repeater', 'slug', 'templatefield'];
        $repeater = $fields[$key];

        if (!isset($repeater['fields']) || !is_array($repeater['fields'])) {
            return;
        }

        foreach ($repeater['fields'] as $repeaterKey => $repeaterField) {
            if (!isset($repeaterField['type']) || in_array($repeaterField['type'], $blacklist, true)) {
                unset($repeater['fields'][$repeaterKey]);
            }
        }

        return $repeater;
    }

    /**
     * Fine-tune Sqlite configuration parameters.
     *
     * @return array
     */
    protected function parseSqliteOptions(array $config): array
    {
        if (isset($config['memory']) && $config['memory']) {
            // If in-memory, no need to parse paths
            unset($config['path']);

            return $config;
        }
        // Prevent SQLite driver from trying to use in-memory connection
        unset($config['memory']);

        // Get path from config or use database path
        $path = isset($config['path']) ? $config['path'] : $pathResolver->resolve('database');
        if (Path::isRelative($path)) {
            $path = $pathResolver->resolve($path);
        }

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
     * @param array $defaults
     *
     * @return array
     */
    protected function parseConnectionParams(array $params, $defaults = [])
    {
        // Handle host shortcut
        if (is_string($params)) {
            $params = ['host' => $params];
        }

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

        // Merge in defaults
        $params = collect($defaults)->merge($params);

        // Filter out invalid keys
        $validKeys = [
            'user', 'password', 'host', 'port', 'dbname', 'charset',      // common
            'path', 'memory',                                             // Qqlite
            'unix_socket', 'driverOptions', 'collate',                    // MySql
            'sslmode',                                                    // PostgreSQL
            'servicename', 'service', 'pooled', 'instancename', 'server', // Oracle
            'persistent',                                                 // SQL Anywhere
        ];
        $params = $params->intersectByKeys($validKeys);

        return $params;
    }
}
