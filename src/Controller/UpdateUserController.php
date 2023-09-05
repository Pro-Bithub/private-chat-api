<?php

namespace App\Controller;

use App\Entity\UserPresentations;
use App\Repository\UserPresentationsRepository;
use App\services\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\AsController;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

#[AsController]
class UpdateUserController extends AbstractController
{
    /**
    * @var UserPresentationsRepository
    */
    private $UserPresentationsRepository;
    public function __construct(UserPresentationsRepository $UserPresentationsRepository)
    {
        $this->UserPresentationsRepository = $UserPresentationsRepository;
    }
    public function __invoke(Request $request, FileUploader $fileUploader, EntityManagerInterface $entityManagerInterface, SluggerInterface $slugger): UserPresentations
    {
        
        
        //$userPresentations = new UserPresentations();
        $data = json_decode($request->getContent(), true);
        //dd($request->get('idUser'));
        $userPresentations = $this->UserPresentationsRepository->findOneById($request->get('idUser'));
        //dd($userPresentations);
        
        $uploadedFile = $request->files->get('file');
       // $originalFilename = pathinfo($uploadedFile->getClientOriginalName(), PATHINFO_FILENAME);
       // $safeFilename = $slugger->slug($originalFilename);
        $fileName = $userPresentations->id.'.'.$uploadedFile->guessExtension();

   // dd($fileName);

   try {
   // $uploadedFile->move($this->getuploadPath(), $fileName);
    $userPresentations->picture = $fileUploader->upload($uploadedFile);
    //$data = array("data" => "File is valid, and was successfully uploaded.");
    } catch (FileException $e) {
    // ... handle exception if something happens during file upload
    $data = array("data" => "File is not valid, and was successfully uploaded.");
    }

       
        
        // $entityManagerInterface->persist($userPresentations);
        // $entityManagerInterface->flush();

        return $userPresentations;
    }

    public function getuploadPath()
    {
        $uploadPath = 'C:\Users\IHEB\admin-private-chat-app\src\assets\uploads';
        return $uploadPath;
    }
}
