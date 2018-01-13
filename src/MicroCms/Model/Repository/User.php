<?php

namespace MicroCms\Model\Repository;

use Doctrine\ORM\EntityRepository;

class User extends EntityRepository
{

    public function isValid(\MicroCms\Model\Entity\User $user)
    {
        $user = $this->findOneBy(
            [
                "name"=> $user->getName(),
                "password"=> $user->getPassword()
            ]
        );

        if (!($user instanceof \MicroCms\Model\Entity\User)) {
            return false;
        }
        return $user;
    }
}
