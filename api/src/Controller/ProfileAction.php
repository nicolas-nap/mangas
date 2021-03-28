<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Serializer\SerializerInterface;

class ProfileAction
{
    public function __construct(
        private TokenStorageInterface $tokenStorage,
        private SerializerInterface $serializer,
    ) {
    }

    public function __invoke(): JsonResponse
    {
        $user = $this->tokenStorage->getToken()->getUser();

        $serializedUser = $this->serializer->serialize(
            $user,
            'json',
            ['groups' => ['read_user', 'timestampable']]
        );

        return new JsonResponse(json_decode($serializedUser, true));
    }
}