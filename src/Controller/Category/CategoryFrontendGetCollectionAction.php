<?php

namespace App\Controller\Category;

use App\Repository\CategoryRepository;

/**
 * Class CategoryFrontendGetCollectionAction
 * @package App\Controller
 */
final class CategoryFrontendGetCollectionAction
{
    /**
     * @param CategoryRepository $categoryRepository
     * @return array
     */
    public function __invoke(CategoryRepository $categoryRepository): array
    {
        return $categoryRepository->getCategories();
    }
}
