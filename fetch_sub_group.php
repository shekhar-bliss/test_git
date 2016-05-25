<?php
  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");

  $sql_where    = ' 1 ';
  $group_id     = '';
  $group_name   = '';
  $sub_group_id = '';
  if(isset($_REQUEST['group_id'])){
    $group_id = $_REQUEST['group_id'];
    $sql_where  .= " AND user_group_parent_id = '".mysql_real_escape_string($group_id)."'";
  }
  if(isset($_REQUEST['group_name'])){
    $group_name = $_REQUEST['group_name'];
    $sql_where  .= " AND user_group_name LIKE '%".mysql_real_escape_string($group_name)."%'";
  }
  if(isset($_REQUEST['sub_group_id'])){
    $sub_group_id = $_REQUEST['sub_group_id'];

  }
  $data = '';

  // Fetch All Main Group Details
  $sql_sub_group = " SELECT *
                      FROM idc_user_group
                      WHERE ".$sql_where."
                      ";
  //echo $sql_sub_group;
  $res_sub_group = mysql_query($sql_sub_group);

  if($res_sub_group) {
    $data[] = array('id' => '', 'text' => '--- REQUESTOR SUB GROUP ---');
     while($sub_group = mysql_fetch_array($res_sub_group)) {
      $data[] = array('id' => $sub_group['user_group_id'], 'text' => strtoupper($sub_group['user_group_name']));
    }
  }


  echo json_encode($data);
?>
