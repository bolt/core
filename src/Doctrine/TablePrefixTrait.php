<?php
/**
 * @author Rix Beck <rix@neologik.hu>
 */

namespace Bolt\Doctrine;

use Bolt\Common\Str;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Persistence\ObjectManager;

trait TablePrefixTrait
{
    private $tablePrefixes = [];

    protected function setTablePrefix(ObjectManager $manager, string $prefix)
    {
        $key = spl_object_hash($manager);
        $this->tablePrefixes[$key] = Str::ensureEndsWith($prefix, '_');

        return $this;
    }

    protected function setTablePrefixes($tablePrefixes, ManagerRegistry $managerRegistry)
    {
        $prefixes = (array)$tablePrefixes;
        $this->registry = $managerRegistry;

        foreach ($prefixes as $em => $tablePrefix) {
            $manager = $managerRegistry->getManager(is_int($em) ? 'default' : $em);
            if ($manager) {
                $this->setTablePrefix($manager, $tablePrefix);
            }
        }

        return $this;
    }

    protected function getTablePrefix(ObjectManager $manager)
    {
        $key = spl_object_hash($manager);

        return $this->tablePrefixes[$key] ?? '';
    }
}
