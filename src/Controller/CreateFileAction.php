<?php

namespace App\Controller;

use App\Entity\File;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CreateFileAction
 * @package App\Controller
 */
final class CreateFileAction
{
    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $em;

    /**
     * @var ValidatorInterface
     */
    private ValidatorInterface $validator;

    /**
     * CreateFileAction constructor.
     * @param EntityManagerInterface $em
     * @param ValidatorInterface $validator
     */
    public function __construct(
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {
        $this->em = $em;
        $this->validator = $validator;
    }

    /**
     * @param Request $request
     * @return File
     */
    public function __invoke(Request $request): File
    {
        $uploadedFile = $request->files->get('file');
        $file = new File();
        $file->setFile($uploadedFile);
        $this->validator->validate($file);

        $this->em->persist($file);
        $this->em->flush();

        $file->file = null;

        return $file;
    }
}
