<?php
class Logs_Model extends CI_Model {

	public function __construct() {
		$this->db = $this->load->database('default', true);
        $this->load->library('session');
	}

    public function log_login(){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'login';
        $this->db->insert('travel_logs', $data);
    }

    public function log_logout(){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'logout';
        $this->db->insert('travel_logs', $data);
    }

    public function log_create($travel_id){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'create';
        $data['travel_id'] = $travel_id;
        $this->db->insert('travel_logs', $data);
    }

    public function log_edit($travel_id){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'edit';
        $data['travel_id'] = $travel_id;
        $this->db->insert('travel_logs', $data);
    }

    public function log_cancel($travel_id){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'cancel';
        $data['travel_id'] = $travel_id;
        $this->db->insert('travel_logs', $data);
    }

    public function log_delete($travel_id){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'delete';
        $data['travel_id'] = $travel_id;
        $this->db->insert('travel_logs', $data);
    }

    public function log_recommend($travel_id){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'recommended';
        $data['travel_id'] = $travel_id;
        $this->db->insert('travel_logs', $data);
    }

    public function log_approve($travel_id){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'approved';
        $data['travel_id'] = $travel_id;
        $this->db->insert('travel_logs', $data);
    }

    public function log_decline($travel_id, $remarks){
        $data['date'] = date('Y-m-d H:i:s');
        $data['user_id'] = $this->session->userdata('employee_id');
        $data['user_action'] = 'declined';
        $data['travel_id'] = $travel_id;
        $data['remarks'] = $remarks;
        $this->db->insert('travel_logs', $data);
    }

}
?>