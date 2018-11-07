<?php

namespace App\DataFixtures;

use App\Entity\City;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class CountryFixtures
 * @package App\DataFixtures
 */
class CountryFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * CountryFixtures constructor.
     * @param $faker
     */
    public function __construct($faker)
    {
        $this->faker = $faker;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $country = new Country();
            $country->setName($this->faker->country);

            $manager->persist($country);

            for ($j = 0; $j < 10; $j++) {
                $city = new City();
                $city->setName($this->faker->city);
                $city->setCountry($country);

                $manager->persist($city);
            }
        }

        $manager->flush();
    }
}
