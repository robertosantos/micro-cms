<?php

namespace MicroCms\Model\Repository;

use Doctrine\ORM\EntityRepository;

class Post extends EntityRepository
{
    public function getPost($limit = 10, $offSet = 0)
    {
        $posts = $this->createQueryBuilder('post')
            ->setMaxResults($limit)
            ->setFirstResult($offSet)
            ->orderBy('post.id', 'DESC')
            ->getQuery()
            ->execute();

        return $posts;
    }
}
