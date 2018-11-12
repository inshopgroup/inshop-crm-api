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
            'Document' => [],
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
            'Invoice header' => [],
            'Invoice line' => [],
            'Invoice status' => [],
            'Invoice type' => [],
            'Language' => [],
            'Module' => [],
            'Order header' => [],
            'Order line' => [],
            'Order line status' => [],
            'Order status' => [],
            'Payment type' => [],
            'Brand' => [],
            'Channel' => [],
            'Product' => [],
            'Product sell price' => [],
            'Project' => [],
            'Project status' => [],
            'Project type' => [],
            'Purchase order header' => [],
            'Purchase order line' => [],
            'Purchase order line status' => [],
            'Purchase order status' => [],
            'Role' => [],
            'Shipment method' => [],
            'Shipping notice header' => [],
            'Shipping notice line' => [],
            'Shipping notice line status' => [],
            'Shipping notice status' => [],
            'Stock line' => [],
            'Stock line status' => [],
            'Task' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
                'DEADLINE',
            ],
            'Task status' => [],
            'Template' => [],
            'Template type' => [],
            'User' => [
                'LIST',
                'CREATE',
                'SHOW',
                'UPDATE',
                'DELETE',
                'DASHBOARD',
            ],
            'Vat' => [],
            'Warehouse' => [],
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
                'CALENDAR',
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
