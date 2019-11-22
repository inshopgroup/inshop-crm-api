<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\Image;
use App\Form\ImageType;
use Doctrine\Common\Persistence\ManagerRegistry;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Symfony\Component\DependencyInjection\Container;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class CreateImageAction
 * @package App\Controller
 */
final class CreateImageAction
{
    private $validator;
    private $doctrine;
    private $factory;
    private $container;

    /**
     * CreateImageAction constructor.
     * @param ManagerRegistry $doctrine
     * @param FormFactoryInterface $factory
     * @param ValidatorInterface $validator
     * @param Container $container
     */
    public function __construct(
        ManagerRegistry $doctrine,
        FormFactoryInterface $factory,
        ValidatorInterface $validator,
        ContainerInterface $container
    ) {
        $this->validator = $validator;
        $this->doctrine = $doctrine;
        $this->factory = $factory;
        $this->container = $container;
    }

    /**
     * @param Request $request
     * @return Image
     * @throws \Exception
     */
    public function __invoke(Request $request): Image
    {
        $image = new Image();

        $form = $this->factory->create(ImageType::class, $image);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em = $this->doctrine->getManager();
            $em->persist($image);
            $em->flush();

            // Thumbnails
            $this->makeThumbNail($image);

            // Prevent the serialization of the image property
            $image->image = null;

            return $image;
        }

        // This will be handled by API Platform and returns a validation error.
        throw new ValidationException($this->validator->validate($image));
    }

    /**
     * @param Image $image
     * @throws \Exception
     */
    protected function makeThumbNail(Image $image): void
    {
        $filters = [
            'thumbnail_200' => 200,
            'thumbnail_420' => 420,
            'thumbnail_1000' => 1000,
        ];

        foreach ($filters as $filter => $thumbDir) {
           /** @var BinaryInterface $img */
            $img = $this->container->get('liip_imagine.data.manager')->find($filter, '/images/' . $image->getContentUrl());
            $img = $this->container->get('liip_imagine.filter.manager')->applyFilter($img, $filter);

            file_put_contents($this->container->getParameter('kernel.project_dir') . '/public/images/' . $thumbDir . '/' . $image->getContentUrl(), $img->getContent());
        }
    }
}
