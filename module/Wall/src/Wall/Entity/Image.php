<?php

namespace Wall\Entity;

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

    public function setId($id)
    {
        $this->id = (int)$id;
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
