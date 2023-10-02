<?php
//
//namespace App\Controller;
//
//use App\Entity\City;
//use App\Repository\DashboardRepository;
//use App\Repository\InterestRatesRepository;
//use Doctrine\ORM\EntityManagerInterface;
//use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
//use Symfony\Component\HttpFoundation\Request;
//use Symfony\Component\HttpFoundation\Response;
//use Symfony\Component\Routing\Annotation\Route;
//
//class DashboardController extends AbstractController
//{
//    #[Route('/dashboard', name: 'app_dashboard')]
//    public function index(EntityManagerInterface $em, InterestRatesRepository $is): Response
//    {
//        $cityRepository = $em->getRepository(City::class);
//        $housesInCity = $cityRepository->findAll();
//
//        $avg = $is->getAverageRatesPerYear();
//
//       $dash = new DashboardRepository();
//       $avg = $dash->getHousesPerCity($housesInCity, $avg);
//       $avg = $dash->calculateAvr($avg);
//       dd($avg);
//
//        return new Response();
//        return $this->render('dashboard/index.html.twig', [
//            'controller_name' => 'DashboardController',
//        ]);
//    }
//
//}


namespace App\Controller;

use App\Entity\City;
use Symfony\Component\Serializer\Encoder\XmlEncoder;
use App\Entity\HouseSize;
use App\Repository\DashboardRepository;
use App\Repository\InterestRatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use SimpleXMLElement;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


/**
 * @Route("/api", name="api_")
 */
class DashboardController extends AbstractController
{
    /**
     * @Route("/dashboard/table", name="dashboard")
     */
    public function getTableData(EntityManagerInterface $em, InterestRatesRepository $is): JsonResponse
    {
        $cityRepository = $em->getRepository(City::class);
        $housesInCity = $cityRepository->findAll();

        $avg = $is->getAverageRatesPerYear($em);

       $dash = new DashboardRepository();
       $avg = $dash->getHousesPerCity($housesInCity, $avg);

       $avg = $dash->calculateAvr($avg);

        return new JsonResponse($avg);
    }

    /**
     * @Route("/dashboard/customTable-export", name="dashboardCustomExport")
     */
    public function exportTableData(EntityManagerInterface $em,Request $request, InterestRatesRepository $is) {
        $avg = $is->getAverageRatesPerYear($em);
        $start = $request->query->get('startDate');
        $end = $request->query->get('endDate');
        $type = $request->query->get('type');

        $dash = new DashboardRepository();

        $dataToReturn = $dash->getCustomData($type,$start, $end, $em, $avg);
        if (empty($dataToReturn)){
            return new JsonResponse(['status' => 'failed'], 400);
        } else{
            $jsonData = json_encode($dataToReturn);
            return new JsonResponse($jsonData, 200,['Content-Type' => 'application/json']);
        }

    }
    public function soapAction(): Response
    {
        // Tworzenie instancji klasy SoapResponse
        $soapResponse = new SoapResponse($yourData, SOAP_1_2, null, ['encoding' => 'UTF-8']);

        // Ustawianie nagłówków dla odpowiedzi SOAP
        $responseHeaders = $soapResponse->getHeaders();
        $headers = [
            'Content-Type' => 'application/soap+xml; charset=utf-8',
            'Content-Length' => strlen($soapResponse->getResponse()),
            'Cache-Control' => 'no-store, no-cache, must-revalidate',
            // Dodaj inne nagłówki, jeśli są wymagane
        ];

        // Tworzenie obiektu Response z odpowiedzią SOAP
        $response = new Response($soapResponse->getResponse(), Response::HTTP_OK, $headers);

        // Ustawianie nagłówków z odpowiedzi SOAP
        foreach ($responseHeaders as $header) {
            $response->headers->set($header['name'], $header['data']);
        }

        return $response;
    }

    /**
     * @Route("/dashboard/allData-export", name="dashboardExport")
     */
    public function exportAllData(EntityManagerInterface $em,Request $request, InterestRatesRepository $is) {
        $cityRepository = $em->getRepository(City::class);
        $housesInCity = $cityRepository->findAll();

        $avg = $is->getAverageRatesPerYear($em);
        $dash = new DashboardRepository();
        $dataToReturn = $dash->getHousesPerCity($housesInCity, $avg);

        if (empty($dataToReturn)){
            return new JsonResponse(['status' => 'failed'], 400);
        } else{
            $xml = new \SimpleXMLElement('<years></years>');
            $dash->arrayToXml($dataToReturn, $xml);

            $response = new Response($xml->asXML());
            $response->headers->set('Content-Type', 'application/xml');
            $response->headers->set('Content-Disposition', 'attachment; filename="data.xml"');

            return $response;
        }

    }

    /**
     * @Route("/dashboard/customTable", name="dashboard")
     */
    public function getCustomTableData(EntityManagerInterface $em,Request $request, InterestRatesRepository $is): JsonResponse
    {
        $avg = $is->getAverageRatesPerYear($em);
        $start = $request->query->get('startDate');
        $end = $request->query->get('endDate');
        $type = $request->query->get('type');

        $dash = new DashboardRepository();

        $dataToReturn = $dash->getCustomData($type,$start, $end, $em, $avg);
        if (empty($dataToReturn)){
            return new JsonResponse(['status' => 'failed'], 400);
        } else{
            return new JsonResponse($dataToReturn, 200,['Content-Type' => 'application/json']);
        }

    }


    /**
     * @Route("/dashboard/chart", name="dashboardChart")
     */
    public function getChartData(EntityManagerInterface $em,Request $request, InterestRatesRepository $is): JsonResponse
    {
        $cityRepository = $em->getRepository(City::class);
        $housesInCity = $cityRepository->findAll();
        $city = $request->query->get('city');
        $avg = $is->getAverageRatesPerYear($em);

        $dash = new DashboardRepository();
        $avg = $dash->getHousesPerCity($housesInCity, $avg);
        $avg = $dash->getDataToChart($avg, $city);

        return new JsonResponse($avg);
    }
}
