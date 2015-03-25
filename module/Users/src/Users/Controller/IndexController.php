<?php

namespace Users\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Api\Client\ApiClient;
use Users\Forms\SignupForm;
use Users\Entity\User;
use Zend\Validator\File\Size;
use Zend\Validator\File\IsImage;

class IndexController extends AbstractActionController
{
    /**
     * Signup if not logged in
     *
     * @return void
     */
    public function indexAction()
    {
        // set the layout page
        $this->layout('layout/signup');

        $viewData = array();
        // create new signup form
        $signupForm = new SignupForm();
        $signupForm->setAttribute('action', $this->url()->fromRoute('users-signup'));

        $request = $this->getRequest();
        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            $signupForm->setInputFilter(User::getInputFilter());
            $signupForm->setData($data);

            // validate the $signupForm
            if ($signupForm->isValid()) {
                // place the files in an array
                $files = $request->getFiles()->toArray();
                // get the data, too
                $data = $signupForm->getData();
                // set the 'avatar' array key
                $data['avatar'] = $files['avatar']['name'] != '' ? $files['avatar']['name'] : null;

                // if avatar exists, then save it
                if ($data['avatar'] !== null) {
                    $size = new Size(array('max' => 2048000));
                    $isImage = new IsImage();
                    $filename = $data['avatar'];

                    $adapter = new \Zend\File\Transfer\Adapter\Http();
                    $adapter->setValidators(array($size, $isImage), $filename);

                    if (!$adapter->isValid($filename)){
                        $errors = array();
                        foreach($adapter->getMessages() as $key => $row) {
                            $errors[] = $row;
                        }
                        $signupForm->setMessages(array('avatar' => $errors));
                    }

                    $destPath = 'data/tmp/';
                    $adapter->setDestination($destPath);

                    $fileinfo = $adapter->getFileInfo();
                    preg_match('/.+\/(.+)/', $fileinfo['avatar']['type'], $matches);
                    $newFilename = sprintf('%s.%s', sha1(uniqid(time(), true)), $matches[1]);

                    $adapter->addFilter('File\Rename',
                        array(
                            'target' => $destPath . $newFilename,
                            'overwrite' => true,
                        )
                    );

                    if ($adapter->receive($filename)) {
                        $data['avatar'] = base64_encode(
                            file_get_contents(
                                $destPath . $newFilename
                            )
                        );

                        if (file_exists($destPath . $newFilename)) {
                            unlink($destPath . $newFilename);
                        }
                    }
                }

                unset($data['repeat_password']);
                unset($data['csrf']);
                unset($data['register']);

                // register the user
                $response = ApiClient::registerUser($data);

                if ($response['result'] == true) {
                    $this->flashMessenger()->addMessage('Account created!');
                    return $this->redirect()->toRoute('wall', array('username' => $data['username']));
                }
            }
        }

        $viewData['signupForm'] = $signupForm;
        return $viewData;
    }
}
