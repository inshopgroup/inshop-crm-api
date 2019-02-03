<?php

namespace App\Command;

use App\Service\Elastica\Client\ElasticaClientProduct;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

/**
 * Class ElasticaIndexProductCommand
 * @package App\Command
 */
class ElasticaIndexProductCommand extends ContainerAwareCommand
{
    /**
     * @var ElasticaClientProduct
     */
    protected $search;

    /**
     * ElasticaIndexProductCommand constructor.
     * @param null|string $name
     * @param ElasticaClientProduct $search
     */
    public function __construct(?string $name = null, ElasticaClientProduct $search)
    {
        parent::__construct($name);

        $this->search = $search;
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
     * @return int|null|void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->search->createIndex();
        $this->search->createMapping();

        /** @var EntityManagerInterface $em */
        $em = $this->getContainer()->get('doctrine.orm.entity_manager');

        $io = new SymfonyStyle($input, $output);
        $io->note('Indexing candidates');
        $io->note((new \DateTime())->format('Y-m-d H:i:s'));

        /** @var CandidateRepository $repository */
        $repository = $em->getRepository(Candidate::class);
        $entities = $repository->getActiveCandidates();
        $entitiesCount = \count($entities);

        $io->progressStart($entitiesCount);

        $objects = [];

        /** @var Candidate $entity */
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
    }
}
