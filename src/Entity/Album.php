<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AlbumRepository")
 */
class Album
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $idbdgest;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $titre;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $tome;

    /**
     * @ORM\Column(type="string")
     */
    private $depotLegal;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $isbn;

    /**
     * @ORM\Column(type="smallint", nullable=true)
     */
    private $planches;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgCouverture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgDos;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Serie", inversedBy="album")
     */
    private $serie;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $format;
    /**
     * @ORM\Column(type="string", length=255)
     */
    private $editeur;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $scenariste;
    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $dessinateur;
    /**
     * @ORM\Column(type="date", nullable=true)
     */
    private $dateachat;
    /**
     * @ORM\Column(type="float", nullable=true)
     */
    private $prix;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $whislist;
    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $tosell;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return mixed
     */
    public function getIdbdgest()
    {
        return $this->idbdgest;
    }

    /**
     * @param mixed $idbdgest
     */
    public function setIdbdgest($idbdgest)
    {
        $this->idbdgest = $idbdgest;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): self
    {
        $this->titre = $titre;

        return $this;
    }

    public function getTome(): ?string
    {
        return $this->tome;
    }

    public function setTome(?string $tome): self
    {
        $this->tome = $tome;

        return $this;
    }

    public function getDepotLegal(): ?string
    {
        return $this->depotLegal;
    }

    public function setDepotLegal(?string $depotLegal): self
    {
        $this->depotLegal = $depotLegal;

        return $this;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(?string $isbn): self
    {
        $this->isbn = $isbn;

        return $this;
    }

    public function getPlanches(): ?int
    {
        return $this->planches;
    }

    public function setPlanches(?int $planches): self
    {
        $this->planches = $planches;

        return $this;
    }

    public function getImgCouverture(): ?string
    {
        return $this->imgCouverture;
    }

    public function setImgCouverture(?string $imgCouverture): self
    {
        $this->imgCouverture = $imgCouverture;

        return $this;
    }

    public function getImgDos(): ?string
    {
        return $this->imgDos;
    }

    public function setImgDos(?string $imgDos): self
    {
        $this->imgDos = $imgDos;

        return $this;
    }

    public function getSerie(): ?Serie
    {
        return $this->serie;
    }

    public function setSerie(?Serie $serie): self
    {
        $this->serie = $serie;

        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(string $format): self
    {
        $this->format = $format;

        return $this;
    }

    /**
     * @return mixed
     */
    public function getEditeur()
    {
        return $this->editeur;
    }

    /**
     * @param mixed $editeur
     */
    public function setEditeur($editeur)
    {
        $this->editeur = $editeur;
    }

    /**
     * @return mixed
     */
    public function getScenariste()
    {
        return $this->scenariste;
    }

    /**
     * @param mixed $scenariste
     */
    public function setScenariste($scenariste)
    {
        $this->scenariste = $scenariste;
    }

    /**
     * @return mixed
     */
    public function getDessinateur()
    {
        return $this->dessinateur;
    }

    /**
     * @param mixed $dessinateur
     */
    public function setDessinateur($dessinateur)
    {
        $this->dessinateur = $dessinateur;
    }

    /**
     * @return mixed
     */
    public function getDateachat()
    {
        return $this->dateachat;
    }

    /**
     * @param mixed $dateachat
     */
    public function setDateachat($dateachat)
    {
        $this->dateachat = $dateachat;
    }

    /**
     * @return mixed
     */
    public function getPrix()
    {
        return $this->prix;
    }

    /**
     * @param mixed $prix
     */
    public function setPrix($prix)
    {
        $this->prix = $prix;
    }

    /**
     * @return mixed
     */
    public function getWhislist()
    {
        return $this->whislist;
    }

    /**
     * @param mixed $whislist
     */
    public function setWhislist($whislist)
    {
        $this->whislist = $whislist;
    }

    /**
     * @return mixed
     */
    public function getTosell()
    {
        return $this->tosell;
    }

    /**
     * @param mixed $tosell
     */
    public function setTosell($tosell): void
    {
        $this->tosell = $tosell;
    }

}
