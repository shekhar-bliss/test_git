<?php
  session_start();
  $pei_current_module = 'REQUEST';
  require_once(__dir__.'/../header.php');

  // Include Pagination
  require_once($pei_config['paths']['base'].'/pei_paginate.php');

  //error_reporting(E_ALL);ini_set('display_errors', 1);
  //echo '<pre> <br /><br /><br /><br />';
  //var_dump($_REQUEST);

  // Initialize variables
  $pei_msg        = '';
  $pei_page_access= FALSE;
  $data_ref = '';

  $pei_page_access= TRUE;

  $data_ref[] = array('col' => 'A', 'col' => 'A', 'ref' => 'APPLICATION NAME', 'validation' => 'Should not be empty.');
  $data_ref[] = array('col' => 'B', 'ref' => 'REQUESTOR GROUP', 'validation' => 'Should not be empty. <br /> Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'C', 'ref' => 'REQUESTOR SUB-GROUP', 'validation' => 'Should not be empty. <br /> Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'D', 'ref' => 'ENVIRONMENT', 'validation' => 'Should not be empty. <br /> Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'E', 'ref' => 'LOCATION', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'F', 'ref' => 'SERVER HALL', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'G', 'ref' => 'ROW-RACK', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'L', 'ref' => 'EQUIPMENT TYPE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'M', 'ref' => 'HYPERVISOR', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'N', 'ref' => 'EQUIPMENT ROLE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'O', 'ref' => 'EQUIPMENT SERIAL NUMBER', 'validation' => 'For RFI, Serial Number of HP Server should be available in Stores.', 'link' => FALSE);
  $data_ref[] = array('col' => 'P', 'ref' => 'EQUIPMENT MAKE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'Y', 'ref' => '# of NICs - 1G', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'Z', 'ref' => '# of NICs - 10G', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AA', 'ref' => '# of FC HBA CARDS', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AB', 'ref' => '# of FC HBA PORTS', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AC', 'ref' => 'FC HBA PORT SPEED', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AD', 'ref' => '# of DATA LAN PORTS', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AE', 'ref' => 'DATA LAN INTERFACE TYPE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AF', 'ref' => 'DATA LAN INTERFACE SPEED', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AG', 'ref' => '# of PRIVATE LAN PORTS', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AH', 'ref' => 'PRIVATE LAN INTERFACE TYPE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AI', 'ref' => 'PRIVATE LAN INTERFACE SPEED', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AJ', 'ref' => '# of CLUSTER LAN PORTS', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AK', 'ref' => 'CLUSTER LAN INTERFACE TYPE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AL', 'ref' => 'CLUSTER LAN INTERFACE SPEED', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AM', 'ref' => 'NETWORK ZONE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AN', 'ref' => 'NETWORK SUB ZONE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AO', 'ref' => 'LOAD BALANCER REQUIRED', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AP', 'ref' => 'HA / CLUSTER', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AQ', 'ref' => 'HA TYPE / CLUSTER SOFTWARE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AS', 'ref' => 'OS', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AU', 'ref' => 'DB', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AW', 'ref' => 'EXTERNAL STORAGE TYPE', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AY', 'ref' => 'STORAGE ARRAY', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'AZ', 'ref' => 'EXTERNAL STORAGE RAID CONFIG', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'BF', 'ref' => 'VOLUME MANAGER', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'BJ', 'ref' => 'IDC SUPPORT REQUIREMENT', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'BN', 'ref' => 'REMOVE - NIC', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'BR', 'ref' => 'ADD - NIC', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'BO', 'ref' => 'REMOVE - FC HBA', 'validation' => 'Value should be from specified range.', 'link' => TRUE);
  $data_ref[] = array('col' => 'BS', 'ref' => 'ADD - FC HBA', 'validation' => 'Value should be from specified range.', 'link' => TRUE);

  //var_dump($data_ref);
  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li class="active">IGF Validation Reference</li>

        <div class="pei-breadcrumb-right">
          <a style="padding:2px !important" href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_reference_download.php?file=DDL-IGF-V4.xlsx">IGF Reference Download</a>
        </div>
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
            <th width="50px" class="text-center">Column</th>
            <th width="300px">Name</th>
            <th>Validation</th>
            <th width="50px" class="text-center">Action</th>
          </tr>
<?php
        if($data_ref) {
          $i = 0;
          foreach ($data_ref as $ref) {
            $i++;
?>
          <tr>
            <td class="text-center"><?php echo $ref['col'];?></td>
            <td><?php echo $ref['ref'];?></td>
            <td><?php echo nl2br($ref['validation']);?></td>
            <td>
              <?php
              if(isset($ref['link']) && $ref['link']) {
              ?>
              <a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_reference_value.php?action=View&ref=<?php echo urlencode($ref['ref']);?>">View</a>
              <?php
              }
              ?>
            </td>
          </tr><?php
          }
        }
?>
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
