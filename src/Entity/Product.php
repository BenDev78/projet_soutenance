<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=ProductRepository::class)
 */
class Product
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\Length(
     *     max="255", maxMessage="Le nom de produit ne peut depasser {{ limit }} caractères"
     * )
     * @Assert\NotBlank(message="Vous avez oublié le nom du produit")
     */
    private $name;

    /**
     * @ORM\Column(type="text")
     * @Assert\NotBlank(message="Vous avez oublié la description")
     */
    private $description;

    /**
     * @ORM\Column(type="integer")
     * @Assert\NotBlank(message="Vous avez oublié le prix")
     */
    private $price;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Positive(
     *     message="La valeur du litrage doit être positive"
     * )
     * @Assert\Length(
     *     max="3", maxMessage="Cette valeur ne peut pas dépasser 3 caractères"
     * )
     * @Assert\NotBlank(message="Vous avez oublié le litrage")
     */
    private $quantity;

    # TODO : Lmiter les dimensions de l'image
    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\File(
     *     mimeTypes={"image/png", "image/svg+xml", "image/jpg", "image/jpeg"},
     *     mimeTypesMessage="Le type de fichier et incorrect {{ type }}, vous devez choisir un fichier de type {{ types }}",
     *     maxSize="2M", maxSizeMessage="Le fichier ne peut pas dépasser 2Mo",
     * )
     */
    private $image;

    /**
     * @ORM\Column(type="integer")
     * @Assert\Length(
     *     min="4",
     *     max="4",
     *     exactMessage="L'année doit comporter exactement {{ limit }}"
     * )
     */
    private $year;

    /**
     * @ORM\ManyToOne(targetEntity=Category::class, inversedBy="products")
     * @ORM\JoinColumn(nullable=false)
     */
    private $category;

    /**
     * @ORM\OneToMany(targetEntity=Review::class, mappedBy="product")
     */
    private $reviews;

    /**
     * @ORM\OneToMany(targetEntity=Detail::class, mappedBy="product")
     */
    private $details;


    public function __construct()
    {
        $this->reviews = new ArrayCollection();
        $this->details = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?int
    {
        return $this->price;
    }

    public function setPrice(int $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): self
    {
        $this->image = $image;

        return $this;
    }

    public function getYear(): ?int
    {
        return $this->year;
    }

    public function setYear(int $year): self
    {
        $this->year = $year;

        return $this;
    }

    public function getCategory(): ?Category
    {
        return $this->category;
    }

    public function setCategory(?Category $category): self
    {
        $this->category = $category;

        return $this;
    }

    /**
     * @return Collection|Review[]
     */
    public function getReviews(): Collection
    {
        return $this->reviews;
    }

    public function addReview(Review $review): self
    {
        if (!$this->reviews->contains($review)) {
            $this->reviews[] = $review;
            $review->setProduct($this);
        }

        return $this;
    }

    public function removeReview(Review $review): self
    {
        if ($this->reviews->removeElement($review)) {
            // set the owning side to null (unless already changed)
            if ($review->getProduct() === $this) {
                $review->setProduct(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Detail[]
     */
    public function getDetails(): Collection
    {
        return $this->details;
    }

    public function addDetail(Detail $detail): self
    {
        if (!$this->details->contains($detail)) {
            $this->details[] = $detail;
            $detail->setProduct($this);
        }

        return $this;
    }

    public function removeDetail(Detail $detail): self
    {
        if ($this->details->removeElement($detail)) {
            // set the owning side to null (unless already changed)
            if ($detail->getProduct() === $this) {
                $detail->setProduct(null);
            }
        }

        return $this;
    }
    
}
