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

        $groupDemo = new Group();
        $groupDemo->setName('Demo');

        /** @var Role $role */
        foreach ($roles as $role) {
            if (!\in_array($role->getRole(), ['ROLE_GROUP_UPDATE', 'ROLE_GROUP_DELETE', 'ROLE_USER_UPDATE', 'ROLE_USER_DELETE'])) {
                $groupDemo->addRole($role);
            }
        }
        $manager->persist($groupDemo);


        $user = new User();
        $user->setLanguage($language);
        $user->setUsername('demo');
        $user->setName(sprintf('%s %s', $this->faker->firstName, $this->faker->lastName));
        $user->addGroup($groupDemo);
        $user->setPassword($this->encoder->encodePassword($user, 'demo'));
        $manager->persist($user);
        $manager->flush();


        $groupAdmin = new Group();
        $groupAdmin->setName('Administrators');
        foreach ($roles as $role) {
            $groupAdmin->addRole($role);
        }
        $manager->persist($groupAdmin);


        $user = new User();
        $user->setLanguage($language);
        $user->setUsername('admin');
        $user->setName(sprintf('%s %s', $this->faker->firstName, $this->faker->lastName));
        $user->addGroup($groupAdmin);
        $user->setPassword($this->encoder->encodePassword($user, 'admin'));
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
