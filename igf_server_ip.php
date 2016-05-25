<?php
  session_start();
  // load up config file

  $pei_current_module = 'REQUEST';

  require_once(__dir__.'/../header.php');
  // Include Pagination
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  //error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';

  // Fetch ALL Server Hall Detail
  if(!function_exists('get_all_ip_type')){
    function get_all_ip_type() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_ip_type
               ORDER BY ip_type_name
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  // Initialize variables
  $pei_msg        = '';
  $start_from     = 0;
  $record_per_page= $pei_config['pagination']['record_per_page'];
  $record_found   = 0;
  $sql_where      = ' 1 ';
  $show_page      = 1;
  $page           = 0;

  $req_id         = '';
  $req_group      = '';
  $user_group_sub = '';
  $req_name       = '';
  $req_igf_server_serial_no = '';
  $req_igf_server_data_ip   = '';
  $server_ip_type      = '';
  $req_igf_server_ip   = '';
  $req_sh         = '';

  // Get list of all requests
  $data_request         = get_all_request();
  // Get list of all requestor groups
  $data_req_user_group  = get_requestor_user_group();
  // Get All Server Hall
  $data_sh              = get_all_server_hall();
  // Get All IP type
  $data_ip_type         = get_all_ip_type();

  $data_igf_server = array(
                      array('key' => 'req_id', 'name' => 'REQUEST ID', 'width' => '120' , 'db_col_name' => 'req_id'),
                      array('key' => 'igf_server_app_name', 'name' => 'APPLICATION NAME', 'width' => '150' , 'db_col_name' => 'igf_server_app'),
                      //array('key' => 'igf_server_user_group', 'name' => 'REQUESTOR GROUP', 'width' => '150', 'db_col_name' => 'igf_server_req_group_name' ),
                      //array('key' => 'igf_server_user_sub_group', 'name' => 'REQUESTOR SUB-GROUP', 'width' => '150', 'db_col_name' => 'igf_server_req_sub_group_name' ),
                      //array('key' => 'igf_server_env', 'name' => 'ENVIRONMENT', 'db_col_name' => 'igf_server_env'),
                      array('key' => 'igf_server_loc', 'name' => 'LOCATION', 'width' => '100', 'db_col_name' => 'igf_server_loc'),
                      array('key' => 'igf_server_sh', 'name' => 'SERVER HALL', 'width' => '100', 'db_col_name' => 'igf_server_server_hall'),
                      array('key' => 'igf_server_serial', 'name' => 'SERVER SERIAL NUMBER', 'width' => '130', 'db_col_name' => 'igf_server_serial_number'),
                      array('key' => 'igf_server_make', 'name' => 'SERVER MAKE','db_col_name' => 'igf_server_make'),
                      array('key' => 'igf_server_model', 'name' => 'SERVER MODEL', 'width' => '125', 'db_col_name' => 'igf_server_model'),
                      array('key' => 'ip_type_name', 'name' => 'IP TYPE', 'width' => '125', 'db_col_name' => 'ip_type_name'),
                      array('key' => 'igf_server_ip_address', 'name' => 'IP', 'width' => '125', 'db_col_name' => 'igf_server_ip_address'),
                      array('key' => 'igf_server_ip_sm', 'name' => 'SUBNET MASK', 'width' => '125', 'db_col_name' => 'igf_server_ip_sm'),
                      array('key' => 'igf_server_ip_gw', 'name' => 'GATEWAY', 'width' => '125', 'db_col_name' => 'igf_server_ip_gw'),
                      array('key' => 'igf_server_ip_vlan', 'name' => 'VLAN', 'width' => '125', 'db_col_name' => 'igf_server_ip_vlan'),
                      array('key' => 'action', 'name' => 'ACTION',),
                    );

  // Search Query
  if(isset($_GET['search'])) {

    if(isset($_GET['req_id']) && $_GET['req_id'] != '') {
      $req_id = $_GET['req_id'];
      $sql_where .= " AND r.req_id = '".mysql_real_escape_string($req_id)."' ";
    }

    if(isset($_GET['user_group']) && $_GET['user_group'] != '') {
      $req_group = $_GET['user_group'];
      $sql_where .= " AND r.req_group_id = '".mysql_real_escape_string($req_group)."' ";
    }

    if (isset($_GET['user_group_sub']) && $_GET['user_group_sub'] != '') {
      $user_group_sub = $_GET['user_group_sub'];
      $sql_where .= " AND r.req_group_sub_id = '".mysql_real_escape_string($user_group_sub)."' ";
    }

    if (isset($_GET['req_name']) && $_GET['req_name'] != '') {
      $req_name   = $_GET['req_name'];
      $sql_where .= " AND r.req_title LIKE '%".mysql_real_escape_string($req_name)."%' ";
    }

    if (isset($_GET['req_igf_server_serial_no']) && $_GET['req_igf_server_serial_no'] != '') {
      $req_igf_server_serial_no = $_GET['req_igf_server_serial_no'];;
      $sql_where .= " AND iser.igf_server_serial_number LIKE '%".mysql_real_escape_string($req_igf_server_serial_no)."%' ";
    }

    if(isset($_GET['server_ip_type']) && $_GET['server_ip_type'] != '') {
      $server_ip_type = $_GET['server_ip_type'];
      $sql_where .= " AND isip.igf_server_ip_type = '".mysql_real_escape_string($server_ip_type)."' ";
    }

    if (isset($_GET['req_igf_server_data_ip']) && $_GET['req_igf_server_data_ip'] != '') {
      $req_igf_server_data_ip = $_GET['req_igf_server_data_ip'];
      $sql_where .= " AND isip.igf_server_ip_address LIKE '%".mysql_real_escape_string($req_igf_server_data_ip)."%' ";
    }

    if(isset($_GET['req_sh']) && $_GET['req_sh'] != '') {
      $req_sh = $_GET['req_sh'];
      $sql_where .= " AND isip.igf_server_server_hall_id = '".mysql_real_escape_string($req_sh)."' ";
    }
  }

  $sql_where .= " AND i.igf_deleted=0 ";

  // Find total no. of records found for search query required for pagination
  $sql_total = "SELECT isip.igf_server_ip_id
                FROM
                  idc_igf_server_ip AS isip
                  LEFT JOIN
                  idc_ip_type AS ipt ON isip.igf_server_ip_type = ipt.ip_type_id
                  LEFT JOIN
                  idc_igf_server AS iser ON isip.igf_server_id = iser.igf_server_id
                  LEFT JOIN
                  idc_igf AS i ON iser.igf_id = i.igf_id
                  LEFT JOIN
                  idc_request AS r ON i.req_id = r.req_id
                WHERE ".$sql_where." ;";
  //echo $sql_total.'<br />';
  $res_total      = mysql_query($sql_total);
  $total_records  = mysql_num_rows($res_total);  //count number of records
  //echo '$total_records :'.$total_records.'<br />';
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
  $sql_order_by = ' ORDER BY isip.igf_server_ip_id DESC';
  $sql_search = "SELECT
                    isip.*, ipt.ip_type_name, iser.*, r.req_id
                 FROM
                  idc_igf_server_ip AS isip
                  LEFT JOIN
                  idc_ip_type AS ipt ON isip.igf_server_ip_type = ipt.ip_type_id
                  LEFT JOIN
                  idc_igf_server AS iser ON isip.igf_server_id = iser.igf_server_id
                  LEFT JOIN
                  idc_igf AS i ON iser.igf_id = i.igf_id
                  LEFT JOIN
                  idc_request AS r ON i.req_id = r.req_id
                 WHERE ".$sql_where."
                ".$sql_order_by.'  '.$sql_limit.';';
  //echo $sql_search.'<br />';
  $res_search = mysql_query($sql_search);

  if(!$res_search){
    $pei_msg .= 'Something went wrong.';
  }
  else {

  }

  // display pagination
  if(isset($_GET['page'])) {
    $page = intval($_GET['page']);
  }
  $tpages=$total_pages;
  if ($page <= 0) {
    $page = 1;
  }

  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/index.php" >User Requests</a></li>
        <li class="active">IP Search</li>
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
          <label class="sr-only" for="pr_po_req_id">Server Hall</label>
            <select class="placeholder form-control dropdown-select2" name="req_sh" id="req-sh">
              <option value="">-- Select Server Hall --</option>
            <?php
            foreach ($data_sh as $key => $sh) {
            ?>
              <option value="<?php echo $sh['sh_id'];?>" <?php if($req_sh ==  $sh['sh_id']) { ?> selected="selected" <?php } ?>><?php echo $sh['sh_name'];?></option>
            <?php
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_igf_server_serial_no">Serial Number</label>
          <input type="text" class="form-control input-sm" name="req_igf_server_serial_no" id="req-igf-server-serial-no" placeholder="Server Serial Number" size="40" value="<?php echo $req_igf_server_serial_no;?>">
        </div>
        <div class="form-group">
          <label class="sr-only" for="server_ip_type">IP Type</label>
            <select class="placeholder form-control dropdown-select2" name="server_ip_type" id="server-ip-type">
              <option value="">-- Select IP Type --</option>
            <?php
            foreach ($data_ip_type as $key => $ip) {
            ?>
              <option value="<?php echo $ip['ip_type_id'];?>" <?php if($server_ip_type ==  $ip['ip_type_id']) { ?> selected="selected" <?php } ?>><?php echo $ip['ip_type_name'];?></option>
            <?php
            }
            ?>
          </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_igf_server_data_ip">IP</label>
          <input type="text" class="form-control input-sm" name="req_igf_server_data_ip" id="req-igf-server-data-ip" placeholder="IP" size="40" value="<?php echo $req_igf_server_data_ip;?>">
        </div>
        <button type="submit" value="Search" name="search" class="btn btn-primary">Search</button>
      </form>

      <div class="clearfix"></div>
        <table class="table table-bordered table-hover table-request-list">
          <tr>
          <?php
          if($data_igf_server) {
            foreach ($data_igf_server as $key => $igf_server) {
            ?>
            <th <?php if(isset($igf_server['width'])) { ?>width="<?php echo $igf_server['width'];?>px" <?php }?> ><?php echo $igf_server['name'];?></th>
            <?php
            }
          }
          ?>
          </tr>
<?php
  if($res_search) {
    if(mysql_num_rows($res_search)) {
      while($row = mysql_fetch_array($res_search)){
?>
          <tr>
          <?php
          foreach ($data_igf_server as $key => $igf_server) {
            switch ($igf_server['key']) {
              case 'action':
                ?>
                <td>
                  <a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_server.php?id=<?php echo $row['igf_server_id'];?>">View</a>
                </td>
                <?php
                # code...
                break;
              default:
              ?>
              <td><?php if(isset($igf_server['db_col_name'])) { echo $row[$igf_server['db_col_name']]; }?></td>
              <?php
                # code...
                break;
            }
          }
          ?>
          </tr>
<?php
      }// END while
    }
    else {
?>
        <tr class="info">
          <td colspan="12" style="text-align:center;">No Record Found!
          </td>
        </tr>
<?php
    }
  }
?>
      </table>


<?php
$reload = $_SERVER['PHP_SELF'] . "?";
if(isset($_GET['search'])) {
  $reload .= '&search=';
}
if(isset($_GET['req_id'])) {
  $reload .= '&req_id='.$_GET['req_id'];
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
if(isset($_GET['req_igf_server_serial_no'])) {
  $reload .= '&req_igf_server_serial_no='.$_GET['req_igf_server_serial_no'];
}

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

  $('.dropdown-select2, #req-user-group').select2({allowClear: true});

  $('#req-sub-group')
  .select2({
    minimumInputLength: 0,
    //placeholder: 'Requestor Sub Group',
    allowClear: true,
    ajax: {
      url: "<?php echo $pei_config['urls']['baseUrl'];?>/request/fetch_sub_group.php",
      dataType: 'json',
      delay: 50,
      data: function (params) {
        console.log('TEST...');
        return {
          group_id: $("#req-user-group").val(),
          group_name: params.term, // search term
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

});
</script>
