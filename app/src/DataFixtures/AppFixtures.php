<?php

namespace App\DataFixtures;

use App\Entity\Book;
use App\Factory\BookFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
//        for ($i = 0; $i < 100; $i++) {
//            $newBook = new Book();
//            $newBook->setName('randName' . $i);
//
//            $color = $content['color'] ?? 'red';
//            $newBook->setColor($color);
//
//            $manager->persist($newBook);
//        }

        BookFactory::createMany(25);

        $manager->flush();
    }
}
