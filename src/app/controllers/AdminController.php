<?php

use Phalcon\Mvc\Controller;

session_start();

class AdminController extends Controller
{
    public function indexAction()
    {
        // redirected to view
        $output = $this->mongo->books->find();
        $data = [];
        foreach ($output as $value) {
            $data[] = $value;
        }
        $this->view->data = json_encode($data);
    }

    public function showAllUsersAction()
    {
        $output = $this->mongo->users->find();
        $data = [];
        foreach ($output as $value) {
            $data[] = $value;
        }
        $this->view->data = json_encode($data);
    }

    public function showAllOrdersAction()
    {
        $output = $this->mongo->order->find();
        $data = [];
        foreach ($output as $value) {
            $data[] = $value;
        }
        $this->view->data = json_encode($data);
    }
}
