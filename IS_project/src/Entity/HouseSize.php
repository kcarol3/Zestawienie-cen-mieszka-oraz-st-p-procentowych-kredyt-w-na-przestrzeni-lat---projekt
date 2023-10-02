<?php

namespace App\Entity;

use App\Repository\HouseSizeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: HouseSizeRepository::class)]
class HouseSize
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $size = null;

    #[ORM\OneToMany(mappedBy: 'houseSize', targetEntity: Houses::class)]
    private Collection $houses;

    public function __construct()
    {
        $this->houses = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSize(): ?string
    {
        return $this->size;
    }

    public function setSize(string $size): self
    {
        $this->size = $size;

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
            $house->setHouseSize($this);
        }

        return $this;
    }

    public function removeHouse(Houses $house): self
    {
        if ($this->houses->removeElement($house)) {
            // set the owning side to null (unless already changed)
            if ($house->getHouseSize() === $this) {
                $house->setHouseSize(null);
            }
        }

        return $this;
    }
}
