<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiResource;
use ApiPlatform\Core\Annotation\ApiSubresource;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ApiResource(
 *     normalizationContext={"groups"={"get_role_user"}},
 *     collectionOperations={
 *          "get"={
 *              "normalization_context"= {"groups"={"get_role_user"}}
 *          },
 *          "post"={
 *              "path"="/users/{id}",
 *              "access_control"="is_granted('ROLE_MANAGER')",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource",
 *              "denormalization_context"= {"groups"={"post_role_manager"}}
 *          },
 *          "statBorrowsNumberPerUser"={
 *           "method"="GET",
 *            "route_name"="users_borrows_number",
 *            "controller"=StatsController::class
 *          }
 *     },
 *     itemOperations={
 *        "getBorrowsNumber"={
 *           "method"="GET",
 *            "route_name"="user_borrows_count"
 *         },
 *          "get"={
 *              "path"="/users/{id}",
 *              "access_control"="(is_granted('ROLE_MANAGER') or is_granted('ROLE_USER') and object == user)",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource",
 *              "normalization_context"= {
 *                  "groups"={"get_role_user"}
 *              }
 *          },
 *          "put"={
 *              "path"="/user/{id}",
 *              "access_control"="(is_granted('ROLE_MANAGER') or is_granted('ROLE_USER') and object == user)",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource",
 *              "denormalization_context"= {
 *                  "groups"={"put_role_manager"}
 *              },
 *              "normalization_context"= {
 *                  "groups"={"get_role_user"}
 *              }
 *          },
 *          "delete"={
 *              "path"="/user/{id}",
 *              "access_control"="is_granted('ROLE_ADMIN')",
 *              "access_control_message"="Vous n'avez pas les droits d'accéder à cette ressource"
 *          }
 *     }
 * )
 * @ApiFilter(
 *      SearchFilter::class,
 *      properties={
 *          "mail": "exact"
 *      }
 * )
 * @UniqueEntity(
 *     fields={"email"},
 *     message="This email is already in use on that website."
 * )
 */
class User implements UserInterface
{
    public const ROLE_ADMIN = 'ROLE_ADMIN';
    public const ROLE_MANAGER = 'ROLE_MANAGER';
    public const ROLE_USER = 'ROLE_USER';
    public const ROLE_DEFAULT = 'ROLE_USER';

    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     * @Groups({"get_role_user","get_borrowsNumberPerUser"})
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_user","post_role_manager","put_role_manager","get_borrowNumberPerUser"})
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_user","post_role_manager","put_role_manager","get_borrowNumberPerUser"})
     */
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_user","post_role_manager","put_role_manager"})
     */
    private $address;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_user","post_role_manager","put_role_manager"})
     */
    private $cityCode;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"get_role_user","post_role_manager","put_role_admin"})
     */
    private $email;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups({"get_role_user","post_role_manager","put_role_manager"})
     */
    private $telephone;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups({"post_role_manager","put_role_admin"})
     */
    private $password;

    /**
     * @ORM\Column(type="json")
     * @Groups({"get_role_user","get_role_manager","post_role_admin","put_role_admin"})
     */
    private $roles = [];

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\Borrow", mappedBy="user")
     * @Groups({"get_role_user"})
     * @ApiSubresource
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

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(?string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getCityCode(): ?string
    {
        return $this->cityCode;
    }

    public function setCityCode(?string $cityCode): self
    {
        $this->cityCode = $cityCode;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getTelephone(): ?string
    {
        return $this->telephone;
    }

    public function setTelephone(?string $telephone): self
    {
        $this->telephone = $telephone;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
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
            $borrow->setUser($this);
        }

        return $this;
    }

    public function removeBorrow(Borrow $borrow): self
    {
        if ($this->borrows->contains($borrow)) {
            $this->borrows->removeElement($borrow);
            // set the owning side to null (unless already changed)
            if ($borrow->getuser() === $this) {
                $borrow->setuser(null);
            }
        }

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = self::ROLE_DEFAULT;

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @inheritDoc
     */
    public function getSalt()
    {
        // TODO: Implement getSalt() method.
    }

    /**
     * @inheritDoc
     */
    public function getUsername(): string
    {
        return $this->getEmail();
    }

    /**
     * @inheritDoc
     */
    public function eraseCredentials()
    {
        // TODO: Implement eraseCredentials() method.
    }
}
