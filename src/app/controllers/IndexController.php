<?php

use Phalcon\Mvc\Controller;

session_start();

class IndexController extends Controller
{
    public function indexAction()
    {
        // redirected to view
    }

    public function registerAction()
    {
        // creating a new user, with name and email obtained by post method
        $_POST['uid'] = uniqid();
        $result = $this->mongo->users->insertOne($_POST);

        $success = $result->getInsertedCount();
        if ($success > 0) {
            $this->response->redirect('/index/login/');
        } else {
            $this->response->redirect('/index/signup/');
        }
    }

    public function loginAction()
    {
        // redirected to view
    }
    public function dologinAction()
    {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $result = $this->mongo->users->findOne(['email' => $email, 'password' => $password]);
        if ($result['email'] == $email && $email != '') {
            // credentials are correct
            $_SESSION['uid'] = $result['uid'];
            $_SESSION['role'] = $result['role'];
            if ($result['role'] == 'admin') {
                $this->response->redirect('/admin/');
            } else {
                $this->response->redirect('/book/');
            }
        } else {
            // invalid credentials
            echo "<h3>Invalid Credentials</h3>";
            die;
        }
    }
}
