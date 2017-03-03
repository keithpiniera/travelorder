<?php
class Travel_Orders_Model extends CI_Model {

	public function __construct() {
        $this->db = $this->load->database('default', true);
        $this->load->library('session');
	}

    public function get_user($username, $password){
        $this->db->select('employee_id, name, division, office, user_type');
        $this->db->from('employees');
        $this->db->where('username', $username);
        $this->db->where('password', $password);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_employees(){
        $this->db->from('employees');
        $this->db->where('division', $this->session->userdata('division'));
        if ( $this->session->userdata('type') != 'preparer' ) {
            $this->db->where('employee_id', $this->session->userdata('employee_id'));  
        }
        $this->db->order_by('name', 'asc');
        $query = $this->db->get();
        return $query->result();
    }

    public function get_signatories($division="", $office=""){
        if ($division=="" && $office=="") {
            $division = $this->session->userdata('division');
            $office = $this->session->userdata('office');
        }
        $this->db->from('employees');
        $this->db->where('is_head', 1);
        $this->db->group_start();
        $this->db->where('division', $division);
        $this->db->or_where('division', '');
        $this->db->group_end();
        $this->db->where('office', $office);
        $this->db->order_by('name', 'asc'); 
        $query = $this->db->get();
        return $query->result();
    }

    public function insert_new_travel($details, $employees, $destinations){
        $this->db->trans_start();
            $this->db->insert('travel_details', $details);
            $tid = $this->db->insert_id();

            foreach ($employees as $employee) {
                $employee['travel_id'] = $tid;
                $this->db->insert('travel_employees', $employee);
            }

            foreach ($destinations as $destination) {
                $destination['travel_id'] = $tid;
                $this->db->insert('travel_destinations', $destination);
            }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            die($this->db->_error_message());
        }

        return $tid;
    }

    public function update_travel($details, $employees, $destinations){
        unset($details['tracking_number']);
        $this->db->trans_start();
            $this->db->where('travel_id', $details['travel_id']);
            $this->db->update('travel_details', $details);

            $this->db->where('travel_id', $details['travel_id']);
            $this->db->delete('travel_employees');
            foreach ($employees as $employee) {
                $employee['travel_id'] = $details['travel_id'];
                $this->db->insert('travel_employees', $employee);
            }

            $this->db->where('travel_id', $details['travel_id']);
            $this->db->delete('travel_destinations');
            foreach ($destinations as $destination) {
                $destination['travel_id'] = $details['travel_id'];
                $this->db->insert('travel_destinations', $destination);
            }
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            die($this->db->_error_message());
        }

        return $details['travel_id'];
    }

    public function delete_travel($travel_id){
        $this->db->trans_start();
            $this->db->where('travel_id', $travel_id);
            $this->db->delete('travel_details');

            $this->db->where('travel_id', $travel_id);
            $this->db->delete('travel_employees');

            $this->db->where('travel_id', $travel_id);
            $this->db->delete('travel_destinations');
        $this->db->trans_complete();

        if ($this->db->trans_status() === FALSE){
            die($this->db->_error_message());
        }

        return $travel_id;
    }

    public function get_travels_upcoming(){
        $from = date('Y-m-d');
        $to = date('Y-m-d', strtotime("+1 week"));

        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->where("a.date_from BETWEEN '$from' AND '$to'");
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_ongoing(){
        $today = date('Y-m-d');

        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->where("a.date_from <= '$today'");
        $this->db->where("a.date_to >= '$today'");
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_previous(){
        $today = date('Y-m-d');

        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->where("a.date_to <= '$today'");
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'asc');
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_all($limit,$offset){
        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        // either preparer or employee on travel
        $this->db->group_start();
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->or_where('a.preparing_id', $this->session->userdata('employee_id'));
        if ( $this->session->userdata('type') == 'preparer' ) {
            $this->db->or_where('a.preparing_id IN (SELECT employee_id FROM employees WHERE division = "'.$this->session->userdata('division').'")');
        }
        $this->db->group_end();
        if ( $this->session->userdata('type') == 'admin' ) {
            $this->db->or_group_start();
            $this->db->where('1 = 1');
            $this->db->group_end();
        }
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_pending($limit,$offset){
        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        // either preparer or employee on travel
        $this->db->group_start();
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->or_where('a.preparing_id', $this->session->userdata('employee_id'));
        if ( $this->session->userdata('type') == 'preparer' ) {
            $this->db->or_where('a.preparing_id IN (SELECT employee_id FROM employees WHERE division = "'.$this->session->userdata('division').'")');
        }
        $this->db->group_end();
        if ( $this->session->userdata('type') == 'admin' ) {
            $this->db->or_group_start();
            $this->db->where('1 = 1');
            $this->db->group_end();
        }
        // condition for pending
        $this->db->where("a.recommend <> 1"); // not declined by recommender
        $this->db->where("a.approve = 0"); // not declined or approved
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_canceled($limit,$offset){
        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        // either preparer or employee on travel
        $this->db->group_start();
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->or_where('a.preparing_id', $this->session->userdata('employee_id'));
        if ( $this->session->userdata('type') == 'preparer' ) {
            $this->db->or_where('a.preparing_id IN (SELECT employee_id FROM employees WHERE division = "'.$this->session->userdata('division').'")');
        }
        $this->db->group_end();
        if ( $this->session->userdata('type') == 'admin' ) {
            $this->db->or_group_start();
            $this->db->where('1 = 1');
            $this->db->group_end();
        }
        // condition for canceled
        $this->db->where("a.is_cancelled = 1"); // canceled
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_declined($limit,$offset){
        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        // either preparer or employee on travel
        $this->db->group_start();
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->or_where('a.preparing_id', $this->session->userdata('employee_id'));
        if ( $this->session->userdata('type') == 'preparer' ) {
            $this->db->or_where('a.preparing_id IN (SELECT employee_id FROM employees WHERE division = "'.$this->session->userdata('division').'")');
        }
        $this->db->group_end();
        if ( $this->session->userdata('type') == 'admin' ) {
            $this->db->or_group_start();
            $this->db->where('1 = 1');
            $this->db->group_end();
        }
        // condition for declined
        $this->db->group_start();
        $this->db->where("a.recommend = 1"); // declined by recommender
        $this->db->or_where("a.approve = 1"); // declined by approver
        $this->db->group_end();
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_approved($limit,$offset){
        $this->db->from('travel_details a ');
        $this->db->join('travel_employees b', 'b.travel_id = a.travel_id', 'left');
        // either preparer or employee on travel
        $this->db->group_start();
        $this->db->where('b.employee_id', $this->session->userdata('employee_id'));
        $this->db->or_where('a.preparing_id', $this->session->userdata('employee_id'));
        if ( $this->session->userdata('type') == 'preparer' ) {
            $this->db->or_where('a.preparing_id IN (SELECT employee_id FROM employees WHERE division = "'.$this->session->userdata('division').'")');
        }
        $this->db->group_end();
        if ( $this->session->userdata('type') == 'admin' ) {
            $this->db->or_group_start();
            $this->db->where('1 = 1');
            $this->db->group_end();
        }
        // condition for approved
        $this->db->where("a.approve = 2"); // approved by approver
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->group_by('a.travel_id');
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_for_recommendation($limit,$offset){
        $this->db->from('travel_details a ');
        // if user is recommending officer
        if ($this->session->userdata('type') != 'admin') {
            $this->db->where('a.recommending_id', $this->session->userdata('employee_id'));
        }
        // condition for recommendation
        $this->db->where("a.recommend = 0"); // not yet recommended
        $this->db->where("a.is_cancelled <> 1"); // not canceled 
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && (!empty($offset) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travels_for_approval($limit,$offset){
        $this->db->from('travel_details a ');
        // if user is recommending officer
        if ($this->session->userdata('type') != 'admin') {
            $this->db->where('a.approving_id', $this->session->userdata('employee_id'));
        }
        // condition for approval
        $this->db->group_start();
        $this->db->where("a.recommend = 2"); // recommended
        $this->db->or_where("a.recommend = 3"); // no recommendation needed
        $this->db->group_end();
        $this->db->where("a.approve = 0"); // not yet approve
        $this->db->where("a.is_cancelled <> 1"); // not canceled
        $this->db->order_by('a.date_from', 'desc');
        if (!empty($limit) && ((!empty($offset) || $offset == 0 ) || $offset == 0 )) {
            $this->db->limit($limit, $offset);
        }
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travel_details($travel_id){
        $this->db->from('travel_details');
        $this->db->where('travel_id', $travel_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function search_travel($keyword){
        $this->db->from('travel_details');
        $this->db->where('travel_id', $keyword);
        $this->db->or_like('tracking_number', $keyword,'both');
        $this->db->or_like('purpose', $keyword,'both');
        //die($this->db->get_compiled_select());
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travel_destinations($travel_id){
        $this->db->from('travel_destinations');
        $this->db->where('travel_id', $travel_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_travel_employees($travel_id){
        $this->db->select('a.*, b.name');
        $this->db->from('travel_employees a');
        $this->db->join('employees b', 'a.employee_id = b.employee_id');
        $this->db->where('travel_id', $travel_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_destination_string($destination_id){
        $this->db->select('overseas, officeName, officeDesc, citymunDesc, provDesc');
        $this->db->from('travel_destinations a');
        $this->db->join('refoffice b', 'b.officeCode = a.officeCode', 'LEFT');
        $this->db->join('refcitymun c', 'c.citymunCode = a.citymunCode', 'LEFT');
        $this->db->join('refprovince d', 'd.provCode = a.provCode', 'LEFT');
        $this->db->where('a.destination_id', $destination_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function get_employee($employee_id){
        $this->db->from('employees');
        $this->db->where('employee_id', $employee_id);
        $query = $this->db->get();
        return $query->result_array();
    }

    public function recommend_travel($travel_id){
        $details['travel_id'] = $travel_id;
        $details['recommend'] = 2;
        $this->db->where('travel_id', $travel_id);
        $this->db->update('travel_details', $details);
    }

    public function approve_travel($travel_id){
        $details['travel_id'] = $travel_id;
        $details['approve'] = 2;
        $this->db->where('travel_id', $travel_id);
        $this->db->update('travel_details', $details);
    }

    public function cancel_travel($travel_id, $remarks){
        $details['travel_id'] = $travel_id;
        $details['is_cancelled'] = 1;
        $details['cancel_remarks'] = $remarks;
        $this->db->where('travel_id', $travel_id);
        $this->db->update('travel_details', $details);
    }

    public function recommender_decline_travel($travel_id, $remarks){
        $details['travel_id'] = $travel_id;
        $details['recommend'] = 1;
        $details['recommend_remarks'] = $remarks;
        $this->db->where('travel_id', $travel_id);
        $this->db->update('travel_details', $details);
    }

    public function approver_decline_travel($travel_id, $remarks){
        $details['travel_id'] = $travel_id;
        $details['approve'] = 1;
        $details['approve_remarks'] = $remarks;
        $this->db->where('travel_id', $travel_id);
        $this->db->update('travel_details', $details);
    }

    public function employee_availability($employee_id,$from,$to,$travel_id){
        $this->db->from('travel_employees a');
        $this->db->join('travel_details b', 'a.travel_id = b.travel_id', 'left');
        $this->db->where('a.employee_id', $employee_id);
        if (!empty($travel_id)) $this->db->where('b.travel_id <>', $travel_id);
        $this->db->group_start();
        $this->db->where("'$from' BETWEEN b.date_from AND b.date_to");
        $this->db->or_where("'$to' BETWEEN b.date_from AND b.date_to");
        $this->db->group_end();
        $query = $this->db->get();
        return count($query->result_array());
    }


}
?>