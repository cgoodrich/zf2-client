<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;
use Users\Entity\User;

class Comment
{
    protected $id = null;
    protected $user = null;
    protected $comment = null;
    protected $createdAt = null;
    protected $updatedAt = null;

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setUser($user)
    {
        $hydrator = new ClassMethods();

        $this->user = $hydrator->hydrate($user, new User());
    }

    public function setComments($comments)
    {
        $this->comments = $comments;
    }

    public function setCreatedAt($createdAt)
    {
        $this->createdAt = new \DateTime($createdAt);
    }

    public function setUpdatedAt($updatedAt)
    {
        $this->updatedAt = new \DateTime($updatedAt);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }
}
