<?php

namespace App\Entity;

use App\Repository\DevisRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as assert;

/**
 * @ORM\Entity(repositoryClass=DevisRepository::class)
 */
class Devis
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("form:reads")
     */
    private $id;

    /**
     * @ORM\Column(type="float")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $somme;

    /**
     * @ORM\Column(type="integer")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $nbrParticipant;

    /**
     * @ORM\OneToOne(targetEntity=Reservation::class, mappedBy="devis", cascade={"persist", "remove"})
     * @assert\NotBlank
     */
    private $reservation;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSomme(): ?float
    {
        return $this->somme;
    }

    public function setSomme(float $somme): self
    {
        $this->somme = $somme;

        return $this;
    }

    public function getNbrParticipant(): ?int
    {
        return $this->nbrParticipant;
    }

    public function setNbrParticipant(int $nbrParticipant): self
    {
        $this->nbrParticipant = $nbrParticipant;

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
            $this->reservation->setDevis(null);
        }

        // set the owning side of the relation if necessary
        if ($reservation !== null && $reservation->getDevis() !== $this) {
            $reservation->setDevis($this);
        }

        $this->reservation = $reservation;

        return $this;
    }
    public function __toString()
    {
        return  strval($this->id);
    }
}
