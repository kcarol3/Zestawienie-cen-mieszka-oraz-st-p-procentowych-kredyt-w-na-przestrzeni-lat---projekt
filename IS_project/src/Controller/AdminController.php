<?php

namespace App\Controller;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class AdminController extends AbstractController
{
    #[Route('/delete-all', name: 'app_admin', methods: 'delete')]
    public function deleteAll(EntityManagerInterface $entityManager): JsonResponse
    {
        $connection = $entityManager->getConnection();
        $query = 'DELETE FROM houses;DELETE FROM city;DELETE FROM house_size;DELETE FROM interest_rates;';
        $statement = $connection->prepare($query);
        $statement->execute();

        return new JsonResponse(["status" => "deleted all"]);
    }
}
