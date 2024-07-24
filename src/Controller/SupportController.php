<?php

namespace App\Controller;

use App\Entity\ContactLogs;
use App\Entity\Supportickets;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpClient\HttpClient;
use Sinergi\BrowserDetector\Browser;
use Sinergi\BrowserDetector\Os;
use Sinergi\BrowserDetector\UserAgent;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupportController extends AbstractController
{
    protected $parameterBag;

    public function __construct(ParameterBagInterface $parameterBag)
    {
        $this->parameterBag = $parameterBag;
    }


    #[Route('/support/new/ticket', name: 'supportnewticket')]
    public function supportnewticket(Request $request, EntityManagerInterface $entityManagerInterface): Response
    {



        $profile_id = $request->get('profile_id');
        $subsql = "";
        if (isset($profile_id) && $profile_id != null) {
            $subsql = " and p.id =" . $profile_id;
        } else {
            $subsql = " and c.email like '" . $request->get('mail') . "'";
        }

        $sql = "SELECT p.id , c.source_type 
    FROM `contacts` AS c
    LEFT JOIN `profiles` AS p ON p.u_id = c.id and p.u_type =2
    WHERE c.account_id = :accountId " . $subsql;

        $statement = $entityManagerInterface->getConnection()->prepare($sql);
        $statement->bindValue('accountId', $request->get('account_id'));
        $contact = $statement->executeQuery()->fetchAssociative();
        $id = null;
        $type = null;

        if ($contact) {
            $id = $contact['id'];
            $type = $contact['source_type'];
        }

        $supporticket = new Supportickets();
        $supporticket->first_name = $request->get('firstname');
        $supporticket->last_name = $request->get('lastname');
        $supporticket->email = $request->get('mail');
        $supporticket->subject = $request->get('object');
        if ($supporticket->subject == 3)
            $supporticket->details = $request->get('details');
        $supporticket->created_at = new \DateTimeImmutable();

        $supporticket->ip_address = $request->get('ip_address');
        $supporticket->source = $request->get('source');
        $supporticket->profile_type = $type;
        $supporticket->profile_id = $id;
        $supporticket->status = 1;
        $supporticket->customer_account_id = $request->get('account_id');


        $userAgent = $request->headers->get('User-Agent');

        // Use a library like BrowserDetect to parse the user agent string
        $browser = new \Sinergi\BrowserDetector\Browser($userAgent);
        $os = new Os();



        //dd($os->getName());
        $browserName = $browser->getName();
        $supporticket->browser = $browserName . ';' . $os->getName();


        $entityManagerInterface->persist($supporticket);
        $entityManagerInterface->flush();



        $time =  new \DateTimeImmutable();
        $ContactLogs = new ContactLogs();
        $ContactLogs->profile_id = $profile_id;
        $ContactLogs->action = 7;
        $ContactLogs->element = 'btn-contact-form';
        $ContactLogs->log_date = $time;
        $ContactLogs->browsing_data =    $browserName . ';' . $os->getName();
        $entityManagerInterface->persist($ContactLogs);
        $entityManagerInterface->flush();


        $client = HttpClient::create();
        $data = [
            "event" => "new_ticket",
            "ticket" => $supporticket,
            "account_id" => $request->get('account_id'),
            "user" => ["id" => $id, "type" => $type, "source" => $request->get('source'), "ip" => $request->get('ip_address')],
        ];



        function addTrailingSlashIfMissing($str)
        {
            if (!in_array(substr($str, -1), ['/', '\\'])) {
                $str .= '/';
            }
            return $str;
        }


        $ws_library = addTrailingSlashIfMissing($this->parameterBag->get('ws_library'));
        $url = $ws_library . 'users/new_ticket';



     


        $status = null;
        $timeout = 10;
        $istimeout = false;
        try {
            $response = $client->request('POST', $url, [
                'json' => $data,
                'timeout' => $timeout,
            ]);
            $status = $response->getStatusCode();
        } catch (\Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface $e) {
            $istimeout = true;
        }







        //SupporticketsRepository

        return new JsonResponse([
            'success' => true,
            "ddd" => $supporticket,
            "status" => $status,
            "istimeout" => $istimeout

        ]);
    }
}
