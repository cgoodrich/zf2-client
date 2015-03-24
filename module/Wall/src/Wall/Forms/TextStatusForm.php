<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;

// Typically, a form will extend the base Form class.
class TextStatusForm extends Form
{
    /*
     * After that, everything can be accomplished in a
     * constructor.
     */
    public function __construct($name = null)
    {
        // initialize the parent class, passing the name
        // of the form as the first parameter.
        parent::__construct('text-content');

        // start setting attributes of the form.
        $this->setAttribute('method', 'post');
        // set the CSS attributes in the Form class. We also use
        // view helpers to customize the HTML.
        $this->setAttribute('class', 'well input-append');

        /*
         * When we add an element to a form:
         * We usually only have to pass an array with the element
         * configuration.
         *
         * This array contains:
         * 1. The name of the element
         * 2. Type of element (can be just simple string
         *  that ZF2 will understand or the fully qualified
         *  class name of the element and some attributes that
         *  will be used while printing the HTML of the object.
         */
        $this->add(array(
            'name' => 'status',
            // add a textarea element for the 'status' field.
            'type'  => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'span11',
                'placeholder' => 'How are you?'
            ),
        ));

        /* @NOTE: CSRF is a type of malicious exploit whereby
         * unauthorized commands are transmitted from a user
         * that the website trusts.
         *
         * Add a hidden field to protect us from Cross Site
         * Request Forgery (CSRF). ZF2 will do the job of
         * generating the CSRF token and validating it for us.
         *
         */
        $this->add(new Element\Csrf('csrf'));
        $this->add(array(
            // add a 'submit' button.
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Submit',
                'class' => 'btn'
            ),
        ));
    }
}
