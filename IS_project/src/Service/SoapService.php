<?php

namespace App\Service;
use App\Repository\CityRepository;
use App\Repository\HousesRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\Houses;

class SoapService
{
    private $entityManager;
    private $repository;
    public function __construct(EntityManagerInterface $entityManager, HousesRepository $housesRepository)
    {
        $this->entityManager = $entityManager;
        $this->repository = $housesRepository;
    }

    /**
     * @return int
     * @throws \Doctrine\ORM\NoResultException
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @soap
     */
    public function getRecordCount(): int
    {
        return $this->repository->count([]);
    }
}
