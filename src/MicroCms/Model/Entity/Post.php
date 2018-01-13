<?php

namespace MicroCms\Model\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Post
 *
 * @Table(name="post", indexes={@Index(name="fk_post_user_idx", columns={"user_id"})})
 * @Entity(repositoryClass="MicroCms\Model\Repository\Post")
 */
class Post
{
    /**
     * @var string
     *
     * @Column(name="body", type="text", nullable=false)
     */
    private $body;

    /**
     * @var \DateTime
     *
     * @Column(name="created_at", type="datetime", nullable=true)
     */
    private $createdAt;

    /**
     * @var string
     *
     * @Column(name="title", type="string", length=400, nullable=false)
     */
    private $title;

    /**
     * @var string
     *
     * @Column(name="path", type="string", length=45, nullable=false)
     */
    private $path;

    /**
     * @var \DateTime
     *
     * @Column(name="updated_at", type="datetime", nullable=true)
     */
    private $updatedAt;

    /**
     * @var integer
     *
     * @Column(name="id", type="bigint")
     * @Id
     * @GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var \MicroCms\Model\Entity\User
     *
     * @ManyToOne(targetEntity="User", inversedBy="posts")
     * @JoinColumns({
     *   @JoinColumn(name="user_id", referencedColumnName="id")
     * })
     */
    private $user;

    public function __construct($user, $title, $path, $body)
    {
        $this->createdAt = new \DateTime('now');
        $this->user = $user;
        $this->title = $title;
        $this->body = $body;
        $this->path = $path;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     * @return Post
     */
    public function setBody(string $body): Post
    {
        $this->body = $body;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return Post
     */
    public function setCreatedAt(\DateTime $createdAt): Post
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     * @return Post
     */
    public function setTitle(string $title): Post
    {
        $this->title = $title;
        return $this;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param \DateTime $updatedAt
     * @return Post
     */
    public function setUpdatedAt(\DateTime $updatedAt): Post
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }


     /**
     * @return \MicroCms\Model\Entity\User
     */
    public function getUser(): User
    {
        return $this->user;
    }

    /**
     * @param \MicroCms\Model\Entity\User $user
     * @return Post
     */
    public function setUser(User $user): Post
    {
        $this->user = $user;
        return $this;
    }

    /**
     * @return string
     */
    public function getPath(): string
    {
        return $this->path;
    }

    /**
     * @param string $path
     * @return Post
     */
    public function setPath(string $path): Post
    {
        $this->path = $path;
        return $this;
    }
}
