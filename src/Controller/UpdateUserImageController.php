<?php

namespace App\Controller;

use App\services\FileUploader;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\UrlHelper;
use Symfony\Component\String\Slugger\SluggerInterface;

class UpdateUserImageController extends AbstractController
{
    // #[Route('/update/user/image', name: 'app_update_user_image')]
    // public function index(): Response
    // {
    //     return $this->render('update_user_image/index.html.twig', [
    //         'controller_name' => 'UpdateUserImageController',
    //     ]);
    // }
    // private $uploadPath;
    // private $slugger;
    // private $urlHelper;
    // private $relativeUploadsDir;
 
    // public function __construct($publicPath, $uploadPath, SluggerInterface $slugger, UrlHelper $urlHelper)
    // {
    //     $this->uploadPath = 'C:\Users\IHEB\admin-private-chat-app\src\assets\uploads';
    //     $this->slugger = $slugger;
    //     $this->urlHelper = $urlHelper;
 
    //     // get uploads directory relative to public path //  "/uploads/"
    //     $this->relativeUploadsDir = str_replace($publicPath, '', $this->uploadPath).'/';
    // }
#[Route('/updateFile')]
/**
 * Undocumented function
 *
 * @param Request $request
 * @return JsonResponse
 */
public function updatefile(Request $request, SluggerInterface $slugger, FileUploader $fileUploader){
    $target_dir = "uploads/"; //image upload folder name
    
    $file = $request->files->get('file');
    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
    $safeFilename = $slugger->slug($originalFilename);
    $fileName = $request->get('idUser').'.'.$file->guessExtension();
    $uploadedFile = $request->files->get('file');

    //dd($fileName);

   try {
    $fileUploader->uploadProfile($uploadedFile,$request->get('idUser'));
   // $file->move($this->getuploadPath(), $fileName);
    $data = array("data" => "File is valid, and was successfully uploaded.");
} catch (FileException $e) {
    // ... handle exception if something happens during file upload
    $data = array("data" => "File is not valid");
}


//     $target_file = $target_dir . basename($_FILES["image"]["name"]);
//    dd($_FILES["image"],$target_file,$_FILES["image"]["name"]);

//     //moving multiple images inside folder
//     if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
//         $data = array("data" => "File is valid, and was successfully uploaded.");
//         print json_encode($data);
//     }

     return new JsonResponse([
        'success' => true,
        'data' => $data,
    ]);
}
public function getuploadPath()
    {
        $uploadPath = 'C:\Users\IHEB\admin-private-chat-app\src\assets\uploads';
        return $uploadPath;
    }
}
