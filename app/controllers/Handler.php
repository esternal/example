<?php

class Handler extends CI_Controller
{
    public function execute()
    {
        $this->load->model('queque_model');
        $tasks = $this->queque_model->getPortion();
        foreach ($tasks as $task) {
            $this->{$task->handler}((object)json_decode($task->params, JSON_UNESCAPED_UNICODE));
            $this->queque_model->taskCompleted($task->id);
        }
    }

    public function check()
    {
        $this->load->model('products_model');
        $this->products_model->sync();
    }

    public function createProduct($params)
    {
        $this->load->library('sklad');
        $msProduct = (object)$this->sklad->createProduct($params->name, $params->article, $params->price);
        $this->load->model('products_model');
        $this->products_model->addMsId($params->id, $msProduct->id);
    }

    public function updateStock()
    {
        $this->load->model('tasker_model');
        $this->tasker_model->updateStock();
    }


    public function addToStock($params)
    {
        $this->load->library('sklad');
        $this->sklad->addToStock($params->msId, $params->quantity);
        $this->load->model('products_model');
        $this->products_model->updateStock($params->id, $params->stock);
    }

    public function removeFromStock($params)
    {
        $this->load->library('sklad');
        $this->sklad->removeFromStock($params->msId, $params->quantity);
        $this->load->model('products_model');
        $this->products_model->updateStock($params->id, $params->stock);
    }
}