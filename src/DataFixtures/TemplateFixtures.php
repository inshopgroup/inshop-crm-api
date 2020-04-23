<?php

namespace App\DataFixtures;

use App\Entity\File;
use App\Entity\Template;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

/**
 * Class TemplateFixtures
 * @package App\DataFixtures
 */
class TemplateFixtures extends Fixture
{
    /**
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * TemplateFixtures constructor.
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
        for ($i = 0; $i < 20; $i++) {
            $file = new File();
            $file->setOriginalName($this->faker->name);
            $file->setMimeType('plain/text');
            $file->setContentUrl('testfile.txt');
            $file->setSize($this->faker->numberBetween(111, 123123123123));

            $manager->persist($file);
        }

        $manager->flush();

        for ($i = 0; $i < 20; $i++) {
            $template = new Template();
            $template->setName($this->faker->jobTitle);
            $template->addFile($file);

            $manager->persist($template);
        }

        $manager->flush();
    }
}
