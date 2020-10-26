<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Welcome extends CI_Controller
{

    public function index()
    {
        $this->load->model('products_model');
        $this->twig->display('index', params([
            'categories' => $this->products_model->getVisible(),
        ]));
    }
}
