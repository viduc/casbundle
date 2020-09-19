<?php

namespace Viduc\CasBundle\Entity;

use Viduc\CasBundle\Repository\PersonaRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PersonaRepository::class)
 */
class Persona
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $prenom;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $nom;

    /**
     * @ORM\Column(type="integer")
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lieu;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $aisanceNumerique;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expertiseDomaine;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $frequenceUsage;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $metier;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $citation;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $histoire;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $buts;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $personnalite;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $photo;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlPhoto;

    /**
     * @ORM\Column(name="roles", type="array")
     */
    private $roles = [];

    /**
     * @ORM\Column(name="is_active", type="boolean")
     */
    private $isActive;

    public function __construct() {
        $this->isActive = true;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrenom(): ?string
    {
        return $this->prenom;
    }

    public function setPrenom(string $prenom): self
    {
        $this->prenom = $prenom;

        return $this;
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

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getLieu(): ?string
    {
        return $this->lieu;
    }

    public function setLieu(?string $lieu): self
    {
        $this->lieu = $lieu;

        return $this;
    }

    public function getAisanceNumerique(): ?int
    {
        return $this->aisanceNumerique;
    }

    public function setAisanceNumerique(?int $aisanceNumerique): self
    {
        $this->aisanceNumerique = $aisanceNumerique;

        return $this;
    }

    public function getExpertiseDomaine(): ?int
    {
        return $this->expertiseDomaine;
    }

    public function setExpertiseDomaine(?int $expertiseDomaine): self
    {
        $this->expertiseDomaine = $expertiseDomaine;

        return $this;
    }

    public function getFrequenceUsage(): ?int
    {
        return $this->frequenceUsage;
    }

    public function setFrequenceUsage(?int $frequenceUsage): self
    {
        $this->frequenceUsage = $frequenceUsage;

        return $this;
    }

    public function getMetier(): ?string
    {
        return $this->metier;
    }

    public function setMetier(?string $metier): self
    {
        $this->metier = $metier;

        return $this;
    }

    public function getCitation(): ?string
    {
        return $this->citation;
    }

    public function setCitation(?string $citation): self
    {
        $this->citation = $citation;

        return $this;
    }

    public function getHistoire(): ?string
    {
        return $this->histoire;
    }

    public function setHistoire(?string $histoire): self
    {
        $this->histoire = $histoire;

        return $this;
    }

    public function getButs(): ?string
    {
        return $this->buts;
    }

    public function setButs(?string $buts): self
    {
        $this->buts = $buts;

        return $this;
    }

    public function getPersonnalite(): ?string
    {
        return $this->personnalite;
    }

    public function setPersonnalite(?string $personnalite): self
    {
        $this->personnalite = $personnalite;

        return $this;
    }

    public function getPhoto(): ?string
    {
        return $this->photo;
    }

    public function setPhoto(?string $photo): self
    {
        $this->photo = $photo;

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

    public function getRoles() {
        if (empty($this->roles)) {
            return ['ROLE_USER'];
        }
        return $this->roles;
    }

    function addRole($role) {
        $this->roles[] = $role;
    }

    function setIsActive($isActive) {
        $this->isActive = $isActive;
    }
}
