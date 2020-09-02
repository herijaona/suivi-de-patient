<?php


namespace App\Service;


use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploadService
{

    private $container;

    public function __construct(
        ContainerInterface $container
    ) {
        $this->container = $container;
    }

    public function upload(UploadedFile $file = null, $fileDir = null)
    {
        if (!$file)
            return null;
        //$fileName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileName = uniqid() . '.' . $file->getClientOriginalExtension();
        if (!$fileDir)
            $fileDir = $this->container->getParameter('import_directory');

        if (!file_exists($fileDir))
            mkdir($fileDir, 0777, true);

        try {
            $file->move(
                $fileDir,
                $fileName
            );
        } catch (FileException $e) {
            return null;
        }
        return $fileName;
    }

    public function removeFile($dir, $file)
    {
        if (!$file)
            return;
        $baseDir = $this->container->getParameter('import_directory');
        unlink($baseDir . "/" . $file);
    }
}
