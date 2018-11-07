<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\Language;
use App\Entity\Product;
use App\Entity\ProductTranslation;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class ProductFixtures
 * @package App\DataFixtures
 */
class ProductFixtures extends Fixture
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * CompanyFixtures constructor.
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
        $brand = new Brand();
        $brand->setName('Roland');
        $manager->persist($brand);

        $categories = $manager->getRepository(Category::class)->findAll();
        $languages = $manager->getRepository(Language::class)->findAll();

        foreach ($categories as $category) {
            for ($i = 0; $i <= 50; $i++) {
                $product = new Product();
                $product->setCategory($category);
                $product->setBrand($brand);
                $product->setEan($this->faker->ean8);

                foreach ($languages as $language) {
                    $productTranslation = new ProductTranslation();
                    $productTranslation->setTranslatable($product);
                    $productTranslation->setLanguage($language);
                    $productTranslation->setName($this->faker->title);
                    $productTranslation->setDescription($this->faker->title);

                    $manager->persist($productTranslation);
                }

                $manager->persist($product);
            }

            $manager->flush();
        }
    }
}
