<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Language;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture implements DependentFixtureInterface
{
    protected UserPasswordHasherInterface $encoder;

    protected Faker\Generator $faker;

    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Faker\Factory::create();
    }

    public function load(ObjectManager $manager): void
    {
        $roles = $manager->getRepository(Role::class)->findAll();
        $language = $manager->getRepository(Language::class)->findOneBy(['code' => 'en']);

        $groupDemo = new Group();
        $groupDemo->setName('Demo');

        /** @var Role $role */
        foreach ($roles as $role) {
            $groupDemo->addRole($role);
        }
        $manager->persist($groupDemo);


        $user = new User();
        $user->setLanguage($language);
        $user->setUsername('demo');
        $user->setName(sprintf('%s %s', $this->faker->firstName(), $this->faker->lastName()));
        $user->addGroup($groupDemo);
        $user->setPassword($this->encoder->hashPassword($user, 'demo'));

        $manager->persist($user);
        $manager->flush();
    }

    public function getDependencies(): array
    {
        return array(
            RoleFixtures::class,
        );
    }
}
