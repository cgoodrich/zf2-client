<?php

namespace Wall\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Users\Entity\User;
use Wall\Forms\TextStatusForm;
use Wall\Forms\ImageForm;
use Wall\Entity\Status;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;
use Api\Client\ApiClient as ApiClient;

class IndexController extends AbstractActionController
{
    /*
     * We have one action: indexAction().
     *
     * This action is the one in charge of wall requests.
     */
    public function indexAction()
    {
        $viewData = array();
        $flashMessenger = $this->flashMessenger();

        // we retrieve the username from the URL
        $username = $this->params()->fromRoute('username');
        // pass the username to the layout
        $this->layout()->username = $username;
        // use the ApiClient to get the data of the wall by
        // calling getWall()
        $response = ApiClient::getWall($username);

        // if we have a response, then decode it.
        if ($response !== FALSE) {
            // hydrator is a component from Stdlib in ZF2
            // it is used to populate objects with data.

            /*
             * We use a ClassMethods() hydrator, which means that when
             * the hydrator tries to populate, an object will try to
             * use setter functions inside the object.
             */
            $hydrator = new ClassMethods();

            // hydrate a new User entity (Users\Entity\User) with the
            // $response object.
            $user = $hydrator->hydrate($response, new User());
        } else {
            // otherwise, set a 404 code and return if the $response is FALSE.
            $this->getResponse()->setStatusCode(404);
            return;
        }


        // Get the request object
        $request = $this->getRequest();
        // Create a new instance of TextStatusForm
        $statusForm = new TextStatusForm();
        // Create a new instance of ImageForm
        $imageForm = new ImageForm();
        // Check if we are posting any data.
        if ($request->isPost()) {
            // If it is a POST, then convert the data to an Array.
            $data = $request->getPost()->toArray();

            // Check if it is a 'status' entry.
            if (array_key_exists('status', $data)) {
                /*
                 * If it is a status entry, then pass the form, user and data
                 * to the createStatus() method in order to create
                 * the status.
                 */
                $result = $this->createStatus($statusForm, $user, $data);
            }
            if (!empty($request->getFiles()->image)) {
                $data = array_merge_recursive(
                    $data,
                    $request->getFiles()->toArray()
                );
                $result = $this->createImage($imageForm, $user, $data);
            }

            /*
             * After calling the createStatus() method, check the returned
             * value to see if the data was sotred correctly or a form
             * containing errors has been returned.
             */
            switch (true) {
            case $result instanceOf TextStatusForm:
                $statusForm = $result;
                break;
            case $result instanceOf ImageForm:
                $imageForm = $result;
                /*
                if ($result instanceOf ImageForm) {
                    $imageForm = $result;
                } else {
                    if ($result == true) {
                        $this->flashMessenger()->addSuccessMessage(
                            'Your image has been posted!'
                        );

                        return $this->redirect()->toRoute(
                            'wall',
                            array('username' => $user->getUsername())
                        );
                    } else {
                        return $this->getResponse()->setStatusCode(500);
                    }
                }
                 */
                break;
            default:
                /*
                 * If the post has been a success, add a success message
                 * with the flash messenger and redirect back to the wall
                 * to show the new content.
                 */
                if ($result == true) {
                    $flashMessenger->addSuccessMessage('New status posted!');
                    return $this->redirect()->toRoute('wall', array(
                        'username' => $user->getUsername()));
                } else {
                    // In case of an error, force a 500 status code.
                    // @NOTE: This is ugly, should be improved to be handled
                    // properly in a production environment.
                    return $this->getResponse()->setStatusCode(500);
                }
                break;
            }
        }

        /*
         * We pass the data through to the view and configure the action
         * of the form.
         *
         * Use the setAttribute() method of the form to set the action
         * we want for the form, which is getUsername().
         * After that, just add the form to the array that we will
         * return to the view.
         *
         * In this case, the $user->getUsername() method can be inspected
         * in the class Users\Entity\User.
         */
        $statusForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));
        $imageForm->setAttribute('action', $this->url()->fromRoute('wall', array('username' => $user->getUsername())));

        $viewData['profileData'] = $user;
        $viewData['textContentForm'] = $statusForm;
        $viewData['imageContentForm'] = $imageForm;

        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }

        // return an array containing $viewData to the view.
        return $viewData;
    }

    /**
     * Upload a new image
     *
     * @param Zend\Form\Form $form
     * @param Users\Entity\User $user
     * @param array $data
     */
    protected function createImage($form, $user, $data)
    {
        /*
         * Check if there is an error while uploading the file.
         * If we find one, set the value to null to force
         * the form validation to fail.
         */
        if ($data['image']['error'] != 0) {
            $data['image'] = NULL;
        }

        $form->setData($data);

        /*
         * This will fail (IsImage()) if the above result is NULL.
         */
        $size = new Size(array('max' => 2048000));
        $isImage = new IsImage();
        $filename = $data['image']['name'];

        /*
         * This will take care of receiving the file and
         * validating it, using the validators we configured
         * as shown below.
         */
        // Create an instance of the HTTP validator.
        $adapter = new \Zend\File\Transfer\Adapter\Http();
        // Set the validators we created above.
        // Pass the name of the file as the second parameter.
        $adapter->setValidators(array($size, $isImage), $filename);

        // Validate the file itself
        if (!$adapter->isValid($filename)){
            $errors = array();
            // Iterate over any errors to add them as an error message
            // to the form.
            foreach($adapter->getMessages() as $key => $row) {
                $errors[] = $row;
            }
            $form->setMessages(array('image' => $errors));
        }

        if ($form->isValid()) {
            $destPath = 'data/tmp/';
            // Set the temporary destination for the file.
            // After the file is uploaded, we will remove it from this folder.
            $adapter->setDestination($destPath);

            /*
             * Get info related to the image from the adapter
             * Do a regular expression to get the file extension
             * Generate a new filename utilizing the same method as the API.
             *
             * In this case, though, we reuse the original file extension.
             */
            $fileinfo = $adapter->getFileInfo();
            preg_match('/.+\/(.+)/', $fileinfo['image']['type'], $matches);
            $extension = $matches[1];
            $newFilename = sprintf('%s.%s', sha1(uniqid(time(), true)), $extension);

            /*
             * Add a filter to the file adapter to rename the file when we
             * receive it.
             *
             * Set the target of the file passing the path and the filename
             * and then force it to overwrite.
             *
             * In theory, we will never have to overwrite filenames because
             * they are unique.
             */
            $adapter->addFilter('File\Rename',
                array(
                    'target' => $destPath . $newFilename,
                    'overwrite' => true,
                )
            );

            /*
             * Receive the file, move it from teh temp folder PHP uses to the
             * one that will execute the filters we attached.
             */
            if ($adapter->receive($filename)) {
                // build an array that will be the container for the data.
                $data = array();
                // read the file contents, base64 encode as a string
                // on the $data array.
                $data['image'] = base64_encode(
                    file_get_contents(
                        $destPath . $newFilename
                    )
                );
                // add the 'user_id' to $data['user_id']
                $data['user_id'] = $user->getId();

                // Delete the temporary file
                unlink($destPath . $newFilename);

                // Use ApiClient to send the data to our API.
                $response = ApiClient::postWallContent($user->getUsername(), $data);
                // Return the API response.
                return $response['result'];
            }
        }

        return $form;
    }

    protected function createStatus($form, $user, array $data)
    {
        $form->setInputFilter(Status::getInputFilter());
        return $this->processSimpleForm($form, $user, $data);
    }

    /*
     * Handle the creation of wall entries coming from forms, where we
     * don't need to do a special treatment of the information.
     *
     * After validating the contents of the request against the filters
     * and validations as they are configured, process data to add and remove
     * information we need / do not need.
     *
     * After that, we call ApiClient::postWallContent() to send the data
     * to the API.
     */
    protected function processSimpleForm($form, $user, array $data)
    {
        $form->setData($data);

        if ($form->isValid()) {
            $data = $form->getData();
            $data['user_id'] = $user->getId();
            unset($data['submit']);
            unset($data['csrf']);

            $response = ApiClient::postWallContent($user->getUsername(), $data);
            return $response['result'];
        }
        return $form;

    }
}
