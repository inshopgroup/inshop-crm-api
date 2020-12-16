<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Currency;
use App\Entity\File;
use App\Entity\InvoiceHeader;
use App\Entity\InvoiceLine;
use App\Entity\InvoiceStatus;
use App\Entity\InvoiceType;
use App\Entity\Language;
use App\Entity\Product;
use App\Entity\Vat;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Exception;
use Faker;

/**
 * Class InvoiceFixtures
 * @package App\DataFixtures
 */
class InvoiceFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var Faker\Generator
     */
    protected Faker\Generator $faker;

    /**
     * InvoiceFixtures constructor.
     */
    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param ObjectManager $manager
     * @throws Exception
     */
    public function load(ObjectManager $manager): void
    {
        $vats = $manager->getRepository(Vat::class)->findAll();
        $currencies = $manager->getRepository(Currency::class)->findAll();
        $languages = $manager->getRepository(Language::class)->findAll();
        $invoiceStatuses = $manager->getRepository(InvoiceStatus::class)->findAll();
        $invoiceTypes = $manager->getRepository(InvoiceType::class)->findAll();
        $companies = $manager->getRepository(Company::class)->findAll();
        $products = $manager->getRepository(Product::class)->findAll();
        $files = $manager->getRepository(File::class)->findAll();

        for ($i = 0; $i < 10; $i++) {
            /** @var Company $company */
            $company = $this->faker->randomElement($companies);

            $ih = new InvoiceHeader();
            $ih->setCompanyFrom($this->faker->randomElement($companies));
            $ih->setCompanyTo($this->faker->randomElement($companies));
            $ih->setCurrency($this->faker->randomElement($currencies));
            $ih->setLanguage($this->faker->randomElement($languages));
            $ih->setStatus($this->faker->randomElement($invoiceStatuses));
            $ih->setType($this->faker->randomElement($invoiceTypes));
            $ih->setNumber($this->faker->randomNumber());
            $ih->setDateOfInvoice(new DateTime());
            $ih->setDateOfSale(new DateTime());
            $ih->setMaturity('payment deadline - 30 days');
            $ih->addFile($this->faker->randomElement($files));

            $manager->persist($ih);

            for ($j = 0; $j < 5; $j++) {
                $il = new InvoiceLine();
                $il->setHeader($ih);
                $il->setProduct($this->faker->randomElement($products));
                $il->setVat($this->faker->randomElement($vats));
                $il->setName($this->faker->name);
                $il->setUnitPriceNetto($this->faker->randomFloat(2));
                $il->setUnitsCount($this->faker->numberBetween(1, 200));

                $manager->persist($il);
            }

            $manager->flush();
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
            ProductFixtures::class,
            OrderFixtures::class,
        );
    }
}
