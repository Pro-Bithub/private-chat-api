<?php

namespace App\services;


use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\HttpFoundation\UrlHelper;


use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class FileUploader
{
    private $uploadPath;
    private $slugger;
    private $urlGenerator;
    private $relativeUploadsDir;
 
    public function __construct($publicPath, $uploadPath, SluggerInterface $slugger, UrlGeneratorInterface $urlGenerator)
    {
        $this->uploadPath = $uploadPath;
        $this->slugger = $slugger;
        $this->urlGenerator = $urlGenerator;
 
        // Get uploads directory relative to public path (e.g., "/uploads/")
        $this->relativeUploadsDir = str_replace($publicPath, '', $this->uploadPath) . '/';
    }
    public function upload(UploadedFile $file, $file_name = null)
    {
        $originalFilename ="";
        if($file_name==null)
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        else
        $originalFilename = $file_name;

        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $safeFilename .'.' . $file->guessExtension();
        // $fileName1 = $id . '.' . $file->guessExtension();
    
        try {
            $file->move($this->getUploadPath(), $fileName);
        } catch (FileException $e) {
            // Handle exception if something happens during file upload
            throw new \Exception('File upload failed: ' . $e->getMessage());
        }
    
        return $fileName;
    }
    public function uploadProfile(UploadedFile $file, $id)
    {
        $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $safeFilename = $this->slugger->slug($originalFilename);
        $fileName = $id . '.' . $file->guessExtension();
 
        try {
            $file->move($this->getUploadPath(), $fileName);
        } catch (FileException $e) {
            // Handle exception if something happens during file upload
            throw new \Exception('Profile picture upload failed: ' . $e->getMessage());
        }
 
        return $fileName;
    }
    
    public function getUploadPath()
    {
        return $this->uploadPath;
    }
 
    public function getUrl(?string $fileName, bool $absolute = true)
    {
        if (empty($fileName)) return null;
 
        if ($absolute) {
            return $this->urlGenerator->generate('some_route_name', ['filename' => $fileName], UrlGeneratorInterface::ABSOLUTE_URL);
        }
 
        return $this->urlGenerator->generate('some_route_name', ['filename' => $fileName], UrlGeneratorInterface::RELATIVE_PATH);
    }
}
