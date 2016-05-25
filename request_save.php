<?php
  session_start();
  //ini_set('display_errors', 0);
  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");

  $uname              = strtolower($_SESSION['pei_user']);
  $pei_msg            = '';
  $req_id             = '';
  $req_priority       = '';
  $req_eta            = '';

  if(isset($_REQUEST['req_id'])) {
    $req_id = $_REQUEST['req_id'];
    $data['req_id'] = $req_id;
  }

  if(isset($_REQUEST['req_priority'])) {
    $req_priority = $_REQUEST['req_priority'];
  }

  if(isset($_REQUEST['req_eta'])) {
    $req_eta = $_REQUEST['req_eta'];
  }

  $request = request_detail($req_id);
  /*
  echo '$req_id :'.$req_id.'<br />';
  echo '$req_priority :'.$req_priority.'<br />';
  echo '$req_eta :'.$req_eta.'<br />';
  */
  if($request) {
    // Save Request Priority
    if($req_priority){
      request_priority_save_detail($req_id, $req_priority, $uname);
    }

    // Save Request ETA
    if($req_eta){
      request_eta_save_detail($req_id, $req_eta, $uname);
    }
  }
  else {
    $pei_msg = 'Invalid Request Id.';
  }

  echo $pei_msg;

?>
