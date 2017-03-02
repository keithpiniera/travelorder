<?php
class PDTS_model extends CI_Model {

  public function __construct() {
    //$this->db = $this->load->database('pdts', TRUE);
    $this->load->library('session');
    $server     = "ISD-KEITH";
    $user       = "PDTSUSERS";
    $password   = "PDTSUSERS";
    $database   = "PDTS";
    $this->conn = odbc_connect("Driver={SQL Server Native Client 11.0};Server=$server;Database=$database;", $user, $password);
    if (!$this->conn) {
      die('Something went wrong while connecting to SQL Server');
    }
  }

  private function db_insert(){

  }

  private function db_query($sql){
    $data = array();
    $res = odbc_exec($this->conn, $sql);
    if($res){
      while($row = odbc_fetch_array($res)){
        $data[] = $row;
      }
    }
    return $data;
  }


  private function get_control_time($type){
    //get deadline for respective doc type
    $sql = "SELECT * FROM controlTime WHERE type = '{$type}'";
    $rows = $this->db_query($sql);
    foreach ($rows as $row) {
      $data = $row['documentTime'];
    }
    return $data;
  }

  private function get_controller($station){
    //get deadline for respective doc type
    $sql = "SELECT * FROM controller WHERE station = '{$station}'";
    $rows = $this->db_query($sql);
    foreach ($rows as $row) {
      $data = $row['nextID'];
    }
    return $data;
  }

  public function compute_date_span_expected($date){
    // compute date span and expected
    $date_new = substr($date, 0,2).' hours'; //convert time

    $date_now = date('Y-m-d h:i:s A');
    $date_s = date_create($date_now);
    date_add($date_s, date_interval_create_from_date_string("'$date_new'"));
    return date_format($date_s, 'Y-m-d h:i:s A');
  }

  public function create_document($data) {

    $details = $data['details'];
    unset($data['details']);
    $branch = 'CES';
    $recipientDiv = $details['recommending_id'] == '' ? $details['approving_division']:$details['recommending_division'];// name = recommender or approver
    $recipientName = $details['recommending_id'] == '' ? $details['approving_name']:$details['recommending_name'];

    $deadlineTime = $this->get_control_time('Correspondence');
    $dateExpected =  $this->compute_date_span_expected($deadlineTime);
    $dateSpanDeadline = $this->get_control_time('Internal');
    $dateSpan =  $this->compute_date_span_expected($dateSpanDeadline);

    $nextID = $this->get_controller($branch);
    $nextID = $nextID == '' ? 0:$nextID;
    $philriceID = $branch.++$nextID;

    $col['classification'] = 'C'; // not yet sure
    $col['barcode'] = $details['tracking_number']; // tracking code
    $col['thread'] = $details['tracking_number'].'-1-'.$branch;
    $col['particulars'] = '['. $this->session->userdata('division') .'-'. $this->session->userdata('name') .'] - '. 'Date from '. $details['date_from'] .' to '. $details['date_to'] .' | Destination(s) '. $data['destination_string'];
    $col['author'] = $this->session->userdata('name');
    $col['attachments'] = 'TO';
    $col['sender'] = $this->session->userdata('division')==''?$this->session->userdata('office'):$this->session->userdata('division'); // division = preparer // division = preparer
    $col['senderName'] = $this->session->userdata('name');
    $col['recipient'] =  $recipientDiv;
    $col['recipientName'] = $recipientName; 
    $col['dateReleased'] = date("Y-m-d H:i:s");
    $col['dateReceived'] = 'Pending'; 
    $col['remarks'] = $data['status']['status'];
    $col['courier'] = 'Direct';
    $col['personConcerned'] = $recipientName;
    $col['dateLog'] = date("Y-m-d H:i:s a");
    $col['status'] = 0;
    $col['forwardCounter'] = 0;
    $col['dateExpected'] = $dateExpected; // date
    $col['philriceID'] = $philriceID; 
    $col['dateSpan'] = $dateSpan; // date
    $col['via_messenger'] = 'N/A'; 
    $col['via_status'] = 'N/A'; 


    $sql = "INSERT INTO [dbo].[document]
           ([classification]
           ,[barcode]
           ,[thread]
           ,[particulars]
           ,[author]
           ,[attachments]
           ,[sender]
           ,[senderName]
           ,[recipient]
           ,[recipientName]
           ,[dateReleased]
           ,[dateReceived]
           ,[remarks]
           ,[courier]
           ,[personConcerned]
           ,[dateLog]
           ,[status]
           ,[forwardCounter]
           ,[dateExpected]
           ,[philriceID]
           ,[dateSpan]
           ,[via_messenger]
           ,[via_status])
     VALUES
           ('". $col['classification'] ."'
           ,'". $col['barcode'] ."'
           ,'". $col['thread'] ."'
           ,'". $col['particulars'] ."'
           ,'". $col['author'] ."'
           ,'". $col['attachments'] ."'
           ,'". $col['sender'] ."'
           ,'". $col['senderName'] ."'
           ,'". $col['recipient'] ."'
           ,'". $col['recipientName'] ."'
           ,'". $col['dateReleased'] ."'
           ,'". $col['dateReceived'] ."'
           ,'". $col['remarks'] ."'
           ,'". $col['courier'] ."'
           ,'". $col['personConcerned'] ."'
           ,'". $col['dateLog'] ."'
           ,'". $col['status'] ."'
           ,'". $col['forwardCounter'] ."'
           ,'". $col['dateExpected'] ."'
           ,'". $col['philriceID'] ."'
           ,'". $col['dateSpan'] ."'
           ,'". $col['via_messenger'] ."'
           ,'". $col['via_status'] ."')";


    //for document logs
    $logQuery = "INSERT INTO document_logs(documentID,threadID,docTime,docDescription)VALUES('$col[barcode]','$col[thread]','$col[dateLog]','Transaction has been created - author: ".$this->session->userdata['name']." || Concerned Division/Unit(s): ".$recipientDiv."')";
    //end
  
    //for user notifications
    $notifQuery = "INSERT INTO notifications(recipient,recipientName,barcode,thread,status,notificationDate,notificationStatus)VALUES('$recipientDiv','$recipientName','$col[barcode]','$col[thread]','1','$col[dateLog]','New Pending Transaction')";
    //end

    //add counter to station controller
    $control_query = "UPDATE controller SET nextID='$nextID' WHERE station='$branch'";
    //end

    odbc_autocommit($this->conn, false);
    odbc_exec($this->conn, $sql);
    odbc_exec($this->conn, $logQuery);
    odbc_exec($this->conn, $notifQuery);
    odbc_exec($this->conn, $control_query);

    if (!odbc_error()) odbc_commit($this->conn);
    else odbc_rollback($this->conn);
  }

  public function forward_document($data){
    $details = $data['details'];
    $branch = 'CES';
    unset($data['details']);

    // determine recipient
    if ($details['recommend'] == 0) {
      // recommender
      $recipientDiv = $details['recommending_division'];
      $recipientName = $details['recommending_name'];
    }
    elseif ($details['recommend'] == 1 || $details['approve'] == 1 || $details['approve'] == 2) {
      // preparer
      $recipientDiv = $details['preparing_division'];
      $recipientName = $details['preparing_name'];
    }
    elseif ($details['recommend'] == 2 || $details['recommend'] == 3) {
      // approver
      $recipientDiv = $details['approving_division'];
      $recipientName = $details['approving_name'];
    }

    $deadlineTime = $this->get_control_time('Correspondence');
    $dateExpected =  $this->compute_date_span_expected($deadlineTime);
    $dateSpanDeadline = $this->get_control_time('Internal');
    $dateSpan =  $this->compute_date_span_expected($dateSpanDeadline);

    $col['barcode'] = $details['tracking_number']; // tracking code
    $col['thread'] = $details['tracking_number'].'-1-'.$branch;
    $col['sender'] = $this->session->userdata('division')==''?$this->session->userdata('office'):$this->session->userdata('division'); // division = preparer
    $col['senderName'] = $this->session->userdata('name');
    $col['recipient'] =  $recipientDiv;
    $col['recipientName'] = $recipientName; 
    $col['dateReleased'] = date("Y-m-d H:i:s");
    $col['dateReceived'] = 'Pending'; 
    $col['remarks'] = $data['status']['status'];
    $col['personConcerned'] = $recipientName;
    $col['dateLog'] = date("Y-m-d H:i:s a");
    $col['status'] = 0;
    $col['forwardCounter'] = 0;
    $col['dateExpected'] = $dateExpected; // date
    $col['dateSpan'] = $dateSpan; // date


     $sql = "UPDATE [dbo].[document]
           SET [sender] = '$col[sender]'
           ,[senderName] = '$col[senderName]'
           ,[recipient] = '$col[recipient]'
           ,[recipientName] = '$col[recipientName]'
           ,[dateReleased] = '$col[dateReleased]'
           ,[dateReceived] = '$col[dateReceived]'
           ,[remarks] = '$col[remarks]'
           ,[personConcerned] = '$col[personConcerned]'
           ,[dateLog] = '$col[dateLog]'
           ,[status] = '$col[status]'
           ,[forwardCounter] = '$col[forwardCounter]'
           ,[dateExpected] = '$col[dateExpected]'
           ,[dateSpan] = '$col[dateSpan]'
           WHERE [barcode] = '$col[barcode]'";
 
    //for document logs
    $logQuery = "INSERT INTO document_logs(documentID,threadID,docTime,docDescription)VALUES('$col[barcode]','$col[thread]','$col[dateLog]','Transaction has been forwarded - author: ".$this->session->userdata['name']." || Concerned Division/Unit(s): ".$recipientDiv."')";
    //end
  
    //for user notifications
    $notifQuery = "INSERT INTO notifications(recipient,recipientName,barcode,thread,status,notificationDate,notificationStatus)VALUES('$recipientDiv','$recipientName','$col[barcode]','$col[thread]','1','$col[dateLog]','Pending Transaction')";
    //end
    
    odbc_autocommit($this->conn, false);
    odbc_exec($this->conn, $sql);
    odbc_exec($this->conn, $logQuery);
    odbc_exec($this->conn, $notifQuery);

    if (!odbc_error()) odbc_commit($this->conn);
    else odbc_rollback($this->conn);

  }
}
?>