<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Serializer\Filter\PropertyFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BookRepository")
 * @ApiResource(
 *     attributes={
 *          "order"={"title": "ASC",},
 *          },
 *     collectionOperations={
 *     "get"={
 *          "normalization_context"={"groups"={"get_role_adherent"}}
 *               },
 *      "post"={
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *             "access_control_message"="you don't have the right to access this resource"
 *          }
 *      },
 *     itemOperations={
 *     "get"={
 *          "normalization_context"={"groups"={"get_role_adherent"}}
 *               },
 *     "put"={
 *          "access_control"="is_granted('ROLE_MANAGER')",
 *          "access_control_message"="you don't have the right to access this resource",
 *          "denormalization_context"={"groups"={"put_role_manager"}}
 *              },
 *     "delete"={
 *          "access_control"="is_granted('ROLE_ ADMIN')",
 *          "access_control_message"="you don't have the right to access this resource"
 *              }
 *     }
 * )
 * @ApiFilter(
 *     SearchFilter::class, properties={
 *          "isbn": "exact",
 *          "title": "partial",
 *          "language": "partial",
 *          "genre": "partial",
 *          "author": "partial",
 *          }
 * )
 * @ApiFilter(
 *     OrderFilter::class, properties={
 *     "price",
 *     "publicationYear",
 *      }
 * )
 * @ApiFilter(
 *     PropertyFilter::class, arguments={
 *                "parameterName": "properties",
 *                "overrideDefaultProperties": false,
 *                "whitelist": {"isbn", "price","title","publicationYear"}
 *            }
 * )
 */
class Book
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $isbn;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $title;

    /**
     * @ORM\Column(type="float", nullable=true)
     * @Groups({"get_role_manager", "put_role_admin"})
     */
    private $price;

    /**
     * @ORM\Column(type="integer", nullable=true)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $publicationYear;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $language;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Genre", inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $genre;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Author", inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $author;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Editor", inversedBy="books")
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_role_adherent", "put_role_manager"})
     */
    private $editor;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Borrow", mappedBy="book")
     * @Groups({"get_role_manager"})
     */
    private $borrows;

    public function __construct()
    {
        $this->borrows = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getIsbn(): ?string
    {
        return $this->isbn;
    }

    public function setIsbn(string $isbn): self
    {
        $this->isbn = $isbn;

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

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(?float $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getGenre(): ?Genre
    {
        return $this->genre;
    }

    public function setGenre(?Genre $genre): self
    {
        $this->genre = $genre;

        return $this;
    }

    public function getAuthor(): ?Author
    {
        return $this->author;
    }

    public function setAuthor(?Author $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getEditor(): ?Editor
    {
        return $this->editor;
    }

    public function setEditor(?Editor $editor): self
    {
        $this->editor = $editor;

        return $this;
    }

    public function getPublicationYear(): ?int
    {
        return $this->publicationYear;
    }

    public function setPublicationYear(?int $publicationYear): self
    {
        $this->publicationYear = $publicationYear;

        return $this;
    }

    public function getLanguage(): ?string
    {
        return $this->language;
    }

    public function setLanguage(?string $language): self
    {
        $this->language = $language;

        return $this;
    }

    public function __toString()
    {
        return (string) $this->title.' '.$this->isbn.' '.$this->language;
    }

    /**
     * @return Collection|Borrow[]
     */
    public function getBorrows(): Collection
    {
        return $this->borrows;
    }

    public function addBorrow(Borrow $borrow): self
    {
        if (!$this->borrows->contains($borrow)) {
            $this->borrows[] = $borrow;
            $borrow->setBook($this);
        }

        return $this;
    }

    public function removeBorrow(Borrow $borrow): self
    {
        if ($this->borrows->contains($borrow)) {
            $this->borrows->removeElement($borrow);
            // set the owning side to null (unless already changed)
            if ($borrow->getBook() === $this) {
                $borrow->setBook(null);
            }
        }

        return $this;
    }

}
