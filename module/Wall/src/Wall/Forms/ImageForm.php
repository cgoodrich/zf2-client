<?php
namespace Wall\Forms;

use Zend\Form\Element;
use Zend\Form\Form;
// Allows us to use getInputFilterSpecification()
use Zend\InputFilter\InputFilterProviderInterface;

/*
 * We create a new form for image input.
 *
 * Difference from other forms:
 * Store the configuration of the input filters.
 *
 * In order to autoconfigure the form with config, we will implement
 * a new interface called InputFilterProviderInterface, which will
 * force us to implement a method called getInputFIlterSpecification()
 * which in turn will return the filter configurations when called.
 */
class ImageForm extends Form implements InputFilterProviderInterface
{
    public function __construct($name = null)
    {
        parent::__construct('image-content');

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'well input-append');

        // Adds elements to the forms through the method
        // shown below.
        $this->prepareElements();
    }

    public function prepareElements()
    {
        $this->add(array(
            'name' => 'image',
            // The image is of type 'File'
            'type'  => 'Zend\Form\Element\File',
            'attributes' => array(
                'class' => 'span11',
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

    /*
     * This is the method we should create to implement the
     * InputFilterProviderInterface. It shoudl return the input
     * filter configuration array.
     */
    public function getInputFilterSpecification()
    {
        return array(
            'image' => array(
                /*
                 * We just mark the field as required because we validate
                 * the contents of the uploaded file manually on the server
                 * side.
                 */
                'required' => true,
            )
        );
    }
}
