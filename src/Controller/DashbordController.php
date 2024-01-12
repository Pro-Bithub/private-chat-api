<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;

use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class DashbordController extends AbstractController
{
    #[Route('/dashbordData')]
    public function getDashbordData(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {


        $authorizationHeader = $request->headers->get('Authorization');

        $startDate = $request->query->get('startDate');
        $endDate = $request->query->get('endDate');
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

        //SalesByCountry
        $RAW_QUERY13 =
            'SELECT c.country, 
        COUNT(c.country) as sales_country,
        SUM(p.tariff) as sales_total,
        p.currency
    FROM `sales` AS s 
    INNER JOIN `contacts` AS c ON c.id = s.contact_id   
    INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE (p.account_id = :id or c.account_id = :id)
    AND s.status = 1 and s.date_creation BETWEEN :startDate AND :endDate
    GROUP BY c.country
    ORDER BY sales_total DESC
    limit 5;
        ';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY13);
        $stmt->bindValue('id', $user->accountId);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);

        $result12 = $stmt->executeQuery()->fetchAllAssociative();


        //getSalesByPlans

        $RAW_QUERY1 =
            'SELECT  p.billing_volume , p.billing_type ,p.name, COUNT(p.id) as sales_plans , SUM(p.tariff) as sales_total , p.currency
       FROM `sales` AS s 
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE MONTH(s.date_creation) = MONTH(CURRENT_DATE)
       AND YEAR(s.date_creation) = YEAR(CURRENT_DATE) AND p.account_id = :id and s.status = 1
       GROUP BY p.id, p.currency
       ORDER BY sales_plans DESC
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY1);
        $stmt->bindValue('id', $user->accountId);
        $result = $stmt->executeQuery()->fetchAllAssociative();




        //    $RAW_QUERY2 = 
        //    'SELECT c.country ,p.currency ,COUNT(*) AS sales_total, ROUND( COUNT(*) / t.total * 100,1) AS percentage_difference
        //     FROM sales AS s
        //     INNER JOIN contacts AS c ON c.id = s.contact_id
        //     INNER JOIN plans AS p ON p.id = s.plan_id
        //     INNER JOIN (
        //         SELECT COUNT(*) AS total
        //         FROM sales
        //         WHERE MONTH(date_creation) = MONTH(CURRENT_DATE)
        //         AND YEAR(date_creation) = YEAR(CURRENT_DATE)
        //     ) AS t
        //     WHERE (p.account_id = :id OR c.account_id = :id)
        //     AND s.status = 1
        //     AND MONTH(s.date_creation) = MONTH(CURRENT_DATE)
        //     AND YEAR(s.date_creation) = YEAR(CURRENT_DATE)
        //     GROUP BY c.country, p.currency, t.total
        //     ORDER BY sales_total DESC;
        //     limit 5;
        //    ';
        //    $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY2);
        //    $stmt->bindValue('id', $user->accountId);
        //    $result1 = $stmt->executeQuery()->fetchAllAssociative();


        //getTotalSalesAmountByTransactions

        $RAW_QUERY3 =
            'SELECT COUNT(*) as sales_total,
       ROUND( (COUNT(*) / (
          SELECT COUNT(*)
          FROM sales AS prev_s
          INNER JOIN plans AS prev_p ON prev_p.id = prev_s.plan_id
          WHERE prev_p.account_id = :id
          AND prev_s.status = 1
          AND MONTH(prev_s.date_creation) = MONTH(NOW() - INTERVAL 1 MONTH)
          AND YEAR(prev_s.date_creation) = YEAR(NOW() - INTERVAL 1 MONTH)
        )) * 100,1) AS percentage_sales
      FROM sales AS s
      INNER JOIN plans AS p ON p.id = s.plan_id
      WHERE p.account_id = :id
      AND s.status = 1
      AND MONTH(s.date_creation) = MONTH(NOW())
      AND YEAR(s.date_creation) = YEAR(NOW())
        ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $user->accountId);
        $result2 = $stmt->executeQuery()->fetchAllAssociative();




        //getTotalSalesAmountByPlans
        $RAW_QUERY4 =
            'SELECT  p.name, (SUM(
    p.tariff)) as sales_total , p.currency
     
        FROM `sales` AS s 
        INNER JOIN `plans` AS p ON p.id = s.plan_id
        WHERE p.account_id = :id and s.status = 1
        and s.date_creation BETWEEN :startDate AND :endDate
            ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY4);
        $stmt->bindValue('id', $user->accountId);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $result3 = $stmt->executeQuery()->fetchAllAssociative();

        //Conversion Rate

        $RAW_QUERY5 =
            'SELECT count(DISTINCT c.id) as count_contact,
            ROUND( (COUNT(DISTINCT c.id) / (SELECT COUNT(DISTINCT contacts.id) FROM contacts   LEFT JOIN contact_custom_fields cd ON cd.contact_id = contacts.id where  contacts.account_id= :id  and cd.created_at IS NOT NULL and contacts.date_start BETWEEN :startDate AND :endDate )) * 100,1) AS percentage_contacts_with_sales
           FROM sales AS s
           INNER JOIN contacts AS c ON c.id = s.contact_id
           INNER JOIN plans AS p ON p.id = s.plan_id
          
           WHERE p.account_id = :id   
           AND s.status = 1
           and c.date_start BETWEEN :startDate AND :endDate
            ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY5);
        $stmt->bindValue('id', $user->accountId);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);
        $result4 = $stmt->executeQuery()->fetchAllAssociative();


        //PercentageSalesCountry
        $RAW_QUERY6 =
            'SELECT c.country, COUNT(c.country) as sales_country ,SUM(p.tariff) as sales_total , p.currency,
       ROUND(
        ((
            SUM(CASE
                    WHEN MONTH(s.date_creation) = MONTH(NOW())
                        THEN p.tariff
                    ELSE 0
                END) - 
            SUM(
                CASE
                    WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                        THEN p.tariff
                    ELSE 0
                END
            )
        ) / SUM(
            CASE
                WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                    THEN p.tariff
                ELSE 0
            END
        )) * 100,
        1
    ) AS percentage_difference
       FROM `sales` AS s 
       INNER JOIN `contacts` AS c ON c.id = s.contact_id   
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE  p.account_id = :id and s.status = 1
       GROUP BY c.country, p.currency
       ORDER BY sales_country DESC
       limit 5
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY6);
        $stmt->bindValue('id', $user->accountId);
        $result5 = $stmt->executeQuery()->fetchAllAssociative();


        //getPercentageNewContact
        $RAW_QUERY7 =
            'SELECT
      COUNT( DISTINCT  c.id) AS percentageNewContacts
       FROM
       `contacts` AS c
       LEFT JOIN contact_custom_fields cd ON cd.contact_id = c.id
        WHERE  c.account_id = :id and c.status = 1 and cd.created_at IS NOT NULL
        and c.date_start BETWEEN :startDate AND :endDate
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY7);
        $stmt->bindValue('id', $user->accountId);
        $stmt->bindValue('startDate', $startDate);
        $stmt->bindValue('endDate', $endDate);

        $result6 = $stmt->executeQuery()->fetchAllAssociative();


        //BestSellerContact
        $RAW_QUERY8 =
            'SELECT c.name AS best_selling_contact, COUNT(s.contact_id) AS total_sales, s.date_creation, SUM(p.tariff) as sales_total,p.currency, c.id as contact_id,CURRENT_DATE() as CURRENTDATE,
       ROUND((COUNT(s.contact_id) / (SELECT COUNT(*) FROM sales INNER JOIN `contacts` AS c ON c.id = s.contact_id
       INNER JOIN `plans` AS p ON p.id = s.plan_id WHERE c.account_id = :id AND s.status = 1 and MONTH(date_creation) = MONTH(CURRENT_DATE()) AND YEAR(date_creation) = YEAR(CURRENT_DATE()))) * 100) AS sales_percentage
       FROM sales AS s
       INNER JOIN `contacts` AS c ON c.id = s.contact_id
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE  (c.account_id = :id AND s.status = 1) and MONTH(s.date_creation) = MONTH(CURRENT_DATE()) AND YEAR(s.date_creation) = YEAR(CURRENT_DATE())
       GROUP BY s.contact_id
       ORDER BY sales_total DESC 
       LIMIT 1
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY8);
        $stmt->bindValue('id', $user->accountId);
        $result7 = $stmt->executeQuery()->fetchAllAssociative();


        //countryContact
        $RAW_QUERY9 =
            'SELECT 
       c.country, 
       COUNT(c.country) AS sales_country,
       ROUND((COUNT(c.country) / (SELECT COUNT(*) FROM contacts WHERE MONTH(date_start) = MONTH(CURRENT_DATE()) AND YEAR(date_start) = YEAR(CURRENT_DATE()) AND account_id = :id AND country IS NOT NULL AND status = 1)) * 100, 2) AS contact_percentage
        FROM 
        contacts AS c 
        WHERE 
        account_id = :id AND country IS NOT NULL AND status = 1 AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
        GROUP BY 
        c.country
        ORDER BY 
        sales_country DESC
        LIMIT 7;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY9);
        $stmt->bindValue('id', $user->accountId);
        $result8 = $stmt->executeQuery()->fetchAllAssociative();



        //contactBrowser
        $RAW_QUERY10 =
            "SELECT SUBSTRING_INDEX(p.browser_data, ';', 1) as browser, COUNT(SUBSTRING_INDEX(p.browser_data, ';', 1)) AS num_contacts,
        ROUND((COUNT(*) / (SELECT COUNT(*) FROM profiles pr INNER JOIN contacts co on pr.u_id = co.id WHERE MONTH(co.date_start) = MONTH(CURRENT_DATE()) AND YEAR(co.date_start) = YEAR(CURRENT_DATE()) AND (pr.account_id = :id or co.account_id = :id)  AND co.status = 1)) * 100) AS contact_percentage
              
               FROM profiles p
               
               INNER JOIN contacts c ON p.u_id = c.id
               WHERE c.account_id = :id 
               AND c.status = 1 
               AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) 
               AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
               GROUP BY browser
               ORDER by contact_percentage DESC
         
        ;";
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY10);
        $stmt->bindValue('id', $user->accountId);
        $result9 = $stmt->executeQuery()->fetchAllAssociative();


        //contactOS
        $RAW_QUERY11 =
            "SELECT SUBSTRING_INDEX(p.browser_data, ';', -1) as os, COUNT(SUBSTRING_INDEX(p.browser_data, ';', -1)) AS num_contacts,
        ROUND((COUNT(*) / (SELECT COUNT(*) FROM profiles pr INNER JOIN contacts co on pr.u_id = co.id WHERE MONTH(co.date_start) = MONTH(CURRENT_DATE()) AND YEAR(co.date_start) = YEAR(CURRENT_DATE()) AND (pr.account_id = :id or co.account_id = :id)  AND co.status = 1)) * 100) AS contact_percentage
             
              FROM profiles p
              
              INNER JOIN contacts c ON p.u_id = c.id
              WHERE c.account_id = :id 
              AND c.status = 1 
              AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) 
              AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
              GROUP BY os
              ORDER by contact_percentage DESC
       ;";
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY11);
        $stmt->bindValue('id', $user->accountId);
        $result10 = $stmt->executeQuery()->fetchAllAssociative();


        //SalesChart
        $RAW_QUERY12 =
            'SELECT CAST(S.date_creation AS date) AS DateOnly, COUNT(*) as total_sales
       FROM `sales` AS S
       INNER JOIN `contacts` AS c ON c.id = S.contact_id
       INNER JOIN `plans` AS p ON p.id = S.plan_id
       WHERE (p.account_id = :id or c.account_id = :id)
         AND S.status = 1
         AND MONTH(S.date_creation) = MONTH(CURRENT_DATE)
         AND YEAR(S.date_creation) = YEAR(CURRENT_DATE)
       GROUP BY CAST(S.date_creation AS date)       
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY12);
        $stmt->bindValue('id', $user->accountId);
        $result11 = $stmt->executeQuery()->fetchAllAssociative();



        return new JsonResponse([
            'success' => 'true',
            'SalesByCountry' => $result12,
            'SalesByPlans' => $result,
            'TotalSalesAmountByPlans' => $result3,
            'TotalSalesAmountByTransactions' => $result2,
            'ConversionRate' => $result4,
            'PercentageSalesCountry' => $result5,
            'PercentageNewContact' => $result6,
            'BestSellerContact' => $result7,
            'countryContact' => $result8,
            'contactBrowser' => $result9,
            'contactOS' => $result10,
            'SalesChart' => $result11,


        ]);
    }


    // #[Route('/getSalesByCountry/{id}')]
    // public function getSalesByCountry(EntityManagerInterface $entityManagerInterface, $id): Response
    // {

    // //    return new JsonResponse([
    // //        'success' => 'true',
    // //        'data' => $result
    // //    ]);
    // }

    #[Route('/getSalesByPlans/{id}')]
    public function getSalesByPlans(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT  p.billing_volume , p.billing_type ,p.name, COUNT(p.id) as sales_plans , SUM(p.tariff) as sales_total , p.currency
       FROM `sales` AS s 
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE MONTH(s.date_creation) = MONTH(CURRENT_DATE)
       AND YEAR(s.date_creation) = YEAR(CURRENT_DATE) AND p.account_id = :id and s.status = 1
       GROUP BY p.id, p.currency
       ORDER BY sales_plans DESC
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }

    #[Route('/getTotalSalesAmountByPlans/{id}')]
    public function getTotalSalesAmountByPlans(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT  p.name, (SUM(
        CASE
            WHEN MONTH(s.date_creation) = MONTH(NOW())
                THEN p.tariff
            ELSE 0
        END
            ) ) as sales_total , p.currency,
        ROUND(
        ((
            SUM(CASE
                    WHEN MONTH(s.date_creation) = MONTH(NOW())
                        THEN p.tariff
                    ELSE 0
                END) - 
            SUM(
                CASE
                    WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                        THEN p.tariff
                    ELSE 0
                END
            )
        ) / SUM(
            CASE
                WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                    THEN p.tariff
                ELSE 0
            END
        )) * 100,
        1
        ) AS percentage_difference
        FROM `sales` AS s 
        INNER JOIN `plans` AS p ON p.id = s.plan_id
        WHERE p.account_id = :id and s.status = 1
            ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }

    #[Route('/getTotalSalesAmountByTransactions/{id}')]
    public function getTotalSalesAmountByTransactions(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT  p.name, (SUM(
        CASE
            WHEN MONTH(s.date_creation) = MONTH(NOW())
                THEN p.tariff
            ELSE 0
        END
            ) ) as sales_total , p.currency,
        ROUND(
        ((
            SUM(CASE
                    WHEN MONTH(s.date_creation) = MONTH(NOW())
                        THEN p.tariff
                    ELSE 0
                END) - 
            SUM(
                CASE
                    WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                        THEN p.tariff
                    ELSE 0
                END
            )
        ) / SUM(
            CASE
                WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                    THEN p.tariff
                ELSE 0
            END
        )) * 100,
        1
        ) AS percentage_difference
        FROM `sales` AS s 
        INNER JOIN `plans` AS p ON p.id = s.plan_id
        WHERE p.account_id = :id 
        ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }

    #[Route('/getPercentageSalesTransactions/{id}')]
    public function getPercentageSalesTransactions(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT p.name,SUM(p.tariff) as sales_total , p.currency,
       ROUND(
        ((
            SUM(CASE
                    WHEN MONTH(s.date_creation) = MONTH(NOW())
                        THEN p.tariff
                    ELSE 0
                END) - 
            SUM(
                CASE
                    WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                        THEN p.tariff
                    ELSE 0
                END
            )
        ) / SUM(
            CASE
                WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                    THEN p.tariff
                ELSE 0
            END
        )) * 100,
        1
        ) AS percentage_difference
       FROM `sales` AS s 
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE p.account_id = :id 
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }


    #[Route('/getPercentageSalesCountry/{id}')]
    public function getPercentageSalesCountry(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT c.country, COUNT(c.country) as sales_country ,SUM(p.tariff) as sales_total , p.currency,
       ROUND(
        ((
            SUM(CASE
                    WHEN MONTH(s.date_creation) = MONTH(NOW())
                        THEN p.tariff
                    ELSE 0
                END) - 
            SUM(
                CASE
                    WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                        THEN p.tariff
                    ELSE 0
                END
            )
        ) / SUM(
            CASE
                WHEN MONTH(s.date_creation) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH))
                    THEN p.tariff
                ELSE 0
            END
        )) * 100,
        1
     ) AS percentage_difference
       FROM `sales` AS s 
       INNER JOIN `contacts` AS c ON c.id = s.contact_id   
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE  p.account_id = :id and s.status = 1
       GROUP BY c.country, p.currency
       ORDER BY sales_country DESC
       limit 5
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }




    #[Route('/getPercentageNewContact/{id}')]
    public function getPercentageNewContact(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT
      ROUND( (COUNT(CASE WHEN MONTH(c.date_start) = MONTH(NOW()) AND YEAR(c.date_start) = YEAR(NOW()) THEN 1 END) / CAST(COUNT(*) AS FLOAT)) * 100) AS percentageNewContacts,
       ROUND(
       (((COUNT(CASE WHEN MONTH(c.date_start) = MONTH(NOW()) AND YEAR(c.date_start) = YEAR(NOW()) THEN 1 END) / CAST(COUNT(*) AS FLOAT)) * 100) - (COUNT(CASE WHEN MONTH(c.date_start) = MONTH(DATE_SUB(NOW(), INTERVAL 1 MONTH)) AND YEAR(c.date_start) = YEAR(DATE_SUB(NOW(), INTERVAL 1 MONTH)) THEN 1 END) / CAST(COUNT(*) AS FLOAT)) * 100),1)
       AS percentageCompareContacts
       FROM
       `contacts` AS c
        WHERE  c.account_id = :id and c.status = 1
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }


    #[Route('/getBestSellerContact/{id}')]
    public function getBestSellerContact(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT c.name AS best_selling_contact, COUNT(s.contact_id) AS total_sales, s.date_creation, SUM(p.tariff) as sales_total,p.currency, c.id as contact_id,CURRENT_DATE() as CURRENTDATE,
       ROUND((COUNT(s.contact_id) / (SELECT COUNT(*) FROM sales INNER JOIN `contacts` AS c ON c.id = s.contact_id
       INNER JOIN `plans` AS p ON p.id = s.plan_id WHERE c.account_id = :id AND s.status = 1 and MONTH(date_creation) = MONTH(CURRENT_DATE()) AND YEAR(date_creation) = YEAR(CURRENT_DATE()))) * 100) AS sales_percentage
       FROM sales AS s
       INNER JOIN `contacts` AS c ON c.id = s.contact_id
       INNER JOIN `plans` AS p ON p.id = s.plan_id
       WHERE  (c.account_id = :id AND s.status = 1) and MONTH(s.date_creation) = MONTH(CURRENT_DATE()) AND YEAR(s.date_creation) = YEAR(CURRENT_DATE())
       GROUP BY s.contact_id
       ORDER BY sales_total DESC 
       LIMIT 1
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }


    #[Route('/getcountryContact/{id}')]
    public function getcountryContact(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT 
       c.country, 
       COUNT(c.country) AS sales_country,
       ROUND((COUNT(c.country) / (SELECT COUNT(*) FROM contacts WHERE MONTH(date_start) = MONTH(CURRENT_DATE()) AND YEAR(date_start) = YEAR(CURRENT_DATE()) AND account_id = :id AND country IS NOT NULL AND status = 1)) * 100, 2) AS contact_percentage
        FROM 
        contacts AS c 
        WHERE 
        account_id = :id AND country IS NOT NULL AND status = 1 AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
        GROUP BY 
        c.country
        ORDER BY 
        sales_country DESC
        LIMIT 7;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }

    #[Route('/getcontactBrowser/{id}')]
    public function getcontactBrowser(EntityManagerInterface $entityManagerInterface, $id): Response
    {

        $RAW_QUERY00 =
            "SELECT COUNT(*) AS num_contacts_with_chrome,
       ROUND((COUNT(*) / (SELECT COUNT(*) FROM contacts INNER JOIN profiles as pr on pr.u_id = contacts.id WHERE MONTH(date_start) = MONTH(CURRENT_DATE()) AND YEAR(date_start) = YEAR(CURRENT_DATE()) AND (pr.account_id = :id or contacts.account_id = :id)  AND status = 1)) * 100) AS contact_percentage
       FROM contacts AS c 
       
       INNER JOIN profiles AS p ON p.u_id = c.id
       WHERE c.account_id = :id 
       AND c.status = 1 
       AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) 
       AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
       AND p.browser_data LIKE '%chrome%'
        ;";
        $RAW_QUERY3 =
            "SELECT SUBSTRING_INDEX(p.browser_data, ';', 1) as browser, COUNT(SUBSTRING_INDEX(p.browser_data, ';', 1)) AS num_contacts,
        ROUND((COUNT(*) / (SELECT COUNT(*) FROM profiles pr INNER JOIN contacts co on pr.u_id = co.id WHERE MONTH(co.date_start) = MONTH(CURRENT_DATE()) AND YEAR(co.date_start) = YEAR(CURRENT_DATE()) AND (pr.account_id = :id or co.account_id = :id)  AND co.status = 1)) * 100) AS contact_percentage
              
               FROM profiles p
               
               INNER JOIN contacts c ON p.u_id = c.id
               WHERE c.account_id = :id 
               AND c.status = 1 
               AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) 
               AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
               GROUP BY browser
               ORDER by contact_percentage DESC
         
        ;";
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }

    #[Route('/getcontactOS/{id}')]
    public function getcontactOS(EntityManagerInterface $entityManagerInterface, $id): Response
    {


        $RAW_QUERY3 =
            "SELECT SUBSTRING_INDEX(p.browser_data, ';', -1) as os, COUNT(SUBSTRING_INDEX(p.browser_data, ';', -1)) AS num_contacts,
         ROUND((COUNT(*) / (SELECT COUNT(*) FROM profiles pr INNER JOIN contacts co on pr.u_id = co.id WHERE MONTH(co.date_start) = MONTH(CURRENT_DATE()) AND YEAR(co.date_start) = YEAR(CURRENT_DATE()) AND (pr.account_id = :id or co.account_id = :id)  AND co.status = 1)) * 100) AS contact_percentage
              
               FROM profiles p
               
               INNER JOIN contacts c ON p.u_id = c.id
               WHERE c.account_id = :id 
               AND c.status = 1 
               AND MONTH(c.date_start) = MONTH(CURRENT_DATE()) 
               AND YEAR(c.date_start) = YEAR(CURRENT_DATE())
               GROUP BY os
               ORDER by contact_percentage DESC
        ;";
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }

    #[Route('/getSalesChart/{id}')]
    public function getSalesChart(EntityManagerInterface $entityManagerInterface, $id): Response
    {
        $RAW_QUERY3 =
            'SELECT CAST(S.date_creation AS date) AS DateOnly, COUNT(*) as total_sales
       FROM `sales` AS S
       INNER JOIN `contacts` AS c ON c.id = S.contact_id
       INNER JOIN `plans` AS p ON p.id = S.plan_id
       WHERE (p.account_id = :id or c.account_id = :id)
         AND S.status = 1
         AND MONTH(S.date_creation) = MONTH(CURRENT_DATE)
         AND YEAR(S.date_creation) = YEAR(CURRENT_DATE)
       GROUP BY CAST(S.date_creation AS date)       
       ;';
        $stmt = $entityManagerInterface->getConnection()->prepare($RAW_QUERY3);
        $stmt->bindValue('id', $id);
        $result = $stmt->executeQuery()->fetchAllAssociative();
        return new JsonResponse([
            'success' => 'true',
            'data' => $result
        ]);
    }
    // #[Route('/getcontactBrow')]
    // public function getcontactBrows(Request $request): Response
    // {
    //     $request = Request::createFromGlobals();
    //     $userAgent = $request->headers->get('User-Agent');

    //     // Use a library like BrowserDetect to parse the user agent string
    //     $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
    //     $os = new Os();



    //     dd($os->getName());
    //     $browserName = $browser->getName();
    //    // dd($_SERVER['HTTP_USER_AGENT']);
    //     echo "Browser name: " . $browserName;
    //     dd($browserName);
    //     // Use the UserAgent component to parse the user agent string
    //     $parser = HttpClient::create()->getUserAgent($userAgent);
    //     $browserData = $parser->toArray();

    //     // Use the parsed browser data for further processing
    //     // For example, you can access the browser name and version like this:
    //     $browserName = $browserData['browser_name'];
    //     $browserVersion = $browserData['browser_version'];

    //    return new JsonResponse([
    //        'success' => 'true',
    //        'data' => $browserName
    //    ]);
    // }


}
