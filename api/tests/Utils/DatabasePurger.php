<?php

namespace App\Tests\Utils;

use Doctrine\Common\DataFixtures\Purger\ORMPurger;
use Doctrine\ORM\EntityManagerInterface;

class DatabasePurger
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function purge(): void
    {
        $purger = new ORMPurger($this->entityManager);
        $purger->purge();
    }
}
