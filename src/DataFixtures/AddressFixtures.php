<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Country;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class AddressFixtures extends Fixture implements DependentFixtureInterface
{
    protected Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $countries = $manager->getRepository(Country::class)->findAll();

        for ($j = 0; $j < 50; $j++) {
            /** @var Country $country */
            $country = $this->faker->randomElement($countries);

            $address = new Address();
            $address->setCountry($country);
            $address->setCity($this->faker->city());
            $address->setRegion($this->faker->address());
            $address->setDistrict($this->faker->address());
            $address->setPostCode($this->faker->postcode());
            $address->setStreet($this->faker->streetAddress());
            $address->setBuilding($this->faker->numberBetween(1, 200));
            $address->setApartment($this->faker->numberBetween(1, 200));
            $address->setComment($this->faker->name());

            $manager->persist($address);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            CountryFixtures::class,
        );
    }
}
