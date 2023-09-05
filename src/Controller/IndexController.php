<?php

namespace App\Controller;

use App\Entity\Notes;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class IndexController extends Notes
{
   
    public function __construct()
    {
    }
    
    /**
     * @Route(
     *     name="notes",
     *     path="/api/notes",
     *     methods={"GET","OPTIONS"},
     *     defaults={
     *          "_api_resource_class"=Notes::class,
     *          "_api_collection_operation_name"="get"
     *     }
     * )
     * @param Notes $data
     * @return JsonResponse
     */
    public function __invoke(Notes $data) {
        return new JsonResponse([
            'success' => 'true',
            'data' => $data
        ]);
    }
    
}
