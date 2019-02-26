<?php

namespace App\Controller\Category;

use App\Entity\Category;
use App\Entity\CategoryTranslation;
use App\Repository\CategoryTranslationRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class CategoryFrontendGetItemAction
 * @package App\Controller\Category
 */
class CategoryFrontendGetItemAction
{
    /**
     * @param Request $request
     * @param CategoryTranslationRepository $categoryTranslationRepository
     * @return Category
     */
    public function __invoke(Request $request, CategoryTranslationRepository $categoryTranslationRepository): Category
    {
        /** @var CategoryTranslation $categoryTranslation */
        $categoryTranslation = $categoryTranslationRepository->findOneBy(['slug' => $request->get('slug')]);

        if (!$categoryTranslation) {
            throw new NotFoundHttpException();
        }

        return $categoryTranslation->getTranslatable();
    }
}
