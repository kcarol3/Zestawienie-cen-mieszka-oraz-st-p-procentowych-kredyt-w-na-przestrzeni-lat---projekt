<?php

namespace App\Repository;

use App\Entity\City;
use App\Entity\HouseSize;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Exception\NotSupported;

class DashboardRepository
{
    public function getHousesPerCity(array $housesInCity, array $avgRates): array{
        foreach ($housesInCity as $houses){
            foreach ($houses->getHouses() as $house){
                $year = $house->getYear();
                $house = [
                    'price' => $house->getPrice(),
                    'floor' => $house->getFloor(),
                    'sq' => $house->getSq(),
                ];

                if( isset($avgRates[$year])){
                    $avgRates[$year][$houses->getCityName()]['houses'][] = $house;
                }
            }
        }

        return $avgRates;
    }

    public function getDataToChart(array $houses, string $city){
        $avgPerCategory = [];
        foreach ($houses as $date => $elements){
            foreach ($elements as $key => $values){
                if($key === "rate"){
                    $rate = $values;
                    continue;
                }
                if($city !== $key){
                    continue;
                }
                $sumPrice = 0;
                $count  = 0;
                foreach ($values as $house){
                    foreach ($house as $item){
                        $sumPrice += $item['price'];
                    }
                    $count = count($house);
                }
                $avgValue = $sumPrice / $count;
                $avgPerCategory[number_format($rate, 2,'.')] = number_format($avgValue, 2, '.', '');
            }
        }

        return $avgPerCategory;
    }

    public function getHousesPerSize(array $houses, array $avgRates): array{
        foreach ($houses as $houses){
            foreach ($houses->getHouses() as $house){
                $year = $house->getYear();
                $house = [
                    'price' => $house->getPrice(),
                    'floor' => $house->getFloor(),
                    'sq' => $house->getSq(),
                ];

                if( isset($avgRates[$year])){
                    $avgRates[$year][$houses->getSize()]['houses'][] = $house;
                }
            }
        }

        return $avgRates;
    }

    public function calculateAvr(array $houses): array{
        $avgPerCategory = [];
        foreach ($houses as $date => $elements){
            foreach ($elements as $key => $values){
                if($key === "rate"){
                    $avgPerCategory[$date]['rate'] = $values;
                    continue;
                }
                $sumPrice = 0;
                $count  = 0;
                foreach ($values as $house){
                    foreach ($house as $item){
                        $sumPrice += $item['price'];
                    }
                    $count = count($house);
                }

                $avgValue = $sumPrice / $count;

                $avgPerCategory[$date][$key]['avgHousePrice'] = round($avgValue,2);
            }
        }

        return $avgPerCategory;
    }

    public function arrayToXml($data, &$xml)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (!is_numeric($key)) {
                        $subnode = $xml->addChild($key);
                        $this->arrayToXml($value, $subnode);
                } else if ($key < 1928) {
                    $subnode = $xml->addChild("house");
                    $this->arrayToXml($value, $subnode);
                } else {
                    $subnode = $xml->addChild("year");
                    $subnode->addAttribute("value", $key);
                    $this->arrayToXml($value, $subnode);
                }
            } else {
                if (is_array($data)) {
                    $xml->addAttribute("$key", "$value");
                } else {
                    $xml->addChild("$key", htmlspecialchars("$value"));
                }
            }
        }
    }

    /**
     * @throws NotSupported
     * @throws Exception
     */
    public function getCustomData(string $type, string $start, string $end, EntityManager $em, array $avg): array
    {
        $em->getConnection()->setTransactionIsolation(TransactionIsolationLevel::READ_COMMITTED);
        $em->getConnection()->beginTransaction();

        try{
            if($type == "Miasta"){
                $cityRepository = $em->getRepository(City::class);
                $housesInCity = $cityRepository->findAll();
                $avg = $this->getHousesPerCity($housesInCity, $avg);
            } else {
                $sizeRepository = $em->getRepository(HouseSize::class);
                $housesBySize = $sizeRepository->findAll();
                $avg = $this->getHousesPerSize($housesBySize, $avg);
            }
            $em->getConnection()->commit(); // 1 => 0, "real" transaction committed
        } catch (\Exception $e) {
            $em->getConnection()->rollBack(); // 1 => 0, "real" transaction rollback
            throw $e;
        }

        $avg = $this->calculateAvr($avg);

        if(isset($start) && isset($end)){
            if($start > $end){
                return [];
            }
            $newTable = [];
            foreach ($avg as $date => $elements){
                if(intval($date) >= $start && intval($date) <= $end){
                    $newTable[$date] = $elements;
                }
            }
            return $newTable;
        } else {
            return $avg;
        }
    }
}