<?php

namespace App\Controller;


use App\Entity\InterestRates;
use App\Repository\InterestRatesRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\File\File;

/**
 * @Route("/api", name="api_")
 */
class InterestRatesController extends AbstractController
{
    #[Route('/interestRates', name: 'app_interest_rates')]
    public function index(): Response
    {
        return $this->render('interest_rates/index.html.twig', [
            'controller_name' => 'InterestRatesController',
        ]);
    }

    #[Route('/import/interestRates', name: 'app_interest_rates_create')]
     public function import(Request $request, InterestRatesRepository $repository): Response
     {
         if ($request->files->count() > 0) {
             $uploadedFile = $request->files->get('rates');
             $content = $uploadedFile->getContent();

             $repository->saveXmlToDatabase($content);

             $status = [
                 'status' => 'success'
             ];
             $status = $content;
         }
         else {
             $status = [
                 'status' => 'failed'
             ];
         }

         $response = new Response($status);
         $response->headers->set('Content-Type', 'application/json');
         return $response;
     }
}