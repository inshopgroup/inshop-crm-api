<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\File;
use App\Form\FileType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CreateFileAction
 * @package App\Controller
 */
final class CreateFileAction
{
    private $validator;
    private $doctrine;
    private $factory;

    /**
     * CreateFileAction constructor.
     * @param RegistryInterface $doctrine
     * @param FormFactoryInterface $factory
     * @param ValidatorInterface $validator
     */
    public function __construct(RegistryInterface $doctrine, FormFactoryInterface $factory, ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->doctrine = $doctrine;
        $this->factory = $factory;
    }

    /**
     * @param Request $request
     * @return File
     */
    public function __invoke(Request $request): File
    {
        $file = new File();

        $form = $this->factory->create(FileType::class, $file);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($file);
            $em->flush();

            // Prevent the serialization of the file property
            $file->file = null;

            return $file;
        }

        // This will be handled by API Platform and returns a validation error.
        throw new ValidationException($this->validator->validate($file));
    }
}
