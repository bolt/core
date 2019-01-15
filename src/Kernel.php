<?php

declare(strict_types=1);

namespace Bolt;

use Bolt\Configuration\Parser\ContentTypesParser;
use Bolt\Configuration\Parser\TaxonomyParser;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\Config\Resource\FileResource;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;
use Symfony\Component\Yaml\Yaml;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    public const CONFIG_EXTS = '.{php,xml,yaml,yml}';

    public function getCacheDir()
    {
        return $this->getProjectDir() . '/var/cache/' . $this->environment;
    }

    public function getLogDir()
    {
        return $this->getProjectDir() . '/var/log';
    }

    public function registerBundles()
    {
        $contents = require $this->getProjectDir() . '/config/bundles.php';
        foreach ($contents as $class => $envs) {
            if (isset($envs['all']) || isset($envs[$this->environment])) {
                yield new $class();
            }
        }
    }

    protected function configureContainer(ContainerBuilder $container, LoaderInterface $loader): void
    {
        $container->addResource(new FileResource($this->getProjectDir() . '/config/bundles.php'));
        $container->setParameter('container.dumper.inline_class_loader', true);
        $confDir = $this->getProjectDir() . '/config';

        $this->setBoltParameters($container, $confDir);
        $this->setContentTypeRequirements($container);
        $this->setTaxonomyRequirements($container);

        $loader->load($confDir . '/{packages}/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{packages}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}' . self::CONFIG_EXTS, 'glob');
        $loader->load($confDir . '/{services}_' . $this->environment . self::CONFIG_EXTS, 'glob');
    }

    protected function configureRoutes(RouteCollectionBuilder $routes): void
    {
        $confDir = $this->getProjectDir() . '/config';

        $routes->import($confDir . '/{routes}/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}/' . $this->environment . '/**/*' . self::CONFIG_EXTS, '/', 'glob');
        $routes->import($confDir . '/{routes}' . self::CONFIG_EXTS, '/', 'glob');
    }

    private function setBoltParameters(ContainerBuilder $container, string $confDir): void
    {
        $fileLocator = new FileLocator([$confDir . '/bolt']);
        $fileName = $fileLocator->locate('config.yaml', null, true);

        $yaml = Yaml::parseFile($fileName);
        unset($yaml['__nodes']);

        $container->set('bolt.config.general', $yaml);

        foreach ($this->flattenKeys($yaml) as $key => $value) {
            $container->setParameter('bolt.' . $key, $value);
        }
    }

    private function flattenKeys(array $array, string $prefix = ''): array
    {
        $result = [];
        foreach ($array as $key => $value) {
            if (is_int($key)) {
                $result[trim($prefix, '.')][] = $value;
            } elseif (is_array($value)) {
                $result += $this->flattenKeys($value, $prefix . $key . '.');
            } else {
                $result[$prefix . $key] = $value;
            }
        }

        return $result;
    }

    /**
     * Set the ContentType requirements that are used in Routing.
     * Note: this functionality is partially duplicated in \Bolt\Configuration\Config.
     *
     * @throws \Exception
     */
    private function setContentTypeRequirements(ContainerBuilder $container): void
    {
        $ContentTypesParser = new ContentTypesParser([]);
        $contentTypes = $ContentTypesParser->parse();

        $pluralslugs = $contentTypes->pluck('slug')->implode('|');
        $slugs = $contentTypes->pluck('slug')->concat($contentTypes->pluck('singular_slug'))->unique()->implode('|');

        $container->setParameter('bolt.requirement.pluralcontenttypes', $pluralslugs);
        $container->setParameter('bolt.requirement.contenttypes', $slugs);
    }

    /**
     * Set the Taxonomy requirements that are used in Routing.
     * Note: this functionality is partially duplicated in \Bolt\Configuration\Config.
     *
     * @throws \Exception
     */
    private function setTaxonomyRequirements(ContainerBuilder $container): void
    {
        $taxonomyParser = new TaxonomyParser();
        $taxonomies = $taxonomyParser->parse();

        $pluralslugs = $taxonomies->pluck('slug')->implode('|');
        $slugs = $taxonomies->pluck('slug')->concat($taxonomies->pluck('singular_slug'))->unique()->implode('|');

        $container->setParameter('bolt.requirement.pluraltaxonomies', $pluralslugs);
        $container->setParameter('bolt.requirement.taxonomies', $slugs);
    }
}
