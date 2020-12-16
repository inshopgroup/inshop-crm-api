<?php

namespace App\Controller;

use App\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * Class DownloadFileAction
 * @package App\Controller
 */
class DownloadFileAction extends AbstractController
{
    /**
     * @param File $file
     * @return BinaryFileResponse
     * @IsGranted("ROLE_FILE_DOWNLOAD")
     * @Route("/files/download/{id}")
     */
    public function indexAction(File $file): BinaryFileResponse
    {
        $path = $this->getParameter('kernel.project_dir').'/data/files/'.$file->getContentUrl();

        return $this->file($path);
    }
}
