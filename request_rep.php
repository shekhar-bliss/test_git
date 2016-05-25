<?php
  session_start();
  $pei_current_module = 'REQUEST';

  require_once(__dir__.'/../header.php');
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  //echo error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';
//echo '$pei_user_access_permission <br />';
//var_dump($pei_user_access_permission);

  // Initialize variables
  $pei_msg        = '';
  $req_msg        = '';
  $req_status     = 'WIP/PARTIAL/ON-HOLD';
  $req_rfi_from   = '';
  $req_rfi_to     = '';
  $req_group      = '';
  $req_user_group = '';
  $user_group     = '';
  $user_group_sub = '';
  $req_sub_group  = '';
  $req_priority   = '';
  $req_name       = '';
  $req_title      = '';
  $start_from     = 0;
  $record_per_page= $pei_config['pagination']['record_per_page'];
  $record_found   = 0;
  $sql_where      = ' 1 ';
  $show_page      = 1;
  $page           = 0;
  $search_query   = '';

  $data                 = '';
  $data_priority        = pei_priority();
  $data_priority_val    = '';
  foreach ($data_priority as $key => $value) {
    $data_priority_val[$value['value']] = $value['name'];
  }
  $data_req_group       = user_group_distinct();
  $data_req_user_group  = get_requestor_user_group();
  $data_req_sub_group   = '';
  $data_req_status      = status_request_status();

  // ON-HOLD => 5;
  $data_hold_status       = status_request_hold_status();
  $req_user               = get_all_user();
  $perm_update_status     = FALSE;
  $perm_download_status   = FALSE;

  // Check if user has permission to updated request status or not
  if(in_array('edit_any_request_status', $pei_user_access_permission)){
    $perm_update_status  = TRUE;
  }

  if(in_array('download_request_status_report', $pei_user_access_permission)){
    $perm_download_status  = TRUE;
  }

  $sql_where .= " AND r.req_status_id IS NOT NULL";

  // Search Query
  if(isset($_GET['search'])) {

    if(isset($_GET['req_rfi_from']) && $_GET['req_rfi_from'] != '') {
      $req_rfi_from = $_GET['req_rfi_from'];
      if(isset($_GET['req_rfi_to']) && $_GET['req_rfi_to'] != '') {
        $req_rfi_to = $_GET['req_rfi_to'];
        //echo date('Y-m-d H:i:s', strtotime($req_rfi_from)).'<br />';
        $sql_where .= " AND rs_rfi.rfi_date BETWEEN '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($req_rfi_from)))."' AND  '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($req_rfi_to)))."' ";
      }
      else {
        $sql_where .= " AND rs_rfi.rfi_date  >= '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($req_rfi_from)))."'  ";
      }
    }
    else {
      $req_rfi_to = $_GET['req_rfi_to'];
      if(isset($_GET['req_rfi_to']) && $_GET['req_rfi_to'] != '') {
        $sql_where .= " AND rs_rfi.rfi_date <= '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($req_rfi_to)))."'  ";
      }
    }

    if(isset($_GET['req_group']) && $_GET['req_group'] != '') {
      $req_group = $_GET['req_group'];
      // Find all sub group of same name
      $sub_group_ids = " '0' ";
      $other_sub_group = user_group_other_group($req_group);
      if($other_sub_group){
        foreach ($other_sub_group as $key => $value) {
          if($value['user_group_id']){
            $sub_group_ids .= ", '".mysql_real_escape_string($value['user_group_id'])."' ";
          }
        }
      }
      $sql_where .= " AND r.req_group_sub_id IN (".$sub_group_ids." ) ";
    }

    if(isset($_GET['user_group']) && $_GET['user_group'] != '') {
      $req_user_group = $_GET['user_group'];
      $sql_where .= " AND r.req_group_id = '".mysql_real_escape_string($req_user_group)."'";
      $data_req_sub_group = get_user_group_sub_group($req_user_group);
    }

    if (isset($_GET['user_group_sub']) && $_GET['user_group_sub'] != '') {
      $req_sub_group  = $_GET['user_group_sub'];
      $sql_where .= " AND r.req_group_sub_id = '".mysql_real_escape_string($req_sub_group)."'";
    }

    if (isset($_GET['req_priority']) && $_GET['req_priority'] != '') {
      $req_priority      = $_GET['req_priority'];
      $sql_where .= " AND rp.req_priority ='".mysql_real_escape_string($req_priority)."'";
    }

    if (isset($_GET['req_name']) && $_GET['req_name'] != '') {
      $req_name      = $_GET['req_name'];
      $sql_where .= " AND r.req_id LIKE '%".mysql_real_escape_string($req_name)."%'";
    }

    if (isset($_GET['req_title']) && $_GET['req_title'] != '') {
      $req_title  = $_GET['req_title'];
      $sql_where .= " AND (
                            r.req_title LIKE '%".mysql_real_escape_string($req_title)."%'
                            OR
                            r.req_id LIKE '%".mysql_real_escape_string($req_title)."%'
                            OR
                            u_rpm.user_name LIKE '%".mysql_real_escape_string($req_title)."%'
                          )";
    }
  }

  if(isset($_GET['req_status']) && $_GET['req_status'] != '') {
    $req_status = $_GET['req_status'];
    switch ($req_status) {
      case 'ALL':
        break;
      case 'WIP/PARTIAL/ON-HOLD':
        $sql_where .= " AND r.req_status_id !='3' ";
        break;
      default:
        $sql_where .= " AND r.req_status_id = '".mysql_real_escape_string($req_status)."'";
        break;
    }
  }
  else{
    $sql_where .= " AND r.req_status_id !='3' ";
  }


  // Find total no. of records found for search query required for pagination
  $sql_total = "SELECT r.req_id
                FROM
                  idc_request AS r
                  LEFT JOIN
                  (
                    SELECT
                      req_status_id,
                      req_id,
                      status_id,
                      MIN(created_at) AS 'rfi_date'
                    FROM
                      idc_request_status
                    WHERE
                      1
                      AND
                      status_id IN (2, 6)
                    GROUP BY req_id
                  ) AS rs_rfi ON r.req_id = rs_rfi.req_id
                  LEFT JOIN
                  idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                  LEFT JOIN
                  idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
                  LEFT JOIN
                  idc_request_pm AS rpm ON r.req_id = rpm.req_id AND rpm.req_pm_status = 1
                  LEFT JOIN
                  idc_user AS u_rpm ON LOWER(rpm.req_pm) = LOWER(u_rpm.user_login)
                  LEFT JOIN
                  idc_request_priority AS rp ON r.req_id = rp.req_id AND rp.req_priority_status = 1
                  LEFT JOIN
                  idc_request_eta AS re ON r.req_id = re.req_id AND re.req_eta_status = 1
                WHERE ".$sql_where;
  //echo $sql_total.'<br />';
  $res_total      = mysql_query($sql_total);
  $total_records  = mysql_num_rows($res_total);  //count number of records
  $total_pages    = ceil($total_records / $record_per_page);//total pages we going to have

  //-------------if page is setcheck------------------//
  if (isset($_GET['page'])) {
    $show_page = $_GET['page']; //current page
    if ($show_page > 0 && $show_page <= $total_pages) {
        $start_from  = ($show_page - 1) * $record_per_page;
        $end = $start_from  + $record_per_page;
    } else {
      // error - show first set of results
      $start_from   = 0;
      $end          = $record_per_page;
    }
  } else {
    // if page isn't set, show first set of results
    $start_from   = 0;
    $end          = $record_per_page;
  }

  $sql_limit    = ' LIMIT '.$start_from.', '.$record_per_page;
  $sql_order_by = ' ORDER BY r.req_id DESC';
  $sql_search   = "SELECT
                    r.*,
                    ug.user_group_name AS req_group_name,
                    ugs.user_group_name AS req_group_name_sub,
                    u_c.user_name AS req_created_by,
                    u_u.user_name AS req_updated_by,
                    u_rpm.user_name AS req_pm_name,
                    rp.req_priority,
                    re.req_eta
                  FROM
                    idc_request AS r
                    LEFT JOIN
                    (
                      SELECT
                        req_status_id,
                        req_id,
                        status_id,
                        MIN(created_at) AS 'rfi_date'
                      FROM
                        idc_request_status
                      WHERE
                        1
                        AND
                        status_id IN (2, 6)
                      GROUP BY req_id
                    ) AS rs_rfi ON r.req_id = rs_rfi.req_id
                    LEFT JOIN
                    idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                    LEFT JOIN
                    idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
                    LEFT JOIN
                    idc_user AS u ON r.req_idc_spoc = u.user_login
                    LEFT JOIN
                    idc_user AS u_c ON r.created_by = u_c.user_login
                    LEFT JOIN
                    idc_user AS u_u ON r.updated_by = u_u.user_login
                    LEFT JOIN
                    idc_request_pm AS rpm ON r.req_id = rpm.req_id AND rpm.req_pm_status = 1
                    LEFT JOIN
                    idc_user AS u_rpm ON LOWER(rpm.req_pm) = LOWER(u_rpm.user_login)
                    LEFT JOIN
                    idc_request_priority AS rp ON r.req_id = rp.req_id AND rp.req_priority_status = 1
                    LEFT JOIN
                    idc_request_eta AS re ON r.req_id = re.req_id AND re.req_eta_status = 1
                  WHERE ".$sql_where."
                  ".$sql_order_by.'  '.$sql_limit.';';
  //echo $sql_search.'<br />';
  $res_search = mysql_query($sql_search);

  if(!$res_search) {
    $pei_msg .= 'Something went wrong.';
  }
  else {
    $record_found = mysql_num_rows($res_search);
    while($row=mysql_fetch_array($res_search)) {
      $row['igf_count']           = 0;
      $row['server_total']        = 0;
      $row['server_physical']     = 0;
      $row['server_virtual']      = 0;
      $row['server_released']     = 0;
      $row['server_released_phy'] = 0;
      $row['server_released_vir'] = 0;
      $row['server_pending']      = 0;
      $row['server_pending_phy']  = 0;
      $row['server_pending_vir']  = 0;
      $row['storage_node_count']    = NULL;
      $row['ha_cluster_node_count'] = NULL;
      $row['rfi_date']              = '';
      $row['released_status']       = '';
      $row['released_date']         = '';
      $row['status_remark']         = '';
      $row['status_remark_name']    = '';


      $row['igf_count'] = get_req_igf_count($row['req_id']);
      if($row['igf_count'] > 0) {
        // Server Information
        $row['server_physical']     = count_igf_server_count($row['req_id'], '3');
        $row['server_virtual']      = count_igf_server_count($row['req_id'], '4');
        $row['server_total']        = $row['server_physical'] + $row['server_virtual'];
        // RELEASED
        $row['server_released']     = count_igf_server_released_by_req_id($row['req_id']);
        $row['server_released_phy'] = count_igf_server_released_by_req_id($row['req_id'], 3);
        $row['server_released_vir'] = count_igf_server_released_by_req_id($row['req_id'], 4);
        // PENDING
        $row['server_pending']      = $row['server_total'] - $row['server_released'];
        $row['server_pending_phy']  = $row['server_physical'] - $row['server_released_phy'];
        $row['server_pending_vir']  = $row['server_virtual'] - $row['server_released_vir'];

        // SAN Node Count in Request IGF
        $storage_ext_type = req_igf_storage_node_count($row['req_id']);
        if($storage_ext_type) {
          $row['storage_node_count'] = $storage_ext_type;
        }

        // HA CLUSTER Node Count in Request IGF
        $ha_cluster = req_igf_ha_cluster_node_count($row['req_id']);
        if($ha_cluster) {
          $row['ha_cluster_node_count'] = $ha_cluster;
        }
      }// END IF $row['igf_count']

      $req_status_history = get_request_status_history($row['req_id']);
      if($req_status_history) {
        $row['status_remark']  = $req_status_history[0]['status_remark'];
        foreach ($req_status_history as $key => $value) {
          $row['rfi_date']    = $value['created_at'];
        }
      }

      switch ($row['req_status']) {
        case 'RELEASED':
          if($req_status_history){
            $row['released_date']       = $req_status_history[0]['created_at'];
            $row['status_remark']       = $req_status_history[0]['status_remark'];
            $row['status_remark_name']  = $req_status_history[0]['status_name'];
          }
          break;
        case 'RFI':
          // Then request is in WIP Stage
          $row['status_remark_name']  = 'WIP';
        case 'WIP':
          // Then request can be in WIP or PARTIAL WIP stage
          $row['status_remark_name']  = 'WIP';
          if( ($row['server_released'] > 0) && ($row['server_pending'] > 0) ){
            $row['status_remark_name']  = 'PARTIAL';
            if($req_status_history){
              foreach ($req_status_history as $key => $value) {
                if($value['status_name'] == 'PARTIAL RELEASED'){
                  $row['released_date']       = $value['created_at'];
                  break;
                }
              }
            }
          }
          break;
        case 'PARTIAL RELEASED':
          // Then request can be in PARTIAL ON-HOLD or PARTIAL WIP stage
          if($req_status_history){
            foreach ($req_status_history as $key => $value) {
              if($value['status_name'] == 'PARTIAL RELEASED'){
                $row['released_date']       = $value['created_at'];
                break;
              }
            }
          }
          $row['status_remark_name']  = 'PARTIAL';
          break;
        case 'ON-HOLD':
          // Then request can be in ON-HOLD or PARTIAL ON-HOLD stage
          $row['status_remark_name']  = 'ON-HOLD';
          if( ($row['server_released'] > 0) && ($row['server_pending'] > 0) ){
            $row['status_remark_name']  = 'PARTIAL ON-HOLD';
            if($req_status_history){
              foreach ($req_status_history as $key => $value) {
                if($value['status_name'] == 'PARTIAL RELEASED'){
                  $row['released_date']       = $value['created_at'];
                  break;
                }
              }
            }
          }
          break;
        default:

      }// END switch $row['req_status']





      $data[] = $row;
    } // END while
  }

  // display pagination
  if(isset($_GET['page'])) {
    $page = intval($_GET['page']);
  }
  $tpages=$total_pages;
  if ($page <= 0) {
    $page = 1;
  }

  $pagination_url = $_SERVER['PHP_SELF'].'?';


  if(isset($_GET['search'])) {
    $pagination_url .= '&search=';
  }
  if(isset($_GET['req_status'])) {
    $search_query .= '&req_status='.$_GET['req_status'];
  }
  if(isset($_GET['req_rfi_from'])) {
    $search_query .= '&req_rfi_from='.$_GET['req_rfi_from'];
  }
  if(isset($_GET['req_rfi_to'])) {
    $search_query .= '&req_rfi_to='.$_GET['req_rfi_to'];
  }
  if(isset($_GET['req_group'])){
    $search_query .= '&req_group='.$_GET['req_group'];
  }
  if(isset($_GET['user_group'])){
    $search_query .= '&user_group='.$_GET['user_group'];
  }
  if(isset($_GET['user_group_sub'])){
    $search_query .= '&user_group_sub='.$_GET['user_group_sub'];
  }
  if(isset($_GET['req_name'])){
    $search_query .= '&req_name='.$_GET['req_name'];
  }
  if(isset($_GET['req_title'])) {
    $search_query .= '&req_title='.$_GET['req_title'];
  }
  if(isset($_GET['req_priority'])) {
    $search_query .= '&req_priority='.$_GET['req_priority'];
  }

  $pagination_url .= $search_query;

  //echo $search_query.'<br />';

  // Disable Status update for now
  $perm_update_status = FALSE;
  //echo '</pre>';
?>

    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="#">Report</a></li>
        <li class="active">Request Status</li>
        <div class="pei-breadcrumb-right">
          Total : <?php echo $total_records;?>
        </div>
      </ol>
      <!-- /breadcrumb  -->

      <div class="box-content">

<?php
  if($pei_msg) {
?>
      <div class="alert alert-danger" role="alert">
      <?php echo preg_replace("/\\\\n/", "<br />", $pei_msg);?>
      </div>
<?php
  }
?>
        <form class="form-inline" method="GET">
        <div class="form-group">
          <label class="sr-only" for="user_group">Request Status</label>
          <select class="placeholder form-control dropdown-select2" name="req_status" id="req-status">
            <option value="ALL">All REQUESTS</option>
            <option value="WIP/PARTIAL/ON-HOLD" <?php if($req_status ==  'WIP/PARTIAL/ON-HOLD') { ?> selected="selected" <?php } ?>>WIP / PARTIAL / ON-HOLD</option>
        <?php
        foreach ($data_req_status as $key => $status) {
          if($status['status_name'] != 'RFI'){
        ?>
            <option value="<?php echo $status['status_id'];?>" <?php if($req_status ==  $status['status_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($status['status_name']);?></option>
        <?php
          }
        }
        ?>
          </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_priority">Priority</label>
          <select class="placeholder form-control dropdown-select2" name="req_priority" id="req-priority">
            <option value="">PRIORITY</option>
        <?php
        foreach ($data_priority as $key => $priority) {
        ?>
            <option value="<?php echo $priority['value'];?>" <?php if($req_priority ==  $priority['value']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($priority['name']);?></option>
        <?php
        }
        ?>
          </select>
        </div>
        <div class="form-group">
          <div class='input-group date' id='req-rfi-from-datetimepicker'>
            <input type='text' class="form-control" name="req_rfi_from" size="13" value="<?php echo $req_rfi_from;?>" placeholder="From RFI Date" />
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
          </div>
        </div>
        <div class="form-group">
          <div class='input-group date' id='req-rfi-to-datetimepicker'>
            <input type='text' class="form-control" name="req_rfi_to" size="13" value="<?php echo $req_rfi_to;?>" placeholder="To RFI Date" />
              <span class="input-group-addon">
              <span class="glyphicon glyphicon-calendar"></span>
            </span>
          </div>
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_group">Requestor Group</label>
          <select class="placeholder form-control dropdown-select2" name="req_group" id="req-group" placeholder="REQUESTOR SUB GROUP">
          <option value="">-- Select a user group --</option>
        <?php
        foreach ($data_req_group as $key => $group) {
        ?>
          <option value="<?php echo $group['user_group_id'];?>" <?php if($req_group ==  $group['user_group_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($group['user_group_name']);?></option>
        <?php
        }
        ?>
        </select>
        </div>
        <!--
        <div class="form-group">
          <label class="sr-only" for="user_group">Requestor Group</label>
          <select class="placeholder form-control dropdown-select2" name="user_group" id="req-user-group" placeholder="REQUESTOR GROUP">
          <option value="">-- Select a user group --</option>
        <?php
        foreach ($data_req_user_group as $key => $user_group) {
        ?>
          <option value="<?php echo $user_group['user_group_id'];?>" <?php if($req_user_group ==  $user_group['user_group_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($user_group['user_group_name']);?></option>
        <?php
        }
        ?>
        </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="user_group_sub">Requestor Sub Group</label>
          <select class="placeholder form-control" name="user_group_sub" id="req-sub-group" placeholder="REQUESTOR SUB GROUP">
            <option value="">-- Select a sub group --</option>
            <?php
            if($data_req_sub_group) {
              foreach ($data_req_sub_group as $key => $sub_group) {
            ?>
            <option value="<?php echo $sub_group['user_group_id'];?>" <?php if($req_sub_group ==  $sub_group['user_group_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($sub_group['user_group_name']);?></option>
            <?php
              }
            }
            ?>
          </select>
        </div>
        -->
        <div class="form-group">
          <label class="sr-only" for="req_title">Request Title</label>
          <input type="text" class="form-control input-sm" name="req_title" id="req-title" placeholder="ID / TITLE / PM / IM" value="<?php echo $req_title;?>">
        </div>
        <button type="submit" value="Search" name="search" class="btn btn-primary">Search</button>
        <?php
        if($perm_download_status) {
        ?>
        <button type="button" value="Download" name="search" class="btn btn-primary" id="dummy-download" onclick="pei_click_real_download();">Download</button>
        <?php
        }
        ?>
      </form>
      <div class="hide">
      <form class="form-inline" method="POST" action="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_rep_download.php">
        <input type='hidden' id="rep-status" name="rep_status" value="<?php echo $req_status;?>" />
        <input type='hidden' id="rep-rfi-from" name="rep_rfi_from" value="<?php echo $req_rfi_from;?>" />
        <input type='hidden' id="rep-rfi-to" name="rep_rfi_to" value="<?php echo $req_rfi_to;?>" />
        <input type='hidden' id="rep-req-group" name="rep_req_group" value="<?php echo $req_group;?>" />
        <input type='hidden' id="rep-user-group" name="rep_user_group" value="<?php echo $req_user_group;?>" />
        <input type='hidden' id="rep-user-group-sub" name="rep_user_group_sub" value="<?php echo $req_sub_group;?>" />
        <input type='hidden' id="rep-req-title" name="rep_req_title" value="<?php echo $req_title;?>" />
        <input type='hidden' id="rep-req-priority" name="rep_req_priority" value="<?php echo $req_priority;?>" />
      <button type="submit" name="download" value="xlsx" onclick="return validate_download();" class="btn btn-primary" id="real-download">Download</button>
      </form>
      </div>
      <div class="clearfix"></div>

<!-- table table-bordered table-hover table-request-list -->
      <table class="table-bordered table-hover table-request-list" style="table-layout: fixed;width: 1500px;">
        <tr class="active">
          <th width="90px" class="text-center">Request Id</th>
          <th width="90px" class="text-center">RFI Date</th>
          <th width="50px" class="text-center">Priority</th>
          <th width="90px" class="text-center">PM / IM</th>
          <th width="90px" class="text-center">Expected / Tentative ETA</th>
          <th width="90px">Requestor <br />Sub-Group</th>
          <th>Request Title</th>
          <th width="90px">Environment</th>
          <th width="70px">Location</th>
          <th width="40px">Server Hall</th>
          <th width="75px" class="text-center">Total Equipment</th>
          <th width="50px" class="text-center">Physical Count</th>
          <th width="50px" class="text-center">Virtual Count</th>
          <th width="50px" class="text-center">SAN (Node Count)</th>
          <th width="70px" class="text-center">CLUSTER (Node Count)</th>
          <th width="90px" class="text-center">Release Date</th>
          <th width="120px" class="text-center">Status</th>
          <th>Remark</th>
          <?php
          if($perm_update_status) {
          ?>
          <th width="60px">Action</th>
          <?php
          }
          ?>
        </tr>
  <?php
    if($data) {
      foreach ($data as $key => $row) {
  ?>
        <tr valign="top">
          <td class="text-center"><?php echo strtoupper($row['req_id']);?></td>
          <td class="text-center"><?php echo ($row['rfi_date']) ? pei_date_format($row['rfi_date']) : '';?></td>
          <td class="text-center"><?php
          if($row['req_priority']){
            echo $data_priority_val[$row['req_priority']];
          }
          ?>
          </td>
          <td><?php echo strtoupper($row['req_pm_name']);?></td>
          <td class="text-center"><?php echo $row['req_eta'];?></td>
          <td><?php echo strtoupper($row['req_group_name_sub']);?></td>
          <td><?php echo strtoupper($row['req_title']);?></td>
          <td><?php
            $loc_env = '';
            $loc_env = get_req_env_string($row['req_id']);
            if($loc_env) {
              $loc_env = str_replace(',', ', ', $loc_env);
            }
            echo strtoupper($loc_env);
            ?>
          </td>
          <td><?php
            $loc_str = '';
            $loc_str = get_req_loc_string($row['req_id']);
            if($loc_str) {
              $loc_str = str_replace(',', ', ', $loc_str);
            }
            echo strtoupper($loc_str);
            ?>
          </td>
          <td><?php
            $sh_str = '';
            $sh_str = get_req_sh_string($row['req_id']);
            if($sh_str) {
              $sh_str = str_replace(',', ', ', $sh_str);
            }
            echo strtoupper($sh_str);
            ?>
          </td>
          <td class="text-center"><?php echo sprintf("%02d", $row['server_total']);?></td>
          <td class="text-center"><?php echo sprintf("%02d", $row['server_physical']);?></td>
          <td class="text-center"><?php echo sprintf("%02d", $row['server_virtual']);?></td>
          <td class="text-center"><?php
          if($row['storage_node_count'] && ($row['storage_node_count']['SAN']['PHYSICAL'] + $row['storage_node_count']['SAN']['VIRTUAL']) != 0){
          ?>
            T:<?php echo sprintf("%02d", ($row['storage_node_count']['SAN']['PHYSICAL'] + $row['storage_node_count']['SAN']['VIRTUAL']) );?>
            <br />P:<?php echo sprintf("%02d", $row['storage_node_count']['SAN']['PHYSICAL']);?>
            <br />V:<?php echo sprintf("%02d", $row['storage_node_count']['SAN']['VIRTUAL']);?>
          <?php
          }
          ?>
          </td>
          <td class="text-center"><?php
          if($row['ha_cluster_node_count'] && ($row['ha_cluster_node_count']['PHYSICAL'] + $row['ha_cluster_node_count']['VIRTUAL']) != 0){
          ?>
            T:<?php echo sprintf("%02d", ($row['ha_cluster_node_count']['PHYSICAL'] + $row['ha_cluster_node_count']['VIRTUAL']) );?>
            <br />P:<?php echo sprintf("%02d", $row['ha_cluster_node_count']['PHYSICAL']);?>
            <br />V:<?php echo sprintf("%02d", $row['ha_cluster_node_count']['VIRTUAL']);?>
          <?php
          }
          ?>
          </td>
          <td class="text-center"><?php echo ($row['released_date']) ? pei_date_format($row['released_date']) : '';?></td>
          <td class="text-center">
            <span id="req-status-text-<?php echo $row['req_id'];?>">
            <?php
              if(isset($row['status_remark_name']) && $row['status_remark_name'] != '') {
                echo $row['status_remark_name'];
              }
              else {
                echo $row['released_status'];
              }
            ?>
            </span>
            <select class="form-control hide" name="req_status_reason" id="req-status-reason-<?php echo $row['req_id'];?>">
            <?php
            if($data_hold_status) {
              foreach ($data_hold_status as $key => $hold_status) {
                $status_remark_name = trim(strtoupper($row['status_remark_name']));
                switch ($status_remark_name) {
                  case 'PARTIAL ON-HOLD':
                    $status_remark_name   = 'ON-HOLD';
                    break;
                  case 'PARTIAL':
                    $status_remark_name   = 'WIP';
                    break;
                  default:
                    # code...
                    break;
                }
            ?>
              <option value="<?php echo $hold_status['status_id'];?>"
              <?php
              if(trim(strtoupper($hold_status['status_name'])) == $status_remark_name ) {
                ?> selected="selected"
                <?php } ?>
                >
              <?php

              echo ($hold_status['status_name'] == 'WIP') ? ( ($row['status_remark_name'] == 'PARTIAL') ? 'PARTIAL' : $hold_status['status_name']) : $hold_status['status_name'];

              ?>
              </option>
            <?php
              }
            }
            ?>
            </select>
          <?php
            if( ($row['server_released'] > 0) && ($row['server_pending'] > 0) ) {
          ?>
          <table class="table table-bordered table-condensed" style="margin-bottom:2px;">
            <tr>
              <th class="text-center">PARTIAL</th>
              <th class="text-center">PENDING</th>
            </tr>
            <tr>
              <td>T:<?php echo sprintf("%02d", $row['server_released'] );?></td>
              <td>T:<?php echo sprintf("%02d", $row['server_pending'] );?></td>
            </tr>
            <tr>
              <td>P:<?php echo sprintf("%02d", $row['server_released_phy']);?></td>
              <td>P:<?php echo sprintf("%02d", $row['server_pending_phy'] );?></td>
            </tr>
            <tr>
              <td>V:<?php echo sprintf("%02d", $row['server_released_vir'] );?></td>
              <td>V:<?php echo sprintf("%02d", $row['server_pending_vir']);?></td>
            </tr>
          </table>
          <?php
          }
          ?>
          </td>
          <td>
          <span id="req-status-remark-text-<?php echo $row['req_id'];?>">
            <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($row['status_remark'], 40);?></span><span class="pei-content-less-link <?php if(strlen($row['status_remark']) < 40 ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
            <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $row['status_remark'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
          </span>
          <textarea class="form-control hide" name="req_status_remark" placeholder="Remarks" data-toggle="tooltip" data-placement="bottom" title="Remarks" id="req-status-remark-<?php echo $row['req_id'];?>"><?php echo $row['status_remark'];?></textarea>
          </td>
          <?php
          if($perm_update_status) {
          ?>
          <td>
          <?php
            if($row['req_status'] != 'RELEASED') {
          ?>
            <button type="button" name="edit" value="edit" class="btn btn-primary" id="req-status-edit-<?php echo $row['req_id'];?>" onclick="return req_status_edit('<?php echo $row['req_id'];?>');">Edit</button>
            <button type="button" name="save" value="save" class="btn btn-primary hide" id="req-status-save-<?php echo $row['req_id'];?>" onclick="return req_status_save('<?php echo $row['req_id'];?>');">Save</button>
          <?php
            }
          ?>
          </td>
          <?php
          }
          ?>
        </tr>
    <?php
      } // END foreach $data
    }
    ?>
      </table>


<?php


if ($total_pages > 1) {

//echo $reload.'<br />';
?>
    <div class="pei-float-center">
     <nav>
      <ul class="pagination">
    <?php echo pei_paginate($pagination_url, $show_page, $total_pages);?>
      </ul>
    </nav>
   </div>
<?php
}
?>
      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
  require_once(__dir__.'/../footer.php');
?>

<script type="text/javascript">

  function pei_click_real_download() {
    $( "#real-download" ).trigger( "click" );
  }
  function validate_download(){
    // update all search value with download form
    var status = $('#req-status').val();
    var from = $( "input[name='req_rfi_from']").val();
    var to = $( "input[name='req_rfi_to']").val();
    var req_group  = $('#req-group').val();
    var user_group = $('#req-user-group').val();
    var sub_group = $('#req-sub-group').val();
    var title     = $('#req-title').val();
    var priority  = $('#req-priority').val();

    $('#rep-status').val(status);
    $('#rep-rfi-from').val(from);
    $('#rep-rfi-to').val(to);
    $('#rep-req-group').val(req_group);
    $('#rep-user-group').val(user_group);
    $('#rep-user-group-sub').val(sub_group);
    $('#rep-req-title').val(title);
    $('#rep-req-priority').val(priority);
    return true;
  }

  function req_status_edit(req_id) {
    $('#req-status-edit-'+req_id).hide();
    $('#req-status-text-'+req_id).hide();
    $('#req-status-remark-text-'+req_id).hide();
    $('#req-status-reason-'+req_id).removeClass('hide');
    $('#req-status-remark-'+req_id).removeClass('hide');
    $('#req-status-save-'+req_id).removeClass('hide');
  }

  function req_status_save(req_id) {
    var remark_text = $('#req-status-remark-'+req_id).val();
    var status_id   = $('#req-status-reason-'+req_id).val();
    var data = {'req_id':req_id, 'req_status_remark' : remark_text, 'req_status_id':status_id};
    $.ajax({
          url: "<?php echo $pei_config['urls']['baseUrl'];?>/request/request_status_update.php",
          type: "POST",
          data: data,
          success: function(data) {
            $('#req-status-save-'+req_id).addClass('hide');
            $('#req-status-remark-'+req_id).addClass('hide');
            $('#req-status-reason-'+req_id).addClass('hide');
            $('#req-status-text-'+req_id).html($('#req-status-reason-'+req_id+' option:selected').text());
            var text = '';
            //text += $('#req-status-reason-'+req_id+' option:selected').text()+'\n';
            text += $('#req-status-remark-'+req_id).val();
            text_short = text.substr(0, 40);
            $('#req-status-remark-text-'+req_id).find('.pei-content-less-text').html(text_short.replace(/\n/g,'<br/>'));
            $('#req-status-remark-text-'+req_id).find('.pei-content-more-text').html(text.replace(/\n/g,'<br/>'));
            var span_less_link = $('#req-status-remark-text-'+req_id).find('.pei-content-less-link');
            if(text.length > 40) {
              span_less_link.removeClass('hide');
            }
            else {
              if(span_less_link.hasClass('hide')){

              }
              else {
                span_less_link.addClass('hide');
              }
            }
            $('#req-status-edit-'+req_id).show();
            $('#req-status-text-'+req_id).show();
            $('#req-status-remark-text-'+req_id).show();
          }
        });

  }

  $(document).ready(function() {

    $(".pei-content-less-more-link").click(function(){
      var parent_span = $(this).parent();
      var grand_parent_span = parent_span.parent();
      var root_span = grand_parent_span.parent();
      if(parent_span.hasClass("pei-content-less-link")) {
        grand_parent_span.addClass('hide');
        root_span.find('.pei-content-more').removeClass('hide');
      }
      else {
        grand_parent_span.addClass('hide');
        root_span.find('.pei-content-less').removeClass('hide');
      }
      return false;
    });

    $('#req-rfi-from-datetimepicker').datetimepicker({
       format: 'DD-MMM-YYYY'
    });

    $('#req-rfi-to-datetimepicker').datetimepicker({
       format: 'DD-MMM-YYYY'
    });

    $('#req-priority').select2({
      placeholder: 'PRIORITY',
      allowClear: true
    });

    $('#req-status').select2({
      placeholder: 'All Projects',
      allowClear: true
    });

    $('#req-group').select2({
      placeholder: 'REQUESTOR SUB GROUP',
      allowClear: true
    });

    $('#req-user-group').select2({
      placeholder: 'REQUESTOR GROUP',
      allowClear: true
    });

    $('#req-sub-group')
      .select2({
        minimumInputLength: 0,
        placeholder: 'REQUESTOR SUB GROUP',
        allowClear: true,
        ajax: {
          url: "<?php echo $pei_config['urls']['baseUrl'];?>/request/fetch_sub_group.php",
          dataType: 'json',
          delay: 50,
          data: function (params) {
            return {
              group_id: $("#req-user-group").val(),
              group_name: params.term, // search term
              sub_group_id: $("#req-sub-group").val(),
              page: params.page
            };
          },
          processResults: function (data, page) {
            // parse the results into the format expected by Select2.
            // since we are using custom formatting functions we do not need to
            // alter the remote JSON data
            return {
              results: data
            };
          },
          cache: false
        }
      });


});
</script>
