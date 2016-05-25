<?php
  session_start();
  $pei_current_module = 'REQUEST';
  require_once(__dir__.'/../header.php');

  //echo error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';

  // Initialize variables
  $pei_messages     = array();
  $action           = 'View';
  $pei_page_access  = FALSE;
  $pei_access_group_wise  = FALSE;
  $pei_user         = $_SESSION['pei_user'];


  $req_count              = 0;
  $total_server           = 0;
  $total_server_phy       = 0;
  $total_server_vir       = 0;
  $total_server_rel       = 0;
  $total_server_phy_rel   = 0;
  $total_server_vir_rel   = 0;
  $total_server_pen       = 0;
  $total_server_phy_pen   = 0;
  $total_server_vir_pen   = 0;

  $total_san              = 0;
  $total_san_phy          = 0;
  $total_san_vir          = 0;
  $total_san_rel          = 0;
  $total_san_phy_rel      = 0;
  $total_san_vir_rel      = 0;
  $total_san_pen          = 0;
  $total_san_phy_pen      = 0;
  $total_san_vir_pen      = 0;

  $total_cluster          = 0;
  $total_cluster_phy      = 0;
  $total_cluster_vir      = 0;
  $total_cluster_rel      = 0;
  $total_cluster_phy_rel  = 0;
  $total_cluster_vir_rel  = 0;
  $total_cluster_pen      = 0;
  $total_cluster_phy_pen  = 0;
  $total_cluster_vir_pen  = 0;

  // CHECK access permission for
  if(in_array('view_request_dashboard', $pei_user_access_permission)) {
    $pei_page_access = TRUE;
  }

  // CHECK access permission for
  if(in_array('view_request_dashboard_user_sub_group_wise', $pei_user_access_permission)) {
    $pei_access_group_wise = TRUE;
  }


  $data_req_user_sub_group  = request_dashboard_user_sub_group_wise_req_count();
  //var_dump($data_req_user_sub_group);
  if($data_req_user_sub_group) {
    // Find Sub Group Wise Server Count
    $data_req_user_sub_group_server = request_dashboard_user_sub_group_wise();
    //var_dump($data_req_user_sub_group_server);

    if($data_req_user_sub_group_server) {
      //var_dump($data_req_user_sub_group_server);
      foreach ($data_req_user_sub_group_server as $key => $value) {
        //var_dump($value);
        if($value['type']){
          $type         = $value['type'];
          $server_type  = $value['server_type_name'];

          $rel          = $value['server_released_status'];
          $type_rel     = $server_type.'_'.$rel;

          $data_req_user_sub_group[strtoupper($value['user_sub_group_name'])][$type][$server_type][$rel] = $value['count'];
        }
      }
    }
  }

  //var_dump($data_req_user_sub_group);
  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/index.php">User Requests</a></li>
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/dashboard.php">Dashboard</a></li>
        <li class="active">Dashboard - User Sub Group Wise</li>
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

<?php
  if($pei_access_group_wise) {
?>
        <div class="clearfix"></div>
        <div class="col-sm-12">
        <table id="user-group-wise-dashboard" class="table table-bordered table-hover" data-order='[[ 12, "dsc" ]]'>
          <thead>
          <tr>
            <th colspan="38" class="info">USER GROUP WISE DASHBOARD</th>
          </tr>
          <tr>
            <th colspan="2">&nbsp</th>
            <th class="info" ></th>
            <th colspan="11" class="text-center bg-primary">SERVERS / EQUIPMENTS</th>
            <th class="info" ></th>
            <th colspan="11" class="text-center">SAN</th>
            <th class="info" ></th>
            <th colspan="11" class="text-center bg-danger">CLUSTER</th>
          </tr>
          <tr class="active">
            <th width="350px">&nbsp</th>
            <th class="text-center" width="120px">&nbsp</th>
            <td class="info" ></td>
            <th class="text-center" colspan="3">TOTAL</th>
            <th class="success" ></th>
            <th class="text-center" colspan="3">RELEASED</th>
            <th class="success" ></th>
            <th class="text-center" colspan="3">PENDING</th>
            <td class="info" ></td>
            <th class="text-center" colspan="3">TOTAL</th>
            <th class="success" ></th>
            <th class="text-center" colspan="3">RELEASED</th>
            <th class="success" ></th>
            <th class="text-center" colspan="3">PENDING</th>
            <td class="info" ></td>
            <th class="text-center" colspan="3">TOTAL</th>
            <th class="success" ></th>
            <th class="text-center" colspan="3">RELEASED</th>
            <th class="success" ></th>
            <th class="text-center" colspan="3">PENDING</th>
          </tr>

          <tr class="active">
            <th width="350px">&nbsp</th>
            <th class="text-center" width="120px">Request Count</th>
            <td class="info" ></td>

            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
            <td class="success" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
            <td class="success" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>

            <td class="info" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
            <td class="success" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
            <td class="success" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>

            <td class="info" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
            <td class="success" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
            <td class="success" ></td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Phy</th>
            <th class="text-center" width="90px">Vir</th>
          </tr>
          </thead>
<?php
        if($data_req_user_sub_group) {
          foreach ($data_req_user_sub_group as $key => $value) {
            $server_total         = 0;
            $server_phy           = 0;
            $server_vir           = 0;
            $server_total_rel     = 0;
            $server_phy_rel       = 0;
            $server_vir_rel       = 0;
            $server_total_pen     = 0;
            $server_phy_pen       = 0;
            $server_vir_pen       = 0;

            $san_total            = 0;
            $san_phy              = 0;
            $san_vir              = 0;
            $san_total_rel        = 0;
            $san_phy_rel          = 0;
            $san_vir_rel          = 0;
            $san_total_pen        = 0;
            $san_phy_pen          = 0;
            $san_vir_pen          = 0;

            $cluster_total        = 0;
            $cluster_phy          = 0;
            $cluster_vir          = 0;
            $cluster_total_rel    = 0;
            $cluster_phy_rel      = 0;
            $cluster_vir_rel      = 0;
            $cluster_total_pen    = 0;
            $cluster_phy_pen      = 0;
            $cluster_vir_pen      = 0;

            $req_count = $req_count + $value['count'];

            if(isset($value['SERVERS / EQUIPMENTS'])) {

              if(isset($value['SERVERS / EQUIPMENTS']['PHYSICAL'])) {

                if(isset($value['SERVERS / EQUIPMENTS']['PHYSICAL']['RELEASED'])) {
                  $server_phy_rel       = $value['SERVERS / EQUIPMENTS']['PHYSICAL']['RELEASED'];
                  $server_total_rel     = $server_total_rel + $server_phy_rel;
                }

                if(isset($value['SERVERS / EQUIPMENTS']['PHYSICAL']['NOT_RELEASED'])) {
                  $server_phy_pen       = $value['SERVERS / EQUIPMENTS']['PHYSICAL']['NOT_RELEASED'];
                  $server_total_pen     = $server_total_pen + $server_phy_pen;
                }
              }

              if(isset($value['SERVERS / EQUIPMENTS']['VIRTUAL'])) {
                if(isset($value['SERVERS / EQUIPMENTS']['VIRTUAL']['RELEASED'])) {
                  $server_vir_rel       = $value['SERVERS / EQUIPMENTS']['VIRTUAL']['RELEASED'];
                  $server_total_rel     = $server_total_rel + $server_vir_rel;
                }

                if(isset($value['SERVERS / EQUIPMENTS']['VIRTUAL']['NOT_RELEASED'])) {
                  $server_vir_pen       = $value['SERVERS / EQUIPMENTS']['VIRTUAL']['NOT_RELEASED'];
                  $server_total_pen     = $server_total_pen + $server_vir_pen;
                }
              }
            }

            if(isset($value['SAN'])) {

              if(isset($value['SAN']['PHYSICAL'])){
                if(isset($value['SAN']['PHYSICAL']['RELEASED'])) {
                  $san_phy_rel        = $value['SAN']['PHYSICAL']['RELEASED'];
                  $san_total_rel      = $san_total_rel + $san_phy_rel;
                }

                if(isset($value['SAN']['PHYSICAL']['NOT_RELEASED'])) {
                  $san_phy_pen        = $value['SAN']['PHYSICAL']['NOT_RELEASED'];
                  $san_total_pen      = $san_total_pen + $san_phy_pen;
                }
              }

              if(isset($value['SAN']['VIRTUAL'])){
                if(isset($value['SAN']['VIRTUAL']['RELEASED'])) {
                  $san_vir_rel        = $value['SAN']['VIRTUAL']['RELEASED'];
                  $san_total_rel      = $san_total_rel + $san_vir_rel;
                }

                if(isset($value['SAN']['VIRTUAL']['NOT_RELEASED'])) {
                  $san_vir_pen        = $value['SAN']['VIRTUAL']['NOT_RELEASED'];
                  $san_total_pen      = $san_total_pen + $san_vir_pen;
                }
              }
            }

            if(isset($value['CLUSTER'])) {

              if(isset($value['CLUSTER']['PHYSICAL'])){
                if(isset($value['CLUSTER']['PHYSICAL']['RELEASED'])) {
                  $cluster_phy_rel        = $value['CLUSTER']['PHYSICAL']['RELEASED'];
                  $cluster_total_rel      = $cluster_total_rel + $cluster_phy_rel;
                }

                if(isset($value['CLUSTER']['PHYSICAL']['NOT_RELEASED'])) {
                  $cluster_phy_pen        = $value['CLUSTER']['PHYSICAL']['NOT_RELEASED'];
                  $cluster_total_pen      = $cluster_total_pen + $cluster_phy_pen;
                }
              }

              if(isset($value['CLUSTER']['VIRTUAL'])){
                if(isset($value['CLUSTER']['VIRTUAL']['RELEASED'])) {
                  $cluster_vir_rel        = $value['CLUSTER']['VIRTUAL']['RELEASED'];
                  $cluster_total_rel      = $cluster_total_rel + $cluster_vir_rel;
                }

                if(isset($value['CLUSTER']['VIRTUAL']['NOT_RELEASED'])) {
                  $cluster_vir_pen        = $value['CLUSTER']['VIRTUAL']['NOT_RELEASED'];
                  $cluster_total_pen      = $cluster_total_pen + $cluster_vir_pen;
                }
              }
            }

            $server_phy           = $server_phy_rel + $server_phy_pen;
            $server_vir           = $server_vir_rel + $server_vir_pen;
            $server_total         = $server_phy + $server_vir;


            $san_phy              = $san_phy_rel + $san_phy_pen;
            $san_vir              = $san_vir_rel + $san_vir_pen;
            $san_total            = $san_phy + $san_vir;

            $cluster_phy          = $cluster_phy_rel + $cluster_phy_pen;
            $cluster_vir          = $cluster_vir_rel + $cluster_vir_pen;
            $cluster_total        = $cluster_phy + $cluster_vir;

            $total_server         = $total_server + $server_total ;
            $total_server_phy     = $total_server_phy + $server_phy;
            $total_server_vir     = $total_server_vir + $server_vir;
            $total_server_rel     = $total_server_rel + $server_total_rel;
            $total_server_phy_rel = $total_server_phy_rel + $server_phy_rel;
            $total_server_vir_rel = $total_server_vir_rel + $server_vir_rel;
            $total_server_pen     = $total_server_pen + $server_total_pen ;
            $total_server_phy_pen = $total_server_phy_pen + $server_phy_pen;
            $total_server_vir_pen = $total_server_vir_pen + $server_vir_pen;

            $total_san            = $total_san + $san_total;
            $total_san_phy        = $total_san_phy + $san_phy;
            $total_san_vir        = $total_san_vir + $san_vir;
            $total_san_rel        = $total_san_rel + $san_total_rel;
            $total_san_phy_rel    = $total_san_phy_rel + $san_phy_rel;
            $total_san_vir_rel    = $total_san_vir_rel + $san_vir_rel;
            $total_san_pen        = $total_san_pen + $san_total_pen;
            $total_san_phy_pen    = $total_san_phy_pen + $san_phy_pen;
            $total_san_vir_pen    = $total_san_vir_pen + $san_vir_pen;

            $total_cluster        = $total_cluster + $cluster_total;
            $total_cluster_phy    = $total_cluster_phy + $cluster_phy;
            $total_cluster_vir    = $total_cluster_vir + $cluster_vir;
            $total_cluster_rel    = $total_cluster_rel + $cluster_total_rel;
            $total_cluster_phy_rel= $total_cluster_phy_rel + $cluster_phy_rel;
            $total_cluster_vir_rel= $total_cluster_vir_rel + $cluster_vir_rel;
            $total_cluster_pen    = $total_cluster_pen + $cluster_total_pen;
            $total_cluster_phy_pen= $total_cluster_phy_pen + $cluster_phy_pen;
            $total_cluster_vir_pen= $total_cluster_vir_pen + $cluster_vir_pen;
?>
          <tr>
            <th class="active"><?php echo strtoupper($value['name']);?></th>
            <td class="text-center"><?php echo $value['count'];?></td>
            <td class="info" ></td>

            <td class="text-center"><?php echo $server_total;?></td>
            <td class="text-center"><?php echo $server_phy;?></td>
            <td class="text-center"><?php echo $server_vir;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $server_total_rel;?></td>
            <td class="text-center"><?php echo $server_phy_rel;?></td>
            <td class="text-center"><?php echo $server_vir_rel;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $server_total_pen;?></td>
            <td class="text-center"><?php echo $server_phy_pen;?></td>
            <td class="text-center"><?php echo $server_vir_pen;?></td>

            <td class="info" ></td>
            <td class="text-center"><?php echo $san_total;?></td>
            <td class="text-center"><?php echo $san_phy;?></td>
            <td class="text-center"><?php echo $san_vir;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $san_total_rel;?></td>
            <td class="text-center"><?php echo $san_phy_rel;?></td>
            <td class="text-center"><?php echo $san_vir_rel;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $san_total_pen;?></td>
            <td class="text-center"><?php echo $san_phy_pen;?></td>
            <td class="text-center"><?php echo $san_vir_pen;?></td>

            <td class="info" ></td>
            <td class="text-center"><?php echo $cluster_total;?></td>
            <td class="text-center"><?php echo $cluster_phy;?></td>
            <td class="text-center"><?php echo $cluster_vir;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $cluster_total_rel;?></td>
            <td class="text-center"><?php echo $cluster_phy_rel;?></td>
            <td class="text-center"><?php echo $cluster_vir_rel;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $cluster_total_pen;?></td>
            <td class="text-center"><?php echo $cluster_phy_pen;?></td>
            <td class="text-center"><?php echo $cluster_vir_pen;?></td>
          </tr>
<?php
          } // END foreach
?>
          <tr class="warning">
            <th class="text-right">TOTAL</th>
            <td class="text-center"><?php echo $req_count;?></td>
            <td class="info" ></td>

            <td class="text-center"><?php echo $total_server;?></td>
            <td class="text-center"><?php echo $total_server_phy;?></td>
            <td class="text-center"><?php echo $total_server_vir;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $total_server_rel;?></td>
            <td class="text-center"><?php echo $total_server_phy_rel;?></td>
            <td class="text-center"><?php echo $total_server_vir_rel;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $total_server_pen;?></td>
            <td class="text-center"><?php echo $total_server_phy_pen;?></td>
            <td class="text-center"><?php echo $total_server_vir_pen;?></td>
            <td class="info" ></td>

            <td class="text-center"><?php echo $total_san;?></td>
            <td class="text-center"><?php echo $total_san_phy;?></td>
            <td class="text-center"><?php echo $total_san_vir;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $total_san_rel;?></td>
            <td class="text-center"><?php echo $total_san_phy_rel;?></td>
            <td class="text-center"><?php echo $total_san_vir_rel;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $total_san_pen;?></td>
            <td class="text-center"><?php echo $total_san_phy_pen;?></td>
            <td class="text-center"><?php echo $total_san_vir_pen;?></td>
            <td class="info" ></td>

            <td class="text-center"><?php echo $total_cluster;?></td>
            <td class="text-center"><?php echo $total_cluster_phy;?></td>
            <td class="text-center"><?php echo $total_cluster_vir;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $total_cluster_rel;?></td>
            <td class="text-center"><?php echo $total_cluster_phy_rel;?></td>
            <td class="text-center"><?php echo $total_cluster_vir_rel;?></td>
            <td class="success" ></td>
            <td class="text-center"><?php echo $total_cluster_pen;?></td>
            <td class="text-center"><?php echo $total_cluster_phy_pen;?></td>
            <td class="text-center"><?php echo $total_cluster_vir_pen;?></td>
          </tr>
<?php
        } // END IF $data_req_user_sub_group
?>
        </table>
        </div>
<?php
   } // END IF $pei_access_group_wise
}
?>
      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
  require_once(__dir__.'/../footer.php');
?>
<script src="<?php echo $pei_config['urls']['baseUrl'];?>/vendors/DataTables/jquery.dataTables.min.js"></script>
<script type="text/javascript">
  $(document).ready(function() {
    $('#user-group-wise-dashboard').DataTable({
      paging: false,
      searching: false,
    });
  } );
</script>
