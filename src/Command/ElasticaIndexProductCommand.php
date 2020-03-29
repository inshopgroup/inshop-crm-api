<?php

namespace App\Command;

use App\Entity\Product;
use App\Repository\ProductRepository;
use App\Service\Elastica\Client\ElasticaClientProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ElasticaIndexProductCommand
 * @package App\Command
 */
class ElasticaIndexProductCommand extends Command
{
    /**
     * @var ElasticaClientProduct
     */
    protected $search;

    /**
     * @var EntityManagerInterface
     */
    protected $entityManager;

    /**
     * ElasticaIndexProductCommand constructor.
     * @param ElasticaClientProduct $search
     * @param EntityManagerInterface $entityManager
     */
    public function __construct(ElasticaClientProduct $search, EntityManagerInterface $entityManager)
    {
        $this->search = $search;
        $this->entityManager = $entityManager;

        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setName('elastica:index:product')
            ->setDescription('Update product index in elasticsearch');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->search->createIndex();
        $this->search->createMapping();

        $io = new SymfonyStyle($input, $output);
        $io->note('Indexing products');
        $io->note((new \DateTime())->format('Y-m-d H:i:s'));

        /** @var ProductRepository $repository */
        $repository = $this->entityManager->getRepository(Product::class);
        $entities = $repository->findAll();
        $entitiesCount = \count($entities);

        $io->progressStart($entitiesCount);

        $objects = [];

        /** @var Product $entity */
        foreach ($entities as $entity) {
            $objects[] = $this->search->toArray($entity);
            $io->progressAdvance();
        }

        if (!empty($objects)) {
            $this->search->addDocuments($objects);
        }

        $io->progressFinish();
        $io->note((new \DateTime())->format('Y-m-d H:i:s'));

        $io->success('Search index updated successfully');

        return 0;
    }
}
