<?php

namespace App\Entity;

use App\Repository\ParticipantRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as assert;

/**
 * @ORM\Entity(repositoryClass=ParticipantRepository::class)
 */
class Participant
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
     * @ORM\Column(type="string", length=255)
     * @Groups("form:read")
     * @Groups("form:reads")
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     * @Groups("form:read")
     * @Groups("form:reads")
     */
    private $nbr;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="participant")
     */
    private $tabRes;

    public function __construct()
    {
        $this->tabRes = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNom(): ?string
    {
        return $this->nom;
    }

    public function setNom(string $nom): self
    {
        $this->nom = $nom;

        return $this;
    }

    public function getNbr(): ?int
    {
        return $this->nbr;
    }

    public function setNbr(int $nbr): self
    {
        $this->nbr = $nbr;

        return $this;
    }

    /**
     * @return Collection|Reservation[]
     */
    public function getTabRes(): Collection
    {
        return $this->tabRes;
    }

    public function addTabRe(Reservation $tabRe): self
    {
        if (!$this->tabRes->contains($tabRe)) {
            $this->tabRes[] = $tabRe;
            $tabRe->setParticipant($this);
        }

        return $this;
    }

    public function removeTabRe(Reservation $tabRe): self
    {
        if ($this->tabRes->removeElement($tabRe)) {
            // set the owning side to null (unless already changed)
            if ($tabRe->getParticipant() === $this) {
                $tabRe->setParticipant(null);
            }
        }

        return $this;
    }
}
