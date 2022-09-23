<?php

namespace App\DataFixtures;

use App\Entity\Address;
use App\Entity\Client;
use App\Entity\Contact;
use App\Entity\ContactType;
use App\Entity\Document;
use App\Entity\File;
use App\Entity\Project;
use App\Entity\ProjectStatus;
use App\Entity\ProjectType;
use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Entity\User;
use DateTime;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;

class ClientFixtures extends Fixture implements DependentFixtureInterface
{
    protected Faker\Generator $faker;

    public function __construct()
    {
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $addresses = $manager->getRepository(Address::class)->findAll();
        $users = $manager->getRepository(User::class)->findAll();
        $projectTypes = $manager->getRepository(ProjectType::class)->findAll();
        $projectStatuses = $manager->getRepository(ProjectStatus::class)->findAll();
        $taskStatuses = $manager->getRepository(TaskStatus::class)->findAll();
        $files = $manager->getRepository(File::class)->findAll();

        for ($j = 0; $j < 10; $j++) {
            $client = new Client();
            $client->setUsername($this->faker->email());
            $client->setPassword(md5($this->faker->email()));
            $client->setToken($this->faker->ean13());
            $client->setTokenCreatedAt(new DateTime());
            $client->setName(sprintf('%s %s', $this->faker->firstName(), $this->faker->lastName()));
            $client->setDescription($this->faker->text());
            $client->addAddress($this->faker->randomElement($addresses));
            $client->setCreatedAt($this->faker->dateTimeBetween('-30 days', '+0 days'));

            $manager->persist($client);

            $phone = new Contact();
            $phone->setContactType($manager->getRepository(ContactType::class)->find(ContactType::TYPE_PHONE));
            $phone->setValue($this->faker->phoneNumber());
            $client->addContact($phone);
            $manager->persist($phone);

            $email = new Contact();
            $email->setContactType($manager->getRepository(ContactType::class)->find(ContactType::TYPE_EMAIL));
            $email->setValue($this->faker->email());
            $client->addContact($email);
            $manager->persist($email);

            for ($k = 0; $k < 3; $k++) {
                $project = new Project();
                $project->setName($this->faker->name());
                $project->setClient($client);
                $project->setType($this->faker->randomElement($projectTypes));
                $project->setStatus($this->faker->randomElement($projectStatuses));
                $manager->persist($project);

                for ($n = 0; $n < 3; $n++) {
                    $task = new Task();
                    $task->setTimeEstimated($this->faker->numberBetween(15, 30));
                    $task->setTimeSpent($this->faker->numberBetween(15, 30));
                    $task->setName($this->faker->name());
                    $task->setProject($project);
                    $task->setAssignee($this->faker->randomElement($users));
                    $task->setStatus($this->faker->randomElement($taskStatuses));
                    $task->setDeadline($this->faker->dateTimeBetween('-30 days', '+30 days'));
                    $manager->persist($task);
                }

                for ($n = 0; $n < 3; $n++) {
                    $document = new Document();
                    $document->setName($this->faker->colorName());
                    $document->addFile($this->faker->randomElement($files));
                    $document->setClient($client);
                    $manager->persist($document);
                }
            }

            $manager->flush();
        }
    }

    public function getDependencies(): array
    {
        return array(
            FileFixtures::class,
            AddressFixtures::class,
            UserFixtures::class,
        );
    }
}
