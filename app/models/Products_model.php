<?php

class Products_model extends CI_Model
{
    public function addProduct($product)
    {
        $this->load->model('categories_model');
        $codes = $this->categories_model->getActiveCodesIds();

        $this->replace(
            $product->productId,
            $product->productName,
            $product->productPrice,
            $product->manufacturerCode,
            $codes[$product->categoryCode]);

        foreach ($product->inTransit as $transit) {
            $this->db->replace('transit', [
                'product_id' => $product->productId,
                'quantity'   => $transit->quantity,
                'stock'      => $transit->stock,
            ]);
        }

        foreach ($product->inStock as $stock) {
            $this->db->replace('stock', [
                'product_id' => $product->productId,
                'quantity'   => $stock->quantity,
                'stock'      => $stock->stock,
            ]);
        }

    }

    public function getVisible()
    {
        $this->db->select('p.id, p.name, p.price, s.quantity as `msk_st`, c.name as `category`');
        $this->db->from('products p');
        $this->db->join('stock s', 's.product_id = p.id and s.stock = "msk"', 'left');
        $this->db->join('categories c', 'c.id = p.category_id', 'left');

        $this->db->where('s.quantity != 0');
        $this->db->where('c.use', true);

        $result = [];
        $rows = $this->db->order_by('p.price, p.name')->get()->result();
        foreach ($rows as $row) {
            $result[$row->category][] = $row;
        }

        return $result;
    }

    public function addMsId($id, $msId)
    {
        $this->db->where('id', $id)->update('products', ['ms_id' => $msId]);
    }

    public function updateStock($id, $stock)
    {
        $this->db->where('id', $id)->update('products', ['stock' => $stock]);
    }

    public function sync()
    {
        $this->load->model('categories_model');
        $codes = $this->categories_model->getActiveCodes();

        $this->load->library('elko');
        $cat = $this->elko->getProducts($codes);

        foreach ($cat as $product) {
            $this->addProduct($product);
        }

        $this->load->model('tasker_model');
        $this->tasker_model->createProduct();
    }

    private function replace($id, $name, $price, $code, $categoryId)
    {
        $this->db->query(
            "INSERT INTO products (id, name, price, code, category_id)
             VALUES ($id, '$name', $price, '$code', $categoryId)
             ON DUPLICATE KEY UPDATE
              id   = $id,
              name  = '$name',
              price = $price,
              code = '$code',
              category_id = $categoryId"
        );
    }
}