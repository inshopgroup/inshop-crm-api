<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Language;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordHasherInterface
     */
    protected UserPasswordHasherInterface $encoder;

    /**
     * @var Faker\Generator
     */
    protected Faker\Generator $faker;

    /**
     * UserFixtures constructor.
     * @param UserPasswordHasherInterface $encoder
     */
    public function __construct(UserPasswordHasherInterface $encoder)
    {
        $this->encoder = $encoder;
        $this->faker = Faker\Factory::create();
    }

    /**
     * @param ObjectManager $manager
     */
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

    /**
     * @return array
     */
    public function getDependencies(): array
    {
        return array(
            RoleFixtures::class,
        );
    }
}
