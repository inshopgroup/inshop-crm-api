<?php

namespace App\Controller\Text;

use App\Entity\Text;
use App\Repository\TextRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TextFrontendGetItemAction
 * @package App\Controller\Text
 */
class TextFrontendGetItemAction
{
    /**
     * @param Request $request
     * @param TextRepository $textRepository
     * @return Text
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function __invoke(Request $request, TextRepository $textRepository): Text
    {
        $text = $textRepository->findBySlug($request->get('slug'));

        if (!$text) {
            throw new NotFoundHttpException();
        }

        return $text;
    }
}
