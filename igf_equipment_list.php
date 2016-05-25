<?php
  session_start();
  $pei_current_module = 'REQUEST';
  require_once(__dir__.'/../header.php');
  require_once($pei_config['paths']['base'].'/vendor/pei_vendor.php');

  // Include Pagination
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  error_reporting(E_ALL);ini_set('display_errors', 1);
  echo '<pre> <br /><br /><br /><br />';
  //var_dump($_REQUEST);

  // Initialize variables
  $pei_messages     = array();
  $pei_page_access= FALSE;

  $start_from     = 0;
  $record_per_page= $pei_config['pagination']['record_per_page'];
  $record_found   = 0;
  $sql_where      = ' 1 ';
  $show_page      = 1;
  $page           = 0;

  $data_vendor_model      = '';
  $vendor_model_name      = '';
  $vendor_id    = '';

  // CHECK access permission for
  if(in_array('view_igf_equipment_list', $pei_user_access_permission)) {
    $pei_page_access = TRUE;
  }
  $pei_page_access = TRUE;

  $data_vendor      = vendor_detail();
  // Search Query
  if(isset($_GET['search'])) {

    if(isset($_GET['vendor_id']) && $_GET['vendor_id'] != '') {
      $vendor_id = $_GET['vendor_id'];
      $sql_where .= " AND vm.vendor_id ='".mysql_real_escape_string($vendor_id)."' ";
    }

    if(isset($_GET['vendor_model_name']) && $_GET['vendor_model_name'] != '') {
      $vendor_model_name = $_GET['vendor_model_name'];
      $sql_where .= " AND vm.vendor_model_name LIKE '%".mysql_real_escape_string($vendor_model_name)."%' ";
    }

  }

  // Find total no. of records found for search query required for pagination
  $sql_total = "SELECT vm.vendor_model_id
                FROM
                  idc_vendor_model AS vm
                  LEFT JOIN
                  idc_vendor AS v ON vm.vendor_id = v.vendor_id
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
  $sql_order_by = ' ORDER BY vm.vendor_model_id DESC ';
  $sql_search   = " SELECT
                      vm.*,
                      v.vendor_name
                    FROM
                      idc_vendor_model AS vm
                      LEFT JOIN
                      idc_vendor AS v ON vm.vendor_id = v.vendor_id
                    WHERE ".$sql_where."
                  ".$sql_order_by.'  '.$sql_limit.';';
  //echo $sql_search.'<br />';
  $res_search = mysql_query($sql_search);

  if(!$res_search){
    $pei_messages['error'][] = 'Something went wrong.';
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

  if(isset($_GET['vendor_id'])) {
    $reload .= '&vendor_id='.$vendor_id;
  }

  if(isset($_GET['vendor_model_name'])) {
    $reload .= '&vendor_model_name='.$vendor_model_name;
  }


  echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li class="active">IGF</li>
      </ol>
      <!-- /breadcrumb  -->

      <div class="box-content">

<?php
  if(isset($pei_messages) && count($pei_messages)) {
    if(isset($pei_messages['error']) && count($pei_messages['error'])) {
    ?>
     <div class="alert alert-danger" role="alert">
     <?php
      foreach ($pei_messages['error'] as $key => $error) {
        echo preg_replace("/\\\\n/", "<br />", $error).'<br />';
      }// END error foreach
    ?>
      </div>
<?php
    }

    if(isset($pei_messages['success']) && count($pei_messages['success'])) {
      foreach ($pei_messages['success'] as $key => $success) {
      ?>
      <div class="alert alert-success" role="alert">
      <?php echo preg_replace("/\\\\n/", "<br />", $success);?>
      </div>
<?php
      }// END error foreach
    }
  }

if($pei_page_access == FALSE) {
?>
  <div class="alert alert-danger" role="alert">
  Unauthorized Access
  </div>

<?php
}
else {
?>

      <form class="form-inline" method="GET">
        <div class="form-group">
          <label class="sr-only" for="vendor_id">Vendor</label>
          <select class="placeholder form-control pei-control-select2" name="vendor_id" id="vendor-id">
            <option value="">--- VENDOR ---</option>
        <?php
        foreach ($data_vendor as $key => $vendor) {
        ?>
          <option value="<?php echo $vendor['vendor_id'];?>" <?php if($vendor_id ==  $vendor['vendor_id']) { ?> selected="selected" <?php } ?>><?php echo $vendor['vendor_name'];?></option>
        <?php
        }
        ?>
          </select>
        </div>
        <div class="form-group">
          <label class="sr-only" for="vendor_model_name">Name</label>
          <input type="text" class="form-control input-sm" name="vendor_model_name" id="vendor-model-name" placeholder="MODEL NAME" size="40" value="<?php echo $vendor_model_name;?>">
        </div>
        <button type="submit" value="Search" name="search" class="btn btn-primary">Search</button>
      </form>

      <div class="clearfix"></div>
        <table class="table table-bordered table-hover">
          <tr>
            <th width="50px" class="text-center">Id</th>
            <th>Vendor</th>
            <th>Name</th>
            <th width="50px" class="text-center">Status</th>
            <th width="50px" class="text-center">Action</th>
          </tr>
    <?php
    if($res_search) {
      if(mysql_num_rows($res_search)) {
        while ($row = mysql_fetch_array($res_search)) {
          $status_flag  = 'glyphicon glyphicon-remove glyphicon-remove-red';
          if($row['vendor_model_status']){
            $status_flag  = 'glyphicon glyphicon-ok glyphicon-ok-green';
          }
    ?>
          <tr class="active">
            <td class="text-center"><?php echo $row['vendor_model_id'];?></td>
            <td><?php echo $row['vendor_name'];?></td>
            <td><?php echo $row['vendor_model_name'];?></td>
            <td class="text-center"><span class="<?php echo $status_flag?>" aria-hidden="true"></span></td>
            <td class="text-center"><a href="<?php echo $pei_config['urls']['baseUrl'];?>/vendor/vendor_model.php?action=Edit&vendor_model_id=<?php echo $row['vendor_model_id'];?>">Edit</a></td>
          </tr>
    <?php
        }
      }
      else {
      ?>
          <tr class="info text-center">
            <td colspan="5">No Record Found!</td>
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
  $('#vendor-id').select2({
    placeholder: 'VENDOR',
    allowClear: true
  });
});
</script>
