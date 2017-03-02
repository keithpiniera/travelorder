<?php
class Address_Model extends CI_Model {

	public function __construct() {
		$this->db = $this->load->database('default', true);
        $this->load->library('session');
	}

    public function get_regions($where = ""){
        $this->db->select('regDesc, regCode');
        $this->db->from('refregion');
        if ( !empty($where) ) {
            $this->db->where('regCode', $where);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_provinces($where = ""){
        $this->db->select('provDesc, provCode');
        $this->db->from('refprovince');
        if ( !empty($where) ) {
            $this->db->where('provCode', $where);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_cities($where = ""){
        $this->db->select('citymunDesc, citymunCode');
        $this->db->from('refcitymun');
        if ( !empty($where) ) {
            $this->db->where('citymunCode', $where);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_brgys($where = ""){
        $this->db->select('brgyDesc, brgyCode');
        $this->db->from('refbrgy');
        if ( !empty($where) ) {
            $this->db->where('brgyCode', $where);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function get_offices($where = "", $limit="", $offset=""){
        $this->db->from('refoffice');
        if ( !empty($where) ) {
            $this->db->where('officeCode', $where);
        }
        $this->db->order_by('officeDesc', 'ASC');
        if ( !empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();

        return $query->result_array();
    }

    public function insert_office($data){
        $this->db->insert('refoffice', $data);
        return $this->db->insert_id();
    }

    public function update_office($data){
        $this->db->where('officeCode', $data['officeCode']);
        $this->db->update('refoffice', $data);
    }

    public function delete_office($officeCode){
        $this->db->where('officeCode', $officeCode);
        $this->db->delete('refoffice');
    }

    
}
?>