<?php

namespace App\EventSubscriber;

use App\Entity\Interfaces\UserIsOwnerInterface;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\EventSubscriber\EventSubscriberInterface;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class UserIsOwnerListener implements EventSubscriberInterface
{
    public function __construct(private TokenStorageInterface $tokenStorage)
    {
    }

    public function prePersist(LifecycleEventArgs $args): void
    {
        $entity = $args->getEntity();
        if (!$entity instanceof UserIsOwnerInterface) {
            return;
        }

        if (!$this->tokenStorage->getToken() || !$this->tokenStorage->getToken()->getUser() instanceof User) {
            return;
        }

        $entity->setUser($this->tokenStorage->getToken()->getUser());
    }

    public function getSubscribedEvents(): array
    {
        return ['prePersist'];
    }
}