<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

class CountryFixtures extends Fixture
{
    protected Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $country = new Country();
            $country->setName($this->faker->country());

            $manager->persist($country);
        }

        $manager->flush();
    }
}
