<?php
namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;
    private $uploadFolder;

    public function __construct($targetDirectory, $uploadFolder)
    {
        $this->targetDirectory = $targetDirectory;
        $this->uploadFolder = '/'.$uploadFolder;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            throw new FileException($e->getMessage());
        }
        return $fileName;
    }

    public function delete($fileName)
    {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getPath($fileName));
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getUploadFolder()
    {
        return $this->uploadFolder;
    }

    public function getPath($fileName) {
        return ($this->getTargetDirectory().'/'.$fileName);
    }
}