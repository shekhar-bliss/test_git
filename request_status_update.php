<?php
  session_start();
  //ini_set('display_errors', 0);
  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");

  //$_REQUEST['req_id'] = 'REQ15-0001';
  $uname              = $_SESSION['pei_user'];
  $pei_msg            = '';
  $req_id             = '';
  $req_status         = 'ON-HOLD';
  $req_status_id      = 5;
  // Status ID released
  $status_id = 5; // ON-HOLD

  if(isset($_REQUEST['req_id'])) {
    $req_id = $_REQUEST['req_id'];
    $data['req_id'] = $req_id;
  }

  if(isset($_REQUEST['req_status_remark'])) {
    $data['status_remark'] = $_REQUEST['req_status_remark'];
  }

  if(isset($_REQUEST['req_status_id'])) {
    $status_id = $_REQUEST['req_status_id'];
  }

  $request = request_detail($req_id);
  //echo '$req_id :'.$req_id.'<br />';
  if($request) {
    if($request['req_status'] == 'RELEASED') {
      $pei_msg = 'Already RELEASED';
    }
    else {
      // Save New Request Status
      $data['status_id']  = $status_id;
      $data['created_by'] = $uname;
      $data['created_at'] = 'NOW';

      $status = status_detail($status_id);
      if($status_id){
        switch($status_id) {
          case '6':
            // Find previous status before ON-HOLD
            $req_status_history = get_request_status_history($req_id);
            if($req_status_history) {
              $req_status     = 'WIP';
              $req_status_id  = 6;
              foreach ($req_status_history as $history) {
                if($history['status_id'] == '2' || $history['status_id'] == '4' ) {
                  $req_status_id  = $history['status_id'];
                  if($history['status_id'] == '4') {
                    $req_status = 'PARTIAL RELEASED';
                    $status_id  = 4;
                    $data['status_id']  = $status_id;
                    $data['created_at'] = $history['created_at'];
                    $data['updated_by'] = $uname;
                    $data['updated_at'] = 'NOW';
                  }
                  break;
                }
              }
              update_request_status($req_id, $req_status, $req_status_id);
            }
            break;
          default:
            if($status && $status['status_parent_id'] == 5){
              // Update Request Status in idc_request
              update_request_status($req_id, $req_status, $req_status_id);
            }
        }
      }
      request_status_save($data);
    }


  }
  else {
    $pei_msg = 'Invalid Request Id.';
  }

  echo $pei_msg;

?>
