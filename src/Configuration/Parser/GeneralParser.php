<?php

declare(strict_types=1);

namespace Bolt\Configuration\Parser;

use Bolt\Common\Arr;
use Bolt\Common\Str;
use Bolt\Utils\Html;
use Tightenco\Collect\Support\Collection;
use Webmozart\PathUtil\Path;

class GeneralParser extends BaseParser
{
    public function __construct(string $projectDir, string $initialFilename = 'config.yaml')
    {
        parent::__construct($projectDir, $initialFilename);
    }

    /**
     * Read and parse the config.yaml and config_local.yaml configuration files.
     */
    public function parse(): Collection
    {
        $defaultconfig = $this->getDefaultConfig();
        $tempconfig = $this->parseConfigYaml($this->getInitialFilename());
        $tempconfiglocal = $this->parseConfigYaml($this->getFilenameLocalOverrides(), true);
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

        return new Collection($general);
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
        $options = (new Collection($options))->merge($master);

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

        $params = new Collection($params);

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
