<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\ContactType;
use App\Entity\Document;
use App\Entity\File;
use App\Entity\Label;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class CompanyFixtures
 * @package App\DataFixtures
 */
class CompanyFixtures extends Fixture implements DependentFixtureInterface
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
        $labelNames = ['A', 'B', 'C', 'D', 'E'];

        foreach ($labelNames as $labelName) {
            $label = new Label();
            $label->setName($labelName);

            $manager->persist($label);
        }

        $manager->flush();

        $addresses = $manager->getRepository(Address::class)->findAll();
        $files = $manager->getRepository(File::class)->findAll();
        $labels = $manager->getRepository(Label::class)->findAll();

        for ($i = 0; $i < 2; $i++) {
            $company = new Company();
            $company->setName($this->faker->company);
            $company->setContactPerson($this->faker->firstName);
            $company->setDescription($this->faker->company);
            $company->setFullName($this->faker->company);
            $company->setBankName($this->faker->company);
            $company->setBankAccountNumber($this->faker->bankAccountNumber);
            $company->setIsVat($this->faker->boolean);
            $company->setVatComment($this->faker->company);
            $company->setKrs($this->faker->numberBetween(1000000, 10000000));
            $company->setNip($this->faker->numberBetween(1000000, 10000000));
            $company->addAddress($this->faker->randomElement($addresses));
            $company->addLabel($this->faker->randomElement($labels));

            $phone = new Contact();
            $phone->setContactType($manager->getRepository(ContactType::class)->find(ContactType::TYPE_PHONE));
            $phone->setValue($this->faker->phoneNumber);
            $company->addContact($phone);
            $manager->persist($phone);

            $email = new Contact();
            $email->setContactType($manager->getRepository(ContactType::class)->find(ContactType::TYPE_EMAIL));
            $email->setValue($this->faker->email);
            $company->addContact($email);
            $manager->persist($email);

            $manager->persist($company);

            for ($n = 0; $n < 3; $n++) {
                $document = new Document();
                $document->setName($this->faker->colorName);
                $document->addFile($this->faker->randomElement($files));
                $document->setCompany($company);
                $manager->persist($document);
            }
        }

        $manager->flush();
    }

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return array(
            AddressFixtures::class,
        );
    }
}
