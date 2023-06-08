<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use phpDocumentor\Reflection\Types\Boolean;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]

#[UniqueEntity(fields: ['email'], message: 'Il existe déjà un compte avec cette email.')]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: "json")]
    private $roles = [];

    // prénom :
    #[ORM\Column(length: 255)]
    #[Assert\Length(
        max: 10,
        maxMessage: 'Votre prénom ne doit pas dépasser {{ limit }} caractères.',
    )]
    private ?string $firstname = null;
    // ---------------------------------------------------


    // nom :
    #[ORM\Column(length: 255)]
    #[Assert\Length(
        max: 10,
        maxMessage: 'Votre nom ne doit pas dépasser {{ limit }} caractères.',
    )]
    private ?string $lastname = null;
    // ---------------------------------------------------

    // email :
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: "L'email ne peut pas être vide")]
    private ?string $email = null;
    // ---------------------------------------------------

    // password :
    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 8,
        minMessage: 'Votre mot de passe doit être au minimun de {{ limit }} caractères.',
    )]

    private ?string $password = null;
    // ---------------------------------------------------


    // confirm password :
    #[ORM\Column(length: 255)]
    #[Assert\Length(
        min: 8,
        minMessage: 'Votre confirmation de mot de passe doit être au minimun de {{ limit }} caractères.',
    )]

    #[Assert\EqualTo(propertyPath: "confirm_password")]
    #[Assert\EqualTo(propertyPath: "password", message: "Votre mot de passe ne correspond pas")]

    public $confirm_password;

    #[ORM\Column]
    private ?bool $is_verified = false;
    // ---------------------------------------------------
    public function getId(): ?int
    {
        return $this->id;
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

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirm_password;
    }

    public function setConfirmPassword(string $confirm_password): self
    {
        $this->confirm_password = $confirm_password;

        return $this;
    }

    public function getRoles(): array
    {
        $roles = $this->roles;

        $roles[] = 'ROLE_ADMIN';

        return array_unique($roles);
    }

    public function setRoles($roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function someMethod(AuthorizationCheckerInterface $authChecker)
    {
        if ($authChecker->isGranted('ROLE_ADMIN')) {
            // L'utilisateur est administrateur
        } else {
            // L'utilisateur n'est pas administrateur
        }
    }

    public function eraseCredentials()
    {
    }
    public function getUserIdentifier(): string
    {
        return $this->email;
    }

    public function getIsVerified(): ?bool
    {
        return $this->is_verified;
    }

    public function setIsVerified(bool $is_verified): self
    {
        $this->is_verified = $is_verified;

        return $this;
    }
}
