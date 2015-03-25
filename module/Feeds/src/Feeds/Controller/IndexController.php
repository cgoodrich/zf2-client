<?php

namespace Feeds\Controller;

use Zend\Mvc\Controller\AbstractActionController;
// hydrate with class getters and setters from the Entity objects.
use Zend\Stdlib\Hydrator\ClassMethods;
// we are going to have navigation now
use Zend\Navigation\Navigation;
// @NOTE: What does this do?
use Zend\Navigation\Page\AbstractPage;
// we are going to use the Paginator
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\ArrayAdapter;

// Application-specific classes to include.
use Api\Client\ApiClient;
use Users\Entity\User;
use Feeds\Entity\Feed;
use Feeds\Forms\SubscribeForm;
use Feeds\Forms\UnsubscribeForm;

class IndexController extends AbstractActionController
{
    /**
     * Get the feed list and the posts of the feed we are looking at now
     *
     * @return void
     */
    public function indexAction()
    {
        $viewData = array();

        $flashMessenger = $this->flashMessenger();

        $username = $this->params()->fromRoute('username');
        $this->layout()->username = $username;

        $currentFeedId = $this->params()->fromRoute('feed_id');

        /*
         * Get the wall for the Username
         */
        $response = ApiClient::getWall($username);
        if ($response !== FALSE) {
            $hydrator = new ClassMethods();

            $user = $hydrator->hydrate($response, new User());
        } else {
            $this->getResponse()->setStatusCode(404);
            return;
        }

        /*
         * We need to instantiate the forms that we need
         */
        $subscribeForm = new SubscribeForm();
        $unsubscribeForm = new UnsubscribeForm();
        // Set the 'submit' action for the forms.
        $subscribeForm->setAttribute('action', $this->url()->fromRoute('feeds-subscribe', array('username' => $username)));
        $unsubscribeForm->setAttribute('action', $this->url()->fromRoute('feeds-unsubscribe', array('username' => $username)));

        // populate with getters/setters in the methods below.
        $hydrator = new ClassMethods();
        // get the user's feeds with ApiClient.
        $response = ApiClient::getFeeds($username);
        $feeds = array();
        // for each feed
        foreach ($response as $r) {
            // Set up the $feeds array using the $response
            $feeds[$r['id']] = $hydrator->hydrate($r, new Feed());
        }

        if ($currentFeedId === null && !empty($feeds)) {
            $currentFeedId = reset($feeds)->getId();
        }

        /*
         * Create a new instance of Zend\Navigation\Navigation
         * and add pages to it based on the feeds that a user has.
         *
         * The AbstractPage factory will create pages of the MVC
         * type, meaning tha thtey are tied to routes or pairs of
         * controllers/actions.
         *
         * The component will detect the element of the menu that is actively
         * looking at the URL of the request by itself, too.
         */
        $feedsMenu = new Navigation();
        $router = $this->getEvent()->getRouter();
        $routeMatch = $this->getEvent()->getRouteMatch()->setParam('feed_id', $currentFeedId);
        foreach ($feeds as $f) {
            $feedsMenu->addPage(
                AbstractPage::factory(array(
                    // We are dealing with a ZF2 Reader instance,
                    // so we use its methods to get properties.
                    'title' => $f->getTitle(),
                    'icon' => $f->getIcon(),
                    'route' => 'feeds',
                    // specify the RouteMatch object that will be used
                    // to test each page against it and decide which one
                    // is active.
                    'routeMatch' => $routeMatch,
                    // Specify the router where all the routes are stored
                    'router' => $router,
                    'params' => array('username' => $username, 'feed_id' => $f->getId())
                ))
            );
        }

        $currentFeed = $currentFeedId != null? $feeds[$currentFeedId] : null;

        /*
         * If we have a $currentFeed, we prepare a Paginator.
         */
        if ($currentFeed != null) {
            /*
             * Prepare a Paginator component to show the posts
             * with a paginator. We have used the ArrayAdapter object, which
             * allows us to pass an array of content to be paginated.
             *
             * After that, we configure the paginator and assign it to the
             * view.
             */
            $paginator = new Paginator(new ArrayAdapter($currentFeed->getArticles()));
            $paginator->setItemCountPerPage(5);
            $paginator->setCurrentPageNumber($this->params()->fromRoute('page'));
            $viewData['paginator'] = $paginator;
            $viewData['feedId'] = $currentFeedId;
        }

        // Set the 'feed_id' value to the $currentFeedId in the
        // $unsubscribeForm
        $unsubscribeForm->get('feed_id')->setValue($currentFeedId);

        $viewData['subscribeForm'] = $subscribeForm;
        $viewData['unsubscribeForm'] = $unsubscribeForm;
        $viewData['username'] = $username;
        $viewData['feedsMenu'] = $feedsMenu;
        $viewData['profileData'] = $user;
        $viewData['feed'] = $currentFeed;

        if ($flashMessenger->hasMessages()) {
            $viewData['flashMessages'] = $flashMessenger->getMessages();
        }

        return $viewData;
    }

    /*
     * @TODO: Implement isValid() calls (form validation) and also
     * improve error handling. In addition, the subscribe and unsubscribe
     * actions are substantially the same and could be refactored into
     * a single method.
     */

    /**
     * Add a new subscription for the specified user
     *
     * @return void
     */
    public function subscribeAction()
    {
        $username = $this->params()->fromRoute('username');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            $response = ApiClient::addFeedSubscription($username, array('url' => $data['url']));

            if ($response['result'] == TRUE) {
                $this->flashMessenger()->addMessage('Subscribed successfully!');
            } else {
                return $this->getResponse()->setStatusCode(500);
            }
        }

        return $this->redirect()->toRoute('feeds', array('username' => $username));
    }

    /**
     * Unsubscribe a user from a specific feed
     *
     * @return void
     */
    public function unsubscribeAction()
    {
        $username = $this->params()->fromRoute('username');
        $request = $this->getRequest();

        if ($request->isPost()) {
            $data = $request->getPost()->toArray();

            $response = ApiClient::removeFeedSubscription($username, $data['feed_id']);

            if ($response['result'] == TRUE) {
                $this->flashMessenger()->addMessage('Unsubscribed successfully!');
            } else {
                return $this->getResponse()->setStatusCode(500);
            }
        }

        return $this->redirect()->toRoute('feeds', array('username' => $username));
    }
}
