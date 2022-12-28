<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Factory\BookFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        BookFactory::createMany(100);
        UserFactory::createMany(20);
        $manager->flush();
    }
}
