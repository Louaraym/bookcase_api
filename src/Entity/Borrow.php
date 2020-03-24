<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\OrderFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\DateFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BorrowRepository")
 * @ApiResource(
 *      attributes={
 *          "order"= {
 *              "borrowDate":"ASC"
 *           }
 *      },
 *     itemOperations={
 *        "get"={
 *          "access_control"="(is_granted('ROLE_USER') && object.getUser() == user) || is_granted('ROLE_MANAGER')",
 *          "access_control_message" = "Vous ne pouvez avoir accès qu'à vos propres prêts."
 *          },
 *        "put"={
 *          "access_control"="is_granted('ROLE_MANAGER')",
 *           "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource",
 *          "denormalization_context"={"groups"={"put_role_manager"}}
 *           },
 *        "delete"={
 *          "access_control"="is_granted('ROLE_MANAGER')",
 *          "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource"
 *            }
 *     },
 *     collectionOperations={
 *        "get"={
 *          "path"="/borrows",
 *          "access_control"="is_granted('ROLE_MANAGER')",
 *           "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource"
 *          },
 *        "post"={
 *             "denormalization_context"= {
 *                  "groups"={"borrow_post_role_user"}
 *              }
 *          }
 *     }
 * )
 * @ApiFilter(
 *      OrderFilter::class,
 *      properties={
 *          "borrowDate",
 *          "borrowExpectedReturnDate",
 *          "borrowRealReturnDate"
 *      }
 * )
 *
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "book.title": "impartial",
 *          "user.id": "exact"
 *      }
 * )
 *
 * @ApiFilter(
 *      DateFilter::class,
 *      properties={
 *          "borrowDate",
 *          "borrowExpectedReturnDate",
 *          "borrowRealReturnDate"
 *      }
 * )
 * @ORM\HasLifecycleCallbacks()
 */
class Borrow
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $borrowDate;

    /**
     * @ORM\Column(type="datetime")
     */
    private $borrowExpectedReturnDate;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     * @Groups({"put_role_manager"})
     */
    private $borrowRealReturnDate;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Book", inversedBy="borrows")
     * @ORM\JoinColumn(nullable=false)
     @Groups({"borrow_post_role_user"})
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\User", inversedBy="borrows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user;

    public function __construct()
    {
        $this->borrowDate = new \DateTime();
        $borrowDate = $this->getBorrowDate()->getTimestamp();
        $borrowExpectedReturnDate = date('Y-m-d H:m:n', strtotime('15 days', $borrowDate));
        $borrowExpectedReturnDate = \DateTime::createFromFormat('Y-m-d H:m:n', $borrowExpectedReturnDate);
        $this->borrowExpectedReturnDate = $borrowExpectedReturnDate;
        $this->borrowRealReturnDate = null;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBorrowDate(): ?\DateTimeInterface
    {
        return $this->borrowDate;
    }

    public function setBorrowDate(\DateTimeInterface $borrowDate): self
    {
        $this->borrowDate = $borrowDate;

        return $this;
    }

    public function getBorrowExpectedReturnDate(): ?\DateTimeInterface
    {
        return $this->borrowExpectedReturnDate;
    }

    public function setBorrowExpectedReturnDate(\DateTimeInterface $borrowExpectedReturnDate): self
    {
        $this->borrowExpectedReturnDate = $borrowExpectedReturnDate;

        return $this;
    }

    public function getBorrowRealReturnDate(): ?\DateTimeInterface
    {
        return $this->borrowRealReturnDate;
    }

    public function setBorrowRealReturnDate(?\DateTimeInterface $borrowRealReturnDate): self
    {
        $this->borrowRealReturnDate = $borrowRealReturnDate;

        return $this;
    }

    public function getBook(): ?Book
    {
        return $this->book;
    }

    public function setBook(?Book $book): self
    {
        $this->book = $book;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }

}
