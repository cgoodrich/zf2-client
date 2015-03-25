<?php

namespace Wall\Entity;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\Stdlib\Hydrator\ClassMethods;

class Status
{
    protected $id = null;
    protected $userId = null;
    protected $status = null;
    protected $createdAt = null;
    protected $updatedAt = null;
    protected $comments = null;

    const COMMENT_TYPE_ID = 1;

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

    public function setStatus($status)
    {
        $this->status = $status;
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

    public function getStatus()
    {
        return $this->status;
    }

    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    public static function getInputFilter()
    {
        $inputFilter = new InputFilter();
        $factory = new InputFactory();

        $inputFilter->add($factory->createInput(array(
            // field name is 'status'
            'name'     => 'status',
            // it is a required field
            'required' => true,
            'filters'  => array(
                // strip the HTML tags
                array('name' => 'StripTags'),
                // strip trailing and leading whitespace
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    // set a StringLength validator.
                    'name'    => 'StringLength',
                    'options' => array(
                        'encoding' => 'UTF-8',
                        // minimum length of 1 character.
                        'min'      => 1,
                        // maximum length of 65535 characters.
                        'max'      => 65535,
                    ),
                ),
            ),
        )));

        return $inputFilter;
    }
}
