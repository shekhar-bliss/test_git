<?php
  session_start();
  $pei_current_module = 'REQUEST';

  require_once(__dir__.'/../header.php');

  // Include SpreadsheetReader library
  require_once($pei_config['paths']['vendors'].'/spreadsheet-reader/php-excel-reader/excel_reader2.php');
  require_once($pei_config['paths']['vendors'].'/spreadsheet-reader/SpreadsheetReader.php');

  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');
  require_once($pei_config['paths']['base'].'/server/pei_server.php');
  require_once($pei_config['paths']['base'].'/vendor/pei_vendor.php');
  require_once($pei_config['paths']['base'].'/network/pei_network_zone.php');

  //error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';
  //echo '$pei_user_access_permission <br />';
  //var_dump($pei_user_access_permission);

  // Initialize variables
  $pei_messages         = array();
  $req_msg              = '';
  $validate_igf_other_col = TRUE;
  $igf_version          = $pei_config['igf']['version'];
  // Request Number start from req_id number
  $igf_version_req_id   = $pei_config['igf']['after_req_id'];
  $req_id_num           = '';

  $data_req_user_group  = get_requestor_user_group();
  $data_env             = get_all_env();
  $data_loc             = get_all_loc();
  $data_server_hall     = get_all_server_hall();
  $data_user            = get_all_user_by_role(3, 'DISTINCT');
  $data_spoc_user       = get_all_spoc_user();
  $data_pm_user         = get_all_user_by_role(11, 'DISTINCT');


  $igf_v3_loc = igf_v3_location_distinct_name();
  $igf_v3_sh  = config_value('IGF', 'SERVER HALL', 'v3.1');

  // Get All Document type for NON IGF
  $data_doc_type        = get_all_doc_type(4);
  $data_req_sub_group   = '';



  $data_distinct_user_group = '';
  $data_distinct_sub_group  = '';
  $data_distinct_env        = '';
  $data_distinct_loc        = '';
  $data_distinct_sh         = '';
  $data_distinct_rr         = '';
  $data_distinct_rack_u     = '';
  $data_distinct_rack_slot  = '';
  $data_distinct_server_type= '';
  $data_distinct_vendor     = '';
  $data_distinct_ha_type    = '';
  $data_distinct_os         = '';
  $data_distinct_os_version = '';
  $data_distinct_support    = '';
  // v4
  $data_distinct_eqpt_type        = '';
  $data_distinct_hypervisor_type  = '';
  $data_distinct_eqpt_role        = '';
  $data_distinct_nic_1g           = '';
  $data_distinct_nic_10g          = '';
  $data_distinct_fc_hba           = '';
  $data_distinct_fc_hba_ports     = '';
  $data_distinct_fc_hba_speed     = '';
  $data_distinct_lan_ports        = '';
  $data_distinct_lan_inter        = '';
  $data_distinct_lan_speed        = '';
  $data_distinct_nw_zone          = '';
  $data_distinct_nw_sub_zone      = '';
  $data_distinct_yes_no           = '';
  $data_distinct_ha_soft          = '';
  $data_distinct_db               = '';


  $data_distinct_user_group = validation_user_group();
  $data_distinct_sub_group_v3  = config_value('IGF', 'REQUESTOR SUB GROUP', 'v3.1');
  $data_distinct_env        = environment_distinct_name();
  $data_distinct_loc        = location_distinct_name();
  $data_distinct_sh         = server_hall_distinct_name();
  $data_distinct_rr         = row_rack_distinct_name();
  $data_distinct_rack_u     = rack_unit_distinct_name();
  $data_distinct_rack_slot  = rack_slot_distinct_name();
  $data_distinct_server_type= validation_equipment_type('v3.1');
  $data_distinct_vendor     = vendor_distinct_name();
  $data_distinct_vendor_model=vendor_model_distinct_name(3);
  $data_distinct_ha_type    = validation_ha_type();
  $data_distinct_os         = os_distinct_name();
  $data_distinct_os_version = os_version_distinct_name();
  $data_distinct_support    = config_value('IGF', 'IDC SUPPORT REQUIREMENT', 'v3.1');

  // v4
  $data_distinct_sub_group        = user_group_distinct_sub_group_name();
  $data_distinct_eqpt_type        = validation_equipment_type('v4');
  $data_distinct_hypervisor_type  = config_value('IGF', 'HYPERVISOR', $igf_version);
  $data_distinct_eqpt_role        = config_value('IGF', 'EQUIPMENT ROLE', $igf_version);
  $data_distinct_nic_1g           = config_value('IGF', '# of NICs - 1G', $igf_version);
  $data_distinct_nic_10g          = config_value('IGF', '# of NICs - 10G', $igf_version);
  $data_distinct_fc_hba           = config_value('IGF', '# of FC HBA CARDS', $igf_version);
  $data_distinct_fc_hba_ports     = config_value('IGF', '# of FC HBA PORTS', $igf_version);
  $data_distinct_fc_hba_speed     = config_value('IGF', 'FC HBA PORT SPEED', $igf_version);
  $data_distinct_lan_ports        = config_value('IGF', 'LAN PORTS', $igf_version);
  $data_distinct_lan_inter        = config_value('IGF', 'LAN INTERFACE TYPE', $igf_version);
  $data_distinct_lan_speed        = config_value('IGF', 'LAN INTERFACE SPEED', $igf_version);
  $data_distinct_nw_zone          = config_value('IGF', 'NETWORK ZONE', $igf_version);
  $data_distinct_nw_sub_zone      = network_sub_zone_name();
  $data_distinct_yes_no           = config_value('IGF', 'YES NO', $igf_version);
  $data_distinct_ha_soft          = config_value('IGF', 'HA TYPE / CLUSTER SOFTWARE', $igf_version);
  $data_distinct_db               = config_value('IGF', 'DB', $igf_version);
  $data_distinct_vol_mg           = config_value('IGF', 'VOLUME MANAGER', $igf_version);
  $data_distinct_ext_storage_type = config_value('IGF', 'EXTERNAL STORAGE TYPE', $igf_version);
  $data_distinct_storage_array    = config_value('IGF', 'STORAGE ARRAY', $igf_version);
  $data_distinct_ext_storage_raid = config_value('IGF', 'EXTERNAL STORAGE RAID CONFIG', $igf_version);
  $data_distinct_idc_support      = config_value('IGF', 'IDC SUPPORT REQUIREMENT', $igf_version);
  $data_distinct_add_nic          = config_value('IGF', 'NIC', $igf_version);
  $data_distinct_add_fc_hba       = config_value('IGF', 'FC HBA', $igf_version);

  $data_request         = '';
  $data_user_group      = '';
  $data_request_mat     = '';

  $action           = 'Add';
  $action           = (isset($_GET['action']) && !empty($_GET['action'])) ? $_GET['action'] : 'Add';
  $uname            = strtolower($_SESSION['usr']);
  $user_id          = isset($_SESSION['idc_portal_user_id']) ? $_SESSION['idc_portal_user_id'] : 1;
  $req_date         = '';
  $req_id           = '';
  $req_group        = '';
  $req_group_sub    = '';
  $req_user_group_sub_other = '';
  $add_group_sub_other  = FALSE;
  $req_group_sub_other  = '';
  $req_requestor    = '';
  $req_env          = array();
  $req_loc          = array();
  $req_sh           = array();
  $req_name         = '';
  $req_remark       = '';
  $req_idc_spoc     = '';
  $req_infra        = 0;
  $req_pm           = '';
  $req_doc_type     = '';
  $req_doc          = '';
  $req_doc_uploaded = '';
  $req_doc_uploaded_files   = '';
  $req_doc_uploaded_count   = '';
  $req_doc_uploaded_del     = '';
  $req_doc_uploaded_update  = '';

  $req_status = '';

  // Request Material
  $req_mat_serial       = '';
  $req_mat_ram          = '';
  $req_mat_ram_ret      = '';
  $req_mat_hdd          = '';
  $req_mat_hdd_ret      = '';
  $req_mat_nic          = '';
  $req_mat_nic_ret      = '';
  $req_mat_fc_hba       = '';
  $req_mat_fc_hba_ret   = '';
  $req_mat_additional   = '';

  $req_doc_uploaded_igf         = '';
  $req_doc_uploaded_igf_file   = '';
  $req_doc_uploaded_del_igf     = '';
  $req_doc_uploaded_update_igf  = '';

  $req_igf        = '';
  $igf_docs       = '';
  $igf_keys       = '';
  $spreadsheet    = '';
  $igf_file_name  = '';
  $igf_doc_delete_flag = TRUE;
  $igf_req_group      = '';
  $igf_req_sub_group  = '';
  $igf_project        = '';
  $igf_env            = '';
  $igf_loc            = '';
  $igf_sh             = '';


  $valid_file_type  = array(
    'application/msword',
    'application/octet-stream',
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'application/vnd.ms-excel',
    'text/plain',
  );
  // Common one time initialization
  $timestamp  = time();
  $idc_users  = fetch_user();
  $idc_requestor_groups = fetch_requestor_group();

  $pei_page_access = FALSE;
  // CHECK access permission for
  switch($action){
    case 'Add':
      if(in_array('create_request', $pei_user_access_permission)) {
        $pei_page_access = TRUE;
      }
      break;
    case 'Edit':
      if(in_array('edit_any_request', $pei_user_access_permission)) {
        $pei_page_access = TRUE;
      }
      break;
    case 'View':
      if(in_array('view_any_request', $pei_user_access_permission)) {
        $pei_page_access = TRUE;
      }
      break;
    default:
      $pei_page_access = FALSE;
  }

  if(isset($_REQUEST['req_id'])) {
    if(!empty($_REQUEST['req_id']) && valid_request_id($_REQUEST['req_id'])) {
      $req_id = $_REQUEST['req_id'];

      // Find IGF version
      $req_id_num = substr($req_id, -4);
      if($req_id_num <= $igf_version_req_id) {
        $igf_version  = 'v3.1';
      }

      // Set form field values if save button is not pressed
      if(!isset($_POST['req_save'])) {
        $request = request_detail($req_id);
        if($request) {


          // IF Request is RELEASES then disable edit action
          if($request['req_status'] && in_array($request['req_status'], array('RELEASED' , 'PARTIAL RELEASED'))) {
            $action = 'View';
          }

          $req_date         = $request['req_date'];

          $req_status       = $request['req_status'];

          $req_group        = $request['req_group_id'];
          $req_group_sub    = $request['req_group_sub_id'];
          $req_requestor    = $request['req_initiator'];
          $req_name         = $request['req_title'];
          $req_remark       = $request['req_remarks'];
          //$req_infra        = $request['req_infra'];
          $req_idc_spoc     = $request['req_idc_spoc'];

          $req_mat_serial     = $request['req_mat_serial_nos'];
          $req_mat_ram        = $request['req_mat_ram'];
          $req_mat_ram_ret    = $request['req_mat_ram_ret'];
          $req_mat_hdd        = $request['req_mat_hdd'];
          $req_mat_hdd_ret    = $request['req_mat_hdd_ret'];
          $req_mat_nic        = $request['req_mat_nic'];
          $req_mat_nic_ret    = $request['req_mat_nic_ret'];
          $req_mat_fc_hba     = $request['req_mat_fc_hba'];
          $req_mat_fc_hba_ret = $request['req_mat_fc_hba_ret'];
          $req_mat_additional = $request['req_mat_additional'];

          // Fetch Request Project Manager Detail
          $req_pm_detail = request_pm_active($req_id);
          if($req_pm_detail){
            $req_pm = $req_pm_detail['req_pm'];
          }

          // Fetch ENV of request
          $req_env = fetch_req_env($req_id);

          // Fetch Locations of request
          $req_loc = fetch_req_loc($req_id);

          // Fetch Server Hall of request
          $req_sh = fetch_req_sh($req_id);

          if($req_date) {
            $req_date_timestamp = strtotime($req_date);
            $after_timestamp    = strtotime('2016-02-25 00:00:00');
            if($req_date_timestamp < $after_timestamp) {
              $validate_igf_other_col = FALSE;
            }
          }
        }
      }
    }
    else{
      $pei_messages['error'][] = 'Invalid Request ID.';
    }
  }

  if( (isset($_POST['req_save']) && $_POST['req_save'] == 'Save') || (isset($_POST['req_save_dummy']) && $_POST['req_save_dummy'] == 'Save') ) {
    if($_POST['user_group'] == '') {
      $pei_messages['error'][] = 'Please Select Requestor Group';
    }
    else {
      $req_group = $_POST['user_group'];
      $data_request['req_group_id'] = $req_group;

      if($_POST['user_group_sub'] == '') {
        $pei_messages['error'][] = 'Please Select Requestor Sub Group.';
      }
      else{
        $req_group_sub = $_POST['user_group_sub'];

        // Check if Requestor Sub Group is of OTHER type or not
        // IF is of OTHER type save new Requestor Sub Group
        $user_group_check_other = user_group_detail($req_group_sub, $req_group);
        if($user_group_check_other) {
          if(strtolower($user_group_check_other['user_group_name'])  == 'other') {
            if($_POST['user_group_sub_other'] == ''){
              $pei_messages['error'][] = 'Please enter Requestor Sub Group name.';
            }
            else {
              $add_group_sub_other = TRUE;
              $req_group_sub_other = $_POST['user_group_sub_other'];
            }
          }
        }
      }
    }

    if($_POST['requestor'] == '') {
      $pei_messages['error'][] = 'Please Enter Request Initiator.';
    }
    else{
      $req_requestor = $_POST['requestor'];
      $data_request['req_initiator'] = $req_requestor;
    }

    if(!isset($_POST['req_env'])){
      $pei_messages['error'][] = 'Please Select Enviroment.';
    }
    else {
      $req_env = $_POST['req_env'];
    }


    if($_POST['req_name'] == '') {
      $pei_messages['error'][] = 'Please Enter Request Title.';
    }
    else{
      $req_name = $_POST['req_name'];
      $data_request['req_title'] = $req_name;
    }

    $req_remark = $_POST['req_remark'];
    $data_request['req_remarks'] = $req_remark;


    //$req_infra = $_POST['req_infra'];
    //$data_request['req_infra'] = $req_infra;

    if(!isset($_POST['req_loc'])){
      $pei_messages['error'][] = 'Please Select Location.';
    }
    else {
      $req_loc = $_POST['req_loc'];
    }

    if(isset($_POST['req_sh'])) {
      $req_sh = $_POST['req_sh'];
    }

    if(isset($_POST['req_pm'])) {
      $req_pm = $_POST['req_pm'];
    }

    if($_POST['req_idc_spoc'] == '') {
      $pei_messages['error'][] = 'Please Select JioDC SPOC.';
    }
    else{
      $req_idc_spoc = $_POST['req_idc_spoc'];
      $data_request['req_idc_spoc'] = $req_idc_spoc;
    }

    if($_FILES['req_doc_uploaded']) {
      //var_dump($_FILES['req_doc_uploaded']);
      $req_doc_uploaded_count = count($_FILES['req_doc_uploaded']['name']);

      for($i=0;$i<$req_doc_uploaded_count;$i++) {
        if($_FILES['req_doc_uploaded']['name'][$i] && $_FILES['req_doc_uploaded']['error'][$i] == 0) {
          $targetFile = $pei_config['paths']['resources']. '/data/request/request_doc/' .time().'----'. $_FILES['req_doc_uploaded']['name'][$i];
          if(move_uploaded_file($_FILES['req_doc_uploaded']['tmp_name'][$i], $targetFile)) {
            $req_doc_uploaded_files[$i]['name'] = $_FILES['req_doc_uploaded']['name'][$i];
            $req_doc_uploaded_files[$i]['path'] = $targetFile;
          }
          else {
            $pei_messages['error'][] = 'Something went wrong while saving file ['.$_FILES['req_doc_uploaded']['name'][$i].'] \n';
          }
        }
      }
    }

    // validate uploaded igf file
    if($_FILES['req_doc_uploaded_igf']) {
      //var_dump($_FILES['req_doc_uploaded_igf']);

       // Check if file field is not empty
      if((!empty($_FILES['req_doc_uploaded_igf']['name']))) {
        //Check if the file is valid or not
        // in_array($_FILES['file_sol']['type'], $valid_file_type)
        if(1) {
          //Create unique file name to save on the server
          $file_name_igf = $pei_config['paths']['resources']. '/data/request/request_doc_igf/'.time().'---'.$_FILES['req_doc_uploaded_igf']['name'];
          if(move_uploaded_file($_FILES['req_doc_uploaded_igf']['tmp_name'], $file_name_igf)) {
            $req_doc_uploaded_igf_file['name'] = $_FILES['req_doc_uploaded_igf']['name'];
            $req_doc_uploaded_igf_file['path'] = $file_name_igf;
          }
          else {
            $pei_messages['error'][] = 'Error :  While saving '.$_FILES['req_doc_uploaded_igf']['name'].' file.\n';
          }
        }
        else {
           $pei_messages['error'][] = 'Invalid file type for IGF Documents! ['.$_FILES['req_doc_uploaded_igf']['name'].'].\n';
        }
      }

    }

    $valid_patching = FALSE;
    // Validate IGF
    $igf_ref_sheets = array('0' => array('name' => 'COVER'),
                            '1' => array('name' => '1. GUIDELINES'),
                            '2' => array('name' => '2. CONTACT & BUDGET INFORMATION'),
                            '3' => array('name' => '3. SERVER DETAILS'),
                            '4' => array('name' => '4. EQUIPMENT LIST'),
                            '5' => array('name' => '5. SOFTWARE LIST'),
                            '6' => array('name' => '6. IDC SUPPORT TYPE'),
                            '7' => array('name' => '7.Patching Sheet', 'optional' => TRUE),
                            'seven' => array('name' => '7.Patching', 'optional' => TRUE),
                          );

    if($req_doc_uploaded_igf_file) {
      // Start Processing uploade IGF file
      // To extract all data for excel

      // First check version of IGF is it v3, v3.1 or v4
      // This is determined base on request id.
      // If request is new then IGF version would be latest
      // Lates IGF version is v4

      $igf_file_name = $req_doc_uploaded_igf_file['name'];

      $igf_ref_sheets_2_key = '';
      $igf_ref_sheets_2_temp = array(
                                  array('key' => 'igf_contact_details', 'name' => 'CONTACT DETAILS'),
                                  array('key' => 'req_group_name', 'name' => 'REQUESTOR GROUP'),
                                  array('key' => 'req_sub_group_name', 'name' => 'REQUESTOR SUB-GROUP'),
                                  array('key' => 'igf_name', 'name' => 'PROJECT / SETUP NAME'),
                                  array('key' => 'igf_contact_spoc', 'name' => 'CONTACT DETAILS - PROJECT (SPOC)'),
                                  array('key' => 'igf_contact_spoc_header', 'name' => 'CONTACT SPOC'),
                                  array('key' => 'igf_contact_name', 'name' => 'CONTACT (NAME)'),
                                  array('key' => 'igf_contact_mobile', 'name' => 'CONTACT (MOBILE)'),
                                  array('key' => 'igf_contact_email', 'name' => 'CONTACT (E-MAIL)'),
                                  array('key' => 'igf_contact_ops', 'name' => 'CONTACT DETAILS - OPERATIONS (APPLICATION OWNER / SUPPORT TEAM)'),
                                  array('key' => 'igf_contact_ops_header', 'name' => 'CONTACT 1'),
                                  array('key' => 'igf_contact_name_ops', 'name' => 'CONTACT (NAME)'),
                                  array('key' => 'igf_contact_mobile_ops', 'name' => 'CONTACT (MOBILE)'),
                                  array('key' => 'igf_contact_email_ops', 'name' => 'CONTACT (E-MAIL)'),
                                  array('key' => 'igf_budget', 'name' => 'BUDGET DETAILS (IF APPLICABLE)'),
                                  array('key' => 'igf_budget_fund_center', 'name' => 'FUND CENTRE'),
                                  array('key' => 'igf_budget_gl', 'name' => 'GL'),
                                  array('key' => 'igf_budget_wbs', 'name' => 'WBS'),
                                );
      $igf_ref_sheets_2_temp_test = $igf_ref_sheets_2_temp;
      $igf_ref_sheets_2 = array(
                            array('', 'CONTACT DETAILS', '', ''),
                            array('', 'REQUESTOR GROUP', '', ''),
                            array('', 'REQUESTOR SUB-GROUP', '', ''),
                            array('', 'PROJECT / SETUP NAME', '', ''),
                            array('', 'CONTACT DETAILS - PROJECT (SPOC)', '', ''),
                            array('', '', 'CONTACT SPOC', 'HOD'),
                            array('', 'CONTACT (NAME)', '', ''),
                            array('', 'CONTACT (MOBILE)', '', ''),
                            array('', 'CONTACT (E-MAIL)', '', ''),
                            array('', 'CONTACT DETAILS - OPERATIONS (APPLICATION OWNER / SUPPORT TEAM)', '', ''),
                            array('', '', 'CONTACT 1', 'CONTACT 2'),
                            array('', 'CONTACT (NAME)', '', ''),
                            array('', 'CONTACT (MOBILE)', '', ''),
                            array('', 'CONTACT (E-MAIL)', '', ''),
                            array('', 'BUDGET DETAILS (IF APPLICABLE)', '', ''),
                            array('', 'FUND CENTRE', '', ''),
                            array('', 'GL', '', ''),
                            array('', 'WBS', '', '')
                          );

      switch($igf_version){
        case 'v3':
        case 'v3.1':
          # Validate IGF v3 and v3.1
          $spreadsheet = new SpreadsheetReader($req_doc_uploaded_igf_file['path']);
          $sheets = $spreadsheet->Sheets();

          $pei_error_sheet = FALSE;
          foreach ($sheets as $index => $name) {
            if($index < 6) {
              if($igf_ref_sheets[$index]['name'] != $name) {
                $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). '.$igf_ref_sheets[$index]['name'].' Sheet missing.\n';
                $pei_error_sheet = TRUE;
                break;
              }
            }
            else {
              // Check if Patching exits or not
              // Also find its sheet index
              //echo trim($name).' == '.trim($igf_ref_sheets[$index]['name']).'<br />';
              if(trim($name) == trim($igf_ref_sheets[7]['name']) ) {
                $igf_keys['patching_sheet_key'] = $index;
              }
              else if(trim($name) == trim($igf_ref_sheets['seven']['name']) ) {
                $igf_keys['patching_sheet_key'] = $index;
              }
            }
          }
          //var_dump($igf_keys);
          // Check if any error occur in previous loop processing
          if($pei_error_sheet) {

          }
          else {

            // Validate Sheet 2 => CONTACT & BUDGET INFORMATION
            $spreadsheet -> ChangeSheet(2);
            $igf_ref_sheets_2_empty = 0;
            $check_ops = FALSE;
            foreach ($spreadsheet as $Key => $Row) {
              //echo 'KEY :'.$Key.'<br />';
              // Check if this row is empty or not
              $check_row_empty = TRUE;
              foreach ($Row as $row_key => $row_value) {
                if(trim($row_value) != ''){
                  $check_row_empty = FALSE;
                  //////////////////////
                  //echo 'NOT EMPTY VALUE :'.trim(preg_replace('/\s+/', ' ', $row_value)).'<br />';
                  if(trim(preg_replace('/\s+/', ' ', $row_value))) {
                    //echo trim(preg_replace('/\s+/', ' ', $row_value)).'<br />';
                    foreach ($igf_ref_sheets_2_temp as $test_key => $test) {
                      if(isset($test['name']) && trim(preg_replace('/\s+/', ' ', $row_value)) == $test['name'] ) {
                        $test['index'] = $Key;
                        $igf_ref_sheets_2_key[$test['key']] = $test;
                        $igf_ref_sheets_2_temp[$test_key]   = '';

                        break;
                      }
                    }
                  }
                  //////////////////////
                }
              }


              if($check_row_empty == FALSE) {
                foreach ($Row as $row_key => $row_value) {
                  //echo '     '.$igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key].' != '.trim($row_value).'<br />';
                  if(isset($igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)]) && isset($igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key]) && $igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key] != '' && $igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key] != trim($row_value)) {
                    $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). CONTACT & BUDGET INFORMATION Sheet has missing cell '.$igf_ref_sheets_2[$Key][$row_key].'.\n';
                    break;
                  }
                }
              }
              else {
                //echo 'ROW['.$Key.'] is empty.<br />';
                $igf_ref_sheets_2_empty++;
              }
            }

            $igf_keys['contact_budget'] = $igf_ref_sheets_2_key;
            // Validate Sheet 3 => 3. SERVER DETAILS
            $igf_ref_sheets_3 = array();
            $igf_ref_sheets_3[1] = array(
                                    'APPLICATION NAME',
                                    'REQUESTOR GROUP',
                                    'REQUESTOR SUB-GROUP',
                                    'ENVIRONMENT',
                                    'LOCATION',
                                    'SERVER HALL',
                                    'ROW-RACK',
                                    'RACK NAME',
                                    'RACK "U"',
                                    'SLOT NO.',
                                    'SERVER NUMBER',
                                    'SERVER TYPE',
                                    'HYPERVISOR',
                                    'SERVER ROLE',
                                    'SERVER SERIAL NUMBER',
                                    'SERVER MAKE',
                                    'SERVER MODEL',
                                    'CPU TYPE',
                                    '# of CPU / vCPU',
                                    'TOTAL # of COREs',
                                    'RAM (GB)',
                                    '# of INTERNAL HDDs',
                                    'SIZE - INTERNAL DISKS',
                                    'RAID CONFIG - INTERNAL DISKS (RAID 1 / RAID 5 / RAID 1+0)',
                                    '# of NICs - 1G',
                                    '# of NICs - 10G',
                                    '# of FC HBA CARDS',
                                    '# of FC HBA PORTS',
                                    'FC HBA PORT SPEED',
                                    '# of DATA LAN PORTS',
                                    'DATA LAN INTERFACE TYPE',
                                    'DATA LAN INTERFACE SPEED',
                                    '# of SERVER LAN PORTS',
                                    'SERVER LAN INTERFACE TYPE',
                                    'SERVER LAN INTERFACE SPEED',
                                    '# of CLUSTER LAN PORTS',
                                    'CLUSTER LAN INTERFACE TYPE',
                                    'CLUSTER LAN INTERFACE SPEED',
                                    'NETWORK ZONE',
                                    'NETWORK SUB ZONE',
                                    'LOAD BALANCER REQUIRED',
                                    'HA / CLUSTER',
                                    'HA TYPE / CLUSTER SOFTWARE',
                                    'HA / CLUSTER PAIR NUMBER',
                                    'OS',
                                    'OS VERSION',
                                    'DB',
                                    'DB VERSION',
                                    'EXTERNAL STORAGE TYPE',
                                    'STORAGE IOPS',
                                    'STORAGE ARRAY',
                                    'EXTERNAL STORAGE RAID CONFIG',
                                    'EXTERNAL STORAGE USABLE SPACE- P-VOL (in GB)',
                                    'S-VOL (BCV) REQUIRED',
                                    'EXTERNAL STORAGE USABLE SPACE- S-VOL (in GB)',
                                    'FILE SYSTEM DETAILS - INTERNAL HDD',
                                    'FILE SYSTEM DETAILS - EXTERNAL STORAGE',
                                    'VOLUME MANAGER',
                                    'KERNEL PARAMETERS',
                                    'ADDITIONAL PACKAGES',
                                    'USER ID : GORUP ID : HOME DIR',
                                    'IDC SUPPORT REQUIREMENT',
                                    'REMARKS / ADDITIONAL NOTES',
                                    'REMOVE - RAM',
                                    'REMOVE - HDD',
                                    'REMOVE - NIC',
                                    'REMOVE - FC HBA',
                                    'ADD - RAM',
                                    'ADD - HDD',
                                    'ADD - NIC',
                                    'ADD - FC HBA',
                                    'HOSTNAME',
                                    'CONSOLE IP (iLO / RSC)',
                                    'SUBNET MASK',
                                    'GATEWAY',
                                    'DATA IP 1',
                                    'DATA IP 2',
                                    'VIP',
                                    'SUBNET MASK',
                                    'GATEWAY',
                                    'LB IP',
                                    'OTHER IP',
                                    'SM',
                                    'GW',
                                    'PUBLIC IP'
                                  );
            $spreadsheet -> ChangeSheet(3);

            foreach ($spreadsheet as $Key => $Row) {
              // Check if this row is empty or not
              $check_row_empty = TRUE;
              foreach ($Row as $row_key => $row_value) {
                if(trim(preg_replace('/\s+/', ' ', $row_value)) != ''){
                  $check_row_empty = FALSE;
                }
              }

              if($check_row_empty == FALSE) {
                $validate_model = FALSE;
                foreach ($Row as $row_key => $row_value) {
                  if($Key < 2){
                    if(isset($igf_ref_sheets_3[$Key][$row_key]) && $igf_ref_sheets_3[$Key][$row_key] != '' ) {
                      // Replace line end with space
                      // trim(preg_replace('/\s+/', ' ', $row_value))
                      //echo $row_key.' - '.$row_value.'<br />';
                      if($igf_ref_sheets_3[$Key][$row_key] == 'USER ID : GORUP ID : HOME DIR') {
                        if(!in_array(trim(preg_replace('/\s+/', ' ', $row_value)), array('USER ID : GORUP ID : HOME DIR', 'USER ID : GROUP ID : HOME DIR'))) {
                          $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). SERVER DETAILS Sheet has missing cell '.$igf_ref_sheets_3[$Key][$row_key].'.\n';
                          break;
                        }
                      }
                      else {
                        if($igf_ref_sheets_3[$Key][$row_key] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                          $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). SERVER DETAILS Sheet has missing cell '.$igf_ref_sheets_3[$Key][$row_key].'.\n';
                          break;
                        }
                      }
                    }
                  }
                  else {
                    // Validate fileds
                    //echo 'Validate filed : '.$row_value.'<br />';
                    //echo 'Validate filed : '.$row_key.' - '.$row_value.'<br />';
                    if($row_key < 65 ) {
                      $row_value_sanitize = trim(preg_replace('/\s+/', ' ', $row_value));
                      // Validate for mandatory fileds in data
                      if($row_key < 4 ) {
                        if($row_value_sanitize == '') {
                          $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has missing mandatory cell value on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                        }
                        else {
                          if($validate_igf_other_col) {
                            // Validate Sub Usergroup
                            if($row_key == 2 && !in_array(strtoupper($row_value_sanitize), $data_distinct_sub_group_v3)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            // Validate Environment
                            if($row_key == 3 && !in_array(strtoupper($row_value_sanitize), $data_distinct_env)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                          }
                        }
                      }

                      if($validate_igf_other_col) {
                        // Validate other columns
                        switch($row_key) {
                          case '4':
                            // Validate Location
                            if($row_value_sanitize != '' && !in_array(strtoupper($row_value_sanitize), $igf_v3_loc)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                          case '5':
                            // Validate Server Hall
                            if($row_value_sanitize != '' && !in_array($row_value_sanitize, $igf_v3_sh)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                            // Validate Rack U
                            /*
                            if($row_value_sanitize != '' && ( preg_match("/^0/", strtoupper($row_value_sanitize))  || !in_array($row_value_sanitize, $data_distinct_rack_u))  ) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            */
                            break;
                          case '9':
                            // Validate Slot
                            if($row_value_sanitize != '' && ( preg_match("/^0/", strtoupper($row_value_sanitize)) || !in_array($row_value_sanitize, $data_distinct_rack_slot)) ) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                          case '11':
                            // Validate Server Type
                            if($row_value_sanitize != '' && !in_array(strtoupper($row_value_sanitize), $data_distinct_server_type)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                          case '15':
                            // Validate Server Make (Vendor)
                            if($row_value_sanitize != '' && !in_array(strtoupper($row_value_sanitize), $data_distinct_vendor)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            else {
                              if(strtoupper($row_value_sanitize) == 'HP') {
                                $validate_model = TRUE;
                              }
                              else {
                                $validate_model = FALSE;
                              }
                            }
                            break;
                          case '16':
                            // Validate Server Model
                            if($row_value_sanitize != '' && $validate_model && !in_array(strtoupper($row_value_sanitize), $data_distinct_vendor_model)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                          case '44':
                            // Validate OS
                            $data_distinct_os[] = 'OTHERS';
                            if($row_value_sanitize != '' && !in_array(strtoupper($row_value_sanitize), $data_distinct_os)) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                          case '45':
                            // Validate OS Version
                            if($row_value_sanitize != '' && !in_array(strtoupper($row_value_sanitize), $data_distinct_os_version )) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                          case '61':
                            // Validate IDC SUPPORT REQUIREMENT
                            //echo $row_key.' ->'.$row_value_sanitize.'<br />';
                            if($row_value_sanitize != '' && !in_array(strtoupper($row_value_sanitize), $data_distinct_support )) {
                              $pei_messages['error'][] = 'Invalid IGF ('.$igf_file_name.'). SERVER DETAILS Sheet has invalid cell value ('.pei_display_string($row_value_sanitize).') on row '.($Key + 1).' & column '.$igf_ref_sheets_3[1][$row_key].'.';
                            }
                            break;
                        }
                      }
                    }
                    else {
                      break;
                    }
                  }
                }
              }
              else {
                //echo 'ROW EMPTY <br />';
              }
            }


            // Validate Sheet 4 => 4. EQUIPMENT LIST
            $igf_ref_sheets_4 = array(
                                  array(
                                    'LOCATION',
                                    'EQUIPMENT TYPE',
                                    'MAKE',
                                    'MODEL',
                                    'RACK SPACE (RU)',
                                    'NO OF INTERFACES',
                                    'INTERFACE SPEED',
                                    'INTERFACE CONNECTIVITY TYPE (COPPER/ FIBER)',
                                    'TYPE OF POWER SUPPLY (AC / DC)',
                                    '# OF POWER SUPPLIES',
                                    'POWER SUPPLY CONNECTOR TYPE'
                                  ),
                                );
            $spreadsheet -> ChangeSheet(4);

            foreach ($spreadsheet as $Key => $Row) {
              foreach ($Row as $row_key => $row_value) {
                if(isset($igf_ref_sheets_4[$Key][$row_key]) && $igf_ref_sheets_4[$Key][$row_key] != '' ) {
                  if($igf_ref_sheets_4[$Key][$row_key] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                    $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). EQUIPMENT LIST Sheet has missing cell '.$igf_ref_sheets_4[$Key][$row_key].'.\n';
                    break;
                  }
                }
              }
            }

            // Validate Sheet 5 => 5. SOFTWARE LIST
            $igf_ref_sheets_5 = array(
                                  array(
                                    'SR. NO.',
                                    'SOFTWARE CATEGORY (APPLICATION / WEB / DB)',
                                    'VENDOR NAME',
                                    'PRODUCT NAME',
                                    'SOFTWARE EDITION',
                                    'SOFTWARE VERSION',
                                    'BASE OS',
                                    'LICENSING TYPE (BASED ON USERS / CPU / CORE/ ANY OTHER)',
                                    '# OF LICENCES REQUIRED',
                                    'SOFTWARE SUPPORT REQUIRED (YES / NO)'
                                  ),
                                );
            $spreadsheet -> ChangeSheet(5);

            foreach ($spreadsheet as $Key => $Row) {
              foreach ($Row as $row_key => $row_value) {
                if(isset($igf_ref_sheets_5[$Key][$row_key]) && $igf_ref_sheets_5[$Key][$row_key] != '' ) {
                  if($igf_ref_sheets_5[$Key][$row_key] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                    $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). EQUIPMENT LIST Sheet has missing cell '.$igf_ref_sheets_5[$Key][$row_key].'.\n';
                    break;
                  }
                }
              }
            }


            /*
            // Validate Sheet 7 => 7. Patching
            // If it exits and data is not blank
            if(isset($igf_keys['patching_sheet_key'])  && $igf_keys['patching_sheet_key']){
              $igf_ref_sheets_7     = array();
              $igf_ref_sheets_7[0][1]  = array('key' => 'igf_patching_cable_detail', 'name' => 'CABLE DETAILS', 'ignore_key' => TRUE);
              $igf_ref_sheets_7[0][3]  = array('key' => 'igf_patching_source_rack', 'name' => 'SOURCE RACK', 'ignore_key' => TRUE);
              $igf_ref_sheets_7[0][8]  = array('key' => 'igf_patching_destination_rack', 'name' => 'DESTINATION RACK', 'ignore_key' => TRUE);
              $igf_ref_sheets_7[1]  = array(
                                        array('key' => 'igf_patching_sh', 'name' => 'Server Hall'),
                                        array('key' => 'igf_patching_cable', 'name' => 'Cable Type'),
                                        array('key' => 'igf_patching_cable_length', 'name' => 'Length'),
                                        array('key' => 'igf_patching_src_rack', 'name' => 'Source Rack'),
                                        array('key' => 'igf_patching_src_u', 'name' => 'U Location'),
                                        array('key' => 'igf_patching_src_sr', 'name' => 'Server serial No'),
                                        array('key' => 'igf_patching_src_port', 'name' => 'System/Port'),
                                        array('key' => 'igf_patching_src_label', 'name' => 'LABEL(Filled by Sigma-Byte)'),
                                        array('key' => 'igf_patching_dst_rack', 'name' => 'Destination Rack'),
                                        array('key' => 'igf_patching_dst_sr_u', 'name' => 'Serial No or U'),
                                        array('key' => 'igf_patching_dst_port', 'name' => 'System /Port'),
                                        array('key' => 'igf_patching_qty', 'name' => 'Qty'),
                                        array('key' => 'igf_patching_vlan', 'name' => 'VLAN', 'optional' => TRUE),
                                        array('key' => 'igf_patching_remark', 'name' => 'Remarks'),
                                      );
              $spreadsheet -> ChangeSheet($igf_keys['patching_sheet_key']);

              foreach ($spreadsheet as $Key => $Row) {
                foreach ($Row as $row_key => $row_value) {
                  if(isset($igf_ref_sheets_7[$Key][$row_key]['name']) && $igf_ref_sheets_7[$Key][$row_key]['name'] != '' ) {
                    if($igf_ref_sheets_7[$Key][$row_key]['name'] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                      if(isset($igf_ref_sheets_7[$Key][$row_key]['optional']) && $igf_ref_sheets_7[$Key][$row_key]['optional'] == TRUE){

                      }
                      elseif(isset($igf_ref_sheets_7[$Key][$row_key]['ignore_key']) && $igf_ref_sheets_7[$Key][$row_key]['ignore_key'] == TRUE){

                      }
                      else {
                        $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). Patching Sheet has missing cell '.$igf_ref_sheets_7[$Key][$row_key]['name'].'.\n';
                        break;
                      }
                    }
                  }
                }
              }
            }
            */
          }

          // END v3, v3.1 IGF validation and save
          break;
        case 'v4':
        default:
          // IGF v4 initialization
          $igf_v4_ref_sheets = array(
                              '0' => array('name' => 'COVER'),
                              '1' => array('name' => '1. GUIDELINES'),
                              '2' => array('name' => '2. CONTACT & BUDGET INFORMATION'),
                              '3' => array('name' => '3. EQUIPMENT DETAILS'),
                              '4' => array('name' => '4. DC SUPPORT TYPE'),
                              '5' => array('name' => '5. STORAGE TEMPLATE'),
                              '6' => array('name' => '6. PATCHING DETAILS'),
                            );

          $igf_ref_sheets_2 = array(
                      array('', 'CONTACT DETAILS', '', ''),
                      array('', 'REQUESTOR GROUP', '', ''),
                      array('', 'REQUESTOR SUB-GROUP', '', ''),
                      array('', 'PROJECT / SETUP NAME', '', ''),
                      array('', 'CONTACT DETAILS - PROJECT (SPOC)', '', ''),
                      array('', 'CONTACT DETAILS', 'CONTACT 1', 'CONTACT 2'),
                      array('', 'CONTACT (NAME)', '', ''),
                      array('', 'CONTACT (MOBILE)', '', ''),
                      array('', 'CONTACT (E-MAIL)', '', ''),
                      array('', 'BUDGET DETAILS (IF APPLICABLE)', '', ''),
                      array('', 'FUND CENTRE', '', ''),
                      array('', 'GL', '', ''),
                      array('', 'WBS', '', '')
                    );

          $igf_v4 = new SpreadsheetReader($req_doc_uploaded_igf_file['path']);
          if($igf_v4) {
            $igf_v4_sheets = $igf_v4->Sheets();
            foreach ($igf_v4_sheets as $index => $name) {
              if($index <= 6) {
                if($igf_v4_ref_sheets[$index]['name'] != $name) {
                  $pei_messages['error'][] = 'Invalid IGF Template. Download latest IGF Template from JioDC Portal -> <a href="http://jiodc.ril.com/site/form.html" target="_blank">Forms & Templates.';
                  break;
                }
              }

            }// END FOREACH $igf_v4_ref_sheets

            // Check if any has error occur during IGF v4 sheet validation
            // If no error found then process each sheet for data

            // Process "2. CONTACT & BUDGET INFORMATION" Sheet
            if(!isset($pei_messages['error'])) {
              $igf_v4->ChangeSheet(2);
              $igf_ref_sheets_2_empty = 0;
              foreach ($igf_v4 as $Key => $Row) {
                // Check if this row is empty or not
                $is_row_empty = TRUE;
                foreach ($Row as $row_key => $row_value) {
                  $row_col_sanitize_val = trim(preg_replace('/\s+/', ' ', $row_value));
                  if($row_col_sanitize_val != '') {
                    foreach ($igf_ref_sheets_2_temp as $test_key => $test) {
                      if(isset($test['name']) && trim(preg_replace('/\s+/', ' ', $row_value)) == $test['name'] ) {
                        $test['index'] = $Key;
                        $igf_ref_sheets_2_key[$test['key']] = $test;
                        $igf_ref_sheets_2_temp[$test_key]   = '';
                        break;
                      }
                    }
                    $is_row_empty = FALSE;
                  }
                }

                if(!$is_row_empty) {
                  foreach ($Row as $row_key => $row_value) {
                    $row_col_sanitize_val = trim(preg_replace('/\s+/', ' ', $row_value));
                    if($row_col_sanitize_val != '') {
                      if(isset($igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)]) && isset($igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key]) && $igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key] != '' && $igf_ref_sheets_2[($Key - $igf_ref_sheets_2_empty)][$row_key] != $row_col_sanitize_val) {
                        $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). CONTACT & BUDGET INFORMATION Sheet has missing cell '.$igf_ref_sheets_2[$Key][$row_key];
                      }

                      if($row_col_sanitize_val == 'CONTACT (E-MAIL)') {
                        $data_email_col = array(2 => 'C', 3 => 'D');
                        // Check for valid email address
                        for($i=2;$i<=3;$i++) {
                          $contact_email  = '';
                          $contact_email  = trim(preg_replace('/\s+/', ' ', $Row[$i]));
                          if($contact_email) {
                            if(substr_count($contact_email, '@') != 1) {
                              $pei_messages['error'][] = 'CHECK IGF...TAB-CONTACT & BUDGET INFORMATION - '.$data_email_col[$i].'12 ('.pei_display_string($contact_email).') - ONLY ONE EMAIL ID ALLOWED.';
                            }
                            else {
                              if (!filter_var($contact_email, FILTER_VALIDATE_EMAIL)) {
                                $pei_messages['error'][] = 'CHECK IGF...TAB-CONTACT & BUDGET INFORMATION - '.$data_email_col[$i].'12 ('.pei_display_string($contact_email).') - INVALID EMAIL ID.';
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                } // END IF $is_row_not_empty
                else {
                  $igf_ref_sheets_2_empty++;
                }


              } // END FOREACH $igf_v4

              $igf_keys['contact_budget'] = $igf_ref_sheets_2_key;
            }

            // Process "3. EQUIPMENT DETAILS" Sheet
            $igf_ref_sheets_3 = array();
            $igf_ref_sheets_3[1] = array(
                            'APPLICATION NAME',
                            'REQUESTOR GROUP',
                            'REQUESTOR SUB-GROUP',
                            'ENVIRONMENT',
                            'LOCATION',
                            'SERVER HALL',
                            'ROW-RACK',
                            'RACK NAME',
                            'RACK "U"',
                            'SLOT NO.',
                            'EQUIPMENT NUMBER (S1 FOR SERVER, S1-V01 FOR VM1)',
                            'EQUIPMENT TYPE',
                            'HYPERVISOR',
                            'EQUIPMENT ROLE',
                            'EQUIPMENT SERIAL NUMBER (<Serial Number>-V01, V02 for VM)',
                            'EQUIPMENT MAKE',
                            'EQUIPMENT MODEL',
                            'CPU TYPE',
                            '# of CPU',
                            'TOTAL # of COREs',
                            'RAM (GB)',
                            '# of INTERNAL HDDs',
                            'SIZE - INTERNAL DISKS',
                            'RAID CONFIG - INTERNAL DISKS',
                            '# of NICs - 1G',
                            '# of NICs - 10G',
                            '# of FC HBA CARDS',
                            '# of FC HBA PORTS',
                            'FC HBA PORT SPEED',
                            '# of DATA LAN PORTS',
                            'DATA LAN INTERFACE TYPE',
                            'DATA LAN INTERFACE SPEED',
                            '# of PRIVATE LAN PORTS',
                            'PRIVATE LAN INTERFACE TYPE',
                            'PRIVATE LAN INTERFACE SPEED',
                            '# of CLUSTER LAN PORTS',
                            'CLUSTER LAN INTERFACE TYPE',
                            'CLUSTER LAN INTERFACE SPEED',
                            'NETWORK ZONE',
                            'NETWORK SUB ZONE',
                            'LOAD BALANCER REQUIRED',
                            'HA / CLUSTER',
                            'HA TYPE / CLUSTER SOFTWARE',
                            'HA / CLUSTER PAIR NUMBER',
                            'OS',
                            'OS VERSION',
                            'DB',
                            'DB VERSION',
                            'EXTERNAL STORAGE TYPE',
                            'STORAGE IOPS',
                            'STORAGE ARRAY',
                            'EXTERNAL STORAGE RAID CONFIG',
                            'EXTERNAL STORAGE USABLE SPACE- P-VOL (in GB) (BSS-OSS TO PROVIDE DETAILS IN TAB 5)',
                            'S-VOL (BCV) REQUIRED',
                            'EXTERNAL STORAGE USABLE SPACE- S-VOL (in GB)',
                            'FILE SYSTEM DETAILS - INTERNAL HDD',
                            'FILE SYSTEM DETAILS - EXTERNAL STORAGE (BSS-OSS TO PROVIDE DETAILS IN TAB 5)',
                            'VOLUME MANAGER',
                            'KERNEL PARAMETERS',
                            'ADDITIONAL PACKAGES',
                            'USER ID : GROUP ID : HOME DIR',
                            'IDC SUPPORT REQUIREMENT (REFER TAB 4)',
                            'REMARKS / ADDITIONAL NOTES',
                            'REMOVE - RAM',
                            'REMOVE - HDD',
                            'REMOVE - NIC',
                            'REMOVE - FC HBA',
                            'ADD - RAM',
                            'ADD - HDD',
                            'ADD - NIC',
                            'ADD - FC HBA',
                            'HOSTNAME',
                            'CONSOLE IP (iLO / RSC)',
                            'SUBNET MASK',
                            'GATEWAY',
                            'DATA LAN IP 1',
                            'DATA LAN IP 2',
                            'DATA LAN VIP',
                            'DATA LAN SUBNET MASK',
                            'DATA LAN GATEWAY',
                            'LB IP',
                            'PUBLIC IP',
                            'EQUIPMENT (PRIVATE) LAN IP',
                            'EQUIPMENT (PRIVATE) SUBNET MASK',
                            'RAC IP',
                            'SCAN IP',
                            'Heartbeat IP',
                            'Cluster Interconnect PRIVATE IP',
                            'Oracle VIP',
                          );
            if(!isset($pei_messages['error'])) {
              $igf_v4->ChangeSheet(3);

              foreach ($igf_v4 as $Key => $Row) {
                // Check if this row is empty or not
                $is_row_empty = TRUE;
                foreach ($Row as $row_key => $row_value) {
                  $row_col_sanitize_val = trim(preg_replace('/\s+/', ' ', $row_value));
                  //echo '[INFO]  '.$row_key.' : '.$row_col_sanitize_val.'<br />';
                  if($row_col_sanitize_val != ''){
                    $is_row_empty = FALSE;
                    break;
                  }
                }

                if(!$is_row_empty) {
                  // Sheet 3 Header validate
                  foreach ($Row as $row_key => $row_value) {
                    $row_col_sanitize_val = trim(preg_replace('/\s+/', ' ', $row_value));
                    if($Key < 2) {
                      //echo '[INFO]  $Key :'.$Key.',   $row_key:'.$row_key.'<br />';
                      if(isset($igf_ref_sheets_3[$Key]) && isset($igf_ref_sheets_3[$Key][$row_key]) && $igf_ref_sheets_3[$Key][$row_key] != '' ) {
                        //echo [INFO] $igf_ref_sheets_3[$Key][$row_key].' != '.$row_col_sanitize_val.'<br />';
                        if($igf_ref_sheets_3[$Key][$row_key] != $row_col_sanitize_val) {
                          $pei_messages['error'][] = 'Invalid IGF Template ('.$igf_file_name.'). EQUIPMENT DETAILS Sheet has missing cell '.$igf_ref_sheets_3[$Key][$row_key].'.';
                        }
                      }
                    }
                    else {
                      // Validate IGF Data
                      switch($row_key) {
                        case '0':
                          // APPLICATION NAME
                          if(!$row_col_sanitize_val) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - A'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - MISSING MANDATORY VALUE.';
                          }
                          break;
                        case '1':
                          // REQUESTOR GROUP
                          if(!$row_col_sanitize_val) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - B'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - MISSING MANDATORY VALUE.';
                          }
                          else {
                            if(!in_array(strtoupper($row_col_sanitize_val), $data_distinct_user_group)) {
                              $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - B'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                            }
                          }
                          break;
                        case '2':
                          // REQUESTOR SUB-GROUP
                          if(!$row_col_sanitize_val) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - C'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - MISSING MANDATORY VALUE.';
                          }
                          else {
                            if(!in_array(strtoupper($row_col_sanitize_val), $data_distinct_sub_group)) {
                              $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - C'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                            }
                          }
                          break;
                        case '3':
                          // ENVIRONMENT
                          if(!$row_col_sanitize_val) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - D'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - MISSING MANDATORY VALUE.';
                          }
                          else {
                            if(!in_array(strtoupper($row_col_sanitize_val), $data_distinct_env)) {
                              $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - D'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                            }
                          }
                          // Capture all ENV specified in IGF
                          if($row_col_sanitize_val) {
                            $igf_env[] = $row_col_sanitize_val;
                          }
                          break;
                        case '4':
                          // LOCATION
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_loc)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - E'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          // Capture all LOCATION specified in IGF
                          if($row_col_sanitize_val) {
                            $igf_loc[] = $row_col_sanitize_val;
                          }
                          break;
                        case '5':
                          // SERVER HALL
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_sh)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - F'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          // Capture all LOCATION specified in IGF
                          if($row_col_sanitize_val) {
                            $igf_sh[] = $row_col_sanitize_val;
                          }
                          break;
                        case '6':
                          // ROW-RACK
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_rr)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - G'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '11':
                          // EQUIPMENT TYPE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_eqpt_type)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - L'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '12':
                          // HYPERVISOR
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_hypervisor_type)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - M'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '13':
                          // EQUIPMENT ROLE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_eqpt_role)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - N'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '14':
                          // EQUIPMENT SERIAL NUMBER
                          // FOR HP Make check if Serial Number exist in STORE data
                          // Validate only before RFI
                          if(!$req_status) {
                            if($row_col_sanitize_val) {
                              $sr_eqpt_type   = trim(preg_replace('/\s+/', ' ', $Row[11]));
                              $serial_no_make = '';
                              $serial_no_make = trim(preg_replace('/\s+/', ' ', $Row[15]));
                              $serial_no_loc  = trim(preg_replace('/\s+/', ' ', $Row[4]));
                              $serial_no_sh   = trim(preg_replace('/\s+/', ' ', $Row[5]));
                              $data_dc_loc    = config_value('IGF', 'DC LOCATION', $igf_version);
                              $data_ag3_sh    = array('SH09');

                              if(strtoupper($sr_eqpt_type) == 'SERVER-PHYSICAL' && strtoupper($serial_no_make) == 'HP' && in_array(strtoupper($serial_no_loc), $data_dc_loc) && !in_array($serial_no_sh, $data_ag3_sh) ) {
                                // Find if this serial number exist in STORE or NOT
                                $valid_sr_no = validation_eqpt_serial_number($row_col_sanitize_val, 'HP', 'SERVER', strstr($serial_no_loc, '-', true));
                                if($valid_sr_no === TRUE) {

                                }
                                else {
                                  if($valid_sr_no === 'ALREADY ISSUED') {
                                    $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - O'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - EQUIPMENT ALREADY ISSUED FROM JioDC Portal STORES';
                                  }
                                  else {
                                    $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - O'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - EQUIPMENT NOT AVAILABLE IN JioDC Portal STORES';
                                  }
                                }
                              }
                            }
                          }
                          break;
                        case '15':
                          // EQUIPMENT MAKE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_vendor)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - P'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '24':
                          // # of NICs - 1G
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_nic_1g)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - Y'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '25':
                          // # of NICs - 10G
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_nic_10g)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - Z'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '26':
                          // # of FC HBA CARDS
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_fc_hba)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AA'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '27':
                          // # of FC HBA PORTS
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_fc_hba_ports)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AB'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '28':
                          // FC HBA PORT SPEED
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_fc_hba_speed)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AC'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '29':
                          // # of DATA LAN PORTS
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_ports)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AD'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '32':
                          // # of PRIVATE LAN PORTS
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_ports)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AG'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '35':
                          // # of CLUSTER LAN PORTS
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_ports)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AJ'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '30':
                          // DATA LAN INTERFACE TYPE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_inter)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AE'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '33':
                          // PRIVATE LAN INTERFACE TYPE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_inter)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AH'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '36':
                          // CLUSTER LAN INTERFACE TYPE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_inter)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AK'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '31':
                          // DATA LAN INTERFACE SPEED
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_speed)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AF'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '34':
                          // PRIVATE LAN INTERFACE SPEED
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_speed)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AI'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '37':
                          // CLUSTER LAN INTERFACE SPEED
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_lan_speed)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AL'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '38':
                          // NETWORK ZONE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_nw_zone)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AM'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '39':
                          // NETWORK SUB ZONE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_nw_sub_zone)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AN'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '40':
                          // LOAD BALANCER REQUIRED
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_yes_no)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AO'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '41':
                          // HA / CLUSTER
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_yes_no)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AP'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '42':
                          // HA TYPE / CLUSTER SOFTWARE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_ha_soft)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AQ'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '44':
                          // OS
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_os)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AR'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '46':
                          // DB
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_db)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AU'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '48':
                          // EXTERNAL STORAGE TYPE
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_ext_storage_type)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AW'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '50':
                          // STORAGE ARRAY
                          if(!$row_col_sanitize_val) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AY'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - MISSING MANDATORY VALUE.';
                          }
                          else {
                            if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_storage_array)) {
                              $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AY'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                            }
                          }
                          break;
                        case '51':
                          // EXTERNAL STORAGE RAID CONFIG
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_ext_storage_raid)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - AZ'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '57':
                          // VOLUME MANAGER
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_vol_mg)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - BF'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '61':
                          // IDC SUPPORT REQUIREMENT
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_idc_support)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - BJ'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '65':
                          // REMOVE - NIC
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_add_nic)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - BN'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '69':
                          // ADD - NIC
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_add_nic)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - BR'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '66':
                          // REMOVE - FC HBA
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_add_fc_hba)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - BO'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                        case '70':
                          // ADD - FC HBA
                          if($row_col_sanitize_val && !in_array(strtoupper($row_col_sanitize_val), $data_distinct_add_fc_hba)) {
                            $pei_messages['error'][] = 'CHECK IGF...TAB-EQUIPMENT DETAILS - BQ'.($Key + 1).' ('.pei_display_string($row_col_sanitize_val).') - INVALID VALUE.';
                          }
                          break;
                      } // END SWITCH $row_key

                    }
                  }
                }// END IF $is_row_empty
              }


              // Validate only before RFI
              if(!$req_status) {
                // Check if all ENV mention IGF are selected in REQUEST FORM
                if($req_env) {
                  foreach ($req_env as $key => $value) {
                    $req_env_detail = environment_detail($value);
                    if($req_env_detail) {
                      $req_env_abb = '';
                      $req_env_abb = strtoupper($req_env_detail['env_name_abbrev']);
                      if($req_env_abb && !in_array($req_env_abb, $igf_env)) {
                        $pei_messages['error'][] = 'MISMATCH - ENVIRONMENT SELECT IN FORM ARE NOT MENTION IN IGF.';
                        break;
                      }
                    }
                  }
                }

                // Check if all LOCATION mention IGF are selected in REQUEST FORM
                $flag_igf_loc = TRUE;
                $data_loc_ag3 = '';
                $igf_loc_ag3  = '';
                $AG3_loc = location_by_key_value('loc_facility', 'AG3');
                if($AG3_loc) {
                  foreach ($AG3_loc as $key => $value) {
                    $data_loc_ag3[] = $value['loc_name'];
                  }
                }

                // FIND AG3 LOCATION Mention in IGF
                if($igf_loc) {
                  foreach ($igf_loc as $key => $value) {
                    if(substr($value, 0, 3) == 'AG3') {
                      $igf_loc_ag3[] = $value;
                    }
                  }
                }

                if($req_loc) {
                  foreach ($req_loc as $key => $value) {
                    $req_loc_detail = location_detail($value);

                    if($req_loc_detail) {
                      switch ($req_loc_detail['loc_cmdb_fac']) {
                        case 'D':
                          if(!in_array($req_loc_detail['loc_name'], $igf_loc)) {
                            $flag_igf_loc = FALSE;
                          }
                          break;
                        case 'A':
                          foreach ($igf_loc_ag3 as $key => $value) {
                            if(!in_array($value, $data_loc_ag3)) {
                              $flag_igf_loc = FALSE;
                              break;
                            }
                          }
                          break;
                        default:
                          if(!in_array('OTHER LOCATION', $igf_loc)) {
                            $flag_igf_loc = FALSE;
                            break;
                          }
                      } // END SWITCH
                    } // END IF $req_loc_detail

                    if($flag_igf_loc == FALSE) {
                      $pei_messages['error'][] = 'MISMATCH - LOCATION SELECT IN FORM ARE NOT MENTION IN IGF.';
                      break;
                    }
                  } // END FOREACH
                }

                // Check if all SH mention IGF are selected in REQUEST FORM
                if($req_sh) {
                  foreach ($req_sh as $key => $value) {
                    $req_sh_detail = server_hall_detail($value);
                    if($req_sh_detail) {
                      $req_sh_name = '';
                      $req_sh_name = strtoupper($req_sh_detail['sh_name']);
                      if($req_sh_name && !in_array($req_sh_name, $igf_sh)) {
                        $pei_messages['error'][] = 'MISMATCH - SERVER HALL SELECT IN FORM ARE NOT MENTION IN IGF.';
                        break;
                      }
                    }
                  }
                } // END IF $req_sh
              }

            } // END Process "3. EQUIPMENT DETAILS" Sheet


          } // END IF $igf_v4
          else {
            $pei_messages['error'][] = 'Something went wrong while reading IGF file ['.$_FILES['req_doc_uploaded_igf']['name'].'] \n';
          }

      }// SWITCH END $igf_version

    } // END IF $req_doc_uploaded_igf_file


    $req_doc_uploaded         = $_POST['req_doc_uploaded'];
    $req_doc_uploaded_del     = $_POST['req_doc_uploaded_del'];
    $req_doc_uploaded_update  = $_POST['req_doc_uploaded_update'];
    $req_doc_type             = $_POST['req_doc_type'];

    $req_doc_uploaded_igf         = $_POST['req_doc_uploaded_igf'];
    $req_doc_uploaded_del_igf     = $_POST['req_doc_uploaded_del_igf'];
    $req_doc_uploaded_update_igf  = $_POST['req_doc_uploaded_update_igf'];

    $req_mat_serial       = isset($_POST['req_mat_serial']) ? $_POST['req_mat_serial'] : $req_mat_serial;
    $data_request_mat['req_mat_serial_nos'] = $req_mat_serial;
    $req_mat_ram          = isset($_POST['req_mat_ram']) ? $_POST['req_mat_ram'] : $req_mat_ram;
    $data_request_mat['req_mat_ram'] = $req_mat_ram;
    $req_mat_ram_ret      = isset($_POST['req_mat_ram_ret']) ? $_POST['req_mat_ram_ret'] : $req_mat_ram_ret;
    $data_request_mat['req_mat_ram_ret'] = $req_mat_ram_ret;
    $req_mat_hdd          = isset($_POST['req_mat_hdd']) ? $_POST['req_mat_hdd'] : $req_mat_hdd;
    $data_request_mat['req_mat_hdd'] = $req_mat_hdd;
    $req_mat_hdd_ret      = isset($_POST['req_mat_hdd_ret']) ? $_POST['req_mat_hdd_ret'] : $req_mat_hdd_ret;
    $data_request_mat['req_mat_hdd_ret'] = $req_mat_hdd_ret;
    $req_mat_nic          = isset($_POST['req_mat_nic']) ? $_POST['req_mat_nic'] : $req_mat_nic;
    $data_request_mat['req_mat_nic'] = $req_mat_nic;
    $req_mat_nic_ret      = isset($_POST['req_mat_nic_ret']) ? $_POST['req_mat_nic_ret'] : $req_mat_nic_ret;
    $data_request_mat['req_mat_nic_ret'] = $req_mat_nic_ret;
    $req_mat_fc_hba       = isset($_POST['req_mat_fc_hba']) ? $_POST['req_mat_fc_hba'] : $req_mat_fc_hba;
    $data_request_mat['req_mat_fc_hba'] = $req_mat_fc_hba;
    $req_mat_fc_hba_ret   = isset($_POST['req_mat_fc_hba_ret']) ? $_POST['req_mat_fc_hba_ret'] : $req_mat_fc_hba_ret;
    $data_request_mat['req_mat_fc_hba_ret'] = $req_mat_fc_hba_ret;
    $req_mat_additional   = isset($_POST['req_mat_additional']) ? $_POST['req_mat_additional'] : $req_mat_additional;
    $data_request_mat['req_mat_additional'] = $req_mat_additional;

    // Save Request
    if($req_msg == '' && !isset($pei_messages['error'])) {

      // Add new other user sub group
      if($add_group_sub_other) {
        $data_user_group['user_group_parent_id']  = $req_group;
        $data_user_group['user_group_name']       = $req_group_sub_other;
        $data_user_group['created_by']            = $uname;
        $data_user_group['created_at']            = 'NOW()';
        $req_group_sub = user_group_save($data_user_group);
      }
      $data_request['req_group_sub_id'] = $req_group_sub;

      // If req id is present then update request detail
      // Else add ne request detail
      if($req_id) {
        $data_request['updated_by'] = $uname;
        $data_request['updated_at'] = 'NOW';

        $req_id = request_save($data_request, $req_id);

        if($req_id) {
          // Update Project Manager Details
          request_pm_save_detail($req_id, $req_pm, $uname);

          //Update Envriorment details
          // Fetch Previous save request env and compare with current values
          $previous_req_env = fetch_req_env($req_id);
          if(!is_array($previous_req_env)){
            $previous_req_env = array();
          }
          // To remove extra env
          $remove_req_env = array_diff($previous_req_env, $req_env);
          if($remove_req_env){
            delete_req_env($req_id, $remove_req_env);
          }
          // To add new addition env
          $add_req_env = array_diff($req_env, $previous_req_env);
          if($add_req_env) {
            save_req_env($req_id, $add_req_env, $uname);
          }

          //Update Location details
          // Fetch Previous save request loc and compare with current values
          $previous_req_loc = fetch_req_loc($req_id);
          if(!is_array($previous_req_loc)){
            $previous_req_loc = array();
          }

          // To add new addition loc
          $add_req_loc = array_diff($req_loc, $previous_req_loc);
          if($add_req_loc) {
            save_req_loc($req_id, $add_req_loc, $uname);
          }

          // To remove extra loc
          $remove_req_loc = array_diff($previous_req_loc, $req_loc);
          if($remove_req_loc){
            delete_req_loc($req_id, $remove_req_loc);
          }

          //Update Server Hall details
          // Fetch Previous save request sh and compare with current values
          $previous_req_sh = fetch_req_sh($req_id);
          if(!is_array($previous_req_sh)){
            $previous_req_sh = array();
          }

          // To remove extra loc
          $remove_req_sh = array_diff($previous_req_sh, $req_sh);
          if($remove_req_sh) {
            delete_req_sh($req_id, $remove_req_sh);
          }

          // To add new addition sh
          $add_req_sh = array_diff($req_sh, $previous_req_sh);
          if($add_req_sh) {
            save_req_sh($req_id, $add_req_sh, $uname);
          }

          // Update Non IGF document
          // Update Non IGF document -- ADD
          if($req_doc_uploaded_files) {
            foreach ($req_doc_uploaded_files as $key => $file) {
              // Save Doc
              $data_req_doc = array(
                                'doc_name' => $file['name'],
                                'doc_file_name' => $file['name'],
                                'doc_file_path' => $file['path'],
                                'created_by'  => $uname,
                                'doc_type_id' => $req_doc_type
                              );
              $req_doc_id = pei_doc_save($data_req_doc);

              // Save Request Doc detail
              $data_request_doc = array(
                                'req_id' => $req_id,
                                'doc_id' => $req_doc_id,
                                'created_by' => $uname,
                                'created_at' => 'NOW()',
                              );
              request_doc_save($data_request_doc);
            }
          }

          // Update document -- Update
          if($req_doc_uploaded_update != '') {
            $req_doc_uploaded_update = rtrim($req_doc_uploaded_update, ',');
            $array_update = explode(",", $req_doc_uploaded_update);
            if($array_update) {
              foreach ($array_update as $key => $value) {
                $row_update = explode(":", $value);
                $request_doc_detail = request_doc_detail($row_update[0]);
                if($request_doc_detail){
                  // Update DOC Doc Type
                  $data_doc_type_req_doc = '';
                  $data_doc_type_req_doc['doc_type_id'] = $row_update[1];
                  $data_doc_type_req_doc['updated_by']  = $uname;
                  $data_doc_type_req_doc['updated_at']  = 'NOW';
                  doc_doc_type_save($data_doc_type_req_doc, $request_doc_detail['doc_doc_type_id']);

                  // Update request_doc
                  $data_req_doc_update = '';
                  $data_req_doc_update['updated_by']  = $uname;
                  $data_req_doc_update['updated_at']  = 'NOW';
                  request_doc_save($data_req_doc_update, $request_doc_detail['req_doc_id']);
                }
              }
            }
          }

          // Update document -- Delete
          if($req_doc_uploaded_del != '') {
            $req_doc_uploaded_del = rtrim($req_doc_uploaded_del, ',');
            $sql_del_req_doc = "DELETE FROM idc_request_doc ";
            $sql_del_req_doc .="WHERE req_doc_id IN (".mysql_real_escape_string($req_doc_uploaded_del).")";
            $res_del_req_doc = mysql_query($sql_del_req_doc);
            if(!$res_del_req_doc) {
              $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
              $req_msg .= 'Whole query: ' . $sql_del_req_doc;
              die($req_msg);
            }
          }

          // IGF UPDATE
          // Update IGF document -- Delete
          if($req_doc_uploaded_del_igf != '') {


            $req_doc_uploaded_del_igf = rtrim($req_doc_uploaded_del_igf, ',');
            $sql_del_req_doc_igf = "UPDATE idc_igf SET igf_deleted=1 ";
            $sql_del_req_doc_igf .="WHERE igf_id IN (".mysql_real_escape_string($req_doc_uploaded_del_igf).")";
            $res_del_req_doc_igf = mysql_query($sql_del_req_doc_igf);
            if(!$res_del_req_doc_igf) {
              $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
              $req_msg .= 'Whole query: ' . $sql_del_req_doc_igf;
              die($req_msg);
            }
          }

          // Save IGF detail
          if($req_doc_uploaded_igf_file) {
            switch($igf_version){
              case 'v3':
              case 'v3.1':
                save_igf_docs($req_doc_uploaded_igf_file, $req_id, $igf_keys);

                // Also make delete status as 1 for previous IGF
                $temp_all_igf   = fetch_req_igf($req_id);
                $temp_igf_count = 0;
                if($temp_all_igf) {
                  foreach ($temp_all_igf as $key => $temp_igf) {
                    if($temp_igf_count > 0) {
                      update_req_igf_delete($temp_igf['igf_id'], '1' , $uname);
                    }
                    $temp_igf_count++;
                  }
                }
                break;
              case 'v4':
              default:
                // First get previous igf_id's for this request
                $prev_igf_id = igf_request_igf($req_id, 0);

                $igf_id = request_igf_save($req_doc_uploaded_igf_file, $req_id, $igf_keys);

                if($igf_id) {
                  // Mark previous active IGF as deleted
                  $data_igf_prev = '';
                  if($prev_igf_id) {
                    foreach ($prev_igf_id as $key => $value) {
                      $data_igf_prev['igf_deleted'] = 1;
                      $data_igf_prev['updated_by']  = $uname;
                      $data_igf_prev['updated_at']  = 'NOW';
                      igf_save($data_igf_prev, $value['igf_id']);
                    }
                  }
                }
            } // END SWITCH $igf_version


          }// END IF $req_doc_uploaded_igf_file

          $data_request_mat['updated_by'] = $uname;
          $data_request_mat['updated_at'] = 'NOW';

          // Update Request Material Details
          request_material_save($data_request_mat, $req_id);

          $pei_messages['success'][] = 'Request updated successfully.';

  ?>
  <script type="text/javascript">
          if (confirm('Request updated successfully.')) {
            window.location.replace("<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php");
          }
  </script>
  <?php
        }
        else {
          $pei_messages['error'][] = 'Some thing went wrong while updating request.';
        }
      }
      else {
        //echo 'NEW REQUEST SAVE <br />';
        $data_request['req_date']   = 'NOW';
        $data_request['created_by'] = $uname;
        $data_request['created_at'] = 'NOW';

        // Add New Request Detail
        $req_id = request_save($data_request, $req_id);

        if($req_id) {
          // Add Project Manager Detail
          if($req_pm) {
            request_pm_save_detail($req_id, $req_pm, $uname);
          }

          // Update Enviroment Detail
          if($req_env) {
            save_req_env($req_id, $req_env, $uname);
          }

          // Update Location Detail
          if($req_loc) {
            save_req_loc($req_id, $req_loc, $uname);
          }

          // Update Location Detail
          if($req_sh) {
            save_req_sh($req_id, $req_sh, $uname);
          }

          if($req_doc_uploaded_files) {
            foreach ($req_doc_uploaded_files as $key => $file) {
              // Save Doc
              $data_req_doc = array(
                                'doc_name' => $file['name'],
                                'doc_file_name' => $file['name'],
                                'doc_file_path' => $file['path'],
                                'created_by'  => $uname,
                                'doc_type_id' => $req_doc_type
                              );
              $req_doc_id = pei_doc_save($data_req_doc);

              // Save Request Doc detail
              $data_request_doc = array(
                                'req_id' => $req_id,
                                'doc_id' => $req_doc_id,
                                'created_by'  => $uname,
                                'created_at'  => 'NOW',
                              );
              request_doc_save($data_request_doc);
            }
          }

          // Save IGF detail
          if($req_doc_uploaded_igf_file) {
            $igf_id = request_igf_save($req_doc_uploaded_igf_file, $req_id, $igf_keys);
          }// END IF $req_doc_uploaded_igf_file
          // Save IGF detail END

          $data_request_mat['req_id']     = $req_id;
          $data_request_mat['created_by'] = $uname;
          $data_request_mat['created_at'] = 'NOW';

          // Add Request Material Details
          request_material_save($data_request_mat);

          $pei_messages['success'][] = 'New Request successfully saved.';

?>
  <script type="text/javascript">
          if (confirm('New Request successfully saved.')) {
            window.location.replace("<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php");
          }
  </script>
<?php
        }
        else {
          $pei_messages['error'][] = 'Some thing went wrong while creating new request.';
        }
      }
    }
  }

  // Fetch IGF for request
  if($req_id){
    $req_igf = fetch_req_igf($req_id);
  }

  // Fetch Documents for request
  if($req_id) {
    $req_doc = fetch_req_doc($req_id);
  }

  if($req_env == ''){
    $req_env  = array();
  }

  if($req_loc == ''){
    $req_loc = array();
  }

  if($req_sh == ''){
    $req_sh = array();
  }

  if($req_group) {
    $data_req_sub_group = get_user_group_sub_group($req_group);
  }

  if($req_id){
    $req_status_history = get_request_status_history($req_id);
    if($req_status_history){
      foreach ($req_status_history as $history) {
        if($history['status_id'] == '3' || $history['status_id'] == '4' ) {
          $igf_doc_delete_flag = FALSE;
          break;
        }
      }
    }
  }

  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/index.php" >User Requests</a></li>
        <?php
        if($action == ('Edit' || 'View')) {
        ?>
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_list.php">View/Track Request Detail</a></li>
        <li class="active"><?php echo $action;?> Request #<?php echo $req_id;?></li>
        <?php
        }
        else {
        ?>
        <li class="active">Create Requests</li>
        <?php
        }
        ?>

        <div class="pei-breadcrumb-right">
          <a style="padding:2px !important" href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_validation_reference.php" target="_blank">IGF Validation Reference</a>
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

<form id="form-req-create" class="form-horizontal" role="form" enctype="multipart/form-data" method="POST" action="">
  <fieldset>
    <legend class="pei-legend-request">
      <a href="#" class="pei-no-underline">Request Information</a>
      <?php if($action != 'View') {?>
      <div style="float:right;padding-bottom:2px;padding-right:2px">
        <button type="submit" name="req_save_dummy" id="req-save-dummy" value="Save" class="btn btn-primary btn-label-left">Save</button>
      </div>
      <?php }?>
    </legend>
    <?php
      if($req_id){
    ?>
    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Request Id</label>
      <div class="col-sm-4">
      <input type="text" name="req_id" id="req-id" class="form-control form-control-text-upper" placeholder="Requestor Id" data-toggle="tooltip" data-placement="bottom" title="Request Id" value="<?php echo $req_id;?>" readonly>
      </div>
      <label class="col-sm-2 control-label pei-control-label">Request Date</label>
      <div class="col-sm-4">
      <input type="text" name="req_date" id="req-date" class="form-control form-control-text-upper" placeholder="Requestor Date" data-toggle="tooltip" data-placement="bottom" title="Request Date" value="<?php echo pei_date_format($req_date);?>" readonly>
      </div>
    </div>
    <?php
      }
    ?>
    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Requestor Group/ Sub Group *</label>
      <div class="col-sm-2">
        <select class="placeholder form-control form-control-text-upper" name="user_group" id="req-user-group">
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
      <div class="col-sm-2">
        <select class="placeholder form-control form-control-text-upper" name="user_group_sub" id="req-sub-group">
          <option value="">-- Select a sub group --</option>
          <?php
          if($data_req_sub_group) {
            foreach ($data_req_sub_group as $key => $user_group) {
        ?>
          <option value="<?php echo $user_group['user_group_id'];?>" <?php if($req_group_sub ==  $user_group['user_group_id']) { ?> selected="selected" <?php } ?>><?php echo strtoupper($user_group['user_group_name']);?></option>
        <?php
            }
          }
          ?>
        </select>
      </div>


      <label class="col-sm-2 control-label pei-control-label" for="req-requestor">Request Initiator *</label>
      <div class="col-sm-4">
        <input type="text" name="requestor" id="req-requestor"  class="form-control form-control-text-upper" placeholder="Requestor Name" data-toggle="tooltip" data-placement="bottom" title="Request initiator name" value="<?php echo $req_requestor;?>">
      </div>
    </div>

    <div class="form-group pei-form-group hide" id="custom-sub-group">
      <label class="col-sm-2 control-label">&nbsp</label>
      <div class="col-sm-4">
        <input type="text" name="user_group_sub_other" id="req-group-sub-other" class="form-control form-control-text-upper" placeholder="New Request Sub Group Name" data-toggle="tooltip" data-placement="bottom" title="New Request Sub Group Name" value="<?php echo $req_user_group_sub_other;?>">
      </div>
    </div>
    <div class="clearfix"></div>

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Request Title *</label>
      <div class="col-sm-10">
        <input type="text" name="req_name" class="form-control form-control-text-upper" placeholder="Request Title" data-toggle="tooltip" data-placement="bottom" title="Request Title" value="<?php echo $req_name;?>">
      </div>

    </div>

    <div class="clearfix"></div>

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Project Manager (PM) / Implementation Manager (IM)</label>
      <div class="col-sm-4">
        <select class="placeholder form-control" name="req_pm" id="req-pm">
          <option value="">-- Select --</option>
      <?php
          foreach ($data_pm_user as $key => $user) {
        ?>
          <option value="<?php echo $user['user_login'];?>" <?php if(strtolower($req_pm) == strtolower($user['user_login'])){?> selected="selected" <?php }?> > <?php echo strtoupper($user['user_name']);?></option>
      <?php
          }
        ?>
        </select>
      </div>
      <label class="col-sm-2 control-label pei-control-label">JioDC SPOC * </label>
      <div class="col-sm-4">
        <select class="placeholder form-control" name="req_idc_spoc" id="req-idc-spoc">
          <option value="">-- Select --</option>
      <?php
          // Set Logged in user as default IDC SPOC
          if($req_idc_spoc == '') {
            $req_idc_spoc = strtolower($_SESSION['usr']);
          }
          foreach ($data_spoc_user as $key => $user) {
        ?>
          <option value="<?php echo $user['user_login'];?>" <?php if(strtolower($req_idc_spoc) == strtolower($user['user_login'])){?> selected="selected" <?php }?> > <?php echo strtoupper($user['user_name']);?></option>
      <?php
          }
        ?>
        </select>
      </div>

    </div>

    <div class="clearfix"></div>

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Environment *  </label>
      <div class="col-sm-10 panel panel-default pei-panel-default">
        <div class="checkbox">
          <label>
        <?php
        foreach ($data_env as $key => $env) {
        ?>
            <input type="checkbox" class="pei-request-checkbox" name="req_env[]" value="<?php echo $env['env_id'];?>" <?php if(in_array($env['env_id'], $req_env)){?> checked <?php }?>> <?php echo strtoupper($env['env_name']);?>
            <i class="fa fa-square-o small"></i>
        &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
        <?php
        }
        ?>
          </label>
        </div>
      </div>
    </div>

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Location *</label>
      <div class="col-sm-10 panel panel-default pei-panel-default">
        <div class="checkbox">
          <label>
        <?php
        foreach ($data_loc as $key => $loc) {
        ?>
            <input type="checkbox" class="pei-request-checkbox" name="req_loc[]" value="<?php echo $loc['loc_id'];?>" <?php if(in_array($loc['loc_id'], $req_loc)){?> checked <?php }?> > <?php echo $loc['loc_name'];?>
            <i class="fa fa-square-o small"></i>
            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
        <?php
        }
        ?>
          </label>
        </div>
      </div>
    </div>

    <div class="clearfix"></div>

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Server Hall </label>
      <div class="col-sm-10 panel panel-default pei-panel-default">
        <div class="checkbox">
          <label>
          <?php
          foreach ($data_server_hall as $key => $server_hall) {
          ?>
            <input type="checkbox" class="pei-request-checkbox" name="req_sh[]" value="<?php echo $server_hall['sh_id'];?>" <?php if(in_array($server_hall['sh_id'], $req_sh)){?> checked <?php }?>> <?php echo strtoupper($server_hall['sh_name']);?>
            <i class="fa fa-square-o small"></i>
            &nbsp&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp
          <?php
          }
          ?>
          </label>
        </div>
      </div>
    </div>
  <!--
    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Infrastructure </label>
      <div class="col-sm-10 panel panel-default pei-panel-default">
        <div class="radio">
          <label><input class="pei-request-checkbox" type="radio" name="req_infra" value="1" <?php if($req_infra == '1'){?> checked="checked" <?php }?> />YES</label>
          <label><input class="pei-request-checkbox" type="radio" name="req_infra" value="0" <?php if($req_infra == '0'){?> checked="checked" <?php }?> />NO</label>
        </div>
      </div>
    </div>
    -->

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Remarks / Additional Info </label>
      <div class="col-sm-10">
        <textarea class="form-control" name="req_remark" placeholder="Remarks / Additional Info" data-toggle="tooltip" data-placement="bottom" title="Remarks / Additional Info"><?php echo $req_remark;?></textarea>
      </div>
    </div>

  </fieldset>

  <!-- UPLOAD DOCUMENTS -->
  <fieldset>
    <legend class="pei-legend-request"><a href="#" class="pei-no-underline">Upload Documents</a></legend>
    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">IGF Documents</label>
      <?php
      if($action != 'View') {
      ?>
      <div class="col-sm-2">
        <input type="file" name="req_doc_uploaded_igf" id="req-doc-uploaded-igf" <?php if(!$igf_doc_delete_flag) {?> disabled <?php }?> >
        <input type="hidden" name="req_doc_uploaded_igf" id="req_doc_uploaded_igf">
        <input type="hidden" name="req_doc_uploaded_del_igf" id="req_doc_uploaded_del_igf">
        <input type="hidden" name="req_doc_uploaded_update_igf" id="req_doc_uploaded_update_igf">
      </div>
      <div class="col-sm-2">
        <p class="help-block pei-request-help-block">Can select single file only.</p>
      </div>
      <?php
      }
      ?>
    </div>

    <div class="clearfix"></div>
      <?php
      if($req_igf){
      ?>
      <table class="table table-bordered table-hover table-request-list">
        <tr>
          <th>IGF File Name</th>
          <th width="120px" class="pei-float-center">Server Count</th>
          <th width="120px" class="pei-float-center">Physical Servers</th>
          <th width="120px" class="pei-float-center">Virtual Servers</th>
          <th width="150px" class="pei-float-center">Action</th>
        </tr>
    <?php
    foreach ($req_igf as $key => $value) {
      $file_name = '';
      $file_name = strstr($value['igf_file_path'], 'IGF_upload/');
      $file_name = ltrim($file_name, 'IGF_upload/');
      $server_phy   = count_igf_server_count($req_id, '3', $value['igf_id']);
      $server_vir   = count_igf_server_count($req_id, '4', $value['igf_id']);
      $server_total = $server_phy + $server_vir;
    ?>
        <tr id="tr_req_doc_igf_<?php echo $value['igf_id'];?>">
          <td><?php echo $value['igf_file_name']?></td>
          <td class="pei-float-center"><?php echo ($server_total) ? sprintf("%02d", $server_total) : '00';?></td>
          <td class="pei-float-center"><?php echo ($server_phy) ? sprintf("%02d", $server_phy) : '00';?></td>
          <td class="pei-float-center"><?php echo ($server_vir) ? sprintf("%02d", $server_vir) : '00';?></td>
          <td class="pei-float-center">
            <?php if( $igf_doc_delete_flag && ($action == 'Edit') ) {?>
            <a href="javascript:void(0)" class="del-igf-file" data-igf-id="<?php echo $value['igf_id'];?>">Delete</a> |
            <?php }?>
            <a href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_download.php?igf_id=<?php echo $value['igf_id'];?>">Download</a>
          </td>
        </tr>
      <?php
      }
      ?>
      </table>
      <?php
      }
      ?>

    <div class="form-group pei-form-group">
      <label class="col-sm-2 control-label pei-control-label">Non IGF Documents</label>
      <?php
      if($action != 'View') {
      ?>
      <div class="col-sm-2">
        <input type="file" name="req_doc_uploaded[]" id="req-doc-uploaded" multiple="true">
        <input type="hidden" name="req_doc_uploaded" id="req_doc_uploaded">
        <input type="hidden" name="req_doc_uploaded_del" id="req_doc_uploaded_del">
        <input type="hidden" name="req_doc_uploaded_update" id="req_doc_uploaded_update">
      </div>
      <div class="col-sm-2">
        <p class="help-block pei-request-help-block">Can select multiple files.</p>
      </div>
      <label class="col-sm-2 control-label pei-control-label">Document Type</label>
      <div class="col-sm-4">
      <select class="placeholder form-control" name="req_doc_type" id="req-doc-type">
          <option value="">-- Select --</option>
      <?php
          foreach ($data_doc_type as $key => $doc_type) {
        ?>
          <option value="<?php echo $doc_type['doc_type_id'];?>"><?php echo $doc_type['doc_type_name'];?></option>
      <?php
          }
        ?>
        </select>
      </div>
      <?php
      }
      ?>
    </div>
    <div class="clearfix"></div>
    <?php
    if($req_doc){
    ?>
    <table class="table table-bordered table-hover table-request-list">
    <tr>
      <th>Document Name</th>
      <th>Document Type</th>
      <th class="text-center">Action</th>
    </tr>
    <?php
    foreach ($req_doc as $key => $value) {
    ?>
      <tr id="tr_req_doc_<?php echo $value['req_doc_id'];?>">
        <td><?php echo $value['doc_name']?></td>
        <td>
          <select class="scope-assign update-req-doc-type" name="req_doc_type_<?php echo $value['req_doc_id']?>" id="req_doc_type_<?php echo $value['req_doc_id']?>" data-req-doc-id="<?php echo $value['req_doc_id'];?>" data-doc-type-id="<?php echo $value['doc_type_id'];?>">
        <?php
        foreach($data_doc_type AS $idc_doc_type) {
      ?>
            <option value="<?php echo $idc_doc_type['doc_type_id'];?>" <?php if($value['doc_type_id'] == $idc_doc_type['doc_type_id']){?> selected="selected" <?php }?>><?php echo $idc_doc_type['doc_type_name'];?></option>
      <?php
        }
        ?>
        </select>

        </td>
        <td class="text-center">
          <?php if($action == 'Edit') {?>
          <a href="javascript:void(0)" class="del-non-igf-file" data-id="<?php echo $value['req_doc_id'];?>">Delete</a> |
          <?php }?>
          <a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/request_doc_download.php?file=<?php echo $value['req_doc_id'];?>">Download</a>
          </td>
      </tr>
    <?php
    }
    ?>
    </table>
    <?php
    }
    ?>


  </fieldset>

  <!-- MATERIAL -->
  <div class="clearfix"></div>

  <div class="row">
    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <a href="#" class="pei-no-underline">Material Issue</a>
        </div>
        <div class="panel-body">
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">Servers bearing Serial Numbers </label>
            <div class="col-sm-8">
              <textarea class="form-control form-control-text-upper" name="req_mat_serial" rows="2" placeholder="Serial Numbers" data-toggle="tooltip" data-placement="bottom" title="Serial Numbers"><?php echo $req_mat_serial;?></textarea>
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">RAM</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_ram" class="form-control form-control-text-upper" placeholder="RAM" data-toggle="tooltip" data-placement="bottom" title="RAM" value="<?php echo $req_mat_ram;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">HDD</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_hdd" class="form-control form-control-text-upper" placeholder="HDD" data-toggle="tooltip" data-placement="bottom" title="HDD" value="<?php echo $req_mat_hdd;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">NIC</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_nic" class="form-control form-control-text-upper" placeholder="NIC" data-toggle="tooltip" data-placement="bottom" title="NIC" value="<?php echo $req_mat_nic;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">FC HBA</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_fc_hba" class="form-control form-control-text-upper" placeholder="FC HBA" data-toggle="tooltip" data-placement="bottom" title="FC HBA" value="<?php echo $req_mat_fc_hba;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">Additional material required</label>
            <div class="col-sm-8">
              <?php
              if($req_mat_additional == '') {
                $req_mat_additional = 'e.g. UTP/TWINAX...etc.';
              }
              ?>
              <textarea class="form-control form-control-text-upper" name="req_mat_additional" rows="2" placeholder="Additional material required " data-toggle="tooltip" data-placement="bottom" title="Additional material required "><?php echo $req_mat_additional;?></textarea>
            </div>
          </div>
        </div>
      </div>
    </div>


    <div class="col-sm-6">
      <div class="panel panel-default">
        <div class="panel-heading">
          <a href="#" class="pei-no-underline">Material Return to Stores</a>
        </div>
        <div class="panel-body">
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">RAM</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_ram_ret" class="form-control form-control-text-upper" placeholder="RAM" data-toggle="tooltip" data-placement="bottom" title="RAM" value="<?php echo $req_mat_ram_ret;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">HDD</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_hdd_ret" class="form-control form-control-text-upper" placeholder="HDD" data-toggle="tooltip" data-placement="bottom" title="HDD" value="<?php echo $req_mat_hdd_ret;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">NIC</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_nic_ret" class="form-control form-control-text-upper" placeholder="NIC" data-toggle="tooltip" data-placement="bottom" title="NIC" value="<?php echo $req_mat_nic_ret;?>">
            </div>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-4 control-label pei-control-label">FC HBA</label>
            <div class="col-sm-8">
              <input type="text" name="req_mat_fc_hba_ret" class="form-control form-control-text-upper" placeholder="FC HBA" data-toggle="tooltip" data-placement="bottom" title="FC HBA" value="<?php echo $req_mat_fc_hba_ret;?>">
            </div>
          </div>
          <!-- Height Adjustment -->
          <div class="form-group pei-form-group">
            <label class="col-sm-12 control-label">&nbsp</label>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-12 control-label">&nbsp</label>
          </div>
          <div class="form-group pei-form-group">
            <label class="col-sm-12 control-label">&nbsp</label>
          </div>
          <!-- Height Adjustment  END -->
        </div>
      </div>
    </div>
  </div>

  <div class="clearfix"></div>

  <?php if($action != 'View') {?>
  <fieldset>
    <legend>&nbsp</legend>
    <div class="form-group">
      <div class="col-sm-offset-5 col-sm-2">
        <button type="submit" name="req_save" value="Save" class="btn btn-primary btn-label-left">
          <span><i class="fa fa-clock-o"></i></span>
          Save
        </button>
      </div>
    </div>
  </fieldset>
  <?php }?>

  <div class="clearfix"></div>
  <p class="help-block">
  * MANDATORY Field
  </p>
</form>

<?php
} // END if($pei_page_access)
?>
      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
  require_once(__dir__.'/../footer.php');
?>
<script type="text/javascript">

$(document).ready(function() {
  $('.scope-assign').select2();

  $('.del-non-igf-file').on( "click", function( event ) {
    var id = $(this).attr("data-id")
    if(id) {
      $('#tr_req_doc_'+id).remove();
      var str = $('#req_doc_uploaded_del').val();
      str = str+id+',';
      $('#req_doc_uploaded_del').val(str);
    }
  });



  $('.update-req-doc-type').on( "change", function( event ) {
    var req_doc_id  = $(this).attr("data-req-doc-id");
    var doc_type    = $(this).attr("data-doc-type-id");
    var current_val = $('#req_doc_type_'+req_doc_id).val();
    var str = $('#req_doc_uploaded_update').val();
    str = str+req_doc_id+':'+current_val+',';
    $('#req_doc_uploaded_update').val(str);
  });

  $('.del-igf-file').on( "click", function( event ) {
    var id = $(this).attr("data-igf-id")
    if(id) {
      $('#tr_req_doc_igf_'+id).remove();
      var str = $('#req_doc_uploaded_del_igf').val();
      str = str+id+',';
      $('#req_doc_uploaded_del_igf').val(str);
    }
  });

  $('#form-req-create')
    .find('#req-user-group')
    .select2({
      placeholder: 'Select Requestor User Group',
      width : '180px',
      allowClear: true
    })
    // Revalidate the color when it is changed
    .change(function(e) {
      $('#form-req-create').formValidation('revalidateField', 'user_group');
    })
    .end()
    .find('#req-sub-group')
    .select2({
      width : '180px',
      minimumInputLength: 0,
      placeholder: 'Select Requestor Sub Group',
      allowClear: true,
      ajax: {
        url: "fetch_sub_group.php",
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
    })
    // Revalidate the color when it is changed
    .change(function(e) {
      var test_other = $("#req-sub-group option:selected").text().toLowerCase();
      var isEnable = false;
      if(test_other == 'other') {
        isEnable = true;
        $("#custom-sub-group").removeClass('hide');
      }
      else {
        $("#custom-sub-group").addClass('hide');
      }
      $('#form-req-create').formValidation('enableFieldValidators', 'user_group_sub_other', isEnable);
    })
    .end()
    .find('#req-pm')
    .select2({
      placeholder: 'SELECT PM/IM',
      allowClear: true
    })
    // Revalidate the color when it is changed
    .change(function(e) {
      $('#form-req-create').formValidation('revalidateField', 'req_pm');
    })
    .end()
    .find('#req-idc-spoc')
    .select2({
      placeholder: 'SELECT JioDC SOLUTION SPOC',
      allowClear: true
    })
    // Revalidate the color when it is changed
    .change(function(e) {
      $('#form-req-create').formValidation('revalidateField', 'req_idc_spoc');
    })
    .end()
    .find('#req-doc-type')
    .select2({
      placeholder: 'SELECT DOCUMENT TYPE',
      allowClear: true
    })
    // Revalidate the color when it is changed
    .change(function(e) {
      $('#form-req-create').formValidation('revalidateField', 'req_doc_type');
    })
    .end()
    .formValidation({
      framework: 'bootstrap',
      excluded: ':disabled',
      icon: {
          valid: 'glyphicon glyphicon-ok',
          invalid: 'glyphicon glyphicon-remove',
          validating: 'glyphicon glyphicon-refresh'
      },
      fields: {
        user_group: {
          validators: {
            callback: {
            message: 'Please Select Requestor Group.',
              callback: function(value, validator, $field) {
                // Get the selected options
                var options = validator.getFieldElements('user_group').val();
                return (options != null && options.length > 0);
              }
            }
          }
        },
        user_group_sub: {
          validators: {
            callback: {
              message: 'Please Select Requestor Sub Group.',
              callback: function(value, validator, $field) {
                // Get the selected options
                var options = validator.getFieldElements('user_group_sub').val();
                return (options != null && options.length > 0);
              }
            }
          }
        },
        user_group_sub_other: {
          row: '.col-sm-4',
          enabled: false,
          validators: {
            notEmpty: {
              message: 'The Request Sub Group Name is required'
            }
          }
        },
        requestor: {
          row: '.col-sm-4',
          validators: {
            notEmpty: {
              message: 'The Request Initiator is required'
            }
          }
        },
        'req_env[]': {
          validators: {
            choice: {
              min: 1,
              message: 'Please select environment.'
            }
          }
        },
        req_name: {
          validators: {
            notEmpty: {
              message: 'The Request Title is required'
            }
          }
        },
        'req_loc[]': {
          validators: {
            choice: {
              min: 1,
              message: 'Please select location.'
            }
          }
        },
        req_idc_spoc: {
          row: '.col-sm-4',
          validators: {
            callback: {
              message: 'Please select JioDC Solution SPOC.',
              callback: function(value, validator, $field) {
                // Get the selected options
                var options = validator.getFieldElements('req_idc_spoc').val();
                return (options != null && options.length > 0);
              }
            }
          }
        },
        req_doc_type: {
          row: '.col-sm-4',
          enabled: false,
          validators: {
            callback: {
              message: 'Please select document type.',
              callback: function(value, validator, $field) {
                // Get the selected options
                var options = validator.getFieldElements('req_doc_type').val();
                return (options != null && options.length > 0);
              }
            }
          }
        },
      }
    })
    .on('change', '#req-doc-uploaded', function() {
      var isFileSelected = $('#req-doc-uploaded').val();
      $('#form-req-create').formValidation('enableFieldValidators', 'req_doc_type', isFileSelected);
    })
    .on('change', '#req-doc-uploaded-igf', function() {
      var isFileSelected = $('#req-doc-uploaded-igf').val();
    })
    .on('success.form.fv', function(e) {
        // Prevent form submission
        e.preventDefault();

        var $form = $(e.target),
        fv  = $(e.target).data('formValidation');

        // Do whatever you want here ...
        if(confirm('Please confirm if everything is correct.') )  {
          // Then submit the form as usual
          fv.defaultSubmit();
        }
      });
});
</script>
