<?php

namespace App\Entity;

use App\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use App\Repository\AdminRepository;
use ApiPlatform\Core\Annotation\ApiResource;

/**
 * @ORM\Entity
 */
#[ApiResource(
    normalizationContext: [ "groups" => [ "read_user", "timestampable" ] ],
    denormalizationContext: [ "groups" => [ "write_user" ] ],
    collectionOperations: [
        "post" => [
            "security" => "is_granted('ROLE_ADMIN')",
        ],
        "get" => [
            "security" => "is_granted('ROLE_ADMIN')",
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
class Admin extends User
{
    public function __construct()
    {
        $this->setRoles(['ROLE_ADMIN']);

        $this->setUserType('admin');
    }
}