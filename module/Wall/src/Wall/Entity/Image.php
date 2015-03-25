<?php

namespace Wall\Entity;

use Zend\Stdlib\Hydrator\Classmethods;

/*
 * $domain will be used in a method that generates the URL
 * of the image.
 *
 * We are passing the image to the API, so it should be accessible
 * via URL in order to see it.
 *
 * In a production project, you would typically serve this from a CDN
 * (content distribution network) or another cookieless domain.
 */

class Image
{
    public $domain = 'http://zf2-api/images/';

    protected $id = null;
    protected $userId = null;
    protected $filename = null;
    protected $createdAt = null;
    protected $updatedAt = null;
    protected $comments = null;

    const COMMENT_TYPE_ID = 2;

    public function setId($id)
    {
        $this->id = (int)$id;
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
    public function setUserId($userId)
    {
        $this->userId = (int)$userId;
    }

    public function setFilename($filename)
    {
        $this->filename = $filename;
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

    public function getFilename()
    {
        return $this->filename;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public function getUrl()
    {
        return $this->domain . $this->getFilename();
    }
}
