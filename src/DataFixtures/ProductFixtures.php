<?php

namespace App\DataFixtures;

use App\Entity\Brand;
use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Entity\Channel;
use App\Entity\Company;
use App\Entity\CompanyProduct;
use App\Entity\Currency;
use App\Entity\Language;
use App\Entity\Product;
use App\Entity\ProductSellPrice;
use App\Entity\ProductTranslation;
use App\Entity\Vat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 *
 * Class AddressFixtures
 * @package App\DataFixtures
 */
class ProductFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * ProductFixtures constructor.
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
        $currencies = $manager->getRepository(Currency::class)->findAll();
        $languages = $manager->getRepository(Language::class)->findAll();

        for ($j = 0; $j < 5; $j++) {
            $channel = new Channel();
            $channel->setName($this->faker->name);
            $channel->setCurrency($this->faker->randomElement($currencies));
            $channel->setIsPublic(true);

            $manager->persist($channel);
        }

        for ($j = 0; $j < 5; $j++) {
            $brand = new Brand();
            $brand->setName($this->faker->name);

            $manager->persist($brand);
        }

        for ($j = 0; $j < 5; $j++) {
            $category = new Category();

            foreach ($languages as $language) {
                $categoryTranslation = new CategoryTranslation();
                $categoryTranslation->setTranslatable($category);
                $categoryTranslation->setLanguage($language);
                $categoryTranslation->setName($this->faker->text(10));
                $categoryTranslation->setDescription($this->faker->text);
                $category->addTranslation($categoryTranslation);

                $manager->persist($categoryTranslation);
            }

            $categories[] = $category;
            $manager->persist($category);

            for ($k = 0; $k < 15; $k++) {
                $subCategory = new Category();
                $subCategory->setParent($category);

                foreach ($languages as $language) {
                    $categoryTranslation = new CategoryTranslation();
                    $categoryTranslation->setTranslatable($subCategory);
                    $categoryTranslation->setLanguage($language);
                    $categoryTranslation->setName($this->faker->text(10));
                    $categoryTranslation->setDescription($this->faker->text);
                    $subCategory->addTranslation($categoryTranslation);

                    $manager->persist($categoryTranslation);
                }

                $categories[] = $subCategory;
                $manager->persist($subCategory);
            }
        }

        $manager->flush();

        $vats = $manager->getRepository(Vat::class)->findAll();
        $channels = $manager->getRepository(Channel::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();
        $brands = $manager->getRepository(Brand::class)->findAll();

        for ($j = 0; $j < 200; $j++) {
            $product = new Product();
            $product->setBrand($this->faker->randomElement($brands));
            $product->setEan($this->faker->ean13);
            $product->setCategory($this->faker->randomElement($categories));

            $companyProduct = new CompanyProduct();
            $companyProduct->setProduct($product);
            $companyProduct->setCompany($this->faker->randomElement($companies));
            $companyProduct->setCurrency($this->faker->randomElement($currencies));
            $companyProduct->setAvailability($this->faker->numberBetween(0, 1));
            $companyProduct->setPriceBuyNetto($this->faker->numberBetween(1, 500));

            $manager->persist($companyProduct);

            $product->addCompanyProduct($companyProduct);

            for ($k = 0; $k < 5; $k++) {
                $productSellPrice = new ProductSellPrice();
                $productSellPrice->setProduct($product);
                $productSellPrice->setChannel($this->faker->randomElement($channels));
                $productSellPrice->setVat($this->faker->randomElement($vats));
                $productSellPrice->setActiveFrom(new \DateTime());
                $productSellPrice->setActiveTo((new \DateTime())->modify('+1 year'));
                $productSellPrice->setPriceSellBrutto($this->faker->numberBetween(501, 1000));
                $productSellPrice->setPriceOldSellBrutto($this->faker->numberBetween(1000, 2000));
                $productSellPrice->setCompanyProduct($product->getCompanyProducts()->first());

                $manager->persist($productSellPrice);
            }

            foreach ($languages as $language) {
                $productTranslation = new ProductTranslation();
                $productTranslation->setTranslatable($product);
                $productTranslation->setLanguage($language);
                $productTranslation->setName($this->faker->company);
                $productTranslation->setDescription($this->faker->text);
                $product->addTranslation($productTranslation);

                $manager->persist($productTranslation);
            }

            $manager->persist($product);
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return array(
            CompanyFixtures::class,
            CountryFixtures::class,
        );
    }
}
