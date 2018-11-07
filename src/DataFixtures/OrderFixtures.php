<?php

namespace App\DataFixtures;

use App\Entity\Channel;
use App\Entity\Client;
use App\Entity\Currency;
use App\Entity\OrderHeader;
use App\Entity\OrderLine;
use App\Entity\OrderLineStatus;
use App\Entity\OrderStatus;
use App\Entity\PaymentType;
use App\Entity\Product;
use App\Entity\ProductSellPrice;
use App\Entity\ShipmentMethod;
use App\Entity\Vat;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class OrderFixtures
 * @package App\DataFixtures
 */
class OrderFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * OrderFixtures constructor.
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
        $channels = $manager->getRepository(Channel::class)->findAll();
        $orderStatuses = $manager->getRepository(OrderStatus::class)->findAll();
        $orderLineStatuses = $manager->getRepository(OrderLineStatus::class)->findAll();

        $clients = $manager->getRepository(Client::class)->findAll();
        $vats = $manager->getRepository(Vat::class)->findAll();
        $paymentTypes = $manager->getRepository(PaymentType::class)->findAll();
        $shipmentMethods = $manager->getRepository(ShipmentMethod::class)->findAll();
        $productSellPrices = $manager->getRepository(ProductSellPrice::class)->findAll();

        for ($i = 0; $i < 50; $i++) {
            $oh = new OrderHeader();
            $oh->setClient($this->faker->randomElement($clients));
            $oh->setChannel($this->faker->randomElement($channels));
            $oh->setStatus($this->faker->randomElement($orderStatuses));
            $oh->setNumber($this->faker->randomNumber());
            $oh->setPaymentType($this->faker->randomElement($paymentTypes));
            $oh->setShipmentMethod($this->faker->randomElement($shipmentMethods));

            $manager->persist($oh);

            for ($j = 0; $j < 2; $j++) {
                /** @var ProductSellPrice $productSellPrice */
                $productSellPrice = $this->faker->randomElement($productSellPrices);

                $ol = new OrderLine();
                $ol->setHeader($oh);
                $ol->setStatus($this->faker->randomElement($orderLineStatuses));
                $ol->setVat($this->faker->randomElement($vats));
                $ol->setName($productSellPrice->getProduct()->getName());
                $ol->setPriceSellBrutto($productSellPrice->getPriceSellBrutto());
                $ol->setProductSellPrice($productSellPrice);

                $manager->persist($ol);
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
        );
    }
}
