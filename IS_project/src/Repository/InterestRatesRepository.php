<?php

namespace App\Repository;

use App\Entity\InterestRates;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Exception;
use Doctrine\DBAL\TransactionIsolationLevel;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use SimpleXMLElement;

/**
 * @extends ServiceEntityRepository<InterestRates>
 *
 * @method InterestRates|null find($id, $lockMode = null, $lockVersion = null)
 * @method InterestRates|null findOneBy(array $criteria, array $orderBy = null)
 * @method InterestRates[]    findAll()
 * @method InterestRates[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class InterestRatesRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, InterestRates::class);
    }

    public function save(InterestRates $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(InterestRates $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    /**
     * @throws \Exception
     */
    public function saveXmlToDatabase(string $xml){

        $rates = new SimpleXMLElement($xml);

        foreach ($rates as $positions) {
            $rate = new InterestRates();
            $dateString = (string) $positions['obowiazuje_od'];
            $date = DateTime::createFromFormat('Y-m-d', $dateString);
            $rate->setDate($date);
            $ref = $positions->pozycja[0]['oprocentowanie']->__toString();
            $ref = str_replace(",",".",$ref);
            $rate->setRef(floatval($ref));

            $this->save($rate);
        }
        $this->getEntityManager()->flush();
    }

    /**
     * @throws Exception
     */
    public function getAverageRatesPerYear(EntityManagerInterface $em): array{

        $em->getConnection()->setTransactionIsolation(TransactionIsolationLevel::REPEATABLE_READ);
        $em->getConnection()->beginTransaction();

        try{
        $rates = $this->findAll();
            $em->getConnection()->commit(); // 1 => 0, "real" transaction committed
        } catch (\Exception $e) {
            $em->getConnection()->rollBack(); // 1 => 0, "real" transaction rollback
            throw $e;
        }

        $currentYear = $rates[0]->getDate()->format('Y');
        $numOfRates = 1;
        $sumOfRates = $rates[0]->getRef();
        $avg = [];
        $lastRate = 0;
        unset($rates[0]);
        foreach ($rates as $rate){
            $year = $rate->getDate()->format('Y');

            if($currentYear == $year){
                $numOfRates++;
                $sumOfRates += $rate->getRef();
            }
            else if(intval($year)-intval($currentYear) > 1) {
                $years = intval($year) - intval($currentYear);
                $i = 0;
                while ($i < $years) {
                    $y = intval($currentYear);
                    $y = strval($y+$i);
                    $avg[$y]['rate'] = $lastRate;
                    $i++;
                }
                $currentYear = $rate->getDate()->format('Y');
            }else {
                $avg[$currentYear]['rate'] = round($sumOfRates / $numOfRates, 2);
                $currentYear = $rate->getDate()->format('Y');
                $numOfRates = 1;
                $sumOfRates = $rate->getRef();
                }
            $lastRate = $rate->getRef();
            }
        $avg[$currentYear]['rate'] = $sumOfRates / $numOfRates;

        return $avg;
    }

}
