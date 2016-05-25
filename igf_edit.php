<?php
  session_start();
  $pei_current_module = 'REQUEST';
  ini_set('max_execution_time', 0);

  require_once(__dir__.'/../header.php');

  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');

  //error_reporting(E_ALL); ini_set('display_errors', 1);
 // echo '<pre><br /><br /><br /><br />';


  // Initialize variables
  $pei_messages         = array();
  $pei_less_word_count  = 60;
  $uname                = strtolower($_SESSION['pei_user']);

  $igf              = '';
  $igf_id           = $_GET['id'];
  $req_id           = '';
  $req_name         = '';
  $igf_request      = '';
  $igf_contact_spoc = '';
  $igf_contact_hod  = '';
  $igf_contact_ops_1= '';
  $igf_contact_ops_2= '';
  $igf_budget       = '';
  $igf_server       = '';
  $igf_equipment    = '';
  $igf_software     = '';
  $igf_patching     = '';
  $igf_locations    = '';
  $igf_server_halls = '';
  $igf_row_racks    = '';

  $data_igf_eqpt        = '';
  $igf_header_3         = 'EQUIPMENT DETAILS';
  $igf_contact_title_1  = 'CONTACT 1';
  $igf_contact_title_2  = 'CONTACT 2';
  $igf_version          = $pei_config['igf']['version'];
  // Request Number start from req_id number
  $igf_version_req_id   = $pei_config['igf']['after_req_id'];
  $req_id_num           = '';

  $igf_locations    = get_all_location();
  $igf_server_halls = get_all_server_hall();
  $igf_row_racks    = get_all_row_rack();

  $pei_page_access = FALSE;

  // Get Request Details from this igf
  $igf_request      = get_request_detail_from_igf_id($igf_id);
  if($igf_request) {
    $req_id   = $igf_request['req_id'];
    $req_name = $igf_request['req_title'];
  }

  // Find IGF version
  $req_id_num = substr($req_id, -4);
  if($req_id_num <= $igf_version_req_id) {
    $igf_version  = 'v3.1';
  }

  switch($igf_version){
    case 'v3':
    case 'v3.1':
      $igf_header_3  = 'SERVER DETAILS';
      $igf_contact_title_1  = 'CONTACT SPOC';
      $igf_contact_title_2  = 'HOD';
      break;
    case 'v4':
    default:
      $igf_header_3  = 'EQUIPMENT DETAILS';
      $igf_contact_title_1  = 'CONTACT 1';
      $igf_contact_title_2  = 'CONTACT 2';
  }
  // Get Server Details from this igf
  $igf_server = get_igf_server_by_igf_id($igf_id);

  if(in_array('edit_any_igf', $pei_user_access_permission)) {
    $pei_page_access = TRUE;
  }

  if(isset($_POST['igf_save']) && $_POST['igf_save'] == 'Save') {
    //var_dump($_POST);

    // Save IGF Equipmet details if not error found in data
    if(!isset($pei_messages['error'])) {
      $server_update_count = 0;
      foreach ($igf_server as $server) {
        $data_igf_eqpt                = '';

        if(isset($_POST['igf_server_loc']) && isset($_POST['igf_server_loc'][$server_update_count])) {
          $data_igf_eqpt['igf_server_loc']      = get_location_name_by_id($_POST['igf_server_loc'][$server_update_count]);
          $data_igf_eqpt['igf_server_loc_id']   = $_POST['igf_server_loc'][$server_update_count];
        }

        if(isset($_POST['igf_server_sh']) && isset($_POST['igf_server_sh'][$server_update_count])) {
          $data_igf_eqpt['igf_server_server_hall']      = get_server_hall_name_by_id($_POST['igf_server_sh'][$server_update_count]);
          $data_igf_eqpt['igf_server_server_hall_id']   = $_POST['igf_server_sh'][$server_update_count];
        }

        if(isset($_POST['igf_server_rr']) && isset($_POST['igf_server_rr'][$server_update_count])) {
          $data_igf_eqpt['igf_server_row_rack']      = get_row_rack_name_by_id($_POST['igf_server_rr'][$server_update_count]);
          $data_igf_eqpt['igf_server_row_rack_id']   = $_POST['igf_server_rr'][$server_update_count];
        }

        if(isset($_POST['igf_server_rack_name']) && isset($_POST['igf_server_rack_name'][$server_update_count])) {
          $data_igf_eqpt['igf_server_rack_name']   = $_POST['igf_server_rack_name'][$server_update_count];
        }

        if(isset($_POST['igf_server_rack_u']) && isset($_POST['igf_server_rack_u'][$server_update_count])) {
          $data_igf_eqpt['igf_server_rack_u']   = $_POST['igf_server_rack_u'][$server_update_count];
        }

        if(isset($_POST['igf_server_slot_no']) && isset($_POST['igf_server_slot_no'][$server_update_count])) {
          $data_igf_eqpt['igf_server_slot_no']   = $_POST['igf_server_slot_no'][$server_update_count];
        }

        if(isset($_POST['igf_server_serial_number']) && isset($_POST['igf_server_serial_number'][$server_update_count])) {
          $data_igf_eqpt['igf_server_serial_number']   = $_POST['igf_server_serial_number'][$server_update_count];
        }

        if(isset($_POST['igf_server_hostname']) && isset($_POST['igf_server_hostname'][$server_update_count])) {
          $data_igf_eqpt['igf_server_hostname']   = $_POST['igf_server_hostname'][$server_update_count];
        }

        if(isset($_POST['igf_server_console_ip']) && isset($_POST['igf_server_console_ip'][$server_update_count])) {
          $data_igf_eqpt['igf_server_console_ip']   = $_POST['igf_server_console_ip'][$server_update_count];
        }

        if(isset($_POST['igf_server_console_ip_sm']) && isset($_POST['igf_server_console_ip_sm'][$server_update_count])) {
          $data_igf_eqpt['igf_server_console_ip_sm']   = $_POST['igf_server_console_ip_sm'][$server_update_count];
        }

        if(isset($_POST['igf_server_console_ip_gw']) && isset($_POST['igf_server_console_ip_gw'][$server_update_count])) {
          $data_igf_eqpt['igf_server_console_ip_gw']   = $_POST['igf_server_console_ip_gw'][$server_update_count];
        }

        if(isset($_POST['igf_server_data_ip_1']) && isset($_POST['igf_server_data_ip_1'][$server_update_count])) {
          $data_igf_eqpt['igf_server_data_ip_1']   = $_POST['igf_server_data_ip_1'][$server_update_count];
        }

        if(isset($_POST['igf_server_data_ip_2']) && isset($_POST['igf_server_data_ip_2'][$server_update_count])) {
          $data_igf_eqpt['igf_server_data_ip_2']   = $_POST['igf_server_data_ip_2'][$server_update_count];
        }

        if(isset($_POST['igf_server_vip']) && isset($_POST['igf_server_vip'][$server_update_count])) {
          $data_igf_eqpt['igf_server_vip']   = $_POST['igf_server_vip'][$server_update_count];
        }

        if(isset($_POST['igf_server_data_ip_sm']) && isset($_POST['igf_server_data_ip_sm'][$server_update_count])) {
          $data_igf_eqpt['igf_server_data_ip_sm']   = $_POST['igf_server_data_ip_sm'][$server_update_count];
        }

        if(isset($_POST['igf_server_other_ip']) && isset($_POST['igf_server_other_ip'][$server_update_count])) {
          $data_igf_eqpt['igf_server_other_ip']   = $_POST['igf_server_other_ip'][$server_update_count];
        }

        if(isset($_POST['igf_server_lb_ip']) && isset($_POST['igf_server_lb_ip'][$server_update_count])) {
          $data_igf_eqpt['igf_server_lb_ip']   = $_POST['igf_server_lb_ip'][$server_update_count];
        }

        if(isset($_POST['igf_server_public_ip']) && isset($_POST['igf_server_public_ip'][$server_update_count])) {
          $data_igf_eqpt['igf_server_public_ip']   = $_POST['igf_server_public_ip'][$server_update_count];
        }

        $data_igf_eqpt['created_by']  = $uname;
        $data_igf_eqpt['created_at']  = 'NOW';

        switch($igf_version){
          case 'v3':
          case 'v3.1':
            if(isset($_POST['igf_server_other_ip_sm']) && isset($_POST['igf_server_other_ip_sm'][$server_update_count])) {
              $data_igf_eqpt['igf_server_other_ip_sm']   = $_POST['igf_server_other_ip_sm'][$server_update_count];
            }

            if(isset($_POST['igf_server_other_ip_gw']) && isset($_POST['igf_server_other_ip_gw'][$server_update_count])) {
              $data_igf_eqpt['igf_server_other_ip_gw']   = $_POST['igf_server_other_ip_gw'][$server_update_count];
            }
            break;
          case 'v4':
          default:
            if(isset($_POST['igf_server_private_lan_ip']) && isset($_POST['igf_server_private_lan_ip'][$server_update_count])) {
              $data_igf_eqpt['igf_server_private_lan_ip']   = $_POST['igf_server_private_lan_ip'][$server_update_count];
            }
            if(isset($_POST['igf_server_private_lan_sm']) && isset($_POST['igf_server_private_lan_sm'][$server_update_count])) {
              $data_igf_eqpt['igf_server_private_lan_sm']   = $_POST['igf_server_private_lan_sm'][$server_update_count];
            }
            if(isset($_POST['igf_server_rac_ip']) && isset($_POST['igf_server_rac_ip'][$server_update_count])) {
              $data_igf_eqpt['igf_server_rac_ip']   = $_POST['igf_server_rac_ip'][$server_update_count];
            }
            if(isset($_POST['igf_server_scan_ip']) && isset($_POST['igf_server_scan_ip'][$server_update_count])) {
              $data_igf_eqpt['igf_server_scan_ip']   = $_POST['igf_server_scan_ip'][$server_update_count];
            }
            if(isset($_POST['igf_server_heartbeat_ip']) && isset($_POST['igf_server_heartbeat_ip'][$server_update_count])) {
              $data_igf_eqpt['igf_server_heartbeat_ip']   = $_POST['igf_server_heartbeat_ip'][$server_update_count];
            }
            if(isset($_POST['igf_server_cluster_ic_ip']) && isset($_POST['igf_server_cluster_ic_ip'][$server_update_count])) {
              $data_igf_eqpt['igf_server_cluster_ic_ip']   = $_POST['igf_server_cluster_ic_ip'][$server_update_count];
            }
            if(isset($_POST['igf_server_oracle_vip']) && isset($_POST['igf_server_oracle_vip'][$server_update_count])) {
              $data_igf_eqpt['igf_server_oracle_vip']   = $_POST['igf_server_oracle_vip'][$server_update_count];
            }

        }// END SWITCH

        $igf_eqpt_id = igf_server_save($data_igf_eqpt, $server['igf_server_id']);

        $server_update_count++;
      }


      if(!isset($pei_messages['error'])) {
        $pei_messages['success'][] = 'IGF updated successfully.';

        $email_env_str = get_req_env_string($req_id);
        $email_loc_str = get_req_loc_string($req_id);

        // Send Email Notification
        $email_template = get_email_template_by_name('REQUEST RFI');
        //$email_recipient= $email_template['igf_email_recipient'];
        $email_recipient= 'chandrashekhar.thalkar@ril.com';
        $email_subject  = $email_template['mail_template_subject'];
        $email_subject  = 'IGF UPDATED - '.$req_id.'-'.$email_env_str.'-'.$req_name;
        $email_message  = $email_template['mail_template_message_text'];
        $email_from     = $email_template['mail_template_from_mail'];
        $email_phy = count_igf_server_count($req_id, '3');
        $email_vir = count_igf_server_count($req_id, '4');
        $search_variables = array(
                              '{REQ_ID}',
                              '{REQ_ENV}',
                              '{REQ_NAME}',
                              '{REQ_LOC}',
                              '{REQ_PHYSICAL_SERVERS}',
                              '{REQ_VIRTUAL_SERVERS}'
                            );
        $replace_values = array($req_id, $email_env_str, $req_name, $email_loc_str, $email_phy, $email_vir);
        $email_message  = str_replace($search_variables, $replace_values, $email_message);
        $email_headers  = 'From: '.$email_from;

        if($email_template) {
          $mail_test = mail($email_recipient,$email_subject,$email_message,$email_headers,'-f'.$email_from);
        }

      }
    }

  }

  $igf_equi_key = array(
                    array('key' => 'igf_eqpt_loc', 'name' => 'LOCATION', 'width' => '150'),
                    array('key' => 'igf_eqpt_type', 'name' => 'EQUIPMENT TYPE'),
                    array('key' => 'igf_eqpt_make', 'name' => 'MAKE'),
                    array('key' => 'igf_eqpt_model', 'name' => 'MODEL'),
                    array('key' => 'igf_eqpt_rack_u', 'name' => 'RACK SPACE (RU)'),
                    array('key' => 'igf_eqpt_if', 'name' => 'NO OF INTERFACES'),
                    array('key' => 'igf_eqpt_if_speed', 'name' => 'INTERFACE SPEED'),
                    array('key' => 'igf_eqpt_if_type', 'name' => 'INTERFACE CONNECTIVITY TYPE (COPPER/ FIBER)'),
                    array('key' => 'igf_eqpt_power_supply_type', 'name' => 'TYPE OF POWER SUPPLY (AC / DC)'),
                    array('key' => 'igf_eqpt_power_supply_no', 'name' => '# OF POWER SUPPLIES'),
                    array('key' => 'igf_eqpt_power_supply_connector', 'name' => 'POWER SUPPLY CONNECTOR TYPE'),
                  );

  $igf_sw_key = array(
                    array('key' => 'igf_sw_sr_no', 'name' => 'SR. NO.', 'width' => '150'),
                    array('key' => 'igf_sw_category', 'name' => 'SOFTWARE CATEGORY (APPLICATION / WEB / DB)'),
                    array('key' => 'igf_sw_vendor_name', 'name' => 'VENDOR NAME'),
                    array('key' => 'igf_sw_product_name', 'name' => 'PRODUCT NAME'),
                    array('key' => 'igf_sw_edition', 'name' => 'SOFTWARE EDITION'),
                    array('key' => 'igf_sw_version', 'name' => 'SOFTWARE VERSION'),
                    array('key' => 'igf_sw_base_os', 'name' => 'BASE OS'),
                    array('key' => 'igf_sw_licence_type', 'name' => 'LICENSING TYPE (BASED ON USERS / CPU / CORE/ ANY OTHER)'),
                    array('key' => 'igf_sw_licence_count', 'name' => '# OF LICENCES REQUIRED'),
                    array('key' => 'igf_sw_support', 'name' => 'SOFTWARE SUPPORT REQUIRED (YES / NO)'),
                  );

  $igf_patching_key = array(
                    array('key' => 'igf_patching_type', 'name' => 'Type'),
                    array('key' => 'igf_patching_sh', 'name' => 'Server Hall'),
                    array('key' => 'igf_patching_cable', 'name' => 'Cable Type'),
                    array('key' => 'igf_patching_cable_length', 'name' => 'Length'),
                    array('key' => 'igf_patching_src_rack', 'name' => 'Source Rack'),
                    array('key' => 'igf_patching_src_u', 'name' => 'Source U Location'),
                    array('key' => 'igf_patching_src_sr', 'name' => 'Source Server serial No'),
                    array('key' => 'igf_patching_src_port', 'name' => 'Source System/Port'),
                    array('key' => 'igf_patching_src_label', 'name' => 'Source LABEL(Filled by Sigma-Byte)'),
                    array('key' => 'igf_patching_dst_rack', 'name' => 'Destination Rack'),
                    array('key' => 'igf_patching_dst_sr_u', 'name' => 'Destination Serial No or U'),
                    array('key' => 'igf_patching_dst_port', 'name' => 'Destination System /Port'),
                    array('key' => 'igf_patching_dst_label', 'name' => 'Destination LABEL'),
                    array('key' => 'igf_patching_qty', 'name' => 'Qty'),
                    array('key' => 'igf_patching_vlan', 'name' => 'VLAN', 'optional' => TRUE),
                    array('key' => 'igf_patching_remark', 'name' => 'Remarks'),
                  );

  $igf = get_igf_detail_by_igf_id($igf_id);

  // Get Contact SPOC
  $igf_contact_spoc = igf_request_igf_contact($req_id, 2);

  // Get Contact HOD
  $igf_contact_hod = igf_request_igf_contact($req_id, 3);

  // Get Contact 1 OPERATIONS
  $igf_contact_ops_1  = igf_request_igf_contact($req_id, 4);

  // Get Contact 2 OPERATIONS
  $igf_contact_ops_2  = igf_request_igf_contact($req_id, 5);

  // Get Budget
  $igf_budget  = get_igf_budget($igf_id);

  // Get Equipment Details
  $igf_equipment  = get_igf_equipment($igf_id);

  // Get Software Details
  $igf_software   = get_igf_software($igf_id);

  // Get Patching Details
  $igf_patching   = get_igf_patching($igf_id);

  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php">User Requests</a></li>
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php">View/Track Request Detail</a></li>
        <li class="active">IGF Edit</li>
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
    <?php if($igf_request){
    ?>
    <h4> #<?php echo $igf_request['req_id'];?> <small><?php echo $igf_request['req_title'];?></small></h4>
    <?php
    }
    ?>

    <form enctype="multipart/form-data" action="" method="POST">

      <ul class="nav nav-tabs" role="tablist" id="myTab">
        <li role="presentation"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">CONTACT & BUDGET INFORMATION</a></li>
        <li role="presentation" class="active"><a href="#server" aria-controls="server" role="tab" data-toggle="tab"><?php echo $igf_header_3;?></a></li>
        <li role="presentation"><a href="#equipment" aria-controls="equipment" role="tab" data-toggle="tab">EQUIPMENT LIST</a></li>
        <li role="presentation"><a href="#software" aria-controls="software" role="tab" data-toggle="tab">SOFTWARE LIST</a></li>
        <li role="presentation"><a href="#patching" aria-controls="patching" role="tab" data-toggle="tab">PATCHING</a></li>
        <div style="float:right;">
          <button type="submit" name="igf_save" id="igf-save-top" value="Save" class="btn btn-primary btn-label-left">Save</button>
        </div>
      </ul>

      <div class="tab-content">
        <div role="tabpanel" class="tab-pane" id="contact">
          <table class="table table-bordered table-hover table-request-list">
            <tr>
            <th colspan="3" align="center" style="text-align:center;">CONTACT DETAILS</th>
            </tr>
            <tr>
            <td colspan="3" >&nbsp</td>
            </tr>
            <tr>
            <th width="20%">REQUESTOR GROUP</th>
            <td colspan="2" align="left"><?php echo $igf['req_group_name'];?></td>
            </tr>
            <tr>
            <th>REQUESTOR SUB-GROUP</th><td colspan="2" align="left"><?php echo $igf['req_sub_group_name'];?></td>
            </tr>
            <tr>
            <th>PROJECT / SETUP NAME</th><td colspan="2" align="left"><?php echo $igf['req_title'];?></td>
            </tr>
            <tr>
            <td colspan="3" >&nbsp</td>
            </tr>
            <tr>
            <th colspan="3" align="center" style="text-align:center;">CONTACT DETAILS - PROJECT (SPOC)</th>
            </tr>
            <tr>
              <td width="20%">&nbsp</td>
              <th width="40%" style="text-align:center;"><?php echo $igf_contact_title_1;?></th>
              <th width="40%" style="text-align:center;"><?php echo $igf_contact_title_2;?></th>
            </tr>
            <tr>
              <th>CONTACT (NAME)</th>
              <td align="left"><?php echo $igf_contact_spoc['igf_contact_name'];?></td>
              <td align="left"><?php echo $igf_contact_hod['igf_contact_name'];?></td>
            </tr>
            <tr>
              <th>CONTACT (MOBILE)</th>
              <td align="left"><?php echo $igf_contact_spoc['igf_contact_mobile'];?></td>
              <td align="left"><?php echo $igf_contact_hod['igf_contact_mobile'];?></td>
            </tr>
            <tr>
              <th>CONTACT (EMAIL)</th>
              <td align="left"><?php echo $igf_contact_spoc['igf_contact_email'];?></td>
              <td align="left"><?php echo $igf_contact_hod['igf_contact_email'];?></td>
            </tr>
            <tr>
            <td colspan="3" >&nbsp</td>
            </tr>
            <tr>
            <th colspan="3" align="center" style="text-align:center;">CONTACT DETAILS - OPERATIONS (APPLICATION OWNER / SUPPORT TEAM)</th>
            </tr>
            <tr>
              <td>&nbsp</td>
              <th style="text-align:center;">CONTACT 1</th>
              <th style="text-align:center;">CONTACT 2</th>
            </tr>
            <tr>
              <th>CONTACT (NAME)</th>
              <td align="left"><?php echo $igf_contact_ops_1['igf_contact_name'];?></td>
              <td align="left"><?php echo $igf_contact_ops_2['igf_contact_name'];?></td>
            </tr>
            <tr>
              <th>CONTACT (MOBILE)</th>
              <td align="left"><?php echo $igf_contact_ops_1['igf_contact_mobile'];?></td>
              <td align="left"><?php echo $igf_contact_ops_2['igf_contact_mobile'];?></td>
            </tr>
            <tr>
              <th>CONTACT (EMAIL)</th>
              <td align="left"><?php echo $igf_contact_ops_1['igf_contact_email'];?></td>
              <td align="left"><?php echo $igf_contact_ops_2['igf_contact_email'];?></td>
            </tr>
            <tr>
            <tr>
            <td colspan="3" >&nbsp</td>
            </tr>
            <th colspan="3" align="center" style="text-align:center;">BUDGET DETAILS (IF APPLICABLE)</th>
            </tr>
            <tr>
              <th>FUND CENTRE</th>
              <td align="left" colspan="2"><?php echo $igf_budget['igf_budget_fund_center'];?></td>
            </tr>
            <tr>
              <th>GL</th>
              <td align="left" colspan="2"><?php echo $igf_budget['igf_budget_gl'];?></td>
            </tr>
            <tr>
              <th>WBS</th>
              <td align="left" colspan="2"><?php echo $igf_budget['igf_budget_wbs'];?></td>
            </tr>
          </table>
        </div>
        <div role="tabpanel" class="tab-pane active" id="server">
          <table class="table-igf-server table-bordered table-hover">
            <tr>
                <th width="150px">APPLICATION NAME</th>
                <th width="150px">REQUESTOR GROUP</th>
                <th width="150px">REQUESTOR SUB-GROUP</th>
                <th width="150px">ENVIRONMENT</th>
                <th width="180px">LOCATION</th>
                <th width="180px">SERVER HALL</th>
                <th width="180px">ROW-RACK</th>
                <th width="180px">RACK NAME</th>
                <th width="180px">RACK "U"</th>
                <th width="180px">SLOT NO.</th>
                <th width="180px">SERVER NUMBER</th>
                <th width="150px">SERVER TYPE</th>
                <th width="150px">HYPERVISOR</th>
                <th width="150px">SERVER ROLE</th>
                <th width="150px">SERVER SERIAL NUMBER</th>
                <th width="150px">SERVER MAKE</th>
                <th width="150px">SERVER MODEL</th>
                <th width="150px">CPU TYPE</th>
                <th width="150px"># of CPU / vCPU</th>
                <th width="150px">TOTAL # of COREs</th>
                <th width="150px">RAM (GB)</th>
                <th width="150px"># of INTERNAL HDDs</th>
                <th width="150px">SIZE -INTERNAL DISKS</th>
                <th width="300px">RAID CONFIG - INTERNAL DISKS (RAID 1 / RAID 5 / RAID 1+0)</th>
                <th width="150px"># of NICs - 1G</th>
                <th width="150px"># of NICs - 10G</th>
                <th width="150px"># of FC HBA CARDS</th>
                <th width="150px"># of FC HBA PORTS</th>
                <th width="150px">FC HBA PORT SPEED</th>
                <th width="150px"># of DATA LAN PORTS</th>
                <th width="150px">DATA LAN INTERFACE TYPE</th>
                <th width="150px">DATA LAN INTERFACE SPEED</th>
                <th width="150px"># of SERVER LAN PORTS</th>
                <th width="150px">SERVER LAN INTERFACE TYPE</th>
                <th width="150px">SERVER LAN INTERFACE SPEED</th>
                <th width="150px"># of CLUSTER LAN PORTS</th>
                <th width="150px">CLUSTER LAN INTERFACE TYPE</th>
                <th width="150px">CLUSTER LAN INTERFACE SPEED</th>
                <th width="150px">NETWORK ZONE</th>
                <th width="150px">NETWORK SUB ZONE</th>
                <th width="150px">LOAD BALANCER REQUIRED</th>
                <th width="150px">HA / CLUSTER</th>
                <th width="150px">HA TYPE / CLUSTER SOFTWARE</th>
                <th width="150px">HA / CLUSTER PAIR NUMBER</th>
                <th width="150px">OS</th>
                <th width="150px">OS VERSION</th>
                <th width="150px">DB</th>
                <th width="150px">DB VERSION</th>
                <th width="150px">EXTERNAL STORAGE TYPE</th>
                <th width="150px">STORAGE IOPS</th>
                <th width="150px">STORAGE ARRAY</th>
                <th width="150px">EXTERNAL STORAGE RAID CONFIG</th>
                <th width="150px">EXTERNAL STORAGE  USABLE SPACE- P-VOL (in GB)</th>
                <th width="150px">S-VOL (BCV) REQUIRED</th>
                <th width="150px">EXTERNAL STORAGE  USABLE SPACE- S-VOL (in GB)</th>
                <th width="150px">FILE SYSTEM DETAILS - INTERNAL HDD</th>
                <th width="150px">FILE SYSTEM DETAILS - EXTERNAL STORAGE</th>
                <th width="150px">VOLUME MANAGER</th>
                <th width="150px">KERNEL PARAMETERS</th>
                <th width="150px">ADDITIONAL PACKAGES</th>
                <th width="150px">USER ID : GROUP ID : HOME DIR</th>
                <th width="150px">IDC SUPPORT REQUIREMENT</th>
                <th width="150px">REMARKS / ADDITIONAL NOTES</th>
                <th width="150px">REMOVE - RAM</th>
                <th width="150px">REMOVE - HDD</th>
                <th width="150px">REMOVE - NIC</th>
                <th width="150px">REMOVE - FC HBA</th>
                <th width="150px">ADD - RAM</th>
                <th width="150px">ADD - HDD</th>
                <th width="150px">ADD - NIC</th>
                <th width="150px">ADD - FC HBA</th>
                <th width="150px">HOSTNAME</th>
                <th width="150px">CONSOLE IP (iLO / RSC)</th>
                <th width="150px">SUBNET MASK</th>
                <th width="150px">GATEWAY</th>
                <th width="150px">DATA IP 1</th>
                <th width="150px">DATA IP 2</th>
                <th width="150px">VIP</th>
                <th width="150px">SUBNET MASK</th>
                <th width="150px">GATEWAY</th>
                <th width="150px">LB IP</th>
<?php
  switch($igf_version){
    case 'v3':
    case 'v3.1':
      ?>
                <th width="150px">OTHER IP</th>
                <th width="150px">SM</th>
                <th width="150px">GW</th>
                <th width="150px">PUBLIC IP</th>
      <?php
      break;
    case 'v4':
    default:
?>
                <th width="150px">PUBLIC IP</th>
                <th width="150px">EQUIPMENT (PRIVATE) LAN IP</th>
                <th width="150px">EQUIPMENT (PRIVATE) SUBNET MASK</th>
                <th width="150px">RAC IP</th>
                <th width="150px">SCAN IP</th>
                <th width="150px">Heartbeat IP</th>
                <th width="150px">Cluster Interconnect PRIVATE IP</th>
                <th width="150px">Oracle IP</th>
<?php
  }
?>
            </tr>
      <?php
      if($igf_server) {
        $server_count = 0;
        foreach ($igf_server as $key => $server) {
          # code...
          /* <?php echo $server[''];?>  */
      ?>
            <tr>
                <td><!-- APPLICATION NAME --><?php echo $server['igf_server_app'];?></td>
                <td><!-- REQUESTOR GROUP --><?php echo $server['igf_server_req_group_name'];?></td>
                <td><!-- REQUESTOR SUB-GROUP --><?php echo $server['igf_server_req_sub_group_name'];?></td>
                <td><!-- ENVIRONMENT --><?php echo $server['igf_server_env'];?></td>
                <td><!-- LOCATION -->
                <select class="placeholder form-control dropdown-select2" name="igf_server_loc[]">
                  <option>---- Select Location ---</option>
      <?php
              foreach ($igf_locations as $location) {
      ?>
                  <option value="<?php echo $location['loc_id'];?>"
                  <?php
                  if(isset($_POST['igf_save']) ){
                    if($location['loc_id'] == $_POST['igf_server_loc'][$server_count]){ ?>selected="selected"<?php }
                  }
                  else {
                    if($location['loc_id'] == $server['igf_server_loc_id']){ ?>selected="selected"<?php }
                  }
                  ?> >
                  <?php echo $location['loc_name'];?>
                  </option>
      <?php
              }
      ?>
                </select>
                </td>
                <td><!-- SERVER HALL-->
                <select class="placeholder form-control dropdown-select2" name="igf_server_sh[]">
                  <option>---- Select Server Hall ---</option>
      <?php
              foreach ($igf_server_halls as $server_hall) {
      ?>
                  <option value="<?php echo $server_hall['sh_id'];?>"
                  <?php
                  if(isset($_POST['igf_save']) ){
                    if($server_hall['sh_id'] == $_POST['igf_server_sh'][$server_count]){ ?>selected="selected"<?php }
                  }
                  else {
                    if($server_hall['sh_id'] == $server['igf_server_server_hall_id']){ ?>selected="selected"<?php }
                  }
                  ?> >
                  <?php echo $server_hall['sh_name'];?>
                  </option>
      <?php
              }
      ?>
                </select>
                </td>
                <td><!-- ROW-RACK-->
                <select class="placeholder form-control dropdown-select2" name="igf_server_rr[]">
                  <option>---- Select Row Rack ---</option>
      <?php
              foreach ($igf_row_racks as $row_rack) {
      ?>
                  <option value="<?php echo $row_rack['rr_id'];?>"
                  <?php
                  if(isset($_POST['igf_save']) ){
                    if($row_rack['rr_id'] == $_POST['igf_server_rr'][$server_count]){ ?>selected="selected"<?php }
                  }
                  else {
                    if($row_rack['rr_id'] == $server['igf_server_row_rack_id']){ ?>selected="selected"<?php }
                  }
                  ?> >
                  <?php echo $row_rack['rr_name'];?>
                  </option>
      <?php
              }
      ?>
                </select>
                </td>
                <td><!-- RACK NAME-->
                <?php
                $igf_server_rack_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_rack_value =  $_POST['igf_server_rack_name'][$server_count];
                }
                else {
                  $igf_server_rack_value = $server['igf_server_rack_name'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_rack_name[]" value="<?php echo $igf_server_rack_value;?>">
                </td>
                <td><!-- RACK "U" -->
                <?php
                $igf_server_rack_u_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_rack_u_value =  $_POST['igf_server_rack_u'][$server_count];
                }
                else {
                  $igf_server_rack_u_value = $server['igf_server_rack_u'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_rack_u[]" value="<?php echo $igf_server_rack_u_value;?>">
                </td>
                <td><!-- SLOT NO. -->
                <?php
                $igf_server_slot_no_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_slot_no_value =  $_POST['igf_server_slot_no'][$server_count];
                }
                else {
                  $igf_server_slot_no_value = $server['igf_server_slot_no'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_slot_no[]" value="<?php echo $igf_server_slot_no_value;?>">
                </td>
                <td><!-- SERVER NUMBER --><?php echo $server['igf_server_number'];?></td>
                <td><!-- SERVER TYPE --><?php echo $server['igf_server_type'];?></td>
                <td><!-- HYPERVISOR --><?php echo $server['igf_server_hypervisor'];?></td>
                <td><!-- SERVER ROLE --><?php echo $server['igf_server_role'];?></td>
                <td><!-- SERVER SERIAL NUMBER -->
                <?php
                $igf_server_serial_number_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_serial_number_value =  $_POST['igf_server_serial_number'][$server_count];
                }
                else {
                  $igf_server_serial_number_value = $server['igf_server_serial_number'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_serial_number[]" value="<?php echo $igf_server_serial_number_value;?>">
                </td>
                <td><!-- SERVER MAKE --><?php echo $server['igf_server_make'];?></td>
                <td><!-- SERVER MODEL --><?php echo $server['igf_server_model'];?></td>
                <td><!-- CPU TYPE --><?php echo $server['igf_server_cpu_type'];?></td>
                <td><!-- # of CPU / vCPU --><?php echo $server['igf_server_cpu_no'];?></td>
                <td><!-- TOTAL # of COREs --><?php echo $server['igf_server_cpu_cores'];?></td>
                <td><!-- RAM (GB) --><?php echo $server['igf_server_ram'];?></td>
                <td><!-- # of INTERNAL HDDs --><?php echo $server['igf_server_storage_int_no'];?></td>
                <td><!-- SIZE -INTERNAL DISKS --><?php echo $server['igf_server_storage_int_size'];?></td>
                <td><!-- RAID CONFIG - INTERNAL DISKS (RAID 1 / RAID 5 / RAID 1+0) --><?php echo $server['igf_server_storage_int_raid_config'];?></td>
                <td><!-- # of NICs - 1G --><?php echo $server['igf_server_nic_1g'];?></td>
                <td><!-- # of NICs - 10G --><?php echo $server['igf_server_nic_10g'];?></td>
                <td><!-- # of FC HBA CARDS --><?php echo $server['igf_server_fc_hba_card'];?></td>
                <td><!-- # of FC HBA PORTS --><?php echo $server['igf_server_fc_hba_port'];?></td>
                <td><!-- FC HBA PORT SPEED --><?php echo $server['igf_server_fc_hba_port_speed'];?></td>
                <td><!-- # of DATA LAN PORTS--><?php echo $server['igf_server_dl_port'];?></td>
                <td><!-- DATA LAN INTERFACE TYPE --><?php echo $server['igf_server_dl_type'];?></td>
                <td><!-- DATA LAN INTERFACE SPEED --><?php echo $server['igf_server_dl_speed'];?></td>
                <td><!-- # of SERVER LAN PORTS --><?php echo $server['igf_server_sl_port'];?></td>
                <td><!-- SERVER LAN INTERFACE TYPE --><?php echo $server['igf_server_sl_type'];?></td>
                <td><!-- SERVER LAN INTERFACE SPEED --><?php echo $server['igf_server_sl_speed'];?></td>
                <td><!-- # of CLUSTER LAN PORTS --><?php echo $server['igf_server_cl_port'];?></td>
                <td><!-- CLUSTER LAN INTERFACE TYPE --><?php echo $server['igf_server_cl_type'];?></td>
                <td><!-- CLUSTER LAN INTERFACE SPEED --><?php echo $server['igf_server_cl_speed'];?></td>
                <td><!-- NETWORK ZONE --><?php echo $server['igf_server_network_zone'];?></td>
                <td><!-- NETWORK SUB ZONE --><?php echo $server['igf_server_network_sub_zone'];?></td>
                <td><!-- LOAD BALANCER REQUIRED --><?php echo $server['igf_server_load_balancer'];?></td>
                <td><!-- HA / CLUSTER --><?php echo $server['igf_server_ha_cluster'];?></td>
                <td><!-- HA TYPE / CLUSTER SOFTWARE --><?php echo $server['igf_server_ha_cluster_type'];?></td>
                <td><!-- HA / CLUSTER PAIR NUMBER --><?php echo $server['igf_server_ha_cluster_pair'];?></td>
                <td><!-- OS --><?php echo $server['igf_server_os'];?></td>
                <td><!-- OS VERSION --><?php echo $server['igf_server_db'];?></td>
                <td><!-- DB --><?php echo $server['igf_server_os_version'];?></td>
                <td><!-- DB VERSION --><?php echo $server['igf_server_db_version'];?></td>
                <td><!-- EXTERNAL STORAGE TYPE --><?php echo $server['igf_server_storage_ext_type'];?></td>
                <td><!-- STORAGE IOPS --><?php echo $server['igf_server_storage_ext_iops'];?></td>
                <td><!-- STORAGE ARRAY --><?php echo $server['igf_server_storage_ext_array'];?></td>
                <td><!-- EXTERNAL STORAGE RAID CONFIG --><?php echo $server['igf_server_storage_ext_raid_config'];?></td>
                <td><!-- EXTERNAL STORAGE  USABLE SPACE- P-VOL (in GB) --><?php echo $server['igf_server_storage_ext_p_vol_space'];?></td>
                <td><!-- S-VOL (BCV) REQUIRED --><?php echo $server['igf_server_storage_ext_s_vol'];?></td>
                <td><!-- EXTERNAL STORAGE  USABLE SPACE- S-VOL (in GB) --><?php echo $server['igf_server_storage_ext_s_vol_space'];?></td>
                <td><!-- FILE SYSTEM DETAILS - INTERNAL HDD -->
                  <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($server['igf_server_storage_int_fs'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_storage_int_fs']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                  <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_storage_int_fs'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
                </td>
                <td><!-- FILE SYSTEM DETAILS - EXTERNAL STORAGE -->
                  <?php echo $server['igf_server_storage_ext_fs'];?>
                  <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($server['igf_server_storage_ext_fs'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_storage_ext_fs']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                  <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_storage_ext_fs'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
                </td>
                <td><!-- VOLUME MANAGER --><?php echo $server['igf_server_volume_manager'];?></td>
                <td><!-- KERNEL PARAMETERS -->
                  <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($server['igf_server_kernel_parameter'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_kernel_parameter']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                  <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_kernel_parameter'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
                </td>
                <td><!-- ADDITIONAL PACKAGES -->
                  <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($server['igf_server_additional_package'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_additional_package']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                  <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_additional_package'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
                </td>
                <td><!-- USER ID : GROUP ID : HOME DIR --><?php echo $server['igf_server_user_id'];?></td>
                <td><!-- IDC SUPPORT REQUIREMENT --><?php echo $server['igf_server_idc_support'];?></td>
                <td><!-- REMARKS / ADDITIONAL NOTES -->
                  <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($server['igf_server_remark'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_remark']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                  <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_remark'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
                </td>
                <td><!-- REMOVE - RAM --><?php echo $server['igf_server_reconfig_rm_ram'];?></td>
                <td><!-- REMOVE - HDD --><?php echo $server['igf_server_reconfig_rm_hdd'];?></td>
                <td><!-- REMOVE - NIC --><?php echo $server['igf_server_reconfig_rm_nic'];?></td>
                <td><!-- REMOVE - FC HBA --><?php echo $server['igf_server_reconfig_rm_fc_hba'];?></td>
                <td><!-- ADD - RAM --><?php echo $server['igf_server_reconfig_add_ram'];?></td>
                <td><!-- ADD - HDD --><?php echo $server['igf_server_reconfig_add_hdd'];?></td>
                <td><!-- ADD - NIC --><?php echo $server['igf_server_reconfig_add_nic'];?></td>
                <td><!-- ADD - FC HBA --><?php echo $server['igf_server_reconfig_add_fc_hba'];?></td>
                <td><!-- HOSTNAME -->
                <?php
                $igf_server_hostname_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_hostname_value =  $_POST['igf_server_hostname'][$server_count];
                }
                else {
                  $igf_server_hostname_value = $server['igf_server_hostname'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_hostname[]" value="<?php echo $igf_server_hostname_value;?>">
                </td>
                <td><!-- CONSOLE IP (iLO / RSC) -->
                 <?php
                $igf_server_console_ip_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_console_ip_value =  $_POST['igf_server_console_ip'][$server_count];
                }
                else {
                  $igf_server_console_ip_value = $server['igf_server_console_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_console_ip[]" value="<?php echo $igf_server_console_ip_value;?>">
                </td>
                <td><!-- SUBNET MASK -->
                <?php
                $igf_server_console_ip_sm_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_console_ip_sm_value =  $_POST['igf_server_console_ip_sm'][$server_count];
                }
                else {
                  $igf_server_console_ip_sm_value = $server['igf_server_console_ip_sm'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_console_ip_sm[]" value="<?php echo $igf_server_console_ip_sm_value;?>">
                </td>
                <td><!-- GATEWAY -->
                 <?php
                $igf_server_console_ip_gw_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_console_ip_gw_value =  $_POST['igf_server_console_ip_gw'][$server_count];
                }
                else {
                  $igf_server_console_ip_gw_value = $server['igf_server_console_ip_gw'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_console_ip_gw[]" value="<?php echo $igf_server_console_ip_gw_value;?>">
                </td>
                <td><!-- DATA IP 1 -->
                 <?php
                $igf_server_data_ip_1_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_data_ip_1_value =  $_POST['igf_server_data_ip_1'][$server_count];
                }
                else {
                  $igf_server_data_ip_1_value = $server['igf_server_data_ip_1'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_data_ip_1[]" value="<?php echo $igf_server_data_ip_1_value;?>">
                </td>
                <td><!-- DATA IP 2 -->
                 <?php
                $igf_server_data_ip_2_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_data_ip_2_value =  $_POST['igf_server_data_ip_2'][$server_count];
                }
                else {
                  $igf_server_data_ip_2_value = $server['igf_server_data_ip_2'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_data_ip_2[]" value="<?php echo $igf_server_data_ip_2_value;?>">
                </td>
                <td><!-- VIP -->
                 <?php
                $igf_server_vip_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_vip_value =  $_POST['igf_server_vip'][$server_count];
                }
                else {
                  $igf_server_vip_value = $server['igf_server_vip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_vip[]" value="<?php echo $igf_server_vip_value;?>">
                </td>
                <td><!-- SUBNET MASK -->
                <?php
                $igf_server_data_ip_sm_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_data_ip_sm_value =  $_POST['igf_server_data_ip_sm'][$server_count];
                }
                else {
                  $igf_server_data_ip_sm_value = $server['igf_server_data_ip_sm'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_data_ip_sm[]" value="<?php echo $igf_server_data_ip_sm_value;?>">
                </td>
                <td><!-- GATEWAY -->
                <?php
                $igf_server_data_ip_gw_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_data_ip_gw_value =  $_POST['igf_server_data_ip_gw'][$server_count];
                }
                else {
                  $igf_server_data_ip_gw_value = $server['igf_server_data_ip_gw'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_data_ip_gw[]" value="<?php echo $igf_server_data_ip_gw_value;?>">
                </td>
                <td><!-- LB IP -->
                <?php
                $igf_server_lb_ip_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_lb_ip_value =  $_POST['igf_server_lb_ip'][$server_count];
                }
                else {
                  $igf_server_lb_ip_value = $server['igf_server_lb_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_lb_ip[]" value="<?php echo $igf_server_lb_ip_value;?>">
                </td>
<?php
  switch($igf_version){
    case 'v3':
    case 'v3.1':
?>
                <td><!-- OTHER IP -->
                <?php
                $igf_server_other_ip_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_other_ip_value =  $_POST['igf_server_other_ip'][$server_count];
                }
                else {
                  $igf_server_other_ip_value = $server['igf_server_other_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_other_ip[]" value="<?php echo $igf_server_other_ip_value;?>">
                </td>
                <td><!-- SM -->
                <?php
                $igf_server_other_ip_sm_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_other_ip_sm_value =  $_POST['igf_server_other_ip_sm'][$server_count];
                }
                else {
                  $igf_server_other_ip_sm_value = $server['igf_server_other_ip_sm'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_other_ip_sm[]" value="<?php echo $igf_server_other_ip_sm_value;?>">
                </td>
                <td><!-- GW -->
                <?php
                $igf_server_other_ip_gw_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_other_ip_gw_value =  $_POST['igf_server_other_ip_gw'][$server_count];
                }
                else {
                  $igf_server_other_ip_gw_value = $server['igf_server_other_ip_gw'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_other_ip_gw[]" value="<?php echo $igf_server_other_ip_gw_value;?>">
                </td>
                <td><!-- PUBLIC IP v3.1 -->
                <?php
                $igf_server_public_ip_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_public_ip_value =  $_POST['igf_server_public_ip'][$server_count];
                }
                else {
                  $igf_server_public_ip_value = $server['igf_server_public_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_public_ip[]" value="<?php echo $igf_server_public_ip_value;?>">
                </td>
<?php
      break;
    case 'v4':
    default:
?>
                <td><!-- PUBLIC IP v4 -->
                <?php
                $igf_server_public_ip_value = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_public_ip_value =  $_POST['igf_server_public_ip'][$server_count];
                }
                else {
                  $igf_server_public_ip_value = $server['igf_server_public_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_public_ip[]" value="<?php echo $igf_server_public_ip_value;?>">
                </td>
                <td><!-- EQUIPMENT (PRIVATE) LAN IP v4 -->
                <?php
                $igf_server_private_lan_ip = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_private_lan_ip =  $_POST['igf_server_private_lan_ip'][$server_count];
                }
                else {
                  $igf_server_private_lan_ip = $server['igf_server_private_lan_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_private_lan_ip[]" value="<?php echo $igf_server_private_lan_ip;?>">
                </td>
                <td><!-- EQUIPMENT (PRIVATE) SUBNET MASK v4 -->
                <?php
                $igf_server_private_lan_sm = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_private_lan_sm =  $_POST['igf_server_private_lan_sm'][$server_count];
                }
                else {
                  $igf_server_private_lan_sm = $server['igf_server_private_lan_sm'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_private_lan_sm[]" value="<?php echo $igf_server_private_lan_sm;?>">
                </td>
                <td><!-- RAC IP v4 -->
                <?php
                $igf_server_rac_ip = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_rac_ip =  $_POST['igf_server_rac_ip'][$server_count];
                }
                else {
                  $igf_server_rac_ip = $server['igf_server_rac_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_rac_ip[]" value="<?php echo $igf_server_rac_ip;?>">
                </td>
                <td><!-- SCAN IP v4 -->
                <?php
                $igf_server_scan_ip = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_scan_ip =  $_POST['igf_server_scan_ip'][$server_count];
                }
                else {
                  $igf_server_scan_ip = $server['igf_server_scan_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_scan_ip[]" value="<?php echo $igf_server_scan_ip;?>">
                </td>
                <td><!-- Heartbeat IP v4 -->
                <?php
                $igf_server_heartbeat_ip = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_heartbeat_ip =  $_POST['igf_server_heartbeat_ip'][$server_count];
                }
                else {
                  $igf_server_heartbeat_ip = $server['igf_server_heartbeat_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_heartbeat_ip[]" value="<?php echo $igf_server_heartbeat_ip;?>">
                </td>
                <td><!-- Cluster Interconnect PRIVATE IP v4 -->
                <?php
                $igf_server_cluster_ic_ip = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_cluster_ic_ip =  $_POST['igf_server_cluster_ic_ip'][$server_count];
                }
                else {
                  $igf_server_cluster_ic_ip = $server['igf_server_cluster_ic_ip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_cluster_ic_ip[]" value="<?php echo $igf_server_cluster_ic_ip;?>">
                </td>
                <td><!-- Oracle IP v4 -->
                <?php
                $igf_server_oracle_vip = '';
                if(isset($_POST['igf_save']) ){
                  $igf_server_oracle_vip =  $_POST['igf_server_oracle_vip'][$server_count];
                }
                else {
                  $igf_server_oracle_vip = $server['igf_server_oracle_vip'];
                }
                ?>
                <input type="text" class="form-control" name="igf_server_oracle_vip[]" value="<?php echo $igf_server_oracle_vip;?>">
                </td>
<?php
  }
?>

            </tr>
      <?php
        // Increment server count
        $server_count++;
        }
      }
      ?>
          </table>
        </div>
        <div role="tabpanel" class="tab-pane" id="equipment">
          <?php
          if($igf_equipment) {
          ?>
            <table class="table table-bordered table-hover table-request-list">
              <tr>
          <?php
            foreach ($igf_equi_key as $key => $value) {
              # code...
          ?>
              <th><?php echo $value['name'];?></th>
          <?php
            }
          ?>
              </tr>
          <?php
            foreach ($igf_equipment as $equipment) {
              # code...
          ?>
              <tr>
          <?php
              foreach ($igf_equi_key as $key => $value) {
                # code...
            ?>
                <td><?php echo $equipment[$value['key']];?></td>
            <?php
              }
            ?>
              </tr>
          <?php
            }
          ?>
            </table>
          <?php
          }
          ?>

          </div>

        <div role="tabpanel" class="tab-pane" id="software">
            <?php

if($igf_software) {
?>
  <table class="table table-bordered table-hover table-request-list">
    <tr>
<?php

  foreach ($igf_sw_key as $key => $value) {
    # code...
?>
    <th width="<?php echo isset($value['width']) ? $value['width'] : '150';?>px"><?php echo $value['name'];?></th>
<?php
  }
?>
    </tr>
<?php
  foreach ($igf_software as $key => $software) {
    # code...
?>
  <tr>
<?php
    foreach ($igf_sw_key as $key => $value) {

?>
    <td><?php echo $software[$value['key']];?></td>
<?php
    }
?>
  </tr>
<?php
  }
?>
  </table>
<?php
}
?>

        </div>

        <div role="tabpanel" class="tab-pane" id="patching">
            <?php

if($igf_patching) {
?>
  <table class="table table-bordered table-hover table-request-list">
    <tr>
<?php

  foreach ($igf_patching_key as $key => $value) {
    # code...
?>
    <th width="<?php echo isset($value['width']) ? $value['width'] : '150';?>px"><?php echo $value['name'];?></th>
<?php
  }
?>
    </tr>
<?php
  foreach ($igf_patching as $patching) {
    # code...
?>
  <tr>
<?php
    foreach ($igf_patching_key as $value) {

?>
    <td><?php echo $patching[$value['key']];?></td>
<?php
    }
?>
  </tr>
<?php
  }
?>
  </table>
<?php
}
?>

        </div>
      </div>

      <button type="submit" name="igf_save" id="igf-save" value="Save" class="btn btn-primary btn-label-left">Save</button>

      </form>

<?php
} // END else ($pei_page_access)
?>
      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
  require_once(__dir__.'/../footer.php');
?>
<script>
  $(function () {
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

    $('#myTab a[href="#server"]').tab('show');

    $('.dropdown-select2').select2();
  })

</script>
