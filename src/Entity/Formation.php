<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\FormationRepository;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as assert;

/**
 * @ORM\Entity(repositoryClass=FormationRepository::class)
 */
class Formation
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
     * @assert\NotBlank
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     * @Groups("form:read")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $nbr_jours;

    /**
     * @ORM\Column(type="integer")
     * @Groups("form:read")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $prix;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="formation")
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

    public function getNbrJours(): ?int
    {
        return $this->nbr_jours;
    }

    public function setNbrJours(int $nbrJours): self
    {
        $this->nbr_jours = $nbrJours;

        return $this;
    }

    public function getPrix(): ?int
    {
        return $this->prix;
    }

    public function setPrix(int $prix): self
    {
        $this->prix = $prix;

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
            $tabRe->setFormation($this);
        }

        return $this;
    }

    public function removeTabRe(Reservation $tabRe): self
    {
        if ($this->tabRes->removeElement($tabRe)) {
            // set the owning side to null (unless already changed)
            if ($tabRe->getFormation() === $this) {
                $tabRe->setFormation(null);
            }
        }

        return $this;
    }
    public function __toString()
    {
        return  strval($this->id);
    }
}
