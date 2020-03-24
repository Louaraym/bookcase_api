<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\AuthorRepository")
 * @ApiResource(
 *     collectionOperations={
 *        "get"={
 *           "denormalization_context"={"groups"={"get_author_role_user"}}
 *          },
 *          "post"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource",
 *              "denormalization_context"= {"groups"={"put_role_manager"}}
 *          }
 *     },
 *      itemOperations={
 *          "get"={
 *              "normalization_context"={"groups"={"get_author_role_user"}}
 *          },
 *          "put"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource",
 *              "denormalization_context"= {"groups"={"put_role_manager"}}
 *          },
 *          "delete"={
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource"
 *          }
 *      }
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "firstName": "ipartial",
 *          "lastNname": "ipartial",
 *          "nationality" : "partial"
 *      }
 * )
 */
class Author
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_author_role_user"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_author_role_user","put_role_manager","get"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_author_role_user","put_role_manager","get"})
     */
    private $lastName;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Nationality", inversedBy="authors", cascade={"persist"})
     * @ORM\JoinColumn(nullable=false)
     * @Groups({"get_author_role_user","put_role_manager"})
     */
    private $nationality;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Book", mappedBy="author")
     * @Groups({"get_author_role_user"})
     */
    private $books;

    public function __construct()
    {
        $this->books = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getNationality(): ?Nationality
    {
        return $this->nationality;
    }

    public function setNationality(?Nationality $nationality): self
    {
        $this->nationality = $nationality;

        return $this;
    }

    /**
     * @return Collection|Book[]
     */
    public function getBooks(): Collection
    {
        return $this->books;
    }

    public function addBook(Book $book): self
    {
        if (!$this->books->contains($book)) {
            $this->books[] = $book;
            $book->setAuthor($this);
        }

        return $this;
    }

    public function removeBook(Book $book): self
    {
        if ($this->books->contains($book)) {
            $this->books->removeElement($book);
            // set the owning side to null (unless already changed)
            if ($book->getAuthor() === $this) {
                $book->setAuthor(null);
            }
        }

        return $this;
    }

    public function __toString()
    {
       return (string) $this->firstName.' '.$this->lastName;
    }

    /**
     * @return int
     * @Groups({"get_author_role_user"})
     */
    public function getAuthorBookNumber(): int
    {
        return $this->books->count();
    }


//    public function getAuthorBookNumberAvailable(): int
//    {
//        return array_reduce($this->books->toArray(), function ($nb, $book){
//            return $nb + ($book->getAvailable() === true ? 1/0);
//        },0);
//    }

//    public function getAvailable(){
//
//    }
}
