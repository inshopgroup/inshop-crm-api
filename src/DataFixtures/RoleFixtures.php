<?php

namespace App\DataFixtures;

use App\Entity\Module;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class Role
 * @package App\DataFixtures
 */
class RoleFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $modules = [
            'Address' => [],
            'Category' => [],
            'City' => [],
            'Client' => [],
            'Company' => [],
            'Company product' => [],
            'Contact' => [],
            'Contact type' => [],
            'Country' => [],
            'Currency' => [],
            'Image' => [],
            'Label' => [],
            'File' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
                'DOWNLOAD',
            ],
            'Group' => [],
            'Language' => [],
            'Module' => [],
            'Brand' => [],
            'Channel' => [],
            'Product' => [],
            'Product sell price' => [],
            'Role' => [],
            'User' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
            ],
            'Vat' => [],
            'Backup' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
                'DOWNLOAD',
            ],
            'Backup type' => [],
            'Backup status' => [],
            'Other' => [
                'SEARCH',
                'HISTORY',
            ],
        ];

        $actions = [
            'LIST',
            'CREATE',
            'SHOW',
            'UPDATE',
            'DELETE',
        ];

        foreach ($modules as $moduleName => $_actions) {
            $module = new Module();
            $module->setName($moduleName);
            $manager->persist($module);

            if (empty($_actions)) {
                $_actions = $actions;
            }

            foreach ($_actions as $action) {
                $role = new Role();
                $role->setModule($module);
                $role->setName(ucfirst(strtolower($action)));
                $role->setRole('ROLE_' . strtoupper(str_replace(' ', '_', $module->getName())) . '_' . $action);
                $manager->persist($role);
            }

            $manager->flush();
        }
    }
}
