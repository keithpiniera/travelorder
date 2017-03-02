<?php (defined('BASEPATH')) OR exit('No direct script access allowed');


class Travel_Orders extends CI_Controller {

    public $conf_pagination = [
        'use_page_numbers'  => TRUE,
        'full_tag_open'     => '<div class="pull-right pagination"><ul class="pagination">',
        'full_tag_close'    => '</ul></div>',
        'num_tag_open'      => '<li class="page-number">',
        'num_tag_close'     => '</li>',
        'cur_tag_open'      => '<li class="page-number active disabled"><a href="#" >',
        'cur_tag_close'     => '</a></li>',
        'prev_tag_open'     => '<li class="page-pre">',
        'prev_tag_close'    => '</li>',
        'next_tag_open'     => '<li class="page-next">',
        'next_tag_close'    => '</li>',
        'first_tag_open'    => '<li class="page-first">',
        'first_tag_close'   => '</li>',
        'first_link'        => '&lt;&lt;',
        'last_tag_open'     => '<li class="page-last">',
        'last_tag_close'    => '</li>',
        'last_link'         => '&gt;&gt;'
    ];

	public function __construct() {
		parent::__construct();
        $this->load->model('travel_orders_model');
        $this->load->model('logs_model');
        $this->load->model('address_model');
        $this->load->model('fmis_model');
		$this->load->model('pdts_model');
        $this->load->library('session');
        $this->load->library('pagination');
        $this->load->helper('url');

        $allowed = array();
        $allowed[] = 'login';

        if ( !in_array($this->router->fetch_method(), $allowed) ) {
            if ( $this->session->userdata('employee_id') == "" ) {
                redirect('travel_orders/login');
            }
        }
	}

	public function index(){
        redirect('travel_orders/status/pending');
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "My Travels";
		$this->lumino_load_view('travel_orders/index', $data);
	}

    public function login(){
        $data = "";
        $data['message'] = '';
        
        if ( isset($_POST['username'] ) && isset($_POST['password']) ) {
            $user = $this->travel_orders_model->get_user($_POST['username'],$_POST['password']);
            if ( count($user) == 1 ) {
                $this->session->set_userdata('employee_id', $user[0]['employee_id']);
                $this->session->set_userdata('name', $user[0]['name']);
                $this->session->set_userdata('type', $user[0]['user_type']);
                $this->session->set_userdata('division', $user[0]['division']);
                $this->session->set_userdata('office', $user[0]['office']);
                $this->logs_model->log_login();
                redirect('travel_orders/index');
            }
            else {
                $data['message'] = "Login failed. Try again.";
            }
        }

        $this->load->view('templates/header', $data);
        $this->load->view('travel_orders/login', $data);
        $this->load->view('templates/footer', $data);
    }

    public function logout(){
        $this->logs_model->log_logout();
        $this->session->sess_destroy();
        redirect('travel_orders/login');
    }

    public function lumino_load_view($page="index", $data=""){
        $data['config'] = $this->conf_pagination;
        $this->load->view('templates/header', $data);
        $this->load->view('templates/nav-top', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/main-open', $data);
        $this->load->view('templates/main-top', $data);
        $this->load->view($page, $data);
        $this->load->view('templates/main-close', $data);
        $this->load->view('templates/footer', $data);
    }

    public function create(){
        if ($this->session->userdata('type')=='admin') show_404();
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "Create Travel Order";

        $data['employees'] = $this->travel_orders_model->get_employees();
        $data['signatories'] = $this->travel_orders_model->get_signatories();
        $data['project_codes'] = $this->fmis_model->get_project_codes_like();

        $data['province'] = $this->address_model->get_provinces();
        $data['city'] = $this->address_model->get_cities();
        $data['office'] = $this->address_model->get_offices();

        $data['access_level'] = array();
        $data['access_level']['type'] = 'preparer';
        $data['access_level']['can_print'] = true;
        $data['access_level']['can_cancel'] = true;
        $data['access_level']['can_recommend'] = false;
        $data['access_level']['can_approve'] = false;
        $data['access_level']['can_decline'] = false;
        $data['access_level']['can_edit'] = true;

        $this->lumino_load_view('travel_orders/create', $data);
    }

    public function view_travel($travel_id){
        if ( empty($travel_id) ) die('ID variable not found.');

        $data['travel_id'] = $travel_id;
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "Travel Order #$travel_id";

        // get travel details
        $data['details'] = $this->travel_orders_model->get_travel_details($travel_id);
        if (count($data['details']) != 1) show_404();
        foreach ($data['details'] as &$detail) {
            $detail['date_from'] = date('m/d/Y', strtotime($detail['date_from']));
            $detail['date_to'] = date('m/d/Y', strtotime($detail['date_to']));
            $preparing = $this->travel_orders_model->get_employee($detail['preparing_id']);
            $detail['preparing_name'] = $preparing[0]['name'];
        }

        $data['employees'] = $this->travel_orders_model->get_employees();
        $data['signatories'] = $this->travel_orders_model->get_signatories();
        $data['project_codes'] = $this->fmis_model->get_project_codes_like();

        $data['province'] = $this->address_model->get_provinces();
        $data['city'] = $this->address_model->get_cities();
        $data['office'] = $this->address_model->get_offices();

        $myid = $this->session->userdata('employee_id');
        $data['access_level'] = array();
        if ( $myid == $data['details'][0]['preparing_id'] || $this->session->userdata('type') == 'preparer') {
            $data['access_level']['type'] = 'preparer';
            $data['access_level']['can_print'] = true;
            $data['access_level']['can_cancel'] = true;
            $data['access_level']['can_recommend'] = false;
            $data['access_level']['can_approve'] = false;
            $data['access_level']['can_decline'] = false;
            $data['access_level']['can_edit'] = true;
        }
        elseif ( $myid == $data['details'][0]['recommending_id'] ) {
            $data['access_level']['type'] = 'recommender';
            $data['access_level']['can_print'] = true;
            $data['access_level']['can_cancel'] = false;
            $data['access_level']['can_recommend'] = true;
            $data['access_level']['can_approve'] = false;
            $data['access_level']['can_decline'] = true;
            $data['access_level']['can_edit'] = false;
        }
        elseif ( $myid == $data['details'][0]['approving_id'] ) {
            $data['access_level']['type'] = 'approver';
            $data['access_level']['can_print'] = true;
            $data['access_level']['can_cancel'] = false;
            $data['access_level']['can_recommend'] = false;
            $data['access_level']['can_approve'] = true;
            $data['access_level']['can_decline'] = true;
            $data['access_level']['can_edit'] = false;
        }
        else {
            //commoner
            $data['access_level']['type'] = 'commoner';
            $data['access_level']['can_print'] = false;
            $data['access_level']['can_cancel'] = false;
            $data['access_level']['can_recommend'] = false;
            $data['access_level']['can_approve'] = false;
            $data['access_level']['can_decline'] = false;
            $data['access_level']['can_edit'] = false;
        }

        $data['status'] = $this->generate_travel_status($data['details'][0], $data['access_level']);
        
        // get travel employees
        $data['selected_employees'] = $this->travel_orders_model->get_travel_employees($travel_id);
        // get travel destinations
        $data['destinations'] = $this->travel_orders_model->get_travel_destinations($travel_id);

        $this->lumino_load_view('travel_orders/create', $data);
    }

    public function save_travel_order(){
        $travel = array();
        $travel_id = $_POST['travel_id'];
        parse_str($_POST['data'], $travel);

        $employees = $travel['employee'];
        $destinations = $travel['destinations'];
        unset($travel['employee']);
        unset($travel['destinations']);
        unset($travel['destination']);

        $travel['date_from'] = date('Y-m-d', strtotime($travel['date_from']));
        $travel['date_to'] = date('Y-m-d', strtotime($travel['date_to']));

        // notify user if save status will be reset
        $travel['recommend'] = $travel['approve'] = $travel['is_cancelled'] = 0;
        $travel['recommend_remarks'] = $travel['approve_remarks'] = $travel['cancel_remarks'] = ''; 
        if (empty($travel['recommending_id'])) $travel['recommend'] = 3;

        if ( !empty($travel_id) ) {
            $travel['travel_id'] = $travel_id;
            $this->travel_orders_model->update_travel($travel, $employees, $destinations);
            $this->logs_model->log_edit($travel_id);
            // update transaction in pdts
            $data = $this->prepare_travel_details($travel_id);
            $this->pdts_model->forward_document($data);
        } else {
            $travel['date_prepared'] = date('Y-m-d');
            $travel['preparing_id'] = $this->session->userdata('employee_id');
            $travel_id = $this->travel_orders_model->insert_new_travel($travel, $employees, $destinations);
            $this->logs_model->log_create($travel_id);
            // create new transaction in pdts
            $data = $this->prepare_travel_details($travel_id);
            $this->pdts_model->create_document($data);
        }
        
    
        echo $travel_id;
    }

    public function delete_travel_order(){
        $this->travel_orders_model->delete_travel($_POST['travel_id']);
        $this->logs_model->log_delete($_POST['travel_id']);
    }

    public function generate_time_of_departure(){
        $option = array();
        $meridiem = array("AM","PM");
        foreach ($meridiem as $m) {
            for ($i = 1; $i <= 12; $i++) {
                for ($j = 0; $j <= 59; $j++){
                    if ($j%30 != 0) continue; 
                    $time = ($i<10 ? "0".$i : $i) . ":" . ($j<10 ? "0".$j : $j) . " " . $m;
                    if ( time() <= strtotime($time) ) {
                        $option[] = $time;
                    }
                }
            }
        }
        echo json_encode($option);
    }

    public function my_travels(){
        $status = $_POST['status'];
        if ( $status == 'upcoming' ) {
            $my_travels = $this->travel_orders_model->get_travels_upcoming();
        } elseif ( $status == 'ongoing') {
            $my_travels = $this->travel_orders_model->get_travels_ongoing();
        } elseif ( $status == 'previous') {
            $my_travels = $this->travel_orders_model->get_travels_previous();
        } else {
            show_404();
        }
        

        $i = 0;
        foreach ($my_travels as &$travel) {
            
            $destinations = $this->travel_orders_model->get_travel_destinations($travel['travel_id']);
            $str_destination = '';
            
            foreach ($destinations as $d) {
                $str_destination .= $this->generate_destination_string($d['destination_id']).'; ';
            }

            $prepared_by = $this->travel_orders_model->get_employee($travel['preparing_id']);
            $prepared_by = $prepared_by[0]['name'];
            
            $travel['str_inclusive'] = $this->generate_inclusive_dates_of_travel($travel['date_from'], $travel['date_to']);

            echo '<tr data-index="'.$i.'" data-travelid="'. $travel['travel_id'] .'">';
            echo '<td>'.$travel['str_inclusive'].'</td>';
            echo '<td>'.$str_destination.'</td>';
            echo '<td>'.$travel['time_of_departure'].'</td>';
            echo '<td>'.$prepared_by.'</td>';
            echo '</tr>';
            $i++;
        }
    }

    private function get_travel_by_status($status,$limit="",$offset=""){
        if ( $status == 'pending') {
            return $this->travel_orders_model->get_travels_pending($limit,$offset);
        } elseif ( $status == 'canceled') {
            return $this->travel_orders_model->get_travels_canceled($limit,$offset);
        } elseif ( $status == 'declined') {
            return $this->travel_orders_model->get_travels_declined($limit,$offset);
        } elseif ( $status == 'approved') {
            return $this->travel_orders_model->get_travels_approved($limit,$offset);
        } elseif ( $status == 'for_recommendation') {
            return $this->travel_orders_model->get_travels_for_recommendation($limit,$offset);
        } elseif ( $status == 'for_approval') {
            return $this->travel_orders_model->get_travels_for_approval($limit,$offset);
        } elseif ( $status == 'search') {
            return $this->travel_orders_model->get_travel_details($_POST['keyword']);
        } elseif ( $status == 'all') {
            return $this->travel_orders_model->get_travels_all($limit,$offset);
        } else {
            show_404();
        }
        
    }

    public function load_travel_orders(){
        $status = $_POST['status'];
        $limit = $_POST['limit'];
        $offset = $_POST['offset'];
        $my_travels = $this->get_travel_by_status($status,$limit,$offset);

        $i = 0;
        foreach ($my_travels as &$travel) {
            // convert destination to strings
            $destinations = $this->travel_orders_model->get_travel_destinations($travel['travel_id']);
            $str_destination = '';
            foreach ($destinations as $d) {
                $str_destination .= $this->generate_destination_string($d['destination_id']).'; ';
            }

            // get preparer name
            $prepared_by = $this->travel_orders_model->get_employee($travel['preparing_id']);
            $prepared_by = $prepared_by[0]['name'];
            
            // format inclusive date
            $travel['str_inclusive'] = $this->generate_inclusive_dates_of_travel($travel['date_from'], $travel['date_to']);

            // if due date
            $is_outed = strtotime(date('Y-m-d')) > strtotime($travel['date_from']) ? 'outdated':''; 

            echo '<tr data-index="'.$i.'" data-travelid="'. $travel['travel_id'] .'" class="'.$is_outed.'">';
            echo '<td>'.$travel['str_inclusive'].'</td>';
            echo '<td>'.$str_destination.'</td>';
            echo '<td>'.$travel['time_of_departure'].'</td>';
            echo '<td>'.$prepared_by.'</td>';
            echo '</tr>';
            $i++;
        }
    }

    public function status($status){
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "Travel Orders";
        $data['panel_header'] = ucfirst($status);

        $data['rows'] = count($this->get_travel_by_status($status));

        $this->lumino_load_view('travel_orders/by_status', $data);
    }

    private function prepare_travel_details($travel_id){
        // get travel details
        $data['details'] = $this->travel_orders_model->get_travel_details($travel_id);
        foreach ($data['details'] as &$detail) {
            $detail['date_from'] = date('F d, Y', strtotime($detail['date_from']));
            $detail['date_to'] = date('F d, Y', strtotime($detail['date_to']));
            $detail['date_prepared'] = date('F d, Y', strtotime($detail['date_prepared']));
        }
        $data['details'] = $data['details'][0];
        
        // get travel employees
        $data['employees'] = $this->travel_orders_model->get_travel_employees($travel_id);
        foreach ($data['employees'] as &$employee) {
            // get employee position
            $emp = $this->travel_orders_model->get_employee($employee['employee_id']);
            $employee['position'] = $emp[0]['position'];
        }
        // get travel destinations
        $data['destinations'] = $this->travel_orders_model->get_travel_destinations($travel_id);
        $data['destination_string'] = '';
        foreach ($data['destinations'] as $destination) {
            $data['destination_string'] .= $this->generate_destination_string($destination['destination_id']).'; ';
        }

        $officers = $this->travel_orders_model->get_employee($data['details']['preparing_id']);
        $data['details']['preparing_name'] = '';
        $data['details']['preparing_division'] = '';
        foreach ($officers as $officer) {
            $data['details']['preparing_name'] = $officer['name'];
            $data['details']['preparing_division'] = $officer['division'];
        }

        $officers = $this->travel_orders_model->get_employee($data['details']['recommending_id']);
        $data['details']['recommending_name'] = '';
        $data['details']['recommending_position'] = '';
        foreach ($officers as $officer) {
            $data['details']['recommending_name'] = $officer['name'];
            $data['details']['recommending_designation'] = $officer['designation'];
            $data['details']['recommending_division'] = $officer['division'];
        }

        $officers = $this->travel_orders_model->get_employee($data['details']['approving_id']);
        $data['details']['approving_name'] = '';
        $data['details']['approving_position'] = '';
        foreach ($officers as $officer) {
            $data['details']['approving_name'] = $officer['name'];
            $data['details']['approving_designation'] = $officer['designation'];
            $data['details']['approving_division'] = empty($officer['division']) ? $officer['office']: $officer['division'];
        }

        $data['access_level']['type'] = 'commoner';
        $data['access_level']['can_print'] = false;
        $data['access_level']['can_cancel'] = false;
        $data['access_level']['can_recommend'] = false;
        $data['access_level']['can_approve'] = false;
        $data['access_level']['can_decline'] = false;
        $data['access_level']['can_edit'] = false;

        $data['status'] = $this->generate_travel_status($data['details'], $data['access_level']);

        return $data;
    }

    public function print_preview($travel_id){
        $data = array();
        $this->load->helper('dompdf');
        $data = $this->prepare_travel_details($travel_id);
        $html = $this->load->view('travel_orders/print', $data, TRUE);
        pdf_create_a4($html, 'travel_order', TRUE);
    }

    public function recommend_travel_order(){
        $travel_id = $_POST['travel_id'];
        $this->travel_orders_model->recommend_travel($travel_id);
        $this->logs_model->log_recommend($travel_id);
        // create new transaction in pdts
        $data = $this->prepare_travel_details($travel_id);
        $this->pdts_model->forward_document($data);


        echo '<div class="alert bg-success" role="alert">
                <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                    Travel Order #'.$travel_id.'  has been successfully recommended. Thank you.
                <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
            </div>';
    }

    public function approve_travel_order(){
        $travel_id = $_POST['travel_id'];
        $this->travel_orders_model->approve_travel($travel_id);
        $this->logs_model->log_approve($travel_id);
        // create new transaction in pdts
        $data = $this->prepare_travel_details($travel_id);
        $this->pdts_model->forward_document($data);

        echo '<div class="alert bg-success" role="alert">
                <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                    Travel Order #'.$travel_id.' has been successfully approved. Thank you.
                <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
            </div>';
    }

    public function cancel_travel_order(){
        $travel_id = $_POST['travel_id'];
        $remarks = $_POST['remarks'];
        $this->travel_orders_model->cancel_travel($travel_id, $remarks);
        $this->logs_model->log_cancel($travel_id,$remarks);
        // create new transaction in pdts
        $data = $this->prepare_travel_details($travel_id);
        $this->pdts_model->forward_document($data);

        echo '<div class="alert bg-success" role="alert">
                <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                    Travel Order #'.$travel_id.' has been successfully canceled. Thank you.
                <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
            </div>';
    }

    public function decline_travel_order(){
        $travel_id = $_POST['travel_id'];
        $remarks = $_POST['remarks'];
        $access = $_POST['access'];
        if ( $access == 'recommender' )  $this->travel_orders_model->recommender_decline_travel($travel_id, $remarks);
        if ( $access == 'approver' )  $this->travel_orders_model->approver_decline_travel($travel_id, $remarks);
        $this->logs_model->log_decline($travel_id,$remarks);
        // create new transaction in pdts
        $data = $this->prepare_travel_details($travel_id);
        $this->pdts_model->forward_document($data);

        echo '<div class="alert bg-success" role="alert">
                <svg class="glyph stroked checkmark"><use xmlns:xlink="http://www.w3.org/1999/xlink" xlink:href="#stroked-checkmark"></use></svg>
                    Travel Order #'.$travel_id.' has been successfully declined. Thank you.
                <a href="#" class="pull-right"><span class="glyphicon glyphicon-remove"></span></a>
            </div>';
    }

    private function generate_inclusive_dates_of_travel($from, $to){
        $strtotime_from = strtotime($from);
        $strtotime_to = strtotime($to);

        $travel_date_from = explode('-', $from);
        $travel_date_to = explode('-', $to);

        // check if year is the same
        if ( $travel_date_from[0] != $travel_date_to[0] ) {
            // mm dd yyyy - mm dd yyyy
            return date('M j, Y', $strtotime_from) .' - '.  date('M j, Y', $strtotime_to);
        } else {
            // check if month is the same
            if ( $travel_date_from[1] != $travel_date_to[1] ) {
                // mm dd - mm dd yyyy
                return date('M j', $strtotime_from) .' - '. date('M j', $strtotime_to)  .' '. $travel_date_from[0];
            } else {
                // check if day is the same
                if ( $travel_date_from[2] != $travel_date_to[2] ) {
                    // mm dd - dd yyyy
                    return date('M', $strtotime_from) .' '. $travel_date_from[2] .' - '. $travel_date_to[2] .' '. $travel_date_from[0];
                } else {
                    // mm dd yyyy 
                    return date('M j, Y', $strtotime_from);
                }
            }
        }
    }

    private function generate_destination_string($destination_id){
        $destination = $this->travel_orders_model->get_destination_string($destination_id);
        foreach ($destination as $d) {
            if (!empty($d['overseas'])) {

                return $d['overseas'];
            }
            return (!empty($d['officeName']) ? $d['officeName'].', ':'') . (!empty($d['officeDesc']) ? $d['officeDesc'].', ':'') . (!empty($d['citymunDesc']) ? $d['citymunDesc'].', ':'') . (!empty($d['provDesc']) ? $d['provDesc']:'');
        }
    }

    private function generate_travel_status($details, &$access){
        $status = '';
        $should_not_print = false;
        $should_not_edit = false;
        $should_not_cancel = false;
        $should_not_recommend = false;
        $should_not_approve = false;
        $should_not_decline = false;

        if ( $details['recommend'] == 0 ) {
            $status = 'Waiting For Recommendation';
        }
        if ( $details['recommend'] == 1 ) {
            $status = 'Recommending Officer declined this travel order. Remarks: ' . $details['recommend_remarks'];
            // if declined, cannot be recommended or declined again
            $should_not_edit = false;
            $should_not_recommend = true;
            $should_not_decline = true;
        }
        if ( $details['recommend'] == 2 ) {
            $status = 'Recommended. Waiting For Approval';
            // if recommended, cannot be edit or recommend again or decline
            $should_not_edit = true;
            $should_not_recommend = true;
            if ( $access['type'] == 'recommender') $should_not_decline = true;
        }
        if ( $details['recommend'] == 3 ) {
            $status = 'Waiting For Approval';
        }

        if ( $details['approve'] == 1 ) {
            $status = 'Approving Officer declined this travel order. Remarks: ' . $details['approve_remarks'];
            // if declined, cannot be approve or declined again
            $should_not_edit = false;
            $should_not_approve = true;
            $should_not_decline = true;
        }
        if ( $details['approve'] == 2 ) {
            $status = 'Approved';
            // if approved, cannot be edit, approve again or decline
            $should_not_edit = true;
            $should_not_approve = true;
            $should_not_decline = true;
        }
        if ( $details['is_cancelled'] == 1 ) {
            $status = 'Preparing Officer canceled this travel order. Remarks: ' . $details['cancel_remarks'];
            // if canceled, cannot print, recommend, approve or decline
            $should_not_print = true;
            $should_not_cancel = true;
            $should_not_recommend = true;
            $should_not_approve = true;
            $should_not_decline = true;
        }

        //if travel date and time has already passed and travel is not yet approve
        $note = '';
        if ( date('Y-m-d', strtotime($details['date_from'])) <= date('Y-m-d') && $details['is_cancelled'] != 1) {
            if (date('Y-m-d', strtotime($details['date_from'])) < date('Y-m-d')) {
                $note = 'Travel Order\'s date from has already passed.';
                // past the departure time
                $should_not_print = false;
                $should_not_cancel = true;
                $should_not_recommend = true;
                $should_not_approve = true;
                $should_not_decline = true;
            } else {
                if ($details['time_of_departure']!='TBA') {
                    if ( time() > strtotime($details['time_of_departure']) ) {
                        $note = 'Travel Order\'s time of departure has already passed.';
                        // past the departure time
                        $should_not_print = false;
                        $should_not_cancel = true;
                        $should_not_recommend = true;
                        $should_not_approve = true;
                        $should_not_decline = true;
                   } 
                }
            }
        }

        if ( $access['can_print'] && $should_not_print) $access['can_print'] = false;
        if ( $access['can_edit'] && $should_not_edit) $access['can_edit'] = false;
        if ( $access['can_cancel'] && $should_not_cancel) $access['can_cancel'] = false;
        if ( $access['can_recommend'] && $should_not_recommend) $access['can_recommend'] = false;
        if ( $access['can_approve'] && $should_not_approve) $access['can_approve'] = false;
        if ( $access['can_decline'] && $should_not_decline) $access['can_decline'] = false;

        return array('status'=>$status,'note'=>$note);
    }

    public function check_employee_availability(){
        $employees_id = $_POST['employees_id'];
        $date_from = date('Y-m-d', strtotime($_POST['date_from']));
        $date_to = date('Y-m-d', strtotime($_POST['date_to']));
        $travel_id = '';
        if (isset($_POST['travel_id'])) {
            $travel_id = $_POST['travel_id'];
        }

        $emp_names = array();
        $emp_ids = array();
        foreach ($employees_id as $id) {
            $ctr = $this->travel_orders_model->employee_availability($id, $date_from, $date_to, $travel_id);

            // get employee name
            if ( $ctr > 0 ) {
                $employee = $this->travel_orders_model->get_employee($id);
                $emp_names[] = $employee[0]['name'];
                $emp_ids[] = $id;
            }
        }

        $str = '';
        if ( count($emp_names) > 1 ) {
            $last = array_pop($emp_names);
            $str = implode(', ', $emp_names);
            $str .= " and " . $last . " are "; 
        } elseif ( count($emp_names) == 1 ) {
            $str = array_pop($emp_names) . " is ";
        }

        $str .=  "not available from " . $_POST['date_from'] ." to ". $_POST['date_to'];
        echo json_encode(array('employee_ids'=>$emp_ids, 'msg'=> $str));
    }

    public function offices(){
        if ($this->session->userdata('type') != 'admin') show_404();
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "Destinations";
        $data['panel_header'] = "Offices";

        $data['province'] = $this->address_model->get_provinces();
        $data['city'] = $this->address_model->get_cities();
        $data['rows'] = count($this->address_model->get_offices());

        $this->lumino_load_view('travel_orders/offices', $data);
    }

    public function get_offices(){
        $offices = $this->address_model->get_offices('',$_POST['limit'],$_POST['offset']);

        $i = 0;
        foreach ($offices as $o) {
            $city = $this->address_model->get_cities($o['citymunCode']);
            $province = $this->address_model->get_provinces($o['provCode']);
            echo '<tr data-index="'.$i.'" data-details="'. htmlentities(json_encode($o)).'">';
            echo '<td>'.$o['officeDesc'].'</td>';
            echo '<td>'.$city[0]['citymunDesc'].'</td>';
            echo '<td>'.$province[0]['provDesc'].'</td>';
            echo '</tr>';
            $i++;
        }
    }

    public function save_office(){
        $office = array();
        parse_str($_POST['data'], $office);
        if (!empty($office['officeCode'])) $this->address_model->update_office($office);
        else $this->address_model->insert_office($office);
    }

    public function delete_office(){
        $this->address_model->delete_office($_POST['officeCode']);
    }

    public function search($keyword){
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "Travel Orders";
        $data['panel_header'] = 'Search';
        $data['keyword'] = $keyword;

        $this->lumino_load_view('travel_orders/by_status', $data);
    }

    public function calendar(){
        $data['page'] = __FUNCTION__;
        $data['page_header'] = "Calendar";

        $this->load->view('templates/header', $data);
        $this->load->view('templates/nav-top', $data);
        $this->load->view('templates/sidebar', $data);
        $this->load->view('templates/main-open', $data);
        $this->load->view('templates/main-top', $data);
        $this->load->view('travel_orders/calendar', $data);
        $this->load->view('templates/main-close', $data);
        $this->load->view('templates/footer', $data);
    }
}
?>