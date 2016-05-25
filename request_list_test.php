<?php
  session_start();
  $pei_current_module = 'REQUEST';

  require_once(__dir__.'/../header.php');
  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');

  // Include Pagination
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  echo error_reporting(E_ALL); ini_set('display_errors', 1);
  echo '<pre><br /><br /><br /><br />';
  //var_dump($pei_user_access_permission);
  // Initialize variables
  $req_msg        = '';
  $pei_msg        = '';
  $req_group      = '';
  $req_sub_group  = '';
  $req_title      = '';
  $req_name       = '';
  $uname          = strtolower($_SESSION['pei_user']);
  $start_from     = 0;
  $record_per_page= $pei_config['pagination']['record_per_page'];
  $record_found   = 0;
  $sql_where      = ' 1 ';
  $show_page      = 1;
  $page           = 0;

  $data                 = '';
  $data_req_user_group  = get_requestor_user_group();
  $data_req_sub_group   = '';
  $data_req_status      = status_request_status();

  // CHECK access permission for
  $perm_edit_request  = FALSE;
  $perm_send_rfi      = FALSE;
  $perm_sync_igf      = FALSE;
  $perm_imp_activity  = FALSE;

  // edit_any_request
  if(in_array('edit_any_request', $pei_user_access_permission)) {
    $perm_edit_request = TRUE;
  }
  // send_any_rfi
  if(in_array('send_any_rfi', $pei_user_access_permission)) {
    $perm_send_rfi = TRUE;
  }
  // sync_any_igf
  if(in_array('sync_any_igf', $pei_user_access_permission)) {
    $perm_sync_igf = TRUE;
  }
  //
  if(in_array('view_implementation_dashboard', $pei_user_access_permission)) {
    $perm_imp_activity = TRUE;
  }


  // Search Query
  if(isset($_GET['search'])) {
    if(isset($_GET['user_group']) && $_GET['user_group'] != '') {
      $req_group = $_GET['user_group'];
      $sql_where .= " AND r.req_group_id = '".mysql_real_escape_string($req_group)."'";
      $data_req_sub_group = get_user_group_sub_group($req_group);
    }

    if (isset($_GET['user_group_sub']) && $_GET['user_group_sub'] != '') {
      $req_sub_group  = $_GET['user_group_sub'];
      $sql_where .= " AND r.req_group_sub_id = '".mysql_real_escape_string($req_sub_group)."'";
    }

    if (isset($_GET['req_name']) && $_GET['req_name'] != '') {
      $req_name   = $_GET['req_name'];
      $sql_where .= " AND r.req_id LIKE '%".mysql_real_escape_string($req_name)."%'";
    }

    if (isset($_GET['req_title']) && $_GET['req_title'] != '') {
      $req_title  = $_GET['req_title'];
      $sql_where .= " AND r.req_title LIKE '%".mysql_real_escape_string($req_title)."%'";
    }
  }

  if(isset($_GET['req_status']) && $_GET['req_status'] != '') {
    $req_status = $_GET['req_status'];
    switch ($req_status) {
      case 'ALL':
        break;
      case 'WIP/PARTIAL/ON-HOLD':
        $sql_where .= " AND ( r.req_status_id NOT IN (3) OR  r.req_status_id IS NULL ) ";
        break;
      default:
         $sql_where .= " AND r.req_status_id = '".mysql_real_escape_string($req_status)."'";
        break;
    }
  }
  else {
    $req_status = 'WIP/PARTIAL/ON-HOLD';
    $sql_where .= " AND ( r.req_status_id NOT IN (3) OR  r.req_status_id IS NULL ) ";
  }

  if(!$perm_send_rfi) {
    $sql_where .= " AND r.req_status_id IS NOT NULL ";
  }

  // Find total no. of records found for search query required for pagination
  $sql_total = "SELECT r.*,
                  r.updated_at AS req_updated_at,
                  ug.user_group_name AS req_group_name,
                  ugs.user_group_name AS req_group_name_sub,
                  u_c.user_name AS req_created_by,
                  u_u.user_name AS req_updated_by
                FROM
                  idc_request AS r
                  LEFT JOIN
                  idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                  LEFT JOIN
                  idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
                  LEFT JOIN
                  idc_user AS u_c ON r.created_by = u_c.user_login
                  LEFT JOIN
                  idc_user AS u_u ON r.updated_by = u_u.user_login
                WHERE ".$sql_where." ;";
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
  $sql_order_by = ' ORDER BY r.req_id DESC ';
  $sql_search = "SELECT r.*,
                  r.updated_at AS req_updated_at,
                  ug.user_group_name AS req_group_name,
                  ugs.user_group_name AS req_group_name_sub,
                  u_c.user_name AS req_created_by,
                  u_u.user_name AS req_updated_by
                FROM
                  idc_request AS r
                  LEFT JOIN
                  idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                  LEFT JOIN
                  idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
                  LEFT JOIN
                  idc_user AS u_c ON r.created_by = u_c.user_login
                  LEFT JOIN
                  idc_user AS u_u ON r.updated_by = u_u.user_login
                WHERE ".$sql_where."
                ".$sql_order_by.'  '.$sql_limit.';';
  //echo $sql_search.'<br />';
  $res_search = mysql_query($sql_search);
  $record_found = NULL;
  if(!$res_search){
    $pei_msg .= 'Something went wrong.';
  }
  else {
    $record_found = mysql_num_rows($res_search);
  }

  // display pagination
  if(isset($_GET['page'])) {
    $page = intval($_GET['page']);
  }
  $tpages=$total_pages;
  if ($page <= 0) {
    $page = 1;
  }

  $reload = $_SERVER['PHP_SELF'] . "?";
  if(isset($_GET['search'])) {
    $reload .= '&search=';
  }
  if(isset($_GET['user_group'])) {
    $reload .= '&user_group='.$_GET['user_group'];
  }
  if(isset($_GET['user_group_sub'])) {
    $reload .= '&user_group_sub='.$_GET['user_group_sub'];
  }

  if(isset($_GET['req_name'])) {
    $reload .= '&req_name='.$_GET['req_name'];
  }

  if(isset($_GET['req_title'])) {
    $reload .= '&req_title='.$_GET['req_title'];
  }

  if(isset($_GET['req_status'])) {
    $reload .= '&req_status='.$_GET['req_status'];
  }

  echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/index.php">User Requests</a></li>
        <li class="active">View/Track Request Detail</li>
        <div class="pei-breadcrumb-right">
          Total : <?php echo $total_records;?>
        </div>
      </ol>
      <!-- /breadcrumb  -->

      <div class="box-content">

<?php
  if($req_msg) {
?>
      <div class="alert alert-danger" role="alert">
      <?php echo preg_replace("/\\\\n/", "<br />", $req_msg);?>
      </div>
<?php
  }
?>
      <form class="form-inline" method="GET">
        <div class="form-group">
          <label class="sr-only" for="user_group">Requestor Group</label>
          <select class="placeholder form-control dropdown-select2" name="user_group" id="req-user-group">
          <option value="">-- Select a user group --</option>
        <?php
        foreach ($data_req_user_group as $key => $user_group) {
        ?>
          <option value="<?php echo $user_group['user_group_id'];?>" <?php if($req_group ==  $user_group['user_group_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($user_group['user_group_name']);?></option>
        <?php
        }
        ?>
        </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="user_group_sub">Requestor Sub Group</label>
          <select class="placeholder form-control" name="user_group_sub" id="req-sub-group">
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
        <div class="form-group">
          <label class="sr-only" for="req_name">Request Id</label>
          <input type="text" class="form-control input-sm" name="req_name" id="req-name" placeholder="REQUEST ID" size="12" value="<?php echo $req_name;?>">
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_title">Request Title</label>
          <input type="text" class="form-control input-sm" name="req_title" id="req-title" placeholder="REQUEST TITLE" size="13" value="<?php echo $req_title;?>">
        </div>

        <div class="form-group">
          <label class="sr-only" for="req_status">Request Status</label>
          <select class="placeholder form-control dropdown-select2" name="req_status" id="req-status" >
            <option value="ALL">ALL REQUESTS</option>
            <option value="WIP/PARTIAL/ON-HOLD" <?php if($req_status ==  'WIP/PARTIAL/ON-HOLD') { ?> selected="selected" <?php } ?>>WIP / PARTIAL / ON-HOLD</option>
          <?php
          foreach ($data_req_status as $key => $status) {
            if($status['status_name'] != 'RFI'){
              if($status['status_name'] != 'ON-HOLD'){
          ?>

            <option value="<?php echo $status['status_id'];?>" <?php if($req_status ==  $status['status_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($status['status_name']);?></option>
          <?php
              }
            }
          }
          ?>
          </select>
        </div>


        <button type="submit" value="Search" name="search" class="btn btn-primary">Search</button>
<?php
if ($total_pages > 1) {
?>
        <div class="form-group pei-form-group-right">
          <nav>
            <ul class="pagination pei-pagination-top">
          <?php echo pei_paginate($reload, $show_page, $total_pages);?>
            </ul>
          </nav>
        </div>
<?php
}
?>
      </form>

      <div class="clearfix"></div>
        <table class="table table-bordered table-hover table-request-list">
          <tr class="active">
            <th width="90px">Request Id</th>
            <th width="90px">Request Date</th>
            <th width="100px">Created By</th>
            <th width="90px">Requestor Group</th>
            <th width="90px">Requestor <br />Sub-Group</th>
            <th>Request Title</th>
            <th width="60px">Environment</th>
            <th width="70px">Location</th>
            <th width="40px">Server Hall</th>
            <th width="100px">Modified By</th>
            <th width="70px">Action</th>
            <th width="50px">IGF(count)</th>
            <th width="50px">SAN (Node Count)</th>
            <th width="50px">CLUSTER (Node Count)</th>
            <?php
            if($perm_send_rfi) {
            ?>
            <th width="50px">RFI Email</th>
            <?php
            }
            ?>
            <?php
            if($perm_sync_igf) {
            ?>
            <th width="50px">Release Action</th>
            <?php
            }
            ?>
            <th width="80px" class="text-center">Status</th>
          </tr>
    <?php
      if($record_found) {
        while($row=mysql_fetch_array($res_search)) {
          //var_dump($row);
          $server_phy           = 0;
          $server_vir           = 0;
          $server_total         = 0;
          $server_rel           = 0;
          $server_rel_pending   = 0;
          $server_released      = 0;
          $server_released_phy  = 0;
          $server_released_vir  = 0;
          $server_pending       = 0;
          $server_pending_phy   = 0;
          $server_pending_vir   = 0;
          $status_text          = '';
          $released_date        = '';

          $row['storage_node_count']    = NULL;
          $row['ha_cluster_node_count'] = NULL;

          $row['igf_count'] = get_req_igf_count($row['req_id']);
          $req_updated_at = $row['req_updated_at'];
          $row['igf_last_modified_by'] = '';
          if($req_updated_at) {
            $row['igf_last_modified_by'] = $row['req_updated_by'];
          }
          // Get IGF Server Last Modifier
          $sql_last =  "  SELECT u.user_name AS igf_last_modified_by, i_ser.updated_at
                          FROM
                            idc_igf_server AS i_ser
                            LEFT JOIN
                            idc_igf AS i ON i_ser.igf_id = i.igf_id
                            LEFT JOIN
                            idc_user AS u ON i_ser.updated_by = u.user_login
                          WHERE
                            1
                            AND
                            i.req_id='".mysql_real_escape_string($row['req_id'])."'
                          ORDER BY i_ser.updated_at DESC
                          LIMIT 0,1";
          //echo $sql_last.'<br />';
          $res_last = mysql_query($sql_last);
          if($res_last) {
            if(mysql_num_rows($res_last)){
              $row_last = mysql_fetch_array($res_last);
              if($row_last['updated_at']) {
                if(strtotime($row_last['updated_at']) > strtotime($req_updated_at)) {
                  $row['igf_last_modified_by'] = $row_last['igf_last_modified_by'];
                }
              }
            }
          }

          if($row['igf_count'] > 0) {
            // Node count
            $server_phy   = igf_request_epqt_count($row['req_id'], 'PHYSICAL');
            $server_vir   = igf_request_epqt_count($row['req_id'], 'VIRTUAL');
            $server_total = $server_phy + $server_vir;
            // Node Released
            $server_released_phy  = count_igf_server_released_by_req_id($row['req_id'], 3);
            $server_released_vir  = count_igf_server_released_by_req_id($row['req_id'], 4);
            $server_released      = $server_released_phy + $server_released_vir;
            // Node Pending
            $server_pending       = $server_total - $server_released;
            $server_pending_phy   = $server_phy - $server_released_phy;
            $server_pending_vir   = $server_vir - $server_released_vir;

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
          }


          $igf_count  = '0';
          $igf_count  = $row['igf_count'];
          var_dump($igf_count);
          $req_status_history = get_request_status_history($row['req_id']);

          // Calculate IGF Server Count
          switch ($row['req_status']) {
            case 'RELEASED':
              $status_text = 'RELEASED';
              break;
            case 'RFI':
              $status_text = 'WIP';
              break;
            case 'WIP':
              // Then request can be in WIP or PARTIAL WIP stage
              $status_text  = 'WIP';
              if($server_pending > 0 &&  $server_released > 0) {
                $status_text = 'PARTIAL';
                $status_text  = '<a href="#" data-id="'.$row["req_id"].'" class="show-igf-released">PARTIAL</a>';
                if($req_status_history){
                  foreach ($req_status_history as $key => $value) {
                    if($value['status_name'] == 'PARTIAL RELEASED'){
                      $released_date = $value['created_at'];
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
                    $released_date = $value['created_at'];
                    break;
                  }
                }
              }
              $status_text  = '<a href="#" data-id="'.$row["req_id"].'" class="show-igf-released">PARTIAL</a>';
              break;
            case 'ON-HOLD':
              // Then request can be in ON-HOLD or PARTIAL ON-HOLD stage
              $status_text = 'ON-HOLD';
              if($server_pending > 0 &&  $server_released > 0) {

                $status_text  = '<a href="#" data-id="'.$row["req_id"].'" class="show-igf-released">PARTIAL</a>'.' ON-HOLD';
                if($req_status_history){
                  foreach ($req_status_history as $key => $value) {
                    if($value['status_name'] == 'PARTIAL RELEASED'){
                      $released_date = $value['created_at'];
                      break;
                    }
                  }
                }
              }
              break;
            default:
              //$status_text = 'WIP';
          }// END switch $row['req_status']

          //echo $row["req_id"].' '.$status_text.' FINAL  <br />';
    ?>
          <tr>
            <td><?php
            if($perm_imp_activity) {
            ?>
              <a href="<?php echo $pei_config['urls']['baseUrl'];?>/implementation/index.php?req_id=<?php echo $row['req_id'];?>"><?php echo strtoupper($row['req_id']);?></a>
            <?php
            }
            else {
              echo strtoupper($row['req_id']);
            }
            ?></td>
            <td><?php echo pei_date_format($row['req_date']);?></td>
            <td><?php echo strtoupper($row['req_created_by']);?></td>
            <td><?php echo strtoupper($row['req_group_name']);?></td>
            <td><?php echo strtoupper($row['req_group_name_sub']);?></td>
            <td><?php echo strtoupper($row['req_title']);?></td>
            <td><?php $loc_env = ''; $loc_env = get_req_env_string($row['req_id']); if($loc_env) { $loc_env = str_replace(',', ', ', $loc_env);} echo strtoupper($loc_env);?></td>
            <td><?php $loc_str = ''; $loc_str = get_req_loc_string($row['req_id']); if($loc_str) { $loc_str = str_replace(',', ', ', $loc_str);} echo strtoupper($loc_str);?></td>
            <td><?php $sh_str = ''; $sh_str = get_req_sh_string($row['req_id']); if($sh_str) { $sh_str = str_replace(',', ', ', $sh_str);} echo strtoupper($sh_str);?></td>

            <td><?php echo strtoupper($row['igf_last_modified_by']);?></td>
            <td><?php if($perm_edit_request && !in_array($row['req_status'], array('RELEASED' , 'PARTIAL RELEASED')) ) { ?>
            <a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request.php?action=Edit&req_id=<?php echo $row['req_id'];?>">Edit</a> |
            <?php } ?>
            <a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request.php?action=View&req_id=<?php echo $row['req_id'];?>">View</a></td>
            <td>
            <?php
            if($server_total> 0) {
            ?>
            <a href="#" data-id="<?php echo $row['req_id'];?>" class="showme"><?php echo $igf_count;?></a>
            <br />T:<?php echo sprintf("%02d", $server_total);?>
            <br />P:<?php echo sprintf("%02d", $server_phy);?>
            <br />V:<?php echo sprintf("%02d", $server_vir);?>
            <?php
            }
            ?>
            </td>
            <td>
            <?php
            var_dump($perm_send_rfi);
            if($row['storage_node_count'] && ($row['storage_node_count']['SAN']['PHYSICAL'] + $row['storage_node_count']['SAN']['VIRTUAL']) != 0){
            ?>
              <br />T:<?php echo sprintf("%02d", ($row['storage_node_count']['SAN']['PHYSICAL'] + $row['storage_node_count']['SAN']['VIRTUAL']) );?>
              <br />P:<?php echo sprintf("%02d", $row['storage_node_count']['SAN']['PHYSICAL']);?>
              <br />V:<?php echo sprintf("%02d", $row['storage_node_count']['SAN']['VIRTUAL']);?>
            <?php
            }
            ?>
            </td>
            <td>
            <?php
            if($row['ha_cluster_node_count'] &&  ($row['ha_cluster_node_count']['PHYSICAL'] + $row['ha_cluster_node_count']['VIRTUAL']) != 0){
            ?>
              <br />T:<?php echo sprintf("%02d", ($row['ha_cluster_node_count']['PHYSICAL'] + $row['ha_cluster_node_count']['VIRTUAL']) );?>
              <br />P:<?php echo sprintf("%02d", $row['ha_cluster_node_count']['PHYSICAL']);?>
              <br />V:<?php echo sprintf("%02d", $row['ha_cluster_node_count']['VIRTUAL']);?>
            <?php
            }

            ?>
            </td>
            <?php
            if($perm_send_rfi) {
            ?>
            <td id="email-<?php echo $row['req_id'];?>">
            <?php
            if($igf_count> 0) {
              if($row['req_status'] == 'RFI' || $row['req_status'] == 'WIP' ) {
                echo 'Sent';
              }
              else if($row['req_status'] == '') {
              ?>
                <a href="#;" data-id="<?php echo $row['req_id'];?>" class="pei-send-rfi-email">Send</a>
              <?php
              }
              else {
                echo 'Sent';
              }
            }
            else  {
              echo 'NA';
            }
            ?>
            </td>
            <?php
            }
            ?>

            <?php
            if($perm_sync_igf) {
            ?>
            <td>
              <?php
              if($igf_count){
                $req_igfs = fetch_req_igf($row['req_id']);
                if($req_igfs[0]) {
                ?>
                <a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_release.php?id=<?php echo $req_igfs[0]['igf_id'];?>">Sync IGF</a>
                <?php
                }
              }
            ?>
            </td>
            <?php
            }
            ?>
            <td style="text-align:center;">
            <?php
            echo $status_text.'<br />';
            if($server_pending > 0 &&  $server_released > 0 && $status_text != 'RELEASED') {
            ?>
              <table class="table table-bordered table-condensed" style="margin-bottom:2px;">
                <tr>
                  <th>PARTIAL</th>
                  <th>PENDING</th>
                </tr>
                <tr>
                  <td>T:<?php echo sprintf("%02d", ($server_released_phy + $server_released_vir) );?></td>
                  <td>T:<?php echo sprintf("%02d", ($server_total - ($server_released_phy + $server_released_vir)) );?></td>
                </tr>
                <tr>
                  <td>P:<?php echo sprintf("%02d", $server_released_phy);?></td>
                  <td>P:<?php echo sprintf("%02d", ($server_phy - $server_released_phy) );?></td>
                </tr>
                <tr>
                  <td>V:<?php echo sprintf("%02d", $server_released_vir);?></td>
                  <td>V:<?php echo sprintf("%02d", ($server_vir - $server_released_vir));?></td>
                </tr>
              </table>
            <?php
            }
            echo ($released_date) ? pei_date_format($released_date) : '';
            ?>
            </td>
          </tr>
    <?php
      }
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
        <?php echo pei_paginate($reload, $show_page, $total_pages);?>
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
$(document).ready(function() {

  $('[data-toggle="tooltip"]').tooltip()

  $('#req-user-group').select2({
    placeholder: 'REQUESTOR GROUP',
    allowClear: true
  });

  $('#req-status').select2({
    placeholder: 'STATUS',
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
            console.log('data');
            console.log('group_id: ',$("#req-user-group").val());
            console.log('sub_group_id: ',$("#req-sub-group").val());
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
          cache: true
        }
      });

    $('a.showme').click(function(ev){
       ev.preventDefault();
       var uid = $(this).data('id');
       $.get('request_igf.php?id=' + uid, function(html){
          $('#modal-7 .modal-body').html('');
          $('#modal-7 .modal-body').html(html);
          $('#modal-7').modal('show', {backdrop: 'static'});
       });
     });

    $('a.show-igf-released').click(function(ev){
       ev.preventDefault();
       var uid = $(this).data('id');
       $.get('request_igf_released.php?id=' + uid, function(html){
          $('#modal-igf-released .modal-body').html('');
          $('#modal-igf-released .modal-body').html(html);
          $('#modal-igf-released').modal('show', {backdrop: 'static'});
       });
     });

    $('a.pei-send-rfi-email').click(function(ev){
      ev.preventDefault();
      var uid = $(this).data('id');

      if(confirm("Are you sure ?This action will initiate RFI email.")) {
        var url   = 'request_rfi_mail.php';
        var data  = {'req_id':uid};
        $('#email-'+uid).html('Sending..');
        $.ajax({
          url: url,
          data: data,
          success: function(data) {
            $('#email-'+uid).html('');
            $('#email-'+uid).html(data);
          }
        });
      }
    });

  function showUrlInDialog(url){
  var tag = $("<div></div>");
  $.ajax({
    url: url,
    success: function(data) {
      tag.html(data).dialog({modal: true,width:600}).dialog('open');
    }
  });
}
});
</script>
<div class="modal fade" id="modal-7">
    <div class="modal-dialog">
        <div class="modal-content">

            <div class="modal-header">

                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Request IGF List</h4>
            </div>

            <div class="modal-body">

                Content is loading...

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
              <!--
                <button type="button" class="btn btn-info">Save changes</button>
              -->
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-igf-released">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                <h4 class="modal-title">Request Release History</h4>
            </div>
            <div class="modal-body">
                Content is loading...
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-white" data-dismiss="modal">Close</button>
              <!--
                <button type="button" class="btn btn-info">Save changes</button>
              -->
            </div>
        </div>
    </div>
</div>


<div id="req-rfi-confirm" class="modal hide fade">
  <div class="modal-body">
    Are you sure?
  </div>
  <div class="modal-footer">
    <button type="button" data-dismiss="modal" class="btn btn-primary" id="delete">Send</button>
    <button type="button" data-dismiss="modal" class="btn">Cancel</button>
  </div>
</div>
