<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="App\Repository\SerieRepository")
 */
class Serie
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
    private $titre;


    /**
     * @ORM\Column(type="integer")
     */
    private $idbdgest;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $imgPlanche;

    /**
     * @ORM\Column(type="boolean")
     */
    private $enCours;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $resume;

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $genre;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Album", mappedBy="serie")
     */
    private $album;

    public function __construct()
    {
        $this->album = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
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

    public function getImgPlanche(): ?string
    {
        return $this->imgPlanche;
    }

    public function setImgPlanche(?string $imgPlanche): self
    {
        $this->imgPlanche = $imgPlanche;

        return $this;
    }

    public function getEnCours(): ?bool
    {
        return $this->enCours;
    }

    public function setEnCours(bool $enCours): self
    {
        $this->enCours = $enCours;

        return $this;
    }

    public function getResume(): ?string
    {
        return $this->resume;
    }

    public function setResume(?string $resume): self
    {
        $this->resume = $resume;

        return $this;
    }

    /**
     * @return Collection|Album[]
     */
    public function getAlbum(): Collection
    {
        return $this->album;
    }

    public function addAlbum(Album $album): self
    {
        if (!$this->album->contains($album)) {
            $this->album[] = $album;
            $album->setSerie($this);
        }

        return $this;
    }

    public function removeAlbum(Album $album): self
    {
        if ($this->album->contains($album)) {
            $this->album->removeElement($album);
            // set the owning side to null (unless already changed)
            if ($album->getSerie() === $this) {
                $album->setSerie(null);
            }
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getGenre()
    {
        return $this->genre;
    }

    /**
     * @param mixed $genre
     */
    public function setGenre($genre)
    {
        $this->genre = $genre;
    }
    public function __toString()
    {
        return $this->getTitre();
    }
}
