<?php

namespace App\DataFixtures;

use App\Entity\File;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class FileFixtures extends Fixture
{
    protected Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 20; $i++) {
            $file = new File();
            $file->setOriginalName($this->faker->name());
            $file->setMimeType('plain/text');
            $file->setContentUrl('testfile.txt');
            $file->setSize($this->faker->numberBetween(111, 123123123123));

            $manager->persist($file);
        }

        $manager->flush();
    }
}
