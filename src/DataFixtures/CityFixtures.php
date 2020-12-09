<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

/**
 * Class CountryFixtures
 * @package App\DataFixtures
 */
class CityFixtures extends Fixture
{
    /**
     * @var Faker\Generator
     */
    protected Faker\Generator $faker;

    /**
     * CountryFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager): void
    {
        for ($i = 0; $i < 5; $i++) {
            $city = new City();
            $city->setName($this->faker->city);

            $manager->persist($city);
        }

        $manager->flush();
    }
}
