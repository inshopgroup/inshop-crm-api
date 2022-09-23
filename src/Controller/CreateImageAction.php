<?php

namespace App\Controller;

use ApiPlatform\Core\Bridge\Symfony\Validator\Exception\ValidationException;
use App\Entity\Image;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Liip\ImagineBundle\Binary\BinaryInterface;
use Liip\ImagineBundle\Imagine\Data\DataManager;
use Liip\ImagineBundle\Imagine\Filter\FilterManager;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

final class CreateImageAction
{
    private DataManager $dataManager;

    private FilterManager $filterManager;

    private EntityManagerInterface $em;

    private ValidatorInterface $validator;

    public function __construct(
        DataManager $dataManager,
        FilterManager $filterManager,
        EntityManagerInterface $em,
        ValidatorInterface $validator
    ) {
        $this->dataManager = $dataManager;
        $this->filterManager = $filterManager;
        $this->em = $em;
        $this->validator = $validator;
    }

    public function __invoke(Request $request): Image
    {
        $uploadedFile = $request->files->get('file');

        $image = new Image();
        $error = $this->validator->validatePropertyValue($image, 'file', $uploadedFile);

        if ($error->count() > 0) {
            throw new ValidationException($error);
        }

        $this->prepareImageFile($uploadedFile);
        $image->setImage($uploadedFile);
        $this->validator->validate($image);

        $this->em->persist($image);
        $this->em->flush();

        $image->image = null;

        return $image;
    }

    private function prepareImageFile(UploadedFile $uploadedFile): void
    {
        $filters = [
            'thumbnail_200' => 'images_200_filesystem',
            'thumbnail_420' => 'images_420_filesystem',
            'thumbnail_1000' => 'images_1000_filesystem',
        ];

        foreach (array_keys($filters) as $filter) {

            /** @var BinaryInterface $img */
            $img = $this->dataManager->find($filter, $uploadedFile->getFilename());
            $img = $this->filterManager->applyFilter($img, $filter);
            file_put_contents(
                sys_get_temp_dir() . '/' . $uploadedFile->getFilename(),
                $img->getContent()
            );
        }
    }
}
