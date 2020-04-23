<?php

namespace App\Command;

use App\Entity\Address;
use App\Entity\City;
use App\Entity\Client;
use App\Entity\Company;
use App\Entity\Contact;
use App\Entity\Country;
use App\Entity\Document;
use App\Entity\Project;
use App\Entity\Task;
use App\Interfaces\SearchInterface;
use App\Service\Elastica\Client\ElasticaClientSearch;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

use function count;

/**
 * Class ElasticaIndexSearchCommand
 * @package App\Command
 */
class ElasticaIndexSearchCommand extends Command
{
    /**
     * @var ElasticaClientSearch
     */
    protected $search;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ElasticaIndexSearchCommand constructor.
     * @param ElasticaClientSearch $search
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ElasticaClientSearch $search, EntityManagerInterface $entityManager)
    {
        $this->search = $search;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('elastica:index:search')
            ->setDescription('Update search index in elasticsearch');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->search->createIndex();
        $this->search->createMapping();

        $io = new SymfonyStyle($input, $output);

        $searchableEntities = [
            Client::class,
            Company::class,
            City::class,
            Country::class,
            Contact::class,
            Address::class,
            Document::class,
            Project::class,
            Task::class,
        ];

        foreach ($searchableEntities as $entityClass) {
            $this->indexEntity($entityClass, $this->entityManager, $io);
        }

        $io->success('Search index updated successfully');

        return 0;
    }

    /**
     * @param string $entityClass
     * @param EntityManagerInterface $em
     * @param SymfonyStyle $io
     * @throws Exception
     */
    private function indexEntity(
        string $entityClass,
        EntityManagerInterface $em,
        SymfonyStyle $io
    ): void {
        $io->note(sprintf('Indexing %s', $entityClass));
        $io->note((new DateTime())->format('Y-m-d H:i:s'));

        $entities = $em->getRepository($entityClass)->findAll();
        $entitiesCount = count($entities);

        $io->progressStart($entitiesCount);

        $objects = [];

        /** @var SearchInterface $entity */
        foreach ($entities as $entity) {
            $objects[] = $this->search->toArray($entity);
            $io->progressAdvance();
        }

        if (!empty($objects)) {
            $this->search->addDocuments($objects);
        }

        $io->progressFinish();
        $io->note((new DateTime())->format('Y-m-d H:i:s'));
    }
}
