<?php

use Phalcon\Mvc\Controller;

session_start();
class BookController extends Controller
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

    public function addToCartAction()
    {
        $arr = [
            "oid" => uniqid(),
            "bid" => $_GET['bid'],
            "uid" => $_SESSION['uid'],
            "quantity" => 1
        ];
        $this->mongo->cart->insertOne($arr);
        $this->response->redirect('/book/showCart');
    }

    public function myCart()
    {
        $output = $this->mongo->cart->find(['uid' => $_SESSION['uid']]);

        $data = [];
        foreach ($output as $value) {
            $data[] = $value;
        }
        return $data;
    }
    public function showCartAction()
    {
        $data = $this->myCart();
        $this->view->data = json_encode($data);
    }

    public function removeFromCartAction()
    {
        $this->mongo->cart->deleteOne(['oid' => $_GET['oid']]);
        $this->response->redirect('/book/showCart');
    }

    public function checkoutAction()
    {
        $uid = $_SESSION['uid'];
        $data = $this->myCart();
        foreach ($data as $value) {
            $arr = [
                "oid" => $value['oid'],
                "bid" => $value['bid'],
                "uid" => $uid,
                "quantity" => $value['quantity']
            ];
            $this->mongo->order->insertOne($arr);
            $this->mongo->cart->deleteOne(['oid' => $value['oid']]);
        }

        $this->response->redirect('/book/myOrder');
    }

    public function myOrderAction()
    {
        $output = $this->mongo->order->find(['uid' => $_SESSION['uid']]);
        $data = [];
        foreach ($output as $value) {
            $data[] = $value;
        }
        $this->view->data = json_encode($data);
    }

    public function addAction()
    {
        // redirected to view
    }

    public function addBookAction()
    {
        $_POST['bid'] = uniqid();
        $_POST['uid'] = $_SESSION['uid'];
        $this->mongo->books->insertOne($_POST);
        $this->response->redirect('/book/');
    }

    public function myBooksAction()
    {
        $mybooks = $this->mongo->books->find(['uid' => $_SESSION['uid']]);
        $data = [];
        foreach ($mybooks as $value) {
            // find my book ids
            $bid = $value['bid'];
            $data[$bid] = [];
            $data[$bid]['title'] = $value['title'];
            $data[$bid]['buyer'] = [];
            $orders = $this->mongo->order->find(['bid' => $bid]);
            // find all the order that contain my books
            foreach ($orders as $order) {
                $user = $order['uid'];
                // find user details
                $output = $this->mongo->users->findOne(['uid' => $user]);
                $data[$bid]['buyer']['name'] = $output['name'];
                $data[$bid]['buyer']['email'] = $output['email'];
            }
        }
        echo "<pre>";
        print_r($data);
        die;
    }
}
