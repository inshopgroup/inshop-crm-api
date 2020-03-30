<?php

namespace App\DataFixtures;

use App\Entity\Language;
use App\Entity\Text;
use App\Entity\TextTranslation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;

/**
 * Class TextFixtures
 * @package App\DataFixtures
 */
class TextFixtures extends Fixture
{
    /**
     * @var Faker\Generator
     */
    protected $faker;

    /**
     * TextFixtures constructor.
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
        $languages = $manager->getRepository(Language::class)->findAll();

        $text = new Text();
        $manager->persist($text);

        /** @var Language $language */
        foreach ($languages as $language) {
            $textTranslation = new TextTranslation();
            $textTranslation->setTitle('Terms of use');
            $textTranslation->setContent($this->faker->text);
            $textTranslation->setTranslatable($text);
            $textTranslation->setLanguage($language);
            $text->addTranslation($textTranslation);

            $manager->persist($textTranslation);
        }

        $text = new Text();
        $manager->persist($text);

        /** @var Language $language */
        foreach ($languages as $language) {
            $textTranslation = new TextTranslation();
            $textTranslation->setTitle('Homepage about');
            $textTranslation->setContent($this->faker->text);
            $textTranslation->setTranslatable($text);
            $textTranslation->setLanguage($language);
            $text->addTranslation($textTranslation);

            $manager->persist($textTranslation);
        }

        $text = new Text();
        $manager->persist($text);

        /** @var Language $language */
        foreach ($languages as $language) {
            $textTranslation = new TextTranslation();
            $textTranslation->setTitle('About');
            $textTranslation->setContent($this->faker->text);
            $textTranslation->setTranslatable($text);
            $textTranslation->setLanguage($language);
            $text->addTranslation($textTranslation);

            $manager->persist($textTranslation);
        }

        $manager->flush();
    }
}
