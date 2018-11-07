<?php

namespace App\Controller;

use App\Entity\Company;
use App\Repository\CompanyRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class CompanyLastAction
 * @package App\Controller
 */
final class CompanyLastAction
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * EventDeadlineAction constructor.
     * @param EntityManagerInterface $em
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @param Request $request
     * @return Company|null
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke(Request $request): ?Company
    {
        /** @var CompanyRepository $companyRepository */
        $companyRepository = $this->em->getRepository(Company::class);

        return $companyRepository->findLast();
    }
}
