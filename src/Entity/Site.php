<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SiteRepository")
 */
class Site
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=30)
     */
    private $nomSite;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Sortie", mappedBy="site")
     */
    private $sortie;

    public function __construct()
    {
        $this->sortie = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNomSite(): ?string
    {
        return $this->nomSite;
    }

    public function setNomSite(string $nomSite): self
    {
        $this->nomSite = $nomSite;

        return $this;
    }

    /**
     * @return Collection|Sortie[]
     */
    public function getSortie(): Collection
    {
        return $this->sortie;
    }

    public function addSortie(Sortie $sortie): self
    {
        if (!$this->sortie->contains($sortie)) {
            $this->sortie[] = $sortie;
            $sortie->setSite($this);
        }

        return $this;
    }

    public function removeSortie(Sortie $sortie): self
    {
        if ($this->sortie->contains($sortie)) {
            $this->sortie->removeElement($sortie);
            // set the owning side to null (unless already changed)
            if ($sortie->getSite() === $this) {
                $sortie->setSite(null);
            }
        }

        return $this;
    }
}
