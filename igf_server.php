<?php
  session_start();
  // load up config file

  $pei_current_module = 'PROJECTS';

  require_once(__dir__.'/../header.php');

  /** Include PHPExcel */
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel/IOFactory.php');
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel.php');

  // load PHPMailer library
  require_once($pei_config['paths']['vendors'].'/PHPMailer/PHPMailerAutoload.php');

 ini_set('max_execution_time', 600);

  //error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';

//var_dump($_POST);
//var_dump($_POST['igf_server_checked']);

  // Initialize variables
  $req_msg          = '';
  $req_msg_success  = '';
  $uname            = $_SESSION['usr'];
  $igf_server_id    = $_GET['id'];
  $igf              = '';
  $igf_id           = '';
  $igf_request      = '';
  $req_id           = '';
  $igf_contact_spoc = '';
  $igf_contact_hod  = '';
  $igf_contact_ops_1= '';
  $igf_contact_ops_2= '';
  $igf_budget       = '';
  $igf_server       = '';
  $igf_equipment    = '';
  $igf_software     = '';

  $sync_succsess    = FALSE;
  $mail_released    = FALSE;
  $mail_attach_path_tmp = '';

  // Status ID released
  $status_id_rfi = 2;
  $status_id = 3;


  $released_flag = FALSE;
  $released_type = '';

  $igf_equi_key = array(
                    array('key' => 'igf_eqpt_loc', 'name' => 'LOCATION', 'width' => '150' ),
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
                    array('key' => 'igf_sw_sr_no', 'name' => 'SR. NO.', 'width' => '100'),
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


  // Get IGF Server Details from this IGF SERVER ID
  $igf_server = get_igf_server_detail($igf_server_id);
  if($igf_server){
    $igf_id = $igf_server['igf_id'];
  }

  // Get Request Details from this igf
  $igf_request      = get_request_detail_from_igf_id($igf_id);
  if($igf_request){
    $req_id = $igf_request['req_id'];
  }
  // Get IGF details
  $igf = get_igf_detail_by_igf_id($igf_id);


  //var_dump($igf);
  // Get Contact SPOC
  $sql_contact_spoc   = "SELECT *
                        FROM idc_igf_contact
                        WHERE igf_id='".mysql_real_escape_string($igf_id)."'
                        AND igf_contact_type_id='2' LIMIT 0,1;";
  $res_contact_spoc   = mysql_query($sql_contact_spoc, $pei_conn);
  if($res_contact_spoc){
    $igf_contact_spoc = mysql_fetch_array($res_contact_spoc);
  }

  // Get Contact HOD
  $sql_contact_hod  = "SELECT *
                        FROM idc_igf_contact
                        WHERE igf_id='".mysql_real_escape_string($igf_id)."'
                        AND igf_contact_type_id='3' LIMIT 0,1;";
  $res_contact_hod  = mysql_query($sql_contact_hod);
  if($res_contact_hod){
    $igf_contact_hod = mysql_fetch_array($res_contact_hod);
  }

  // Get Contact 1 OPERATIONS
  $sql_contact_ops_1  = "SELECT *
                        FROM idc_igf_contact
                        WHERE igf_id='".mysql_real_escape_string($igf_id)."'
                        AND igf_contact_type_id='4' LIMIT 0,1;";
  //echo $sql_contact_ops_1.'<br />';
  $res_contact_ops_1  = mysql_query($sql_contact_ops_1, $pei_conn);
  if($res_contact_ops_1){
    $igf_contact_ops_1 = mysql_fetch_array($res_contact_ops_1);
  }

  // Get Contact 2 OPERATIONS
  $sql_contact_ops_2  = "SELECT *
                        FROM idc_igf_contact
                        WHERE igf_id='".mysql_real_escape_string($igf_id)."'
                        AND igf_contact_type_id='5' LIMIT 0,1;";
  $res_contact_ops_2  = mysql_query($sql_contact_ops_2, $pei_conn);
  if($res_contact_ops_2){
    $igf_contact_ops_2 = mysql_fetch_array($res_contact_ops_2);
  }

  // Get Budget
  $igf_budget     = get_igf_budget($igf_id);



  // Get Equipment Details
  $igf_equipment  = get_igf_equipment($igf_id);
  //echo '<pre>';
  //var_dump($igf_equipment);
  //echo '</pre>';

  // Get Software Details
  $igf_software   = get_igf_software($igf_id);

  // Get Patching Details
  $igf_patching   = get_igf_patching($igf_id);

?>
    <div class="container">

      <div class="box-content">
        <!-- breadcrumb -->
        <ol class="breadcrumb">
          <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php">User Requests</a></li>
          <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php">View/Track Request Detail</a></li>
          <li class="active">IGF View</li>
        </ol>
        <!-- /breadcrumb  -->
<?php
  if($req_msg_success) {
?>
      <div class="alert alert-success" role="alert">
      <?php echo preg_replace("/\\\\n/", "<br />", $req_msg_success);?>
      </div>
<?php
  }
?>
<?php
  if($req_msg) {
?>
      <div class="alert alert-danger" role="alert">
      <?php echo preg_replace("/\\\\n/", "<br />", $req_msg);?>
      </div>
<?php
  }
?>
    <?php if($igf_request) {
      ?>
      <h5> #<?php echo $req_id;?> <small><?php echo strtoupper($igf_request['req_title']);?></small></h5>
      <?php
      }
      ?>
      <form enctype="multipart/form-data" action="" method="POST">
        <ul class="nav nav-tabs" role="tablist" id="myTab">
        <li role="presentation" class="active"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">CONTACT & BUDGET INFORMATION</a></li>
        <li role="presentation"><a href="#server" aria-controls="server" role="tab" data-toggle="tab">SERVER DETAILS</a></li>
        <li role="presentation"><a href="#equipment" aria-controls="equipment" role="tab" data-toggle="tab">EQUIPMENT LIST</a></li>
        <li role="presentation"><a href="#software" aria-controls="software" role="tab" data-toggle="tab">SOFTWARE LIST</a></li>
        <!--
        <li role="presentation"><a href="#patching" aria-controls="patching" role="tab" data-toggle="tab">PATCHING</a></li>
        -->
        <div style="float:right;">
          <a class="btn btn-primary" href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_download.php?igf_id=<?php echo $igf_id;?>" role="button">Download</a>
        </div>
      </ul>

      <div class="tab-content">
        <div role="tabpanel" class="tab-pane active" id="contact">
          <table class="table table-bordered table-hover table-request-list">
            <tr>
            <th colspan="3" align="center" style="text-align:center;">CONTACT DETAILS</th>
            </tr>
            <tr>
            <td colspan="3" >&nbsp</td>
            </tr>
            <tr>
            <th>REQUESTOR GROUP</th><td colspan="2" align="left"><?php echo strtoupper($igf['req_group_name']);?></td>
            </tr>
            <tr>
            <th>REQUESTOR SUB-GROUP</th><td colspan="2" align="left"><?php echo strtoupper($igf['req_sub_group_name']);?></td>
            </tr>
            <tr>
            <th>PROJECT / SETUP NAME</th><td colspan="2" align="left"><?php echo strtoupper($igf['req_title']);?></td>
            </tr>
            <tr>
            <td colspan="3" >&nbsp</td>
            </tr>
            <tr>
            <th colspan="3" align="center" style="text-align:center;">CONTACT DETAILS - PROJECT (SPOC)</th>
            </tr>
            <tr>
              <td>&nbsp</td>
              <th align="left">CONTACT SPOC</th>
              <th align="left">HOD</th>
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
              <th align="left">CONTACT 1</th>
              <th align="left">CONTACT 2</th>
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
        <div role="tabpanel" class="tab-pane" id="server">
          <table class="table-igf-server table-bordered table-hover">
            <tr>
              <th width="60px">RELEASE</th>
              <th width="150px">APPLICATION NAME</th>
              <th width="150px">REQUESTOR GROUP</th>
              <th width="150px">REQUESTOR SUB-GROUP</th>
              <th width="150px">ENVIRONMENT</th>
              <th width="150px">LOCATION</th>
              <th width="150px">SERVER HALL</th>
              <th width="150px">ROW-RACK</th>
              <th width="150px">RACK NAME</th>
              <th width="150px">RACK "U"</th>
              <th width="150px">SLOT NO.</th>
              <th width="150px">SERVER NUMBER</th>
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
              <th width="150px">OTHER IP</th>
              <th width="150px">SM</th>
              <th width="150px">GW</th>
              <th width="150px">PUBLIC IP</th>
              <th width="250px">CLUSTER SPECIFIC INFORMATION / MISCELLANEOUS</th>
            </tr>
      <?php
      if($igf_server) {
        $server = $igf_server;
      ?>
            <tr>
              <td><!-- --><input type="checkbox" name="igf_server_checked[]" value="<?php echo $server['igf_server_id'];?>" <?php if($server['igf_server_checked'] == '1') {?> checked <?php } ?> <?php if($server['igf_server_release_at'] != NULL) { ?> disabled <?php } ?> ></td>
              <td><!-- APPLICATION NAME --><?php echo $server['igf_server_app'];?></td>
              <td><!-- REQUESTOR GROUP --><?php echo $server['igf_server_req_group_name'];?></td>
              <td><!-- REQUESTOR SUB-GROUP --><?php echo $server['igf_server_req_sub_group_name'];?></td>
              <td><!-- ENVIRONMENT --><?php echo $server['igf_server_env'];?></td>
              <td><!-- LOCATION --><?php echo $server['igf_server_loc'];?></td>
              <td><!-- SERVER HALL--><?php echo $server['igf_server_server_hall'];?></td>
              <td><!-- ROW-RACK--><?php echo $server['igf_server_row_rack'];?></td>
              <td><!-- RACK NAME--><?php echo $server['igf_server_rack_name'];?></td>
              <td><!-- RACK "U" --><?php echo $server['igf_server_rack_u'];?></td>
              <td><!-- SLOT NO. --><?php echo $server['igf_server_slot_no'];?></td>
              <td><!-- SERVER NUMBER --><?php echo $server['igf_server_number'];?></td>
              <td><!-- SERVER TYPE --><?php echo $server['igf_server_type'];?></td>
              <td><!-- HYPERVISOR --><?php echo $server['igf_server_hypervisor'];?></td>
              <td><!-- SERVER ROLE --><?php echo $server['igf_server_role'];?></td>
              <td><!-- SERVER SERIAL NUMBER --><?php echo $server['igf_server_serial_number'];?></td>
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
              <td><!-- FILE SYSTEM DETAILS - INTERNAL HDD --><?php echo $server['igf_server_storage_int_fs'];?></td>
              <td><!-- FILE SYSTEM DETAILS - EXTERNAL STORAGE --><?php echo $server['igf_server_storage_ext_fs'];?></td>
              <td><!-- VOLUME MANAGER --><?php echo $server['igf_server_volume_manager'];?></td>
              <td><!-- KERNEL PARAMETERS --><?php echo $server['igf_server_kernel_parameter'];?></td>
              <td><!-- ADDITIONAL PACKAGES --><?php echo $server['igf_server_additional_package'];?></td>
              <td><!-- USER ID : GROUP ID : HOME DIR --><?php echo $server['igf_server_user_id'];?></td>
              <td><!-- IDC SUPPORT REQUIREMENT --><?php echo $server['igf_server_idc_support'];?></td>
              <td><!-- REMARKS / ADDITIONAL NOTES --><?php echo $server['igf_server_remark'];?></td>
              <td><!-- REMOVE - RAM --><?php echo $server['igf_server_reconfig_rm_ram'];?></td>
              <td><!-- REMOVE - HDD --><?php echo $server['igf_server_reconfig_rm_hdd'];?></td>
              <td><!-- REMOVE - NIC --><?php echo $server['igf_server_reconfig_rm_nic'];?></td>
              <td><!-- REMOVE - FC HBA --><?php echo $server['igf_server_reconfig_rm_fc_hba'];?></td>
              <td><!-- ADD - RAM --><?php echo $server['igf_server_reconfig_add_ram'];?></td>
              <td><!-- ADD - HDD --><?php echo $server['igf_server_reconfig_add_hdd'];?></td>
              <td><!-- ADD - NIC --><?php echo $server['igf_server_reconfig_add_nic'];?></td>
              <td><!-- ADD - FC HBA --><?php echo $server['igf_server_reconfig_add_fc_hba'];?></td>
              <td><!-- HOSTNAME --><?php echo $server['igf_server_hostname'];?></td>
              <td><!-- CONSOLE IP (iLO / RSC) --><?php echo $server['igf_server_console_ip'];?></td>
              <td><!-- SUBNET MASK --><?php echo $server['igf_server_console_ip_sm'];?></td>
              <td><!-- GATEWAY --><?php echo $server['igf_server_console_ip_gw'];?></td>
              <td><!-- DATA IP 1 --><?php echo $server['igf_server_data_ip_1'];?></td>
              <td><!-- DATA IP 2 --><?php echo $server['igf_server_data_ip_2'];?></td>
              <td><!-- VIP --><?php echo $server['igf_server_vip'];?></td>
              <td><!-- SUBNET MASK --><?php echo $server['igf_server_data_ip_sm'];?></td>
              <td><!-- GATEWAY --><?php echo $server['igf_server_data_ip_gw'];?></td>
              <td><!-- LB IP --><?php echo $server['igf_server_lb_ip'];?></td>
              <td><!-- OTHER IP --><?php echo $server['igf_server_other_ip'];?></td>
              <td><!-- SM --><?php echo $server['igf_server_other_ip_sm'];?></td>
              <td><!-- GW --><?php echo $server['igf_server_other_ip_gw'];?></td>
              <td><!-- PUBLIC IP --><?php echo $server['igf_server_public_ip'];?></td>
              <td>
                <!-- CLUSTER SPECIFIC INFORMATION / MISCELLANEOUS -->
                <?php ?>
                <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string( $server['igf_server_misc'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_misc']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo nl2br($server['igf_server_misc']);?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
                <?php ?>
              </td>
            </tr>
      <?php
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
              <th width="<?php echo isset($value['width']) ? $value['width'] : '150';?>px"><?php echo $value['name'];?></th>
          <?php
            }
          ?>
              </tr>
          <?php
            foreach ($igf_equipment as $key => $equipment) {
              # code...
              //var_dump($equipment);
          ?>
            <tr>
          <?php
              foreach ($igf_equi_key as $key => $value) {
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

<!--
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
-->

      </div>

      </form>

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


    $('#myTab li:eq(1) a').tab('show');
  });
</script>

