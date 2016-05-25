<?php
  session_start();
  // load up config file

  $pei_current_module = 'REQUEST';

  require_once(__dir__.'/../header.php');
  // Include Pagination
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  //echo error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';

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

  // Get list of all requests
  $data_request         = get_all_request();
  // Get list of all requestor groups
  $data_req_user_group  = get_requestor_user_group();

  // Search Query
  if(isset($_GET['search'])) {

    if(isset($_GET['req_id']) && $_GET['req_id'] != '') {
      $req_id = $_GET['req_id'];
      $sql_where .= " AND r.req_id LIKE '%".mysql_real_escape_string($req_id)."%' ";
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
      $igf_ids = '0';
      // Get IGF ID in which this server serial no is present
      $sql_server = " SELECT DISTINCT iser.igf_id
                      FROM
                        idc_igf AS i
                        LEFT JOIN
                        idc_igf_server AS iser ON i.igf_id = iser.igf_id
                      WHERE
                        i.igf_deleted=0
                        AND
                        iser.igf_server_serial_number LIKE '%".mysql_real_escape_string($req_igf_server_serial_no)."%'
                    ";
      //echo $sql_server.'<br />';
      $res_server = mysql_query($sql_server);
      if(!$res_server) {
        $pei_msg .= 'Something went wrong.';
      }
      else {
        while ($row = mysql_fetch_array($res_server)) {
          $igf_ids .= $row['igf_id'].',';
        }
        $igf_ids = rtrim($igf_ids, ',');
        $sql_where .= " AND i.igf_id IN (".mysql_real_escape_string($igf_ids).")";
      }
    }
  }

  $sql_where .= " AND i.igf_deleted=0 ";

  // Find total no. of records found for search query required for pagination
  $sql_total = "SELECT i.igf_id
                FROM
                  idc_igf AS i
                  LEFT JOIN
                  idc_doc AS d ON i.igf_doc_id = d.doc_id
                  LEFT JOIN
                  idc_request AS r ON i.req_id = r.req_id
                  LEFT JOIN
                  idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                  LEFT JOIN
                  idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
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
  $sql_order_by = ' ORDER BY r.req_id DESC, i.igf_id DESC ';
  $sql_search = "SELECT i.*, r.*,
                  d.doc_file_name AS igf_file_name,
                  ug.user_group_name AS req_group_name,
                  ugs.user_group_name AS req_group_name_sub
                FROM
                  idc_igf AS i
                  LEFT JOIN
                  idc_doc AS d ON i.igf_doc_id = d.doc_id
                  LEFT JOIN
                  idc_request AS r ON i.req_id = r.req_id
                  LEFT JOIN
                  idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                  LEFT JOIN
                  idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
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
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/index.php" >User Requests</a></li>
        <li class="active">IGF Consolidated</li>
      </ol>
      <!-- /breadcrumb  -->

      <div class="box-content">

<?php
  if($req_msg) {
?>
      <div class="alert alert-danger" role="alert">
      <?php echo preg_replace("/\\\\n/", "<br />", $pei_msg);?>
      </div>
<?php
  }
?>
      <form class="form-inline" method="GET">
        <!--<div class="form-group">
          <label class="sr-only" for="pr_po_req_id">Request ID</label>
            <select class="placeholder form-control dropdown-select2" name="req_id" id="req-id">
              <option value="">-- Select Request --</option>
            <?php
           // foreach ($data_request as $key => $request) {
            ?>
              <option value="<?php echo $request['req_id'];?>" <?php if($req_id ==  $request['req_id']) { ?> selected="selected" <?php } ?>><?php echo $request['req_id'];?></option>
            <?php
          //  }
            ?>
          </select>
        </div>-->
		<div class="form-group">
          <label class="sr-only" for="req_id">Request ID</label>
          <input type="text" class="form-control input-sm" name="req_id" id="req_id" placeholder="Request ID" size="15" value="<?php echo $req_id;?>">
        </div>
        <div class="form-group">
          <label class="sr-only" for="user_group">Requestor Group</label>
          <select class="placeholder form-control" name="user_group" id="req-user-group">
            <option value="">REQUESTOR GROUP</option>
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
            <option value="">REQUESTOR SUB GROUP</option>
          </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_name">Request Title</label>
          <input type="text" class="form-control input-sm" name="req_name" id="req_name" placeholder="Request Title" size="40" value="<?php echo $req_name;?>">
        </div>
        <div class="form-group">
          <label class="sr-only" for="req_igf_server_serial_no">Request Title</label>
          <input type="text" class="form-control input-sm" name="req_igf_server_serial_no" id="req-igf-server-serial-no" placeholder="Server Serial Number" size="40" value="<?php echo $req_igf_server_serial_no;?>">
        </div>
        <button type="submit" value="Search" name="search" class="btn btn-primary">Search</button>
      </form>

      <div class="clearfix"></div>
        <table class="table table-bordered table-hover table-request-list">
          <tr>
            <th width="80px">Request Id</th>
            <th width="120px">Requestor Group</th>
            <th width="120px">Requestor <br />Sub-Group</th>
            <th width="80px">Request Date</th>
            <th>Request Title</th>
            <th>IGF File Name</th>
            <th width="50px">View</th>
          </tr>
<?php
  if($res_search) {
    if(mysql_num_rows($res_search)) {
      while($row = mysql_fetch_array($res_search)){
?>
          <tr>
            <td><?php echo $row['req_id'];?></td>
            <td><?php echo strtoupper($row['req_group_name']);?></td>
            <td><?php echo strtoupper($row['req_group_name_sub']);?></td>
            <td><?php echo pei_date_format($row['req_date']);?></td>
            <td><?php echo strtoupper($row['req_title']);?></td>
            <td><?php echo $row['igf_file_name'];?></td>
            <td>
              <a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_view.php?id=<?php echo $row['igf_id'];?>">View</a> |
              <a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_download_consolidated.php?id=<?php echo $row['igf_id'];?>">Download</a>
            </td>
          </tr>
<?php
      }// END while
    }
    else {
?>
        <tr class="info">
          <td colspan="7" style="text-align:center;">No Record Found!
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

  $('.dropdown-select2').select2({
    allowClear: true
  });

  $('#req-user-group').select2({
    placeholder: 'Requestor Group',
    allowClear: true
  });

  $('#req-sub-group')
  .select2({
    minimumInputLength: 0,
    placeholder: 'Requestor Sub Group',
    allowClear: true,
    ajax: {
      url: "<?php echo $pei_config['urls']['baseUrl'];?>/request/fetch_sub_group.php",
      dataType: 'json',
      delay: 50,
      data: function (params) {
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
