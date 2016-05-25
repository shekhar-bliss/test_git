<?php
  session_start();
  // load up config file
  error_reporting(FALSE);
  ini_set('display_errors', FALSE);
  ini_set('display_startup_errors', FALSE);

  //error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';

  $pei_current_module = 'IMPLEMENTATION';
    // load up config file
  require_once(__dir__."/../../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../../pei_function.php");

  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');
  require_once($pei_config['paths']['base'].'/device/pei_device.php');

  /** Include PHPExcel */
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel.php');
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel/IOFactory.php');

  if(!isset($_SESSION['pei_uid'])) {
    header("Location:".$pei_config['urls']['baseUrl'].'/login.php');
    exit;
  }

  // Initialize variables
  $pei_messages     = array();
  $action           = 'Download';
  $pei_page_access  = FALSE;
  $req_id           = '';
  $req_name         = '';
  $output_file_name = '';

  $pei_user_access_permission = fetch_user_access_permission($_SESSION['pei_user']);

  if(in_array('sync_any_igf', $pei_user_access_permission)) {
    $pei_page_access = TRUE;
  }
  else {
    $pei_messages['error'][] = 'Unauthorized Access';
  }

  $rep_header   = array(
    'project_name' => array('name' => 'project_name'),
    'project_id' => array('name' => 'project_id'),
    'server_type' => array('name' => 'SERVER TYPE'),
    'device_type' => array('name' => 'DEVICETYPE'),
    'city' => array('name' => 'City'),
    'facility' => array('name' => 'Facility'),
    'facility_id' => array('name' => 'Facility_ID'),
    'server_hall' => array('name' => 'SERVERHALL'),
    'rack_number' => array('name' => 'RACK_NUMBER'),
    'rack_name' => array('name' => 'RACK_NAME'),
    'u_location' => array('name' => 'U_LOCATION'),
    's_location' => array('name' => 'S_LOCATION'),
    'serial_number' => array('name' => 'SERIALNUMBER'),
    'env' => array('name' => 'ENVIRONMENT'),
    'make' => array('name' => 'MAKE'),
    'model' => array('name' => 'MODEL'),
    'ram' => array('name' => 'RAM'),
    'cpu_type' => array('name' => 'CPU_TYPE'),
    'cpu_nos' => array('name' => 'CPU_NOS'),
    'no_cores' => array('name' => 'NO_CORES'),
    'no_hard_disks' => array('name' => 'NO_HARD_DISKS'),
    'hdd_internal' => array('name' => 'HDDINTERNAL'),
    'no_hba_ports' => array('name' => 'NO_HBA_PORTS'),
    'os' => array('name' => 'OS'),
    'os_version' => array('name' => 'OS_VERSION'),
    'ilorsa_password' => array('name' => 'ilorsa_password'),
    'hardware_profile' => array('name' => 'Hardware_profile'),
    'vlan_id' => array('name' => 'VLAN_ID'),
    'role_profile' => array('name' => 'ROLE_PROFILE'),
    'node_criticality' => array('name' => 'NODE_CRITICALITY'),
    '10g_no' => array('name' => '10g_NO'),
    'bond_type' => array('name' => 'Bond_Type'),
    'backup_mgmt' => array('name' => 'BACKUP_MGMT'),
    'sf_ha_cfs_rac' => array('name' => 'SF_HA_CFS_RAC'),
    'storage_volume_mgr' => array('name' => 'STORAGE_VOLUME_MGR'),
    'hp_om' => array('name' => 'HP OM'),
    'po_number' => array('name' => 'PONUMBER'),
    'amc_start_date'=> array('name' => 'AMC Start Date'),
    'amc_end_date'=> array('name' => 'AMC End Date'),
    'dns_domain' => array('name' => 'DNS_Domain'),
    'ad_domain_name' => array('name' => 'AD_DOMAIN_NAME'),
    'storage_mgnt' => array('name' => 'STORAGE_MGMT'),
    'owner_contact_no' => array('name' => 'OWNER_CONTACTNO'),
    'owner_email' => array('name' => 'OWNER_EMAIL'),
    'app_name' => array('name' => 'APP_NAME'),
    'application_category' => array('name' => 'APPLICATION_CATEGORY'),
    'application_vendor' => array('name' => 'APPLICATION_VENDOR'),
    'assignment_group' => array('name' => 'ASSIGNMENT_GROUP'),
    'application_nodegroup' => array('name' => 'APPLICATION_NODEGROUP'),
    'db_version' => array('name' => 'DB_VERSION'),
    'middleware_version' => array('name' => 'MIDDLEWARE_VERSION'),
    'app_owner_email' => array('name' => 'Application_Owner_Email'),
    'app_owner_contact_no'=> array('name' => 'Application_Owner_Contact_No'),
    'server_role' => array('name' => 'SERVER_ROLE'),
    'gis_neid' => array('name' => 'GIS_NEID'),
    'vcenter_datastore' => array('name' => 'vcenter_datastore'),
    'vcenter_clustername' => array('name' => 'vcenter_clustername'),
    'dv_switch_id' => array('name' => 'dvswitchid'),
    'dv_port_group_id' => array('name' => 'dvportgroupid'),
    'tenants_name' => array('name' => 'TENANTS_NAME'),
    'network_zone' => array('name' => 'NETWORK ZONE'),
    'ha' => array('name' => 'HA'),
    'ha_paring_number' => array('name' => 'HA Paring Number'),
    'interconnect_vlan' => array('name' => 'Interconnect-VLAN'),
    'ha_type' => array('name' => 'HA Type'),
    'vip' => array('name' => 'VIP'),
    'ilo_ip_or_rsc' => array('name' => 'ILO_IP_OR_RSC'),
    'ilo_subnet_mask' => array('name' => 'ILO_SUBNET_MASK'),
    'ilo_gateway' => array('name' => 'ILO_GATEWAY'),
    'data_ip_1' => array('name' => 'DATA_IP1'),
    'data_subnet_mask' => array('name' => 'SUBNET_MASK'),
    'data_gateway' => array('name' => 'GATEWAY'),
    'hostname' => array('name' => 'HOSTNAME'),
    'storage_ext_fs' => array('name' => 'EXTERNAL_FILE_SYSTEM'),
    'idc_support' => array('name' => 'IDC SUPPORT REQUIREMENT'),
    'business_group' => array('name' => 'Business_Group'),
  );

  if($pei_page_access) {

    if(isset($_GET['req_id'])) {
      $req_id   = $_GET['req_id'];
      $output_file_name = $req_id.'.xlsx';

       // Get Request Details
      $igf_request  = get_request_detail_from_req_id($req_id);
      if($igf_request){
        $req_name = $igf_request['req_title'];

      }
      else {
        $pei_messages['error'][] = 'INVALID REQUEST ID';
      }
    }
    else {
      $pei_messages['error'][] = 'REQUEST ID MISSING';
    }

  }
  else {
    $pei_messages['error'][] = 'Unauthorized Access';
  }

  //echo '</pre>';

  if(!isset($pei_messages['error'])) {
    // Generate xls file and send to download
    //echo '<pre>';

    // Get Request's Equipment Details
    $req_epqt = igf_request_epqt_detail($req_id);

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("JioDC Portal")
               ->setLastModifiedBy("JioDC Portal")
               ->setTitle("Mandatory Fields for ".$req_id)
               ->setSubject("Mandatory Fields for ".$req_id)
               ->setDescription("Mandatory Fields for ".$req_id)
               ->setKeywords("office 2007 openxml php Mandatory Fields")
               ->setCategory("Mandatory Fields for ".$req_id." file");

    $objPHPExcel->setActiveSheetIndex(0);

    $col = 0;

    foreach($rep_header as $key => $header_col) {
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $header_col['name']);
      // Style the column
      $columnLetter = PHPExcel_Cell::stringFromColumnIndex($col);
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter.'1')->getFont()->setBold(true);

      $rep_header[$key]['col']        = $col;
      $rep_header[$key]['col_letter'] = $columnLetter;
      $col++;
    }

    if($req_epqt) {
      $download_row             = 2;
      $server_type              = '';
      $serial_number            = '';
      $serial_number_phy        = '';
      $serial_number_vir_index  = 0;
      $cpu_no                   = '';
      $cpu_cores                = '';
      $rack_no                  = '';
      $rack_name                = '';
      $u_loc                    = '';
      $s_loc                    = '';
      $int_hdd_size             = '';
      $os_ver                   = '';


      $contact_1      = igf_request_igf_contact($req_id, 2);
      $contact_ops_1  = igf_request_igf_contact($req_id, 4);

      foreach ($req_epqt as $server) {
        $int_hdd_size = trim($server['igf_server_storage_int_size']);
        $os_ver       = trim($server['igf_server_os_version']);
        $hp_om        = $server['igf_server_idc_support'];
        $server_type  = $server['igf_server_type_id'];
        if($hp_om) {
          $hp_om = 'Y';
        }
        else {
          $hp_om = 'N';
        }

        if($server['igf_server_type_id'] == 3) {
          $server_type              = 'PHYSICAL';
          $serial_number_phy        = $server['igf_server_serial_number'];
          $serial_number            = $serial_number_phy;
          $serial_number_vir_index  = 0;

          $rack_no                  = $server['igf_server_row_rack'];
          $rack_name                = $server['igf_server_rack_name'];
          $u_loc                    = $server['igf_server_rack_u'];
          $s_loc                    = $server['igf_server_slot_no'];

          $city   = trim($server['loc_cmdb_city']);
          $fac    = trim($server['loc_cmdb_fac']);
          $facid  = trim($server['loc_cmdb_fac_id']);
          $sh     = $server['igf_server_server_hall'];

        } // END IF ($server['igf_server_type_id'] == 3)
        else if ($server['igf_server_type_id'] == 4) {
          $server_type   = 'VIRTUAL';
          $pattern = '/^'.$serial_number_phy.'-'.sprintf("%02d", $serial_number_vir_index).'$/';
          $serial_number = trim($server['igf_server_serial_number']);
          if($serial_number == '') {
            if($serial_number_phy) {
              $serial_number = $serial_number_phy.'-V'.sprintf("%02d", $serial_number_vir_index);
            }
          } else if(strtoupper($serial_number) == 'NA') {
            if($serial_number_phy) {
              $serial_number = $serial_number_phy.'-V'.sprintf("%02d", $serial_number_vir_index);
            }
          }else if(preg_match ($pattern, $serial_number) ) {
            $serial_number = $serial_number_phy.'-V'.sprintf("%02d", $serial_number_vir_index);
          }else if(preg_match ('/^[a-zA-Z0-9]{10}-[0-9]{2}$/', $serial_number) ) {
            $serial_number = $serial_number_phy.'-V'.sprintf("%02d", $serial_number_vir_index);
          }
          else {
            if($serial_number_phy) {
              $serial_number = $serial_number_phy.'-V'.sprintf("%02d", $serial_number_vir_index);
            }
          }
        } // END ELSE IF
        else {
          $server_type   = '';
          $city   = trim($server['loc_cmdb_city']);
          $fac    = trim($server['loc_cmdb_fac']);
          $facid  = trim($server['loc_cmdb_fac_id']);
          $sh     = $server['igf_server_server_hall'];
        } // END ELSE


        // CPU NO
        $cpu_no = trim($server['igf_server_cpu_no']);
        if($cpu_no == '') {
          $cpu_no = '';
        }
        else if(strtoupper($cpu_no) == 'NA') {
           $cpu_no = '';
        }
        else {
          $cpu_no = preg_replace("/[^0-9]/","",$cpu_no);
        }

        // CPU CORES
        $cpu_cores = trim($server['igf_server_cpu_cores']);
        if($cpu_cores == '') {
          $cpu_cores = '';
        }
        else if(strtoupper($cpu_cores) == 'NA') {
           $cpu_cores = '';
        }
        else {
          $cpu_cores = preg_replace("/[^0-9]/","",$cpu_cores);
        }

        // OS Version
        if($os_ver){
          preg_match('#\d+(?:\.\d)?#', $os_ver, $os_ver_match);
          if($os_ver_match){
            $os_ver = $os_ver_match[0];
          }
        }

        // project_name
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['project_name']['col'], $download_row, $req_name);
        // project_id
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['project_id']['col'], $download_row, $req_id);
        // server_type
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['server_type']['col'], $download_row, $server_type);
        // device_type
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['device_type']['col'], $download_row, 'SERVER');
        // city
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['city']['col'], $download_row, $city);
        // facility
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['facility']['col'], $download_row, $fac);
        // facility_id
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['facility_id']['col'], $download_row, $facid);
        // server_hall
        switch ($sh) {
          case 'T01':
            $sh = 'T1';
            break;
          case 'T02':
            $sh = 'T2';
            break;
          default:
            # code...
            break;
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['server_hall']['col'], $download_row, $sh);
        // rack_number
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['rack_number']['col'], $download_row, $rack_no);
        // rack_name
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['rack_name']['col'], $download_row, $rack_name);
        // u_location
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['u_location']['col'], $download_row, $u_loc);
        // s_location
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['s_location']['col'], $download_row, $s_loc);

        // serial_number
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['serial_number']['col'], $download_row, $serial_number);
        // env
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['env']['col'], $download_row,$server['igf_server_env']);
        // make
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['make']['col'], $download_row, $server['igf_server_make']);
        // model
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['model']['col'], $download_row, $server['igf_server_model']);
        // ram
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ram']['col'], $download_row, $server['igf_server_ram']);
        // cpu_type
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['cpu_type']['col'], $download_row, $server['igf_server_cpu_type']);
        // cpu_nos
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['cpu_nos']['col'], $download_row, $cpu_no);
        // no_cores
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['no_cores']['col'], $download_row, $cpu_cores);
        // no_hard_disks
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['no_hard_disks']['col'], $download_row, $server['igf_server_storage_int_no']);
        // hdd_internal
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['hdd_internal']['col'], $download_row, $int_hdd_size);
        // no_hba_ports
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['no_hba_ports']['col'], $download_row, $server['igf_server_fc_hba_port']);
        // os
        $os = '';
        $os =  $server['igf_server_os'];
        switch (strtoupper($os)) {
          case 'OTHER':
            $os = 'OTHERS';
            break;
          default:
            # code...
            break;
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['os']['col'], $download_row, $os);
        // os_version
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['os_version']['col'], $download_row, $os_ver);
        // 10g_no
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['10g_no']['col'], $download_row, $server['igf_server_nic_10g']);
        // storage_volume_mgr
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['storage_volume_mgr']['col'], $download_row, $server['igf_server_volume_manager']);
        // hp_om
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['hp_om']['col'], $download_row, $hp_om);
        // owner_contact_no
        $owner_contact_no = '';
        if($contact_ops_1){
          $owner_contact_no = $contact_1['igf_contact_mobile'];
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['owner_contact_no']['col'], $download_row, $owner_contact_no);
        // owner_email
        $owner_email = '';
        if($contact_ops_1){
          $owner_email = $contact_1['igf_contact_email'];
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['owner_email']['col'], $download_row, $owner_email);

        // app_name
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['app_name']['col'], $download_row, $server['igf_server_app']);


        // app_owner_email
        $app_owner_email = '';
        if($contact_ops_1){
          $app_owner_email = $contact_ops_1['igf_contact_email'];
        }
        else {
          $app_owner_email = $server['igf_server_app_owner_email'];
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['app_owner_email']['col'], $download_row, $app_owner_email);
        // app_owner_contact_no
        $app_owner_contact_no = '';
        if($contact_ops_1) {
          $app_owner_contact_no = $contact_ops_1['igf_contact_mobile'];
        }
        else {
          $app_owner_contact_no = $server['igf_server_app_owner_mobile'];
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['app_owner_contact_no']['col'], $download_row, $app_owner_contact_no);


        // server_role
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['server_role']['col'], $download_row, $server['igf_server_role']);
        // db_version
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['db_version']['col'], $download_row, $server['igf_server_db_version']);
        // tenants_name
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['tenants_name']['col'], $download_row, $server['igf_server_req_group_name']);
        // network_zone
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['network_zone']['col'], $download_row, $server['igf_server_network_zone']);
        // ha
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ha']['col'], $download_row, $server['igf_server_ha_cluster']);
        // ha_paring_number
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ha_paring_number']['col'], $download_row, $server['igf_server_ha_cluster_pair']);
        // ha_type
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ha_type']['col'], $download_row, $server['igf_server_ha_cluster_type']);
        // vip
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['vip']['col'], $download_row, $server['igf_server_vip']);
        // ilo_ip_or_rsc
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ilo_ip_or_rsc']['col'], $download_row, $server['igf_server_console_ip']);
        // ilo_subnet_mask
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ilo_subnet_mask']['col'], $download_row, $server['igf_server_console_ip_sm']);
        // ilo_gateway
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['ilo_gateway']['col'], $download_row, $server['igf_server_console_ip_gw']);
        // data_ip_1
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['data_ip_1']['col'], $download_row, $server['igf_server_data_ip_1']);
        // data_subnet_mask
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['data_subnet_mask']['col'], $download_row, $server['igf_server_data_ip_sm']);
        // data_gateway
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['data_gateway']['col'], $download_row, $server['igf_server_data_ip_gw']);
        // hostname
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['hostname']['col'], $download_row, $server['igf_server_hostname']);
        // storage_ext_fs
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['storage_ext_fs']['col'], $download_row, $server['igf_server_storage_ext_fs']);
        // idc_support
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['idc_support']['col'], $download_row, $server['igf_server_idc_support']);
        // business_group
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['business_group']['col'], $download_row, $server['igf_server_req_sub_group_name']);

        $download_row++;
      } // END FOREACH $req_epqt

    } // END IF $req_epqt

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$output_file_name.'"');
    header('Cache-Control: max-age=0');
    // If you're serving to IE 9, then the following may be needed
    header('Cache-Control: max-age=1');

    // If you're serving to IE over SSL, then the following may be needed
    header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
    header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
    header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
    header ('Pragma: public'); // HTTP/1.0

    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
    $objWriter->save('php://output');
    exit;

    //echo '</pre>';

  }
  else {
    require_once($pei_config['paths']['base'].'/header.php');
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/implementation/index.php">Implementation</a></li>
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/implementation/Automation/view_req_for_ttablecsv.php">Download CSV For T Table</a></li>
        <li class="active"><?php echo $action;?></li>
      </ol>
      <!-- /breadcrumb  -->

      <div class="box-content">
      <!-- START -->
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
?>
      <!-- END -->
      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
    require_once($pei_config['paths']['base'].'/footer.php');
  } // END ELSE !isset($pei_messages['error'])


?>
