<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class CommentForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('comment-content');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');

        $this->add(array(
            'name' => 'comment',
            'type'  => 'Zend\Form\Element\Text',
            'attributes' => array(
                'class' => 'span11',
                'placeholder' => 'Write a comment here...'
            ),
        ));
        /*
         * We have a couple of hidden form fields.
         *
         * They hold:
         * 1. The type of content we are commenting
         * 2. The ID of the entry we are commenting.
         *
         * Thanks to these hidden fields, the API will be
         * able to associate
         * the comment with the corresponding entry.
         */
        $this->add(array(
            'name' => 'type',
            'type' => 'Zend\Form\Element\Hidden',
        ));
        $this->add(array(
            'name' => 'entry_id',
            'type' => 'Zend\Form\Element\Hidden',
        ));
        $this->add(array(
            'name' => 'csrf',
            'type' => 'Zend\Form\Element\Csrf'
        ));
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'class' => 'btn'
            ),
        ));
    }
}
