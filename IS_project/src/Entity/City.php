<?php

namespace App\Entity;

use App\Repository\CityRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CityRepository::class)]
class City
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $city_name = null;


    #[ORM\OneToMany(mappedBy: 'City', targetEntity: Houses::class)]
    private Collection $houses;

    public function __construct()
    {
        $this->houses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCityName(): ?string
    {
        return $this->city_name;
    }

    public function setCityName(string $city_name): self
    {
        $this->city_name = $city_name;

        return $this;
    }


    /**
     * @return Collection<int, Houses>
     */
    public function getHouses(): Collection
    {
        return $this->houses;
    }

    public function addHouse(Houses $house): self
    {
        if (!$this->houses->contains($house)) {
            $this->houses->add($house);
            $house->setCity($this);
        }

        return $this;
    }

    public function removeHouse(Houses $house): self
    {
        if ($this->houses->removeElement($house)) {
            // set the owning side to null (unless already changed)
            if ($house->getCity() === $this) {
                $house->setCity(null);
            }
        }

        return $this;
    }
}
