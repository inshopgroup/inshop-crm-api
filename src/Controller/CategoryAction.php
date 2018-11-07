<?php

namespace App\Controller;

use App\Entity\Category;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Class CategoryAction
 * @package App\Controller
 */
final class CategoryAction
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
     * @param $data
     * @return mixed
     */
    public function __invoke()
    {
        return $this->em->getRepository(Category::class)->getCategories();
    }
}
