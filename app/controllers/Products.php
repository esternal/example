<?php

class Products extends CI_Controller
{
    public function update()
    {
        $this->load->model('products_model');
        $this->products_model->sync();

        $this->load->helper('url');
        redirect('/');
    }
}