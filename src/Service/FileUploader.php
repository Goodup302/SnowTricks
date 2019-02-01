<?php
namespace App\Service;

use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileUploader
{
    private $targetDirectory;

    public function __construct($targetDirectory)
    {
        $this->targetDirectory = $targetDirectory;
    }

    public function upload(UploadedFile $file)
    {
        $fileName = md5(uniqid()).'.'.$file->guessExtension();
        try {
            $file->move($this->getTargetDirectory(), $fileName);
        } catch (FileException $e) {
            // ... handle exception if something happens during file upload
        }
        return $fileName;
    }

    public function delete($fileName) {
        $filesystem = new Filesystem();
        $filesystem->remove($this->getPath($fileName));
    }

    public function getTargetDirectory()
    {
        return $this->targetDirectory;
    }

    public function getPath($fileName) {
        return ($this->getTargetDirectory().'/'.$fileName);
    }
}