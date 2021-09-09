<?php

namespace App\Entity;

use App\Repository\FormateurRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as assert;

/**
 * @ORM\Entity(repositoryClass=FormateurRepository::class)
 */
class Formateur
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
     * @ORM\Column(type="string", length=255)
     * @Groups("form:read")
     * @Groups("form:reads")
     * @assert\NotBlank
     */
    private $type;

    /**
     * @ORM\OneToMany(targetEntity=Reservation::class, mappedBy="Formateur")
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

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(string $type): self
    {
        $this->type = $type;

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
            $tabRe->setFormateur($this);
        }

        return $this;
    }

    public function removeTabRe(Reservation $tabRe): self
    {
        if ($this->tabRes->removeElement($tabRe)) {
            // set the owning side to null (unless already changed)
            if ($tabRe->getFormateur() === $this) {
                $tabRe->setFormateur(null);
            }
        }

        return $this;
    }
}
