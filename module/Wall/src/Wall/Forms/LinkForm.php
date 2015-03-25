<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

class LinkForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('link-content');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');

        $this->prepareElements();
    }

    public function prepareElements()
    {
        $this->add(array(
            'name' => 'url',
            /*
             * This includes the HTML5 element on the form and
             * automatically adds the Zend\Validate\Uri validator.
             *
             * Remember that if we use Zend\Form\Element\Url
             * we should also use the formUrl() view helper during
             * rendering.
             */
            'type'  => 'Zend\Form\Element\Url',
            'attributes' => array(
                'class' => 'span11',
                'placeholder' => 'Share a link!'
            ),
        ));
        $this->add(new Element\Csrf('csrf'));
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
