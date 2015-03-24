<?php

namespace Wall\Controller;

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

        // return $viewData['profileData'] (return an array containing the
        // $user object to the view).
        $viewData['profileData'] = $user;

        return $viewData;
    }
}
