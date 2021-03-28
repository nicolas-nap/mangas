<?php

namespace App\Entity;

use App\Controller\ProfileAction;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use App\Entity\Traits\TimestampableEntity;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiFilter;
use ApiPlatform\Core\Bridge\Doctrine\Orm\Filter\SearchFilter;
use ApiPlatform\Core\Annotation\ApiProperty;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="userType", type="string")
 * @ORM\DiscriminatorMap({"admin" = "Admin"})
 */
#[ApiResource(
    normalizationContext: [ "groups" => [ "read_user", "timestampable" ] ],
    denormalizationContext: [ "groups" => [ "write_user" ] ],
    collectionOperations: [
        "post" => [
            "validation_groups" => [ "postValidation" ]
        ],
        "get" => [
            "security" => "is_granted('ROLE_ADMIN')",
        ],
        "get_profile" => [
            "method" => "GET",
            "path" => "/me",
            "controller" => ProfileAction::class,
        ]
    ],
    itemOperations: [
        "put" => [
            "security" => "is_granted('ROLE_ADMIN') or object.id == user.id",
        ],
        "get" => [
            "security" => "is_granted('ROLE_ADMIN') or object.id == user.id",
        ],
        "delete" => [
            "security" => "is_granted('ROLE_ADMIN') or object.id == user.id",
        ]
    ],
    iri: "https://schema.org/Person"
)]
#[ApiFilter(SearchFilter::class, properties: [ 'email' => 'ipartial' ])]
#[UniqueEntity(fields: ["email"])]
#[UniqueEntity(fields: ["userType"])]
abstract class User implements UserInterface
{
    use TimestampableEntity;

    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    #[Groups([ "read_user" ])]
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true, nullable=false)
     */
    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[Groups([ "read_user", "write_user" ])]
    #[ApiProperty(iri: "https://schema.org/email")]
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    #[Assert\NotBlank()]
    #[Groups([ "read_user" ])]
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @var string
     */
    #[Assert\NotBlank(groups:["postValidation"])]
    #[Assert\NotCompromisedPassword(skipOnError: true)]
    #[Groups([ "write_user" ])]
    public $plainPassword;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    #[Assert\NotBlank()]
    #[Groups([ "read_user", "write_user" ])]
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    #[Assert\NotBlank()]
    #[Groups([ "read_user", "write_user" ])]
    private $lastName;

    /**
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    #[Groups([ "read_user", "write_user" ])]
    private $userType;


    public function getId()
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
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

    public function getUserType(): ?string
    {
        return $this->userType;
    }

    public function setUserType(string $userType): self
    {
        $this->userType = $userType;

        return $this;
    }
}