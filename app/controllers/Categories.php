<?php

class Categories extends CI_Controller
{
    public function index()
    {
        $this->load->model('categories_model');
        $this->twig->display('categories', params([
            'categories' => $this->categories_model->getAll(),
        ]));
    }

    public function update()
    {
        $this->load->model('categories_model');
        $this->categories_model->update();
        echo 'ok';
    }

    public function used(){
        $this->load->model('categories_model');
        $this->categories_model->activate($this->input->post('id'), $this->input->post('st'));
    }
}