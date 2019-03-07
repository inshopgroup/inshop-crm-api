<?php

namespace App\DataFixtures;

use App\Entity\Text;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class TextFixtures
 * @package App\DataFixtures
 */
class TextFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * AddressFixtures constructor.
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
        $text = new Text();
        $text->setTitle('Terms of use');
        $text->setContent($this->faker->text);
        $manager->persist($text);

        $text = new Text();
        $text->setTitle('Homepage about');
        $text->setContent($this->faker->text);
        $manager->persist($text);

        $text = new Text();
        $text->setTitle('About');
        $text->setContent($this->faker->text);
        $manager->persist($text);

        $manager->flush();
    }
}
