<?php

namespace Users\Entity;

use Zend\Stdlib\Hydrator\ClassMethods;
use Wall\Entity\Status;

class User
{
    const GENDER_MALE = 1;

    protected $id;
    protected $username;
    protected $name;
    protected $surname;
    protected $avatar;
    protected $bio;
    protected $location;
    protected $gender;
    protected $createdAt = null;
    protected $updatedAt = null;
    /*
     * $feed will contain an array of entries. For now, they
     * will be the Statuses object.
     */
    protected $feed = array();

    public function getFeed()
    {
        return $this->feed;
    }

    public function setFeed($feed)
    {
        /*
         * New ClassMethod hydrator will populate the Status
         * object based on the data we get from the API
         * using the setters defined in the entity.
         *
         * After the object is populated, we just store it
         * on the array we created before as a property.
         */
        $hydrator = new ClassMethods();
        foreach($feed as $entry) {
            if (array_key_exists('status', $entry)) {
                $this->feed[] = $hydrator->hydrate(
                    $entry, new Status()
                );
            }
        }
    }

    public function setId($id)
    {
        $this->id = (int)$id;
    }

    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function setSurname($surname)
    {
        $this->surname = $surname;
    }

    public function setAvatar($avatar)
    {
        $this->avatar = $avatar;
    }

    public function setBio($bio)
    {
        $this->bio = $bio;
    }

    public function setLocation($location)
    {
        $this->location = $location;
    }

    public function setGender($gender)
    {
        $this->gender = (int)$gender;
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

    public function getUsername()
    {
        return $this->username;
    }

    public function getName()
    {
        return $this->name;
    }

    public function getSurname()
    {
        return $this->surname;
    }

    public function getBio()
    {
        return $this->bio;
    }

    public function getLocation()
    {
        return $this->location;
    }

    public function getGender()
    {
        return $this->gender;
    }

    public function getAvatar()
    {
        return $this->avatar;
    }

    public function getGenderString()
    {
        return $this->gender == self::GENDER_MALE? 'Male' : 'Female';
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
