<?php

namespace Wall\Controller;

use Wall\Forms\TextStatusForm;
use Wall\Entity\Status;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\Stdlib\Hydrator\ClassMethods;
use Users\Entity\User as User;
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
        $statusForm = new TextStatusForm;

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

            /*
             * After calling the createStatus() method, check the returned
             * value to see if the data was sotred correctly or a form
             * containing errors has been returned.
             */
            switch (true) {
            case $result instanceOf TextStatusForm:
                $statusForm = $result;
                break;
            default:
                /*
                 * If the post has been a success, add a success message
                 * with the flash messenger and redirect back to the wall
                 * to show the new content.
                 */
                if ($result == true) {
                    $flashMessenger->addSuccessMessage('New content posted!');
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
        $statusForm->setAttribute('action', $this->url()->fromRoute('wall',
            array('username' => $user->getUsername())));
        $viewData['profileData'] = $user;
        $viewData['textContentForm'] = $statusForm;

        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }

        // return an array containing $viewData to the view.
        return $viewData;
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

            $response = ApiClient::postWallContent(
                $user->getUsername(), $data
            );
            return $response['result'];
        }
        return $form;

    }
}
