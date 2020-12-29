<?php

namespace App\Entity;

use App\Repository\ActualityRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ActualityRepository::class)
 */
class Actuality
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $start;

    /**
     * @ORM\Column(type="datetime")
     */
    private $end;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max="255", maxMessage="Ce champ ne peut dépasser 255 caractères."
     * )
     * @Assert\NotBlank(
     *     message="Vous avez oublié le lieu."
     * )
     */
    private $place;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(
     *     message="Vous avez oublié la carte."
     * )
     */
    private $googlemap;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max="255", maxMessage="Ce champ ne peut dépasser 255 caractères."
     * )
     * @Assert\NotBlank(
     *     message="Vous avez oublié le titre."
     * )
     */
    private $title;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max="255", maxMessage="Ce champ ne peut dépasser 255 caractères."
     * )
     * @Assert\NotBlank(
     *     message="Vous avez oublié le contenu."
     * )
     */
    private $content;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Assert\File(
     *     mimeTypes={"image/png", "image/svg+xml", "image/jpg", "image/jpeg"},
     *     mimeTypesMessage="Le type de fichier et incorrect {{ type }}, vous devez choisir un fichier de type {{ types }}",
     *     maxSize="2M", maxSizeMessage="Le fichier ne peut pas dépasser 2Mo"
     * )
     */
    private $flyer;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStart(): ?\DateTimeInterface
    {
        return $this->start;
    }

    public function setStart(\DateTimeInterface $start): self
    {
        $this->start = $start;

        return $this;
    }

    public function getEnd(): ?\DateTimeInterface
    {
        return $this->end;
    }

    public function setEnd(\DateTimeInterface $end): self
    {
        $this->end = $end;

        return $this;
    }

    public function getPlace(): ?string
    {
        return $this->place;
    }

    public function setPlace(string $place): self
    {
        $this->place = $place;

        return $this;
    }

    public function getGooglemap(): ?string
    {
        return $this->googlemap;
    }

    public function setGooglemap(string $googlemap): self
    {
        $this->googlemap = $googlemap;

        return $this;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }

    public function getFlyer(): ?string
    {
        return $this->flyer;
    }

    public function setFlyer(?string $flyer): self
    {
        $this->flyer = $flyer;

        return $this;
    }
}
