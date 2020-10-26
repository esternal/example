<?php

class Categories_model extends CI_Model
{
    public function getAll()
    {
        return $this->db->get('categories')->result();
    }

    public function update()
    {
        $rows = $this->getAll();
        $categories = [];
        foreach ($rows as $row) {
            $categories[$row->code] = true;
        }

        $this->load->library('elko');
        $rows = $this->elko->getCategories();
        foreach ($rows as $row) {
            if (isset($categories[$row->categoryCode])) {
                continue;
            }
            $this->db->insert('categories', [
                'code' => $row->categoryCode,
                'name' => $row->categoryName,
            ]);
        }
    }

    public function activate($id, $st)
    {
        $this->db->where('id', $id)->update('categories', ['use' => (int)$st]);
    }

    public function getActiveCodes()
    {
        $result = [];
        $rows = $this->db->get_where('categories', ['use' => true])->result();
        foreach ($rows as $row) {
            $result[] = $row->code;
        }
        return $result;
    }

    public function getActiveCodesIds()
    {
        $result = [];
        $rows = $this->db->get_where('categories', ['use' => true])->result();
        foreach ($rows as $row) {
            $result[$row->code] = $row->id;
        }
        return $result;
    }
}