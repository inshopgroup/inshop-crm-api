<?php

namespace App\DataFixtures;

use App\Entity\BackupStatus;
use App\Entity\BackupType;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class BackupFixtures
 * @package App\DataFixtures
 */
class BackupFixtures extends Fixture
{
    /**
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        $statuses = [
            BackupStatus::STATUS_PENDING => 'Pending',
            BackupStatus::STATUS_IN_PROGRESS => 'In progress',
            BackupStatus::STATUS_READY => 'Ready',
            BackupStatus::STATUS_FAILED => 'Failed',
        ];

        $types = [
            BackupType::TYPE_DATABASE => 'Database',
            BackupType::TYPE_FILES => 'Files',
            BackupType::TYPE_SQLITE => 'Sqlite',
        ];

        foreach ($statuses as $id => $status) {
            $backupStatus = new BackupStatus();
//            $backupStatus->setId($id);
            $backupStatus->setName($status);
            $manager->persist($backupStatus);
        }

        foreach ($types as $id => $type) {
            $backupType = new BackupType();
//            $backupStatus->setId($id);
            $backupType->setName($type);
            $manager->persist($backupType);
        }

        $manager->flush();
    }
}
