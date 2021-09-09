<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ReservationRepository;
use Symfony\Component\Validator\Constraints\Date;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as assert;
use Symfony\Component\Form\Extension\Core\Type\DateType;

/**
 * @ORM\Entity(repositoryClass=ReservationRepository::class)
 */
class Reservation
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups({"form:reads","read"})
     */
    private $id;

    /**
     * @ORM\Column(type="date")
     * @Groups({"form:reads","read"})
     * @assert\NotBlank
     */
    private $dateDeb;

    /**
     * @ORM\Column(type="date")
     * @Groups({"form:reads","read"})
     * @assert\NotBlank
     */
    private $dateFin;



    /**
     * @ORM\OneToOne(targetEntity=Devis::class, inversedBy="reservation", cascade={"persist", "remove"})
    
     * @Groups({"form:reads","read"})
  
     */
    private $devis;

    /**
     * @ORM\OneToOne(targetEntity=Facture::class, inversedBy="reservation", cascade={"persist", "remove"})
     
     * @Groups({"form:reads","read"})
   
     */
    private $facture;

    /**
     * @ORM\ManyToOne(targetEntity=Formation::class, inversedBy="tabRes", cascade={"persist"})
     
     * @Groups({"form:reads","read"})
     
     */
    private $formation;

    /**
     * @ORM\ManyToOne(targetEntity=Formateur::class, inversedBy="tabRes")
     * @Groups({"form:reads","read"})
     */
    private $Formateur;

    /**
     * @ORM\ManyToOne(targetEntity=Participant::class, inversedBy="tabRes")
     * @Groups({"form:reads","read"})
     */
    private $participant;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDateDeb(): ?\DateTime
    {
        return $this->dateDeb;
    }

    public function setDateDeb(\DateTime $dateDeb): self
    {
        $this->dateDeb = $dateDeb;

        return $this;
    }

    public function getDateFin(): ?\DateTime
    {
        return $this->dateFin;
    }

    public function setDateFin(\DateTime $dateFin): self
    {
        $this->dateFin = $dateFin;

        return $this;
    }



    public function getDevis(): ?Devis
    {
        return $this->devis;
    }

    public function setDevis(?Devis $devis): self
    {
        $this->devis = $devis;

        return $this;
    }

    public function getFacture(): ?Facture
    {
        return $this->facture;
    }

    public function setFacture(?Facture $facture): self
    {
        $this->facture = $facture;

        return $this;
    }

    public function getFormation(): ?Formation
    {
        return $this->formation;
    }

    public function setFormation(?Formation $formation): self
    {
        $this->formation = $formation;

        return $this;
    }

    public function getFormateur(): ?Formateur
    {
        return $this->Formateur;
    }

    public function setFormateur(?Formateur $Formateur): self
    {
        $this->Formateur = $Formateur;

        return $this;
    }

    public function getParticipant(): ?Participant
    {
        return $this->participant;
    }

    public function setParticipant(?Participant $participant): self
    {
        $this->participant = $participant;

        return $this;
    }
}
