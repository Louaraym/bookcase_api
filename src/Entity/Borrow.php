<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\BorrowRepository")
 * @ApiResource(
 *     itemOperations={
 *        "get"={
 *          "access_control"="(is_granted('ROLE_ADHERENT') && object.getAdherent() == user) || is_granted('ROLE_MANAGER')",
 *          "access_control_message"="you don't have the right to access this resource",
 *          },
 *        "put"={
 *          "access_control"="is_granted('ROLE_MANAGER')",
 *          "access_control_message"="you don't have the right to access this resource",
 *          "denormalization_context"={"groups"={"put_role_manager"}}
 *           },
 *        "delete"={
 *          "access_control"="is_granted('ROLE_ MANAGER')",
 *          "access_control_message"="you don't have the right to access this resource"
 *            }
 *     },
 *     collectionOperations={
 *        "get"={
 *          "path"="/borrows",
 *          "access_control"="(is_granted('ROLE_ADHERENT') && object.getAdherent() == user) || is_granted('ROLE_MANAGER')",
 *          "access_control_message"="you don't have the right to access this resource",
 *          },
 *        "post"={
 *          "access_control"="(is_granted('ROLE_ADHERENT') && object.getAdherent() == user) || is_granted('ROLE_MANAGER')",
 *          "access_control_message"="you don't have the right to access this resource",
 *          }
 *     }
 * )
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
     * @Groups({"put_role_admin"})
     */
    private $borrowDate;

    /**
     * @ORM\Column(type="datetime")
     * @Groups({"put_role_admin"})
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
     */
    private $book;

    /**
     * @ORM\ManyToOne(targetEntity="App\Entity\Adherent", inversedBy="borrows")
     * @ORM\JoinColumn(nullable=false)
     */
    private $adherent;

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

    public function getAdherent(): ?Adherent
    {
        return $this->adherent;
    }

    public function setAdherent(?Adherent $adherent): self
    {
        $this->adherent = $adherent;

        return $this;
    }
}
