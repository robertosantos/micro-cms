<?php

namespace MicroCms\System;

use Doctrine\ORM\EntityManager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DoctrineFactory implements ServiceProviderInterface
{

    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param Container $pimple A container instance
     */
    public function register(Container $pimple)
    {
        $pimple['orm'] = $pimple->protect(function (EntityManager $entityManager) {
            return $entityManager;
        });
    }
}
