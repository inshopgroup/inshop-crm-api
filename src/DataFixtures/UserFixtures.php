<?php

namespace App\DataFixtures;

use App\Entity\Group;
use App\Entity\Language;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class UserFixtures
 * @package App\DataFixtures
 */
class UserFixtures extends Fixture implements DependentFixtureInterface
{
    /**
     * @var UserPasswordEncoderInterface
     */
    protected $encoder;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * BaseController constructor.
     * @param UserPasswordEncoderInterface $encoder
     */
    public function __construct(UserPasswordEncoderInterface $encoder, $faker)
    {
        $this->encoder = $encoder;
        $this->faker = $faker;
    }

    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $roles = $manager->getRepository(Role::class)->findAll();
        $language = $manager->getRepository(Language::class)->findOneBy(['code' => 'en']);
        $users = [];
        $groups = [];

        foreach (['Managers', 'Accounting', 'Administrators'] as $groupTitle) {
            $group = new Group();
            $group->setName($groupTitle);
            foreach ($roles as $role) {
                $group->addRole($role);
            }
            $manager->persist($group);

            $groups[] = $group;
        }

        $user = new User();
        $user->setLanguage($language);
        $user->setUsername('demo');
        $user->setName(sprintf('%s %s', $this->faker->firstName, $this->faker->lastName));
        $user->addGroup($group);
        $user->setPassword($this->encoder->encodePassword($user, 'demo'));
        $manager->persist($user);
        $manager->flush();

        for ($i = 0; $i < 20; $i++) {
            $user = new User();
            $user->setLanguage($language);
            $user->setUsername($this->faker->email);
            $user->setName(sprintf('%s %s', $this->faker->firstName, $this->faker->lastName));
            $user->addGroup($this->faker->randomElement($groups));
            $user->setPassword($this->encoder->encodePassword($user, $this->faker->name));
            $manager->persist($user);

            $users[] = $user;

            $manager->flush();
        }
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
