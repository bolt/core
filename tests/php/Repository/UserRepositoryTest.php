<?php

declare(strict_types=1);

namespace Bolt\Tests\Repository;

use Bolt\Entity\User;
use Bolt\Tests\DbAwareTestCase;

class UserRepositoryTest extends DbAwareTestCase
{
    public function testFindOneByUsername(): void
    {
        $admin = $this->getEm()->getRepository(User::class)->findOneByUsername('admin');
        $this->assertInstanceOf(User::class, $admin);

        $administrator = $this->getEm()->getRepository(User::class)->findOneByUsername('administrator');
        $this->assertNull($administrator);
    }

    public function testFindOneByCredentials(): void
    {
        $admin = $this->getEm()->getRepository(User::class)->findOneByCredentials('admin');
        $this->assertInstanceOf(User::class, $admin);

        $adminEmail = $this->getEm()->getRepository(User::class)->findOneByCredentials('admin@example.org');
        $this->assertInstanceOf(User::class, $adminEmail);

        $janeChief = $this->getEm()->getRepository(User::class)->findOneByCredentials('Jane_Chief');
        $this->assertInstanceOf(User::class, $janeChief);

        $janeAdminEmail = $this->getEm()->getRepository(User::class)->findOneByCredentials('Jane_Admin@Example.Org');
        $this->assertInstanceOf(User::class, $janeAdminEmail);

        $administrator = $this->getEm()->getRepository(User::class)->findOneByCredentials('administrator');
        $this->assertNull($administrator);
    }
}
