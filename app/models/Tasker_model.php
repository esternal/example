<?php

class Tasker_model extends CI_Model
{

    public function createProduct()
    {
        $products = $this->db
            ->where('ms_id', null)
            ->get('products')
            ->result();
        $data = [];
        foreach ($products as $product) {
            $data[] = $this->task('createProduct', [
                'id'      => $product->id,
                'name'    => $product->name,
                'article' => $product->code,
                'price'   => $product->price * 100,
            ]);
        }

        $data[] = $this->task('updateStock', null);
        if ($data) {
            $this->db->insert_batch('queque', $data);
        }
    }

    public function updateStock()
    {
        $this->db->select('p.id, p.ms_id, s.quantity, p.stock, c.use');
        $this->db->from('products p');
        $this->db->join('stock s', 's.product_id = p.id and s.stock = "msk"', 'left');
        $this->db->join('categories c', 'c.id = p.category_id', 'left');

        $data = [];
        $products = $this->db->get()->result();
        foreach ($products as $product) {
            if ($product->use) {
                if ((int)$product->quantity > (int)$product->stock) {
                    $data[] = $this->task('addToStock', [
                        'id'       => $product->id,
                        'msId'     => $product->ms_id,
                        'stock'    => $product->quantity,
                        'quantity' => (int)$product->quantity - (int)$product->stock,
                    ]);
                }

                if ((int)$product->quantity < (int)$product->stock) {
                    $data[] = $this->task('removeFromStock', [
                        'id'       => $product->id,
                        'msId'     => $product->ms_id,
                        'stock'    => $product->quantity,
                        'quantity' => (int)$product->stock - (int)$product->quantity,
                    ]);
                }
            } else {
                if ((int)$product->stock > 0) {
                    $data[] = $this->task('removeFromStock', [
                        'id'       => $product->id,
                        'msId'     => $product->ms_id,
                        'stock'    => 0,
                        'quantity' => (int)$product->stock,
                    ]);
                }
            }
        }

        if ($data) {
            $this->db->insert_batch('queque', $data);
        }
    }

    private function task($handler, $params)
    {
        return [
            'handler' => $handler,
            'params'  => json_encode($params, JSON_UNESCAPED_UNICODE),
        ];
    }
}