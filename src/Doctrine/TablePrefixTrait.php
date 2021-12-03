<?php

declare(strict_types=1);

/**
 * @author Rix Beck <rix@neologik.hu>
 */

namespace Bolt\Doctrine;

use Bolt\Common\Str;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;
use InvalidArgumentException;

trait TablePrefixTrait
{
    /** @var string[] */
    private $tablePrefixes = [];

    /** @var ManagerRegistry */
    private $registry;

    protected function setTablePrefix(ObjectManager $manager, string $prefix)
    {
        $key = spl_object_hash($manager);
        $this->tablePrefixes[$key] = Str::ensureEndsWith($prefix, '_');

        return $this;
    }

    protected function setTablePrefixes($tablePrefixes, ManagerRegistry $managerRegistry)
    {
        $prefixes = (array) $tablePrefixes;
        $this->registry = $managerRegistry;

        foreach ($prefixes as $em => $prefix) {
            try {
                $manager = $managerRegistry->getManager(is_int($em) ? 'default' : $em);
                $this->setTablePrefix($manager, $prefix);
            } catch (InvalidArgumentException $exception) {
                throw new InvalidArgumentException(sprintf("'%s' entity manager not defined for table prefix '%s'", $em, $prefix));
            }
        }

        return $this;
    }

    /**
     * Since we introduced `symfony/proxy-manager-bridge`, the keys in the tableprefix
     * no longer match what the manager tells us it should be. For example, the
     * given key was `0000000005ee10ad0000000043b453e3`, but in our reference
     * table we had `0000000005ee10e90000000043b453e3`. We just return the first one, now
     */
    protected function getTablePrefix(): string
    {
        return current($this->tablePrefixes);
    }
}
