<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;

class Link
{
    protected $id = null;
    protected $userId = null;
    protected $url = null;
    protected $title = null;
    protected $createdAt = null;
    protected $updatedAt = null;
    protected $comments = null;

    const COMMENT_TYPE_ID = 3;

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
    }

    /*
     * Setter for coments - iterate over comments, if any, and
     * populate a new Comment instance appending it to the
     * $comments property.
     */
    public function setComments($comments)
    {
        $hydrator = new ClassMethods();

        foreach($comments as $c) {
            // instantiate new Comment instance, hydrating
            // it with each Comment passed in to the setter.
            $this->comments[] = $hydrator->hydrate($c, new Comment());
        }
    }

    public function getComments()
    {
        return $this->comments;
    }

    public function getType()
    {
        return self::COMMENT_TYPE_ID;
    }
    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function setTitle($title)
    {
        $this->title = $title;
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

    public function getUserId()
    {
        return $this->userId;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function getTitle()
    {
        return $this->title;
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
