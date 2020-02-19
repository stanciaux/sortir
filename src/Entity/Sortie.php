<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SortieRepository")
 */
class Sortie
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
    private $nom;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duree;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCloture;

    /**
     * @ORM\Column(type="integer")
     */
    private $nbInscriptionsMax;

    /**
     * @ORM\Column(type="string", length=500, nullable=true)
     */
    private $descriptionInfos;

    /**
     * @ORM\Column(type="string", length=250, nullable=true)
     */
    private $urlPhoto;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Site", inversedBy="sortie")
     * @ORM\JoinColumn(nullable=false)
     */
    private $site;

    /**
     * @var Etat
     * @ORM\ManyToOne(targetEntity="App\Entity\Etat", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=false)
     */
    private $etat;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Lieu", inversedBy="sorties")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $lieu;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="sortiesOrganisees")
     * @ORM\JoinColumn(nullable=false)
     */
    private $organisateur;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Inscription", mappedBy="sortie", orphanRemoval=true)
     * @ORM\JoinColumn(nullable=true)
     */
    private $inscriptions;

    /**
     * @ORM\Column(type="datetime")
     * @ORM\JoinColumn(nullable=false)
     */
    private $dateSortie;

    /**
     * @ORM\Column(type="text")
     * @ORM\JoinColumn(nullable=true)
     */
    private $motifAnnulation;

    public function __construct()
    {
        $this->inscriptions = new ArrayCollection();
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

    public function getDuree(): ?int
    {
        return $this->duree;
    }

    public function setDuree(?int $duree): self
    {
        $this->duree = $duree;

        return $this;
    }

    public function getDateCloture(): ?\DateTimeInterface
    {
        return $this->dateCloture;
    }

    public function setDateCloture(?\DateTimeInterface $dateCloture): self
    {
        $this->dateCloture = $dateCloture;

        return $this;
    }

    public function getNbInscriptionsMax(): ?int
    {
        return $this->nbInscriptionsMax;
    }

    public function setNbInscriptionsMax(int $nbInscriptionsMax): self
    {
        $this->nbInscriptionsMax = $nbInscriptionsMax;

        return $this;
    }

    public function getDescriptionInfos(): ?string
    {
        return $this->descriptionInfos;
    }

    public function setDescriptionInfos(?string $descriptionInfos): self
    {
        $this->descriptionInfos = $descriptionInfos;

        return $this;
    }

    public function getUrlPhoto(): ?string
    {
        return $this->urlPhoto;
    }

    public function setUrlPhoto(?string $urlPhoto): self
    {
        $this->urlPhoto = $urlPhoto;

        return $this;
    }

    public function getDateSortie(): ?\DateTimeInterface
    {
        return $this->dateSortie;
    }

    public function setDateSortie(?\DateTimeInterface $dateSortie): self
    {
        $this->dateSortie = $dateSortie;

        return $this;
    }

    public function getSite(): ?Site
    {
        return $this->site;
    }

    public function setSite(?Site $site): self
    {
        $this->site = $site;

        return $this;
    }

    public function getEtat(): ?Etat
    {
        return $this->etat;
    }

    public function setEtat(?Etat $etat): self
    {
        $this->etat = $etat;

        return $this;
    }

    public function getLieu(): ?Lieu
    {
        return $this->lieu;
    }

    public function setLieu(?Lieu $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getOrganisateur(): ?User
    {
        return $this->organisateur;
    }

    public function setOrganisateur(?User $organisateur): self
    {
        $this->organisateur = $organisateur;

        return $this;
    }

    /**
     * @return Collection|Inscription[]
     */
    public function getInscriptions(): Collection
    {
        return $this->inscriptions;
    }

    public function addInscription(Inscription $inscription): self
    {
        if (!$this->inscriptions->contains($inscription)) {
            $this->inscriptions[] = $inscription;
            $inscription->setSortie($this);
        }
        return $this;
    }

    public function removeInscription(Inscription $inscription): self
    {
        if ($this->inscriptions->contains($inscription)) {
            $this->inscriptions->removeElement($inscription);
            // set the owning side to null (unless already changed)
            if ($inscription->getSortie() === $this) {
                // ne pas mettre la sortie de inscription à null sinon le subscriber ne fonctionnera plus
//                $inscription->setSortie(null);
            }
        }

        return $this;
    }

    public function getMotifAnnulation(): ?string
    {
        return $this->motifAnnulation;
    }

    public function setMotifAnnulation(string $motifAnnulation): self
    {
        $this->motifAnnulation = $motifAnnulation;

        return $this;
    }

    public function isDesinscrirePossible(): bool
    {
        if (
            $this->dateCloture >= new \DateTime() &&
            (
                $this->etat->getLibelle() == Etat::OUVERTE ||
                $this->etat->getLibelle() == Etat::CLOTUREE
            )
        ) {
            return true;
        }
        return false;
    }

    public function isInscrirePossible(User $user): bool
    {
        if ($this->etat->getLibelle() != Etat::OUVERTE) {
            return false;
        }

        if ($this->inscriptions->count() >= $this->nbInscriptionsMax) {
            return false;
        }

        if ($this->dateCloture < new \DateTime()){
            return false;
        }
//        mon utilisateur est inscrit à cette sortie
        if ($this->getInscriptions()->contains($user)){
            return false;
        }

        return true;
    }

    public function isArchivagePossible(User $user): bool
    {
        $dateJour = new \DateTime();
        $interval = $dateJour->diff($this->dateSortie);
        if ($interval->days < 30) {
            return false;
        }
        if ($this->etat->getLibelle() == Etat::ARCHIVEE){
            return false;
        }
        if (! in_array("ROLE_ADMIN", $user->getRoles())){
            return false;
        }
        return true;
    }

    public function isAnnulable(User $user): bool
    {
        if ($this->etat->getLibelle() != Etat::OUVERTE) {
            return false;
        }
        if (
            $this->getOrganisateur()->getId() != $user->getId() &&
            ! in_array("ROLE_ADMIN", $user->getRoles())
        ) {
            return false;
        }
        return true;
    }

    public function isModifiable(User $user)
    {
        if ($this->etat->getLibelle() != Etat::CREEE){
            return false;
        }
        if ($this->getOrganisateur()->getId() != $user->getId()){
            return false;
        }
        return true;
    }

    public function isSupprimable(User $user)
    {
        if ($this->etat->getLibelle() != Etat::CREEE){
            return false;
        }
        if ($this->getOrganisateur()->getId() != $user->getId()){
            return false;
        }
        return true;
    }

    public function isPubliable(User $user)
    {
        if ($this->etat->getLibelle() != Etat::CREEE){
            return false;
        }
        if ($this->getOrganisateur()->getId() != $user->getId()){
            return false;
        }
        return true;
    }

}