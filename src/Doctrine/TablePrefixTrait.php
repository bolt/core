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
        $this->tablePrefixes[$key] = empty($prefix) ? '' : Str::ensureEndsWith($prefix, '_');

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

    protected function getTablePrefix(ObjectManager $manager): string
    {
        $key = spl_object_hash($manager);

        return $this->tablePrefixes[$key] ?? '';
    }
}
