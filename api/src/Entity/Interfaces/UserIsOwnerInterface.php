<?php

namespace App\Entity\Interfaces;

use App\Entity\User;

interface UserIsOwnerInterface
{
    public function setUser(?User $user): self;
}
