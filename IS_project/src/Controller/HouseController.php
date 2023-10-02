<?php

namespace App\Controller;


use App\Entity\City;
use App\Entity\Houses;
use App\Entity\HouseSize;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/api", name="api_")
 */
class HouseController extends AbstractController
{
    #[Route('/house', name: 'app_house')]
    public function index(): Response
    {
        return $this->render('house/index.html.twig', [
            'controller_name' => 'HouseController',
        ]);
    }

    #[Route('/import/houses', name: 'app_house_import')]
    public function importJson(Request $request, EntityManagerInterface $em):JsonResponse {

        if ($request->files->count() > 0) {
            $file = $request->files->get('houses');
            $housesTable = json_decode($file->getContent(), true);

            $counter = 0;
            foreach ($housesTable as $house) {
                $newHouse = new Houses();

                $newHouse->setFloor($house['floor']);
                $newHouse->setPrice($house['price']);
                $newHouse->setRooms($house['rooms']);
                $newHouse->setSq($house['sq']);
                $newHouse->setYear($house['year']);

                $cityRepository = $em->getRepository(City::class);

                if (str_starts_with($house['city'], "Krak")) {
                    $house['city'] = "Kraków";
                }
                if (str_starts_with($house['city'], "Pozna")) {
                    $house['city'] = "Poznań";
                }
                $city = $cityRepository->findOneBy(['city_name' => $house['city']]);

                if (null === $city) {

                    if (str_starts_with($house['city'], "Krak")) {
                        $house['city'] = "Kraków";
                    }
                    if (str_starts_with($house['city'], "Pozna")) {
                        $house['city'] = "Poznań";
                    }
                    $newCity = new City();
                    $newCity->setCityName($house['city']);
                    var_dump($newCity);
                    $em->persist($newCity);
                    $em->flush();
                    $newHouse->setCity($newCity);
                } else {
                    $newHouse->setCity($city);
                }

                $houseSizeRepository = $em->getRepository(HouseSize::class);
                if ($house['sq'] <= 40) {
                    $houseSize = "small";
                } else if ($house['sq'] <= 80) {
                    $houseSize = "medium";
                } else {
                    $houseSize = "big";
                }

                $houseSizeDB = $houseSizeRepository->findOneBy(['size' => $houseSize]);
                if (null === ($houseSizeDB)) {
                    $newHouseSize = new HouseSize();
                    $newHouseSize->setSize($houseSize);
                    $em->persist($newHouseSize);
                    $em->flush();
                    $newHouse->setHouseSize($newHouseSize);
                } else {
                    $newHouse->setHouseSize($houseSizeDB);
                }

                $em->persist($newHouse);
                $counter++;

                if ($counter == 200) {
                    $em->flush();
                    $counter = 0;
                }
                unset($newHouse);
                unset($newCity);
                unset($newHouseSize);

            }
            if ($counter > 0) {
                $em->flush();
            }

            return new JsonResponse(['status' => 'success'], 200);
        } else {
            return new JsonResponse(["status" => "failed"], 400);
        }
    }
}
