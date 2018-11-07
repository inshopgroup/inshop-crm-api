<?php

namespace App\Command;

use App\Entity\Backup;
use App\Entity\BackupStatus;
use App\Entity\BackupType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class BackupDatabaseCommand
 * @package App\Command
 */
class BackupDatabaseCommand extends ContainerAwareCommand
{
    protected function configure(): void
    {
        $this
            ->setName('backup:database')
            ->setDescription('Backup database');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null|void
     * @throws \Doctrine\ORM\ORMException
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $backup = new Backup();
        $backup->setType($em->getReference(BackupType::class, BackupType::TYPE_DATABASE));
        $backup->setStatus($em->getReference(BackupStatus::class, BackupStatus::STATUS_IN_PROGRESS));
        $backup->setName('Automatic backup');
        $backup->setSize(0);

        $em->persist($backup);
        $em->flush();

        $dir = sprintf('%s/data/backups', $this->getContainer()->getParameter('kernel.project_dir'));

        if (!file_exists($dir)) {
            if (!mkdir($dir) && !is_dir($dir)) {
                $message = sprintf('Directory "%s" was not created', $dir);

                $backup->setStatus($em->getReference(BackupStatus::class, BackupStatus::STATUS_FAILED));
                $backup->setNotice($message);

                $em->flush();

                throw new \RuntimeException($message);
            }
        }

        $path = sprintf(
            '%s/data/backups/backup_%s_%s.sql',
            $this->getContainer()->getParameter('kernel.project_dir'),
            $backup->getId(),
            $backup->getCreatedAt()->format('Y-m-d')
        );

        $connection = $em->getConnection();

        $command = sprintf(
            'export PGPASSWORD=%s && pg_dump -U %s -h %s -d %s > %s',
            $connection->getPassword(),
            $connection->getUsername(),
            $connection->getHost(),
            $connection->getDatabase(),
            $path
        );

        $result = exec($command, $out, $return_val);

        if ($return_val !== 0) {
            $backup->setStatus($em->getReference(BackupStatus::class, BackupStatus::STATUS_FAILED));
            $backup->setNotice($result);
        } else {
            $backup->setStatus($em->getReference(BackupStatus::class, BackupStatus::STATUS_READY));
            $backup->setSize(\filesize($path));
            $backup->setContentUrl($path);
        }

        $em->flush();

        $io->note((new \DateTime())->format('Y-m-d H:i:s'));
    }
}
