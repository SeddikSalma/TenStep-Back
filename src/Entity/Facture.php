<?php

namespace App\Entity;

use App\Repository\FactureRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as assert;

/**
 * @ORM\Entity(repositoryClass=FactureRepository::class)
 */
class Facture
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("form:read")
     * @Groups("form:reads")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups("form:read")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $Somme;

    /**
     * @ORM\Column(type="integer")
     * @Groups("form:read")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $nbrPaticipant;

    /**
     * @ORM\Column(type="date")
     * @Groups("form:read")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $date;

    /**
     * @ORM\OneToOne(targetEntity=Reservation::class, mappedBy="facture", cascade={"persist", "remove"})
     
     * @assert\NotBlank
     */
    private $reservation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSomme(): ?float
    {
        return $this->Somme;
    }

    public function setSomme(float $Somme): self
    {
        $this->Somme = $Somme;

        return $this;
    }

    public function getNbrPaticipant(): ?int
    {
        return $this->nbrPaticipant;
    }

    public function setNbrPaticipant(int $nbrPaticipant): self
    {
        $this->nbrPaticipant = $nbrPaticipant;

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): self
    {
        $this->date = $date;

        return $this;
    }

    public function getReservation(): ?Reservation
    {
        return $this->reservation;
    }

    public function setReservation(?Reservation $reservation): self
    {
        // unset the owning side of the relation if necessary
        if ($reservation === null && $this->reservation !== null) {
            $this->reservation->setFacture(null);
        }

        // set the owning side of the relation if necessary
        if ($reservation !== null && $reservation->getFacture() !== $this) {
            $reservation->setFacture($this);
        }

        $this->reservation = $reservation;

        return $this;
    }
    public function __toString()
    {
        return  strval($this->id);
    }
}
