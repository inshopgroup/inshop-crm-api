<?php

namespace App\Controller;

use App\Entity\File;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Class DownloadFileAction
 * @package App\Controller
 */
class DownloadFileAction extends Controller
{
    /**
     * @param File $file
     * @return BinaryFileResponse
     * @IsGranted("ROLE_FILE_DOWNLOAD")
     * @Route("/files/download/{id}")
     */
    public function indexAction(File $file): BinaryFileResponse
    {
        $path = $this->getParameter('kernel.project_dir').'/var/files/'.$file->getContentUrl();

        return $this->file($path);
    }
}
