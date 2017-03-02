<?php
class FMIS_model extends CI_Model {

	public function __construct() {
		$this->db = $this->load->database('fmis',true);
        $this->load->library('session');
	}

    public function get_project_codes_like($name="") {
        $name .= '%';
        $sql = 'SELECT [ProjectCode] FROM [FMIS2016].[dbo].[LIB_Projects] WHERE ProjectCode LIKE ?';
        $query = $this->db->query($sql, $name);
        return $query->result_array();
    }
}
?>