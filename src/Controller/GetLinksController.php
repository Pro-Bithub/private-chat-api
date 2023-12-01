<?php

namespace App\Controller;

use App\Repository\ClickableLinksRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;
class GetLinksController extends AbstractController
{
    private $ClickableLinksRepository;

    public function __construct(ClickableLinksRepository $ClickableLinksRepository)
    {
        $this->ClickableLinksRepository = $ClickableLinksRepository;
    }
    #[Route('/get_link', name: 'app_get_link_controller')]
    public function __invoke(Request $request, EntityManagerInterface $entityManagerInterface): JsonResponse
    {

        $authorizationHeader = $request->headers->get('Authorization');

        // Check if the token is present and in the expected format (Bearer TOKEN)
        if (!$authorizationHeader || strpos($authorizationHeader, 'Bearer ') !== 0) {
            throw new AccessDeniedException('Invalid or missing authorization token.');
        }

        // Extract the token value (without the "Bearer " prefix)
        $token = substr($authorizationHeader, 7);

        $tokenData = $this->get('security.token_storage')->getToken();

        if ($tokenData === null) {
            throw new AccessDeniedException('Invalid token.');
        }
    
        // Now you can access the user data from the token (assuming your User class has a `getUsername()` method)
        $user = $tokenData->getUser();
       
        $draw = (int) $request->get('draw', 1);
        $start = (int) $request->get('start', 0);
        $length = (int) $request->get('length', 5);
        $search = $request->get('columns')[1]['search']['value'] ?? null;


        $columns = $request->get('columns');

        $order = $request->get('order');

        $sort = [];
        foreach ($order as  $orders){
            if(isset($columns[$orders['column']]['name'])){
                $sort[] = $columns[$orders['column']]['name']. ' ' .$orders['dir'];
            } 
        }

        $filters = [];
        $filterValues= [];
        if($request->get('search')['value'] &&  trim($request->get('search')['value']) != '') {
        $filters[] = "(e.id LIKE :searchTerm OR e.name LIKE :searchTerm OR e.url LIKE :searchTerm )";
        $filterValues['searchTerm'] = '%'.trim($request->get('search')['value']).'%';
        }
        
        if($request->get('columns')) {
            foreach($request->get('columns') as $column){
                if(isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    $filters[] = "(e.".$column['name']." LIKE :".$column['name'].")";
        $filterValues[$column['name']] = $column['search']['value'];
                }
            }
        
            }
            		 
if(empty($filters)){
    $filters[] = ' 1=1';
}
        
            $sql1 = "SELECT e.*, GROUP_CONCAT(r.user_id SEPARATOR ',') AS user_ids
            FROM clickable_links e
            left JOIN clickable_links_users r ON r.link_id = e.id and r.status = 1
                 ". (!empty($filters) ? 'where e.account_id = :account_id and' : ''). implode(' AND',$filters)."
                GROUP BY e.id
                ". (!empty($sort) ? 'order BY ' : ''). implode(' ,',$sort)."
                LIMIT :limit OFFSET :offset
                ;";
        
                //dd($sql1,$filters);
                $sql2 = "SELECT e.*, GROUP_CONCAT(r.user_id SEPARATOR ',') AS user_ids
                FROM clickable_links e
                left JOIN clickable_links_users r ON r.link_id = e.id and r.status = 1
                ". (!empty($filters) ? 'where e.account_id = :account_id and' : ''). implode(' AND',$filters)."
                GROUP BY e.id
                ". (!empty($sort) ? 'order BY ' : ''). implode(' ,',$sort)."
                ;";
        
                $sql3= "SELECT e.*, GROUP_CONCAT(r.user_id SEPARATOR ',') AS user_ids
                FROM clickable_links e
                left JOIN clickable_links_users r ON r.link_id = e.id and r.status = 1
                where e.account_id = :account_id 
                    GROUP BY e.id
                  ";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('account_id',$user->accountId);

        $results3 = $statement3->executeQuery()->rowCount();
        
        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
        foreach($filterValues AS $key => $value) {
        $statement->bindValue($key, $value);
        $statement1->bindValue($key, $value);
        }
        // $statement->bindValue('searchTerm', '%' . $search . '%');
        $statement->bindValue('limit', $length, \PDO::PARAM_INT);
        $statement->bindValue('offset', $start, \PDO::PARAM_INT);
        $statement->bindValue('account_id',$user->accountId);
        $statement1->bindValue('account_id',$user->accountId);
        
        $results = $statement->executeQuery()->fetchAllAssociative();
        $results1 = $statement1->executeQuery()->rowCount();
               // $data1 = $this->PredefindTextsRepository->findDataBySearch($search);
               // dd($results);
        
                return new JsonResponse([
                    'draw' => $draw,
                    'recordsTotal' => $results3,
                    'recordsFiltered' => $results1,
                    'data' => $results,
                ]);
    }
}
