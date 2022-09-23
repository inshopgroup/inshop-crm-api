<?php

namespace App\DataFixtures;

use App\Entity\Module;
use App\Entity\Role;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class RoleFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $modules = [
            'Address' => [],
            'Client' => [],
            'Contact' => [],
            'Contact type' => [],
            'Country' => [],
            'Document' => [],
            'Label' => [],
            'File' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
                'DOWNLOAD',
            ],
            'Group' => [
                'LIST',
//                'CREATE',
                'SHOW',
//                'UPDATE',
//                'DELETE',
            ],
            'History' => [
                'LIST',
            ],
            'Module' => [],
            'Project' => [],
            'Project status' => [],
            'Project type' => [],
            'Role' => [],
            'Task' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
                'DEADLINE',
            ],
            'Task status' => [],
            'User' => [
                'LIST',
//                'CREATE',
                'SHOW',
//                'UPDATE',
//                'DELETE',
                'DASHBOARD',
            ],
            'Other' => [
                'SEARCH',
                'CALENDAR',
            ],
            'Language' => [],
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
