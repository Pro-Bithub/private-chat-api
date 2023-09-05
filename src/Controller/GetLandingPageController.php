<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class GetLandingPageController extends AbstractController
{
    #[Route('/get_landing_page', name: 'app_get_landing_page')]
    public function index(Request $request, EntityManagerInterface $entityManagerInterface): Response
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



        $filters = [];
        $filterValues = [];
        if ($request->get('search')['value'] && trim($request->get('search')['value']) != '') {
            $filters[] = "(e.id LIKE :searchTerm OR e.name LIKE :searchTerm OR e.comment LIKE :searchTerm)";
            $filterValues['searchTerm'] = '%' . trim($request->get('search')['value']) . '%';
        }

        if ($request->get('columns')) {
            foreach ($request->get('columns') as $column) {
                if (isset($column['search']['value']) && trim($column['search']['value']) != '') {
                    $filters[] = "(e." . $column['name'] . " LIKE :" . $column['name'] . ")";
                    $filterValues[$column['name']] = $column['search']['value'];
                }
            }
        }

        $sql1 = "SELECT e.*, GROUP_CONCAT(c.field_name SEPARATOR ',') AS field_name
        FROM landing_pages e
        LEFT JOIN landing_page_fields r ON r.page_id = e.id and r.status = 1
        LEFT JOIN custom_fields c ON c.id = r.field_id
         " . (!empty($filters) ? 'where e.account_id = :account_id and' : '') . implode(' AND', $filters) . "
        GROUP BY e.id
        ORDER BY e.id DESC
        LIMIT :limit OFFSET :offset
        ;";

        //dd($sql1,$filters);
        $sql2 = "SELECT e.*, GROUP_CONCAT(c.field_name SEPARATOR ',') AS field_name
        FROM landing_pages e
        LEFT JOIN landing_page_fields r ON r.page_id = e.id and r.status = 1
        LEFT JOIN custom_fields c ON c.id = r.field_id
        " . (!empty($filters) ? 'where e.account_id = :account_id and' : '') . implode(' AND', $filters) . "
        GROUP BY e.id
        ORDER BY e.id DESC
        ;";

        $sql3 = "SELECT * FROM landing_pages p0_ LEFT JOIN landing_page_fields p1_ ON p0_.id = p1_.page_id and  p0_.account_id = :account_id  and p1_.status = 1 GROUP BY p0_.id";
        $statement3 = $entityManagerInterface->getConnection()->prepare($sql3);
        $statement3->bindValue('account_id', $user->accountId);

        $results3 = $statement3->executeQuery()->rowCount();

        $statement = $entityManagerInterface->getConnection()->prepare($sql1);
        $statement1 = $entityManagerInterface->getConnection()->prepare($sql2);
        foreach ($filterValues as $key => $value) {
            $statement->bindValue($key, $value);
            $statement1->bindValue($key, $value);
        }
        // $statement->bindValue('searchTerm', '%' . $search . '%');
        $statement->bindValue('limit', $length, \PDO::PARAM_INT);
        $statement->bindValue('offset', $start, \PDO::PARAM_INT);
        $statement->bindValue('account_id', $user->accountId);
        $statement1->bindValue('account_id', $user->accountId);

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
