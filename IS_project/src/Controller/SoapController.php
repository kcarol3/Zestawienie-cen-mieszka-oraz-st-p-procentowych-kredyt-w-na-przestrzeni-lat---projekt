<?php

namespace App\Controller;

use App\Repository\CityRepository;
use Doctrine\ORM\EntityManagerInterface;
use PHP2WSDL\PHPClass2WSDL;
use SoapServer;
use App\Service\SoapService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class SoapController extends AbstractController
{
    #[Route('/wsdl', name: 'app_soap_wsdl')]
    public function wsdl(): Response
    {
        $serviceURI = "https://localhost:8000/wsdl";
        $wsdlGenerator = new PHPClass2WSDL(SoapService::class, $serviceURI);
        // Generate the WSDL from the class adding only the public methods that have @soap annotation.
        $wsdlGenerator->generateWSDL(true);
        $wsdlContent = $wsdlGenerator->dump();

        // Ustawienie nagłówków odpowiedzi
        $response = new Response($wsdlContent);
        $response->headers->set('Content-Type', 'application/xml');

        return $response;
    }

    /**
     * @Route("/soap")
     */
    public function index(SoapService $soapService)
    {
        $wsdlPath = $this->getParameter('kernel.project_dir') . '/public/soap.wsdl';
        $soapServer = new \SoapServer($wsdlPath);
        $soapServer->setObject($soapService);

        ob_start();
        $soapServer->handle();
        $responseContent = ob_get_clean();

        return new Response($responseContent, Response::HTTP_OK, ['Content-Type' => 'text/xml; charset=ISO-8859-1']);
    }

}
