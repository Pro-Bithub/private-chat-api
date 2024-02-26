<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;


class GetusersbyaccountController extends AbstractController
{

   /**
    * @var UserRepository
    */
    private $UserRepository;
    protected $parameterBag;
    public function __construct(ParameterBagInterface $parameterBag,UserRepository $UserRepository)
    {
        $this->UserRepository = $UserRepository;
        $this->parameterBag = $parameterBag;
    }

    public function __invoke( EntityManagerInterface $entityManagerInterface, Request $request, $id)
    {
        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }

        $uploads_directory = addTrailingSlashIfMissing($this->parameterBag->get('APP_URL'))."uploads/".$id."/";
        $sql = "SELECT 
          CASE
          WHEN  p.picture is not null
            THEN  concat( '$uploads_directory' , p.picture  )  
              ELSE null
         END as avatar
        , u.id ,  u.email , u.firstname,u.lastname , p.id as p_id , p.nickname FROM user u
        left join user_presentations p on p.user_id = u.id and  p.status =1
        where u.account_id = :account_id and  u.status =1
       
        ";

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('account_id', $id);
        $results = $statement->executeQuery()->fetchAllAssociative();


     
        //$user_account = $this->UserRepository->loaduserByAccount($id);
        //dd($user);
        return $results;
    }
    
}
