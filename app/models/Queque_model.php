<?php

class Queque_model extends CI_Model
{
    public function getPortion()
    {
        return $this->db->limit(50)->get('queque')->result();
    }

    public function taskCompleted($id)
    {
        $this->db->where('id', $id)->delete('queque');
    }
}