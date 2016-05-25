<?php
  session_start();
  $pei_current_module = 'REQUEST';
  require_once(__dir__.'/../header.php');

  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');
  require_once($pei_config['paths']['base'].'/vendor/pei_vendor.php');

  // Include Pagination
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  //error_reporting(E_ALL);ini_set('display_errors', 1);
  //echo '<pre> <br /><br /><br /><br />';


  // Initialize variables
  $pei_msg        = '';
  $pei_page_access= FALSE;
  $igf_version          = $pei_config['igf']['version'];
  // Request Number start from req_id number
  $igf_version_req_id   = $pei_config['igf']['after_req_id'];

  $pei_page_access= TRUE;

  $ref_name       = '';
  $data_ref_value = '';

  if(isset($_REQUEST['ref']) && $_REQUEST['ref'] != '') {
    $ref_name = strtoupper(urldecode($_REQUEST['ref']));
    switch ($ref_name) {
      case 'REQUESTOR GROUP':
        $data_ref_value = validation_user_group();
        break;
      case 'REQUESTOR SUB-GROUP':
        $data_ref_value = user_group_distinct_sub_group_name();
        break;
      case 'ENVIRONMENT':
        $data_ref_value = environment_distinct_name();
        break;
      case 'LOCATION':
        $data_ref_value = location_distinct_name();
        break;
      case 'SERVER HALL':
        $data_ref_value = server_hall_distinct_name();
        break;
      case 'ROW-RACK':
        $data_ref_value = row_rack_distinct_name();
        break;
      case 'EQUIPMENT TYPE':
        $data_ref_value = validation_equipment_type($igf_version);
        break;
      case 'HYPERVISOR':
        $data_ref_value = config_value('IGF', 'HYPERVISOR', $igf_version);
        break;
      case 'EQUIPMENT ROLE':
        $data_ref_value = config_value('IGF', 'EQUIPMENT ROLE', $igf_version);
        break;
      case 'EQUIPMENT MAKE':
        $data_ref_value = vendor_distinct_name();
        break;
      case '# OF NICS - 1G':
        $data_ref_value = config_value('IGF', '# of NICs - 1G', $igf_version);
        break;
      case '# OF NICS - 10G':
        $data_ref_value = config_value('IGF', '# of NICs - 10G', $igf_version);
        break;
      case '# OF FC HBA CARDS':
        $data_ref_value = config_value('IGF', '# of FC HBA CARDS', $igf_version);
        break;
      case '# OF FC HBA PORTS':
        $data_ref_value = config_value('IGF', '# of FC HBA PORTS', $igf_version);
        break;
      case 'FC HBA PORT SPEED':
        $data_ref_value = config_value('IGF', 'FC HBA PORT SPEED', $igf_version);
        break;
      case '# OF DATA LAN PORTS':
      case '# OF PRIVATE LAN PORTS':
      case '# OF CLUSTER LAN PORTS':
        $data_ref_value = config_value('IGF', 'LAN PORTS', $igf_version);
        break;
      case 'DATA LAN INTERFACE TYPE':
      case 'PRIVATE LAN INTERFACE TYPE':
      case 'CLUSTER LAN INTERFACE TYPE':
        $data_ref_value = config_value('IGF', 'LAN INTERFACE TYPE', $igf_version);
        break;
      case 'DATA LAN INTERFACE SPEED':
      case 'PRIVATE LAN INTERFACE SPEED':
      case 'CLUSTER LAN INTERFACE SPEED':
        $data_ref_value = config_value('IGF', 'LAN INTERFACE SPEED', $igf_version);
        break;
      case 'NETWORK ZONE':
        $data_ref_value = config_value('IGF', 'NETWORK ZONE', $igf_version);
        break;
      case 'LOAD BALANCER REQUIRED':
      case 'HA / CLUSTER':
        $data_ref_value = config_value('IGF', 'YES NO', $igf_version);
        break;
      case 'HA TYPE / CLUSTER SOFTWARE':
        $data_ref_value = config_value('IGF', 'HA TYPE / CLUSTER SOFTWARE', $igf_version);
        break;
      case 'OS':
        $data_ref_value = os_distinct_name();
        break;
      case 'DB':
        $data_ref_value = config_value('IGF', 'DB', $igf_version);
        break;
      case 'EXTERNAL STORAGE TYPE':
        $data_ref_value = config_value('IGF', 'EXTERNAL STORAGE TYPE', $igf_version);
        break;
      case 'STORAGE ARRAY':
        $data_ref_value = config_value('IGF', 'STORAGE ARRAY', $igf_version);
        break;
      case 'EXTERNAL STORAGE RAID CONFIG':
        $data_ref_value = config_value('IGF', 'EXTERNAL STORAGE RAID CONFIG', $igf_version);
        break;
      case 'VOLUME MANAGER':
        $data_ref_value = config_value('IGF', 'VOLUME MANAGER', $igf_version);
        break;
      case 'IDC SUPPORT REQUIREMENT':
        $data_ref_value = config_value('IGF', 'IDC SUPPORT REQUIREMENT', $igf_version);
        break;
      case 'REMOVE - NIC':
      case 'ADD - NIC':
        $data_ref_value = config_value('IGF', 'NIC', $igf_version);
        break;
      case 'REMOVE - FC HBA':
      case 'ADD - FC HBA':
        $data_ref_value = config_value('IGF', 'FC HBA', $igf_version);
        break;
      default:
        # code...
        break;
    }
  }


  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_validation_reference.php">IGF Validation Reference</a></li>
        <li class="active">IGF Reference Value</li>
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
        <div class="clearfix"></div>
        <table class="table table-bordered table-hover">
          <tr>
            <th width="300px">Name</th>
            <th>Reference Values</th>
          </tr>

          <tr>
            <td><?php echo $ref_name;?></td>
            <td>
              <?php
              if($data_ref_value) {
                foreach ($data_ref_value as $ref_value) {
                  echo $ref_value.'<br />';
                }
              }
              ?>
            </td>
          </tr>
        </table>
<?php
}
?>


      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
  require_once(__dir__.'/../footer.php');
?>
