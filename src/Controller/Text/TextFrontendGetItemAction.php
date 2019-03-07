<?php

namespace App\Controller\Text;

use App\Entity\Text;
use App\Repository\TextRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class TextFrontendGetItemAction
 * @package App\Controller\Candidate
 */
class TextFrontendGetItemAction
{
    /**
     * @param Request $request
     * @param TextRepository $textRepository
     * @return Text
     */
    public function __invoke(Request $request, TextRepository $textRepository): Text
    {
        $text = $textRepository->findOneBy(['slug' => $request->get('slug')]);

        if (!$text) {
            throw new NotFoundHttpException();
        }

        return $text;
    }
}
