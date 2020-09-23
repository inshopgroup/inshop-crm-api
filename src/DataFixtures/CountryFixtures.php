<?php

namespace App\DataFixtures;

use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

/**
 * Class CountryFixtures
 * @package App\DataFixtures
 */
class CountryFixtures extends Fixture
{
    /**
     * @var Faker\Generator
     */
    protected $faker;

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
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 5; $i++) {
            $country = new Country();
            $country->setName($this->faker->country);

            $manager->persist($country);
        }

        $manager->flush();
    }
}
