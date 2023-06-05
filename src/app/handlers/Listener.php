<?php
namespace handler\Listener;

use Phalcon\Acl\Adapter\Memory;
use Phalcon\Mvc\Application;
use Phalcon\Events\Event;
use Phalcon\Mvc\Dispatcher;
use Phalcon\Di\Injectable;

session_start();
class Listener extends injectable
{
    public function beforeHandleRequest(Event $event, Application $app, Dispatcher $dis)
    {
        $acl = new Memory();
        /*
         * Add the roles
         */

        $acl->addRole('admin');
        $acl->addRole('user');
        $acl->addRole('guest');
        /*
         * Add the Components
         */

        $acl->addComponent(
            'index',
            [
                'index',
                'register',
                'login',
                'dologin'
            ]
        );
        $acl->addComponent(
            'book',
            [
                'index',
                'addToCart',
                'myCart',
                'showCart',
                'removeFromCart',
                'checkout',
                'myOrder',
                'addBook',
                'myBooks',
            ]
        );
        $acl->addComponent(
            'admin',
            [
                'index',
                'showAllUsers',
                'showAllOrders'
            ]
        );

        $acl->allow('admin', '*', '*');
        $acl->allow('*', 'index', ['index', 'login', 'dologin']);
        $acl->allow('user', 'book', '*');

        $controller = $dis->getControllerName();
        $action = $dis->getActionName();
        if ($controller == '') {
            $controller = 'index';
        }
        if ($action == '') {
            $action = 'index';
        }
        $role = $_SESSION['role'];
        if ($role == '') {
            $role = 'guest';
        }
        if (true === $acl->isAllowed($role, $controller, $action)) {
            if (file_exists(APP_PATH . "/controllers/$controller/")) {
                $_SESSION['currUser'] = $tokenReceived;
                $this->response->redirect($controller / $action);
            } else {
                echo ('Access Granted') . ':)';
            }
        } else {
            echo ('Access denied') . ':(';
            die;
        }
    }
}
