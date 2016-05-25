<?php
  session_start();
  $pei_current_module = 'REQUEST';
  ini_set('max_execution_time', 0);
  ini_set('memory_limit','1024M');

  require_once(__dir__.'/../header.php');

  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');

  /** Include PHPExcel */
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel/IOFactory.php');
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel.php');

  // load PHPMailer library
  require_once($pei_config['paths']['vendors'].'/PHPMailer/PHPMailerAutoload.php');


  //echo error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';
//var_dump($_POST);
//var_dump($_POST['igf_server_checked']);

  // Initialize variables
  $pei_messages         = array();
  $pei_less_word_count  = 60;
  $uname                = strtolower($_SESSION['pei_user']);
  $pei_user             = '';

  $igf              = '';
  $igf_id           = $_GET['id'];
  $igf_request      = '';
  $req_id           = '';
  $req_loc          = '';
  $igf_contact_spoc = '';
  $igf_contact_hod  = '';
  $igf_contact_ops_1= '';
  $igf_contact_ops_2= '';
  $igf_budget       = '';
  $igf_server       = '';
  $igf_equipment    = '';
  $igf_software     = '';
  $server_release_count = 0;

  $data_igf_eqpt        = '';
  $igf_header_3         = 'EQUIPMENT DETAILS';
  $igf_contact_title_1  = 'CONTACT 1';
  $igf_contact_title_2  = 'CONTACT 2';
  $igf_version          = $pei_config['igf']['version'];
  // Request Number start from req_id number
  $igf_version_req_id   = $pei_config['igf']['after_req_id'];
  $req_id_num           = '';
  $igf_server_misc_col  = 'CL';
  $igf_server_public_col= 'CD';

  $igf_server_checked       = array();
  $igf_server_checked_prev  = array();
  $igf_server_all_checked   = TRUE;
  $igf_server_all_released  = TRUE;
  $igf_server_released      = array();
  $igf_server_released_ver  = '';

  $igf_server_sync          = array();
  $igf_server_unsync        = array();

  $sync_succsess    = FALSE;
  $mail_released    = FALSE;
  $mail_attach_path_tmp = '';


  $pei_page_access      = FALSE;
  $pei_access_sync      = FALSE;
  $pei_access_release   = FALSE;

  $released_flag        = FALSE;

  // Status ID released
  $status_id_rfi = 2;
  $status_id = 3;

  $released_type = '';

  // Get logged in user details
  $pei_user = get_user_detail_from_user_login($_SESSION['pei_user']);

  // CHECK access permission for sync_any_igf
  if(in_array('sync_any_igf', $pei_user_access_permission)) {
    $pei_page_access      = TRUE;
    $pei_access_sync      = TRUE;
  }

  if(in_array('release_any_request', $pei_user_access_permission)) {
    $pei_page_access      = TRUE;
    $pei_access_sync      = TRUE;
    $pei_access_release   = TRUE;
  }


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

  // Get Request Details from this igf
  $igf_request      = get_request_detail_from_igf_id($igf_id);
  if($igf_request){
    $req_id = $igf_request['req_id'];
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
// -----------------------------------------------------------------------------
  // Check if IM / PM acctivites are completed or not
  // PM / IM
  // Get PM / IM Activity ID
  $pmim_activities  = '';
  $pmim_activity    = activity_type_search('PM / IM');
  if($pmim_activity){
    $pmim_activity_id = $pmim_activity[0]['act_type_id'];
  }
  // Get Implementaion Child Activities
  if($pmim_activity_id) {
    $pmim_activities = activity_type_children_children($pmim_activity_id);
  }


  foreach ($pmim_activities as $key => $activity) {
    if(isset($activity['children'])){
      foreach ($activity['children'] as $key => $children_activity) {
        //echo '$req_id :'.$req_id.'<br />';
        $child_activity_detail      = request_activity_by_act_type_id($req_id, $children_activity['act_type_id']);
        $child_req_activity_id      = $child_activity_detail['req_act_id'];
        //echo '$child_req_activity_id :'.$child_req_activity_id.'<br />';
        $child_activity_status = request_activity_status($child_req_activity_id);
        //var_dump($child_activity_status);
        if($child_activity_status) {
          //echo $child_activity_status[0]['status_id'].'<br />';
          if(!in_array($child_activity_status[0]['status_id'], array(100, 4))) {
            $pei_access_release   = FALSE;
            break;
          }
        }
        else {
          $pei_access_release   = FALSE;
          break;
        }
      }
    }
  }

  //var_dump($pei_access_release);

// -----------------------------------------------------------------------------


  // Get IGF details
  $igf = get_igf_detail_by_igf_id($igf_id);
  //var_dump($igf);

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

  // Get Server Details
  $igf_server = get_igf_server_detail_by_igf_id($igf_id);


  if(isset($_POST['igf_server_checked'])) {
    $igf_server_checked = $_POST['igf_server_checked'];
  }

  if(isset($_POST['igf_release']) && $_POST['igf_release'] == 'release') {
    $release_validated      = TRUE;
    $released_igf_server_ids = '';
    $mail_req_released_row  = '';
    $mail_released_server   = 0;
    $mail_released_phy      = 0;
    $mail_released_vir      = 0;
    $mail_pending_server    = 0;
    $mail_pending_phy       = 0;
    $mail_pending_vir       = 0;

    // Validate servers selected for release
    if(count($igf_server_checked))  {
      // Check if selected server are already released or not
      foreach ($igf_server_checked as $key => $value) {
        if(check_igf_server_released_status($value)) {
          if($release_validated) {
            $pei_messages['error'][] = 'Selected Server(s) already released.';
          }
          $release_validated = FALSE;
        }
      }
    }
    else {
      $release_validated = FALSE;
      $pei_messages['error'][] = 'Please Select Server to be released.';
    }

    if($release_validated) {
      // Send Email Notification
      // Get REQUEST RELEASE mail template
      $mail_template = get_email_template_by_name('REQUEST RELEASE');
      //var_dump($mail_template);

      // Get Request Locations
      $req_loc = get_req_loc($req_id);

      $req_rfi_date = '';
      // Get Request RFI status detail
      //$req_status_rfi =  get_request_status_for_req_id($req_id, $status_id_rfi);
      $req_status_rfi =  get_request_status_history($req_id, '', 'ASC');
      if($req_status_rfi){
        $req_rfi_date = pei_date_format($req_status_rfi[0]['created_at']);
      }
      else {
        // Request create date is RFI date
        $req_rfi_date = pei_date_format($igf_request['req_date']);
      }

      $mail_env_str = '';
      $mail_env_str = get_req_env_string($req_id);
      $mail_loc_str = '';
      $mail_loc_str = get_req_loc_string($req_id);
      $mail_loc_sh  = '';
      $mail_loc_sh  = get_req_sh_string($req_id);
      if($mail_loc_sh) {
        $mail_loc_sh = '-'.$mail_loc_sh;
      }
      $mail_phy          = count_igf_server_count($req_id, '3');
      $mail_vir          = count_igf_server_count($req_id, '4');
      $mail_server       = $mail_phy + $mail_vir;

      $mail_from_name     = $mail_template['mail_template_from_name'];
      $mail_from_email    = $mail_template['mail_template_from_mail'];
      $mail_user_recipient= get_req_igf_contact($req_id, $igf_id);
      $mail_recipient     = '';

      if($mail_template['mail_template_recipient']){
        $mail_recipient = explode(",", $mail_template['mail_template_recipient']);
      }

      $mail_recipient_cc = '';
      // Add CC recipient according to request locations
      if($req_loc){
        foreach ($req_loc as $key => $req_locaction) {
          if($req_locaction['loc_contact_mail']) {
            $loc_contact_mail = pei_fetch_mail_from_string($req_locaction['loc_contact_mail']);
            if($loc_contact_mail){
              foreach ($loc_contact_mail as $key => $value_mail) {
                if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                  $mail_recipient_cc[] = $value_mail['mail'];
                }
              }
            }
          }
        }
      }

      if($mail_template['mail_template_recipient_cc']){
        $mail_recipient_cc_temp = explode(",", $mail_template['mail_template_recipient_cc']);
        if($mail_recipient_cc_temp){
          foreach ($mail_recipient_cc_temp as $key => $value) {
            if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
              $mail_recipient_cc[] = $value;
            }
          }
        }
      }

      // Add CC recipient according to request locations cc values
      if($req_loc){
        foreach ($req_loc as $key => $req_locaction) {
          if($req_locaction['loc_contact_mail_cc']) {
            $loc_contact_mail_cc = pei_fetch_mail_from_string($req_locaction['loc_contact_mail_cc']);
            if($loc_contact_mail_cc){
              foreach ($loc_contact_mail_cc as $key => $value_mail) {
                if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                  $mail_recipient_cc[] = $value_mail['mail'];
                }
              }
            }
          }
        }
      }


      $mail_recipient_bcc  = '';
      if($mail_template['mail_template_recipient_bcc']){
        $mail_recipient_bcc = explode(",", $mail_template['mail_template_recipient_bcc']);
      }

      // Add BCC recipient according to request locations bcc values
      if($req_loc){
        foreach ($req_loc as $key => $req_locaction) {
          if($req_locaction['loc_contact_mail_bcc']) {
            $loc_contact_mail_bcc = pei_fetch_mail_from_string($req_locaction['loc_contact_mail_bcc']);
            if($loc_contact_mail_bcc){
              foreach ($loc_contact_mail_bcc as $key => $value_mail) {
                if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                  $mail_recipient_bcc[] = $value_mail['mail'];
                }
              }
            }
          }
        }
      }

      $mail_subject       = $mail_template['mail_template_subject'];
      $mail_message       = $mail_template['mail_template_message_text'];
      $mail_message_path  = $pei_config['paths']['templates'].'/'.ltrim($mail_template['mail_template_message_path'], '/');
      $mail_message_html  = file_get_contents($mail_message_path);


      // IGF path
      $mail_attach_path = $igf['doc_file_path'];
      $mail_attach_name = $igf['doc_file_name'];
      // Overwrite original file name to
      $mail_attach_name = $req_id.'-IGF.xlsx';

      //Create a new PHPMailer instance
      $mail_pei = new PHPMailer;
      //Set who the message is to be sent from
      $mail_pei->setFrom($mail_from_email, $mail_from_name);
      //Set an alternative reply-to address
      //$mail_pei->addReplyTo($mail_from_email,  $mail_from_name);

      if($mail_user_recipient) {
        foreach ($mail_user_recipient as $recipient) {
          if(trim($recipient['igf_contact_email'])) {
            //echo $recipient['igf_contact_email'].'<br />';
            //Set who the message is to be sent to
            $recipient_name = ($recipient['igf_contact_name']) ? $recipient['igf_contact_name'] : '';
            $mail_pei->addAddress($recipient['igf_contact_email'], $recipient_name);
          }
        }
      }
      //Set who the message is to be sent to
      if($mail_recipient) {
        foreach ($mail_recipient as $key => $value) {
          if(trim($value)) {
            //Set who the message is to be sent to
            $mail_pei->addAddress($value);
          }
        }
      }

      // addCc
      if($mail_recipient_cc) {
        foreach ($mail_recipient_cc as $key => $value) {
          if(trim($value)) {
            //Set who the message is to be sent to
            $mail_pei->addCC($value);
          }
        }
      }

      // addBCC
      if($mail_recipient_bcc) {
        foreach ($mail_recipient_bcc as $key => $value) {
          if(trim($value)) {
            //Set who the message is to be sent to
            $mail_pei->addBCC($value);
          }
        }
      }


      //Attach an IGF file
      if($mail_attach_path) {

        $objPHPExcel  = PHPExcel_IOFactory::createReader('Excel2007');
        $objPHPExcel  = $objPHPExcel->load($mail_attach_path);
        //var_dump($objPHPExcel);

        $objPHPExcel->setActiveSheetIndex(3);

        //Setting width of spreadsheet cell using PHPExcel
        $objPHPExcel->getActiveSheet()->getColumnDimension('BT')->setWidth(25);
        $objPHPExcel->getActiveSheet()->getColumnDimension('BU')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('BV')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('BW')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('BX')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('BY')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('BZ')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CA')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CB')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CC')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CD')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CE')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CF')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CG')->setWidth(15);
        $objPHPExcel->getActiveSheet()->getColumnDimension('CH')->setWidth(30);

        switch($igf_version) {
          case 'v3':
          case 'v3.1':
            $igf_server_misc_col  = 'CH';
            $igf_server_public_col= 'CG';

            $objPHPExcel->getActiveSheet()->setCellValue('BI2', 'USER ID : GROUP ID : HOME DIR');
            break;
          case 'v4':
          default:
            $igf_server_misc_col  = 'CL';
            $igf_server_public_col= 'CD';
        } // END SWITCH

        // Add PORTAL INFORMATION column Header in SERVER sheet
        $objPHPExcel->getActiveSheet()->setCellValue($igf_server_misc_col.'1', 'PORTAL GENERATED');
        $objPHPExcel->getActiveSheet()->setCellValue($igf_server_misc_col.'2', 'MISCELLANEOUS INFORMATION');

        $excel_row = 2;
        if($igf_server) {
          foreach ($igf_server as $key => $server) {
            // Check previous igf_server_checked status for server
            if($server['igf_server_checked'] == '1') {
              $igf_server_checked_prev[] = $server['igf_server_id'];
            }

            $excel_row++;
            if($server['igf_server_release_at'] != NULL) {
              //echo 'igf_server_id:'.$server['igf_server_id'].' $excel_row:'.$excel_row.' $key:'.$key.' ['.$objPHPExcel->getActiveSheet()->getCell('K'.($key+3))->getValue().'] XXXX<br />';
              $objPHPExcel->getActiveSheet()->removeRow($excel_row);
              $excel_row = $excel_row - 1;
            }
            else {
              $server_misc = '';
              // First Calculate How Many Servers are to be released
              if(isset($_POST['igf_server_checked']) && count($_POST['igf_server_checked'])) {

                // Release Partial servers
                if(in_array($server['igf_server_id'], $_POST['igf_server_checked']) ) {
                  // Check if server is already released or not

                  $released_igf_server_ids[] = $server['igf_server_id'];
                  // Update igf_server_misc igf_server_misc_
                  $server_misc = $_POST['igf_server_misc_'.$server['igf_server_id']];
                  update_igf_server_misc($server['igf_server_id'], $server_misc);

                  if($server['igf_server_type_id'] == 3 ) {
                    $mail_released_phy = $mail_released_phy + 1;
                  }
                  else {
                    $mail_released_vir = $mail_released_vir + 1;
                  }

                  // Update LOCATION
                  $objPHPExcel->getActiveSheet()->setCellValue('E'.($excel_row), $server['igf_server_loc']);
                  // Update SERVER HALL
                  $objPHPExcel->getActiveSheet()->setCellValue('F'.($excel_row), $server['igf_server_server_hall']);
                  // Update ROW-RACK
                  $objPHPExcel->getActiveSheet()->setCellValue('G'.($excel_row), $server['igf_server_row_rack']);
                  // Update RACK NAME
                  $objPHPExcel->getActiveSheet()->setCellValue('H'.($excel_row), $server['igf_server_rack_name']);
                  // Update RACK "U"
                  $objPHPExcel->getActiveSheet()->setCellValue('I'.($excel_row), $server['igf_server_rack_u']);
                  // Update SLOT NO.
                  $objPHPExcel->getActiveSheet()->setCellValue('J'.($excel_row), $server['igf_server_slot_no']);
                  // Update SERVER SERIAL NUMBER
                  $objPHPExcel->getActiveSheet()->setCellValue('O'.($excel_row), $server['igf_server_serial_number']);
                  // Update HOSTNAME
                  $objPHPExcel->getActiveSheet()->setCellValue('BT'.($excel_row), $server['igf_server_hostname']);
                  // Update CONSOLE IP
                  $objPHPExcel->getActiveSheet()->setCellValue('BU'.($excel_row), $server['igf_server_console_ip']);
                  // Update SUBNET MASK
                  $objPHPExcel->getActiveSheet()->setCellValue('BV'.($excel_row), $server['igf_server_console_ip_sm']);
                  // Update GATEWAY
                  $objPHPExcel->getActiveSheet()->setCellValue('BW'.($excel_row), $server['igf_server_console_ip_gw']);
                  // Update DATA IP 1
                  $objPHPExcel->getActiveSheet()->setCellValue('BX'.($excel_row), $server['igf_server_data_ip_1']);
                  // Update DATA IP 2
                  $objPHPExcel->getActiveSheet()->setCellValue('BY'.($excel_row), $server['igf_server_data_ip_2']);
                  // Update VIP
                  $objPHPExcel->getActiveSheet()->setCellValue('BZ'.($excel_row), $server['igf_server_vip']);
                  // Update SUBNET MASK
                  $objPHPExcel->getActiveSheet()->setCellValue('CA'.($excel_row), $server['igf_server_data_ip_sm']);
                  // Update GATEWAY
                  $objPHPExcel->getActiveSheet()->setCellValue('CB'.($excel_row), $server['igf_server_data_ip_gw']);
                  // Update LB IP
                  $objPHPExcel->getActiveSheet()->setCellValue('CC'.($excel_row), $server['igf_server_lb_ip']);

                  // Update PUBLIC IP
                  $objPHPExcel->getActiveSheet()->setCellValue($igf_server_public_col.($excel_row), $server['igf_server_public_ip']);

                  switch($igf_version) {
                    case 'v3':
                    case 'v3.1':
                      // Update OTHER IP
                      $objPHPExcel->getActiveSheet()->setCellValue('CD'.($excel_row), $server['igf_server_other_ip']);
                      // Update SM
                      $objPHPExcel->getActiveSheet()->setCellValue('CE'.($excel_row), $server['igf_server_other_ip_sm']);
                      // Update GW
                      $objPHPExcel->getActiveSheet()->setCellValue('CF'.($excel_row), $server['igf_server_other_ip_gw']);
                      break;
                    case 'v4':
                    default:
                      // Update EQUIPMENT (PRIVATE) LAN IP
                      $objPHPExcel->getActiveSheet()->setCellValue('CE'.($excel_row), $server['igf_server_private_lan_ip']);
                      // Update EQUIPMENT (PRIVATE) SUBNET MASK
                      $objPHPExcel->getActiveSheet()->setCellValue('CF'.($excel_row), $server['igf_server_private_lan_sm']);
                      // Update RAC IP
                      $objPHPExcel->getActiveSheet()->setCellValue('CG'.($excel_row), $server['igf_server_rac_ip']);
                      // Update SCAN IP
                      $objPHPExcel->getActiveSheet()->setCellValue('CH'.($excel_row), $server['igf_server_scan_ip']);
                      // Update Heartbeat IP
                      $objPHPExcel->getActiveSheet()->setCellValue('CI'.($excel_row), $server['igf_server_heartbeat_ip']);
                      // Update Cluster Interconnect PRIVATE IP
                      $objPHPExcel->getActiveSheet()->setCellValue('CJ'.($excel_row), $server['igf_server_cluster_ic_ip']);
                      // Update Oracle VIP
                      $objPHPExcel->getActiveSheet()->setCellValue('CK'.($excel_row), $server['igf_server_oracle_vip']);
                  } // END SWITCH


                  // Update MISCELLANEOUS INFORMATION
                  $objPHPExcel->getActiveSheet()->setCellValue($igf_server_misc_col.($excel_row), $server_misc);
                }
                else {
                  $objPHPExcel->getActiveSheet()->removeRow($excel_row);
                  $excel_row = $excel_row - 1;
                }
              }
              else {
                // Update igf_server_misc igf_server_misc_
                $server_misc = $_POST['igf_server_misc_'.$server['igf_server_id']];
                update_igf_server_misc($server['igf_server_id'], $server_misc);

                // Release ALL servers
                $released_igf_server_ids[] = $server['igf_server_id'];
                // Update LOCATION
                $objPHPExcel->getActiveSheet()->setCellValue('E'.($key+3), $server['igf_server_loc_name']);
                // Update SERVER HALL
                $objPHPExcel->getActiveSheet()->setCellValue('F'.($key+3), $server['igf_server_server_hall_name']);
                // Update ROW-RACK
                $objPHPExcel->getActiveSheet()->setCellValue('G'.($key+3), $server['igf_server_row_rack_name']);
                // Update RACK NAME
                $objPHPExcel->getActiveSheet()->setCellValue('H'.($key+3), $server['igf_server_rack_name']);
                // Update RACK "U"
                $objPHPExcel->getActiveSheet()->setCellValue('I'.($key+3), $server['igf_server_rack_u']);
                // Update SLOT NO.
                $objPHPExcel->getActiveSheet()->setCellValue('J'.($key+3), $server['igf_server_slot_no']);
                // Update SERVER SERIAL NUMBER
                $objPHPExcel->getActiveSheet()->setCellValue('O'.($key+3), $server['igf_server_serial_number']);
                // Update HOSTNAME
                $objPHPExcel->getActiveSheet()->setCellValue('BT'.($key+3), $server['igf_server_hostname']);
                // Update CONSOLE IP
                $objPHPExcel->getActiveSheet()->setCellValue('BU'.($key+3), $server['igf_server_console_ip']);
                // Update SUBNET MASK
                $objPHPExcel->getActiveSheet()->setCellValue('BV'.($key+3), $server['igf_server_console_ip_sm']);
                // Update GATEWAY
                $objPHPExcel->getActiveSheet()->setCellValue('BW'.($key+3), $server['igf_server_console_ip_gw']);
                // Update DATA IP 1
                $objPHPExcel->getActiveSheet()->setCellValue('BX'.($key+3), $server['igf_server_data_ip_1']);
                // Update DATA IP 2
                $objPHPExcel->getActiveSheet()->setCellValue('BY'.($key+3), $server['igf_server_data_ip_2']);
                // Update VIP
                $objPHPExcel->getActiveSheet()->setCellValue('BZ'.($key+3), $server['igf_server_vip']);
                // Update SUBNET MASK
                $objPHPExcel->getActiveSheet()->setCellValue('CA'.($key+3), $server['igf_server_data_ip_sm']);
                // Update GATEWAY
                $objPHPExcel->getActiveSheet()->setCellValue('CB'.($key+3), $server['igf_server_data_ip_gw']);
                // Update LB IP
                $objPHPExcel->getActiveSheet()->setCellValue('CC'.($key+3), $server['igf_server_lb_ip']);

                // Update PUBLIC IP
                $objPHPExcel->getActiveSheet()->setCellValue($igf_server_public_col.($key+3), $server['igf_server_public_ip']);

                switch($igf_version) {
                  case 'v3':
                  case 'v3.1':
                   // Update OTHER IP
                  $objPHPExcel->getActiveSheet()->setCellValue('CD'.($key+3), $server['igf_server_other_ip']);
                  // Update SM
                  $objPHPExcel->getActiveSheet()->setCellValue('CE'.($key+3), $server['igf_server_other_ip_sm']);
                  // Update GW
                  $objPHPExcel->getActiveSheet()->setCellValue('CF'.($key+3), $server['igf_server_other_ip_gw']);
                    break;
                  case 'v4':
                  default:
                    // Update EQUIPMENT (PRIVATE) LAN IP
                    $objPHPExcel->getActiveSheet()->setCellValue('CE'.($key+3), $server['igf_server_private_lan_ip']);
                    // Update EQUIPMENT (PRIVATE) SUBNET MASK
                    $objPHPExcel->getActiveSheet()->setCellValue('CF'.($key+3), $server['igf_server_private_lan_sm']);
                    // Update RAC IP
                    $objPHPExcel->getActiveSheet()->setCellValue('CG'.($key+3), $server['igf_server_rac_ip']);
                    // Update SCAN IP
                    $objPHPExcel->getActiveSheet()->setCellValue('CH'.($key+3), $server['igf_server_scan_ip']);
                    // Update Heartbeat IP
                    $objPHPExcel->getActiveSheet()->setCellValue('CI'.($key+3), $server['igf_server_heartbeat_ip']);
                    // Update Cluster Interconnect PRIVATE IP
                    $objPHPExcel->getActiveSheet()->setCellValue('CJ'.($key+3), $server['igf_server_cluster_ic_ip']);
                    // Update Oracle VIP
                    $objPHPExcel->getActiveSheet()->setCellValue('CK'.($key+3), $server['igf_server_oracle_vip']);
                } // END SWITCH

                // Update MISCELLANEOUS INFORMATION /
                $objPHPExcel->getActiveSheet()->setCellValue($igf_server_misc_col.($excel_row), $server_misc);
              }
            }
          }
        }


        /*
      <tr>
        <td><b>RELEASED</b></td>
        <td>{REQ_RELEASED_SERVER_COUNT}</td>
        <td>{REQ_RELEASED_PHYSICAL_SERVERS}</td>
        <td>{REQ_RELEASED_VIRTUAL_SERVERS}</td>
      </tr>
      */

        if(count($released_igf_server_ids) == $mail_server) {
          $mail_released_server   = $mail_server;
          $mail_released_phy      = $mail_phy;
          $mail_released_vir      = $mail_vir;
          $mail_req_released_row = '<tr>
            <td><b>RELEASED</b></td>
            <td style="text-align: center;">'.sprintf("%02d", $mail_released_server).'</td>
            <td style="text-align: center;">'.sprintf("%02d", $mail_released_phy).'</td>
            <td style="text-align: center;">'.sprintf("%02d", $mail_released_vir).'</td>
            <td style="text-align: center;">'.pei_datetime_format(date('Y-m-d H:i:s', time()), 'd-M-Y / H:i').'</td>
          </tr>';
        }
        else {
          $released_version = 1;
          $mail_req_released_history = '';
          $mail_released_server_hy  = 0;
          $mail_released_phy_hy     = 0;
          $mail_released_vir_hy     = 0;
          $mail_req_released_history = get_igf_server_released_history($igf_id);
          //var_dump($mail_req_released_history);
          if($mail_req_released_history) {

            foreach ($mail_req_released_history as $key => $value) {
              $mail_released_server_hy = $mail_released_server_hy + $value['total'];
              $mail_released_phy_hy    = $mail_released_phy_hy + $value['physical'];
              $mail_released_vir_hy    = $mail_released_vir_hy + $value['virtual'];
              $mail_req_released_row .= '<tr>
                <td><b>RELEASED '.$released_version.'</b></td>
                <td style="text-align: center;">'.sprintf("%02d", $value['total']).'</td>
                <td style="text-align: center;">'.sprintf("%02d", $value['physical']).'</td>
                <td style="text-align: center;">'.sprintf("%02d", $value['virtual']).'</td>
                <td style="text-align: center;">'.pei_datetime_format($value['date'], 'd-M-Y / H:i').'</td>
              </tr>';
              $released_version++;
            }
          }
          $mail_released_server = count($released_igf_server_ids);
          $mail_req_released_row .= '<tr>
            <td><b>RELEASED '.$released_version.'</b></td>
            <td style="text-align: center;">'.sprintf("%02d", $mail_released_server).'</td>
            <td style="text-align: center;">'.sprintf("%02d", $mail_released_phy).'</td>
            <td style="text-align: center;">'.sprintf("%02d", $mail_released_vir).'</td>
            <td style="text-align: center;">'.pei_datetime_format(date('Y-m-d H:i:s', time()), 'd-M-Y / H:i').'</td>
          </tr>';


          $mail_released_server = $mail_released_server + $mail_released_server_hy;
          $mail_released_phy    = $mail_released_phy + $mail_released_phy_hy;
          $mail_released_vir    = $mail_released_vir + $mail_released_vir_hy;
        }

        $mail_pending_server    = $mail_server - $mail_released_server;
        $mail_pending_phy       = $mail_phy - $mail_released_phy;
        $mail_pending_vir       = $mail_vir - $mail_released_vir;

        $released_contact   = array();
        $released_contact_count         = 0;
        $mail_req_released_contact_row  = '';

        if($req_loc){
          foreach ($req_loc as $key => $req_locaction) {
            if($req_locaction['loc_contact_mail']) {
              $loc_contact_name = $req_locaction['loc_contact_name'];
              $loc_contact_mail = pei_fetch_mail_from_string($req_locaction['loc_contact_mail']);
              if($loc_contact_mail) {
                foreach ($loc_contact_mail as $key => $value_mail) {
                  if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                    if(!in_array($value_mail['mail'], $released_contact)) {
                      $released_contact_count++;
                      $released_contact[] = $value_mail['mail'];
                      $contact_phone  = ($req_locaction['loc_contact_phone']) ? $req_locaction['loc_contact_phone'] : 'NA';
                      if(strtolower($value_mail['mail']) == strtolower('JioDC.ImplementationMumbai@ril.com')) {
                        $loc_contact_name = 'JioDC MUMBAI IMPLEMENTATION TEAM';
                      }
                      $mail_req_released_contact_row .= '<tr>
                        <td><b>CONTACT '.$released_contact_count.'</b></td>
                        <td>'.$loc_contact_name.'</td>
                        <td>'.$value_mail['mail'].'</td>
                      </tr>';
                    }
                  }
                }
              }
            }
          }
        }

        $mail_variables     = array(
                              '{REQ_DATE}' => pei_date_format($igf_request['req_date']),
                              '{REQ_ID}' => $req_id,
                              '{REQ_ENV}' => $mail_env_str,
                              '{REQ_NAME}' => strtoupper($igf_request['req_title']),
                              '{REQ_LOC}' => $mail_loc_str,
                              '{REQ_SH}' => $mail_loc_sh,
                              '{REQ_RFI_DATE}' => $req_rfi_date,
                              '{REQ_RELEASE_DATE}' => pei_date_format(date('Y-m-d H:i:s', time())),
                              '{REQ_RELEASE_BY}' => strtoupper($pei_user['user_name']),
                              '{REQ_SERVER_COUNT}' => sprintf("%02d", $mail_server),
                              '{REQ_PHYSICAL_SERVERS}' => sprintf("%02d", $mail_phy),
                              '{REQ_VIRTUAL_SERVERS}' => sprintf("%02d", $mail_vir),
                              '{REQ_RELEASED}' => $mail_req_released_row,
                              '{REQ_PENDING_SERVER_COUNT}' => sprintf("%02d", $mail_pending_server),
                              '{REQ_PENDING_PHYSICAL_SERVERS}' => sprintf("%02d", $mail_pending_phy),
                              '{REQ_PENDING_VIRTUAL_SERVERS}' => sprintf("%02d", $mail_pending_vir),
                              '{REQ_RELEASE_CONTACT_2_NAME}' => strtoupper($pei_user['user_name']),
                              '{REQ_RELEASE_CONTACT_2_MAIL}' => strtolower($pei_user['user_email']),
                              '{REQ_RELEASE_CONTACT_2_PHONE}' => $pei_user['user_mobile'],
                              '{REQ_RELEASE_CONTACT}' => $mail_req_released_contact_row,
                            );

        // Check if its partial or final etc
        if($mail_pending_server != 0) {
          $mail_subject = 'PARTIAL '.$released_version.'- '.$mail_subject;
          $mail_attach_name = 'PARTIAL RELEASED '.$released_version.' - '.$mail_attach_name;
          $released_type = 'PARTIAL';
        }
        else {
          if(count($released_igf_server_ids) != $mail_server) {
            $mail_subject = 'FINAL - '.$mail_subject;
            $mail_attach_name = 'FINAL RELEASED - '.$mail_attach_name;
          }
        }
        $mail_subject       = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_subject);

        $mail_message_text  = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_message);
        $mail_message_html  = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_message_html);

        //Set the subject line
        $mail_pei->Subject = $mail_subject;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail_pei->msgHTML($mail_message_html);
        //Replace the plain text body with one created manually
        $mail_pei->AltBody = $mail_message_text;


        // Save Updated IGF to temporary location for adding attachement
        $temp_path = '/tmp/';
        $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
        $mail_attach_path = $temp_path.time().'----temp-igf-'.$igf_id.'.xlsx';
        $mail_attach_path_tmp = $mail_attach_path;
        $objWriter->save($mail_attach_path);
        $mail_pei->addAttachment($mail_attach_path, $mail_attach_name);

      }

      //send the message, check for errors
      $mail_sent =  $mail_pei->send();
      if($mail_sent) {
        $pei_messages['success'][] = 'Release Mail Sent';

        // Updated igf_server_checked status
        // Fetch Previous save igf_server_checked status and compare with current values
        // To remove extra igf_server_checked status
        $remove_checked = array_diff($igf_server_checked_prev, $igf_server_checked);
        if($remove_checked) {
          //update_igf_server_checked($remove_checked, '0');
          $sql_check_rm = " UPDATE idc_igf_server SET ";
          $sql_check_rm .=" igf_server_checked ='0' ";
          $sql_check_rm .=" WHERE  ";
          $sql_check_rm .=" igf_server_release_at IS NULL ";
          $sql_check_rm .=" AND ";
          $sql_check_rm .=" igf_server_id IN (";
          foreach ($remove_checked as $key => $value) {
            $sql_check_rm .="'".mysql_real_escape_string($value)."',";
          }
          $sql_check_rm = rtrim($sql_check_rm, ',');
          $sql_check_rm .=") ";
          $res_check_rm = mysql_query($sql_check_rm, $pei_conn);
        }
        // To add new addition igf_server_checked status
        $add_checked = array_diff($igf_server_checked, $igf_server_checked_prev);
        if($add_checked) {
          //update_igf_server_checked($add_checked, '1');
          $sql_check_add = " UPDATE idc_igf_server SET ";
          $sql_check_add .=" igf_server_checked ='1' ";
          $sql_check_add .=" WHERE  ";
          $sql_check_add .=" igf_server_id IN (";
          foreach ($add_checked as $key => $value) {
            $sql_check_add .="'".mysql_real_escape_string($value)."',";
          }
          $sql_check_add = rtrim($sql_check_add, ',');
          $sql_check_add .=") ";
          $res_check_add = mysql_query($sql_check_add, $pei_conn);
        }

        // Add RELEASE entry
        $sql_insert   = " INSERT INTO idc_igf_server_release (igf_id, release_by, release_at)";
        $sql_insert  .= " VALUE (";
        $sql_insert  .= "'".mysql_escape_string($igf_id)."', ";
        $sql_insert  .= "'".mysql_escape_string($uname)."', ";
        $sql_insert  .= " NOW() )";

        $res_insert   = mysql_query($sql_insert, $pei_conn);
        if (!$res_insert) {
          $pei_messages['error'][] = 'Something went wrong with database query.';
          //$req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
          //$req_msg .= 'Whole query: ' . $sql_insert;
          //die($req_msg);
        }
        else {
          // Get Release ID
          $igf_release_id =  mysql_insert_id($pei_conn);
          //echo '$igf_release_id <br />';
          //var_dump($igf_release_id);

          // Update IGF Server Released Details
          if($released_igf_server_ids) {
            update_igf_server_released($released_igf_server_ids, $uname);

            // Save released server details
            foreach ($released_igf_server_ids as $key => $value) {
              $sql_rs   = " INSERT INTO idc_igf_server_release_server (igf_server_release_id, igf_server_id, created_by, created_at)";
              $sql_rs  .= " VALUE (";
              $sql_rs  .= "'".mysql_escape_string($igf_release_id)."', ";
              $sql_rs  .= "'".mysql_escape_string($value)."', ";
              $sql_rs  .= "'".mysql_escape_string($uname)."', ";
              $sql_rs  .= " NOW() )";

              $res_rs   = mysql_query($sql_rs, $pei_conn);
              if (!$res_rs) {
                $pei_messages['error'][] = 'Something went wrong with database query.';
                //$req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                //$req_msg .= 'Whole query: ' . $sql_rs;
                //die($req_msg);
              }
            }
          }

          $released_type_name = 'RELEASED';
          if($released_type == 'PARTIAL') {
            $status_id = 4;
            $released_type_name = 'PARTIAL '.$released_type_name;

            // Check if Request Activity Status is ON-HOLD
            // THEN Request Status will have two entries
            // 1. First  Partial Release
            // 2. Second ON-HOLD
            // So that request status will be PARTIAL ON-HOLD
            $req_act_status = request_activity_activites_status_latest($req_id, 1, 1);
            if($req_act_status){
              foreach ($req_act_status as $key => $value) {
                //
                if(isset($value['status_parent_id'])){
                  if($value['status_parent_id'] == 5){
                    // Request Activity is on ON-HOLD

                    // 1. First Partial Release (REQUEST STATUS)
                    $data_req_status_partial = NULL;
                    $data_req_status_partial['req_id']      = $req_id;
                    $data_req_status_partial['status_id']   = 4; // PARTIAL RELEASED
                    $data_req_status_partial['created_by']  = strtolower($uname);

                    request_status_save($data_req_status_partial);

                    // 2. Second ON-HOLD
                    $released_type_name = 'ON-HOLD';
                    $status_id          = 5;
                    break;
                  }
                }
              }
            }
          }

          // Also updated status of request
          update_request_status($req_id, $released_type_name, $status_id);

          // Below code is commented and equivalent code is written for it
          //save_request_status_for_req_id($req_id, $status_id, $uname);
          $data_req_status = NULL;
          $data_req_status['req_id']      = $req_id;
          $data_req_status['status_id']   = $status_id;
          $data_req_status['created_by']  = strtolower($uname);
          request_status_save($data_req_status);
        }

        // Delete temp attachment file
        if($mail_attach_path_tmp) {
          @unlink($mail_attach_path_tmp);
        }
      }

    }
  }






  if(isset($_POST['igf_sync']) && $_POST['igf_sync'] == 'Sync') {

    $pei_conn_2 = mysql_connect($pei_config['db']['db2']['host'], $pei_config['db']['db2']['username'], $pei_config['db']['db2']['password']);
    if (!$pei_conn_2) {
      $pei_messages['error'][] = 'CMDB database connection failed.';
    }
    else{
      mysql_select_db($pei_config['db']['db2']['dbname'], $pei_conn_2);

      $vm_index = 0;
      $phy_sr   = '';
      if($igf_server) {
        $sync_succsess = TRUE;
        foreach ($igf_server as $key => $server) {
          //var_dump($server);
          // Check previous igf_server_checked status for server
          if($server['igf_server_checked'] == '1') {
            $igf_server_checked_prev[] = $server['igf_server_id'];
          }
          $server_misc = '';
          // Update igf_server_misc igf_server_misc_
          if(isset($_POST['igf_server_misc_'.$server['igf_server_id']])) {
            $server_misc = $_POST['igf_server_misc_'.$server['igf_server_id']];
          }


          //echo '<hr/>';
          // Check Server Type
          // If PHYSICAL  i.e. id is 3
          if($server['igf_server_type_id'] == 3) {
            $vm_index = 1;
            if($server['igf_server_serial_number']) {
              $phy_sr     = $server['igf_server_serial_number'];
              $sql_server = " SELECT *
                              FROM t_server_provisioning
                              WHERE SERIALNUMBER =  TRIM('".mysql_real_escape_string($server['igf_server_serial_number'])."')
                              ";
              //echo 'SQL :'.$sql_server.'<br />';
              $res_server = mysql_query($sql_server, $pei_conn_2);
              if (!$res_server) {
                $pei_messages['error'][] = 'Something went wrong with database query.';
                //$req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                //$req_msg .= 'Whole query: ' . $sql_server;
                die('Something went wrong with database query');
              }
              else {
                $row_count  = 0;
                $row        = '';
                $row_count = mysql_num_rows($res_server);
                //echo '$row_count :'.$row_count.'<br />';
                if($row_count == 1){
                  $row = mysql_fetch_array($res_server);
                  //var_dump($row);

                  /*
                  // Update server detail :-
                      HOSTNAME, ILORSA_IP, ILORSA_GATEWAY, ILORSA_SUBNET_MASK,
                      VLAN_ID,
                      DATA_IP, DATA_SUBNET_MASK, DATA_GATEWAY
                      ilorsa_password
                  */

                  $sql_update =   " UPDATE idc_igf_server SET ";
                  $sql_update .=  " igf_server_hostname       ='".mysql_real_escape_string($row['HOSTNAME'])."',  ";
                  $sql_update .=  " igf_server_console_ip     ='".mysql_real_escape_string($row['ILORSA_IP'])."',  ";
                  $sql_update .=  " igf_server_console_ip_sm  ='".mysql_real_escape_string($row['ILORSA_SUBNET_MASK'])."',  ";
                  $sql_update .=  " igf_server_console_ip_gw  ='".mysql_real_escape_string($row['ILORSA_GATEWAY'])."',  ";
                  $sql_update .=  " igf_server_data_ip_1      ='".mysql_real_escape_string($row['DATA_IP'])."',  ";
                  //$sql_update .=  " igf_server_data_ip_2      ='".mysql_real_escape_string($row['DATA_IP'])."',  ";
                  //$sql_update .=  " igf_server_vip            ='".mysql_real_escape_string($row['VIRTUAL_IP_ADDRESS'])."',  ";
                  $sql_update .=  " igf_server_data_ip_sm     ='".mysql_real_escape_string($row['DATA_SUBNET_MASK'])."',  ";
                  $sql_update .=  " igf_server_data_ip_gw     ='".mysql_real_escape_string($row['DATA_GATEWAY'])."',  ";
                  //$sql_update .=  " igf_server_lb_ip          ='".mysql_real_escape_string($row[''])."',  ";
                  //$sql_update .=  " igf_server_other_ip       ='".mysql_real_escape_string($row[''])."',  ";
                  //$sql_update .=  " igf_server_other_ip_sm    ='".mysql_real_escape_string($row[''])."',  ";
                  //$sql_update .=  " igf_server_other_ip_gw    ='".mysql_real_escape_string($row[''])."',  ";
                  //$sql_update .=  " igf_server_public_ip      ='".mysql_real_escape_string($row[''])."',  ";
                  if($server_misc) {
                    $sql_update .=  " igf_server_misc             ='".mysql_real_escape_string($server_misc)."',  ";
                  }
                  $sql_update .=  " updated_by                ='".mysql_real_escape_string($uname)."',  ";
                  $sql_update .=  " updated_at                = NOW()";
                  $sql_update .=  " WHERE ";
                  $sql_update .=  " igf_server_serial_number  = '".mysql_real_escape_string($server['igf_server_serial_number'])."' ";
                  $sql_update .=  " AND ";
                  $sql_update .=  " igf_server_id='".mysql_real_escape_string($server['igf_server_id'])."' ;";
                  //echo $sql_update.'<br />';
                  //var_dump($pei_conn);
                  $res_update   = mysql_query($sql_update, $pei_conn);
                  if (!$res_update) {
                    $sync_succsess = FALSE;
                    $pei_messages['error'][] = 'Something went wrong with database query.';
                    //$req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                    //$req_msg .= 'Whole query: ' . $sql_update;
                    //die($req_msg);
                  }
                }
                else {
                  $sync_succsess = FALSE;
                  $igf_server_unsync[] = $server['igf_server_serial_number'];
                }
              }
            }
          }

          // If VIRTUAL  i.e. id is 4
          if($server['igf_server_type_id'] == 4) {

            if($server['igf_server_number']) {
              $vm_phy_sr      = '';
              $server_vm_sr   = '';
              $server_vm_sr_2 = '';
              //var_dump($server['igf_server_serial_number']);
              if($server['igf_server_serial_number']) {
                // Extract Physical serial number form vm serial number
                $vm_phy_sr = strstr(trim($server['igf_server_serial_number']), '-', true);

                // Check is virtual serial number has V or not
                if(strstr(strtoupper(trim($server['igf_server_serial_number'])), '-V')) {
                  $vm_index = strstr(strtoupper(trim($server['igf_server_serial_number'])), '-V');
                  $vm_index = ltrim($vm_index, '-V');

                  $server_vm_sr   = $vm_phy_sr.'-'.sprintf("%02d", $vm_index);
                  $server_vm_sr_2 = $server['igf_server_serial_number'];
                }
                else {
                  $vm_index = strstr(strtoupper(trim($server['igf_server_serial_number'])), '-');
                  $vm_index = ltrim($vm_index, '-');

                  $server_vm_sr   = $server['igf_server_serial_number'];
                  $server_vm_sr_2 = $vm_phy_sr.'-V'.sprintf("%02d", $vm_index);
                }

              }
              else {
                if($phy_sr) {
                  $vm_phy_sr = $phy_sr;
                }
                $server_vm_sr   = $vm_phy_sr.'-'.sprintf("%02d", $vm_index);
                $server_vm_sr_2 = $vm_phy_sr.'-V'.sprintf("%02d", $vm_index);
              }
              //echo '$server_vm_sr---:'.$server_vm_sr.'<br />';
              //echo '$server_vm_sr_2-:'.$server_vm_sr_2.'<br />';
              $sql_server_vm = " SELECT *
                                  FROM t_vm_provisioning
                                  WHERE
                                    (
                                      VM_SERIALNUMBER = TRIM('".mysql_real_escape_string($server_vm_sr_2)."')
                                    )
                                    AND
                                    SERIALNUMBER    = TRIM('".mysql_real_escape_string($vm_phy_sr)."')
                                  ";
              //echo 'SQL :'.$sql_server_vm.'<br />';
              $res_server_vm = mysql_query($sql_server_vm, $pei_conn_2);
              if (!$res_server_vm) {
                $pei_messages['error'][] = 'Something went wrong with database query.';
                //$req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                //$req_msg .= 'Whole query: ' . $sql_server_vm;
                //die($req_msg);
              }
              else {
                $row_count_vm  = 0;
                $row_vm        = '';
                $row_count_vm = mysql_num_rows($res_server_vm);
                //echo '$row_count_vm :'.$row_count_vm.'<br />';
                if($row_count_vm == 1){
                  $row_vm = mysql_fetch_array($res_server_vm);
                  //var_dump($row_vm);

                  /*
                  // Update server vm detail :-
                      VM_HOSTNAME, ILORSA_IP
                      VM_VLAN_ID,
                      VM_DATA_IP, VM_DATA_SUBNET_MASK, VM_DATA_GATEWAY
                  */

                  $sql_update_vm =   " UPDATE idc_igf_server SET ";
                  $sql_update_vm .=  " igf_server_serial_number  ='".mysql_real_escape_string($server_vm_sr)."',  ";
                  $sql_update_vm .=  " igf_server_hostname       ='".mysql_real_escape_string($row_vm['VM_HOSTNAME'])."',  ";
                  $sql_update_vm .=  " igf_server_console_ip     ='".mysql_real_escape_string($row_vm['ILORSA_IP'])."',  ";
                  //$sql_update_vm .=  " igf_server_console_ip_sm  ='".mysql_real_escape_string($row_vm['ILORSA_SUBNET_MASK'])."',  ";
                 // $sql_update_vm .=  " igf_server_console_ip_gw  ='".mysql_real_escape_string($row_vm['ILORSA_GATEWAY'])."',  ";
                  $sql_update_vm .=  " igf_server_data_ip_1      ='".mysql_real_escape_string($row_vm['VM_DATA_IP'])."',  ";
                  //$sql_update_vm .=  " igf_server_data_ip_2      ='".mysql_real_escape_string($row_vm['DATA_IP'])."',  ";
                  //$sql_update_vm .=  " igf_server_vip            ='".mysql_real_escape_string($row_vm['VIRTUAL_IP_ADDRESS'])."',  ";
                  $sql_update_vm .=  " igf_server_data_ip_sm     ='".mysql_real_escape_string($row_vm['VM_DATA_SUBNET_MASK'])."',  ";
                  $sql_update_vm .=  " igf_server_data_ip_gw     ='".mysql_real_escape_string($row_vm['VM_DATA_GATEWAY'])."',  ";
                  //$sql_update_vm .=  " igf_server_lb_ip          ='".mysql_real_escape_string($row_vm[''])."',  ";
                  //$sql_update_vm .=  " igf_server_other_ip       ='".mysql_real_escape_string($row_vm[''])."',  ";
                  //$sql_update_vm .=  " igf_server_other_ip_sm    ='".mysql_real_escape_string($row_vm[''])."',  ";
                  //$sql_update_vm .=  " igf_server_other_ip_gw    ='".mysql_real_escape_string($row_vm[''])."',  ";
                  //$sql_update_vm .=  " igf_server_public_ip      ='".mysql_real_escape_string($row_vm[''])."',  ";
                  if($server_misc) {
                    $sql_update .=  " igf_server_misc             ='".mysql_real_escape_string($server_misc)."',  ";
                  }
                  $sql_update_vm .=  " updated_by                ='".mysql_real_escape_string($uname)."',  ";
                  $sql_update_vm .=  " updated_at                = NOW()";
                  $sql_update_vm .=  " WHERE ";
                  $sql_update_vm .=  " 1 ";
                  $sql_update_vm .=  " AND ";
                  //$sql_update_vm .=  " igf_server_serial_number  ='".mysql_real_escape_string($server['igf_server_serial_number'])."' ";
                  //$sql_update_vm .=  " AND ";
                  $sql_update_vm .=  " igf_server_id='".mysql_real_escape_string($server['igf_server_id'])."' ;";
                  //echo $sql_update_vm.'<br />';
                  $res_update_vm   = mysql_query($sql_update_vm, $pei_conn);
                  if (!$res_update_vm) {
                    $sync_succsess = FALSE;
                    $pei_messages['error'][] = 'Something went wrong with database query.';
                    //$req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                    //$req_msg .= 'Whole query: ' . $sql_update_vm;
                    //die($req_msg);
                  }
                }
                else {
                  $sync_succsess = FALSE;
                  $igf_server_unsync[] = $server_vm_sr;
                }
              }


              /*
              $sql_test = "SELECT DISTINCT SERIALNUMBER FROM t_vm_provisioning ORDER BY SERIALNUMBER ";
              echo $sql_test.'<br />';
              $res_test = mysql_query($sql_test, $pei_conn_2);
              if (!$res_test) {
                $req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                $req_msg .= 'Whole query: ' . $sql_test;
                //die($req_msg);
              }
              else {
                while($r = mysql_fetch_array($res_test)) {
                  $sql_check = "SELECT * FROM idc_igf_server WHERE igf_server_serial_number='".mysql_real_escape_string($r['SERIALNUMBER'])."'";
                  echo $sql_check.'<br />';
                  $res_check = mysql_query($sql_check, $pei_conn);
                  if (!$res_check) {
                    $req_msg .= 'Invalid query: ' . mysql_error() . "<br />";
                    $req_msg .= 'Whole query: ' . $sql_check;
                    //die($req_msg);
                  }
                  else {
                    if(mysql_num_rows($res_check)){
                      $test_server = mysql_fetch_array($res_check);
                      //var_dump($test_server);
                      echo $test_server['igf_server_id'].'<br />';
                    }
                  }
                }
              }

              */

              $vm_index++;
            }
          }

        }

        mysql_close ($pei_conn_2);
        //echo $pei_config['db']['db1']['dbname'].'<br />';
        mysql_select_db($pei_config['db']['db1']['dbname'], $pei_conn);
        $igf_server = '';
        // Get Updated IGF Server information
        //$igf_server = get_igf_server_by_igf_id($igf_id);
        $sql  = " SELECT s.*
                  FROM
                  idc_igf_server AS s
                  WHERE s.igf_id='".mysql_real_escape_string($igf_id)."' ";
        $res = mysql_query($sql, $pei_conn);
        if($res){
          while ( $row = mysql_fetch_array($res)) {
            $igf_server[] = $row;
          }
        }
      }

      // Updated igf_server_checked status
      // Fetch Previous save igf_server_checked status and compare with current values
      // To remove extra igf_server_checked status
      $remove_checked = array_diff($igf_server_checked_prev, $igf_server_checked);
      if($remove_checked) {
        //update_igf_server_checked($remove_checked, '0');
        $sql_check_rm = " UPDATE idc_igf_server SET ";
        $sql_check_rm .=" igf_server_checked ='0' ";
        $sql_check_rm .=" WHERE  ";
        $sql_check_rm .=" igf_server_release_at IS NULL ";
        $sql_check_rm .=" AND ";
        $sql_check_rm .=" igf_server_id IN (";
        foreach ($remove_checked as $key => $value) {
          $sql_check_rm .="'".mysql_real_escape_string($value)."',";
        }
        $sql_check_rm = rtrim($sql_check_rm, ',');
        $sql_check_rm .=") ";
        $res_check_rm = mysql_query($sql_check_rm, $pei_conn);
      }
      // To add new addition igf_server_checked status
      $add_checked = array_diff($igf_server_checked, $igf_server_checked_prev);
      if($add_checked) {
        //update_igf_server_checked($add_checked, '1');
        $sql_check_add = " UPDATE idc_igf_server SET ";
        $sql_check_add .=" igf_server_checked ='1' ";
        $sql_check_add .=" WHERE  ";
        $sql_check_add .=" igf_server_id IN (";
        foreach ($add_checked as $key => $value) {
          $sql_check_add .="'".mysql_real_escape_string($value)."',";
        }
        $sql_check_add = rtrim($sql_check_add, ',');
        $sql_check_add .=") ";
        $res_check_add = mysql_query($sql_check_add, $pei_conn);
      }

      // Check if Request STATUS is RFI or not
      // IF not then disabel RELEASE Button
      if($igf_request['req_status'] == ('RFI' || 'RELEASE') ){
        // SET Send Released Mail flag
        $mail_released = TRUE;
      }

      if($sync_succsess) {
        $pei_messages['success'][] = 'IFG Data Successfully Sync With CMDB Data.';
      }
      else {
        $pei_messages['error'][] = 'Failed to sync following Serial Numbers with CMDB Data.';
        if($igf_server_unsync) {
          foreach ($igf_server_unsync as $key => $value) {
            $pei_messages['error'][] = $value;
          }
        }
      }




    }

  }



  $igf_count = 0;
  // Get UPDATED Server Details
  $sql_server = " SELECT
                  i_ser.*,
                  l.loc_name AS igf_server_loc_name,
                  sh.sh_name AS igf_server_server_hall_name,
                  rr.rr_name AS igf_server_row_rack_name,
                  isrs.igf_server_release_id
                FROM
                  idc_igf_server AS i_ser
                  LEFT JOIN
                  idc_location AS l ON i_ser.igf_server_loc_id = l.loc_id
                  LEFT JOIN
                  idc_server_hall AS sh ON i_ser.igf_server_server_hall_id = sh.sh_id
                  LEFT JOIN
                  idc_row_rack AS rr ON i_ser.igf_server_row_rack_id = rr.rr_id
                  LEFT JOIN
                  idc_igf_server_release_server AS isrs ON i_ser.igf_server_id = isrs.igf_server_id
                WHERE i_ser.igf_id='".mysql_real_escape_string($igf_id)."'
                ORDER BY i_ser.igf_server_id
                ";
  //echo 'SQL :'.$sql_server.'<br />';
  $res_server   = mysql_query($sql_server, $pei_conn);
  if($res_server) {
    $igf_server = '';
    while ( $row = mysql_fetch_array($res_server)) {
      $igf_server[] = $row;
      $igf_count++;
    }
  }


  // Get Equipment Details
  $sql_equi   = " SELECT s.*
                  FROM
                  idc_igf_equipment AS s
                  WHERE s.igf_id='".mysql_real_escape_string($igf_id)."' ";
  $res_equi   = mysql_query($sql_equi, $pei_conn);
  if($res_equi){
    while ( $row = mysql_fetch_array($res_equi)) {
      $igf_equipment[] = $row;
    }
  }

  //echo '<pre>';
  //var_dump($igf_equipment);
  //echo '<pre>';

  // Get Software Details
  $sql_sw   = " SELECT s.*
                  FROM
                  idc_igf_software AS s
                WHERE s.igf_id='".mysql_real_escape_string($igf_id)."' ";
  $res_sw   = mysql_query($sql_sw, $pei_conn);
  if($res_sw){
    while ( $row = mysql_fetch_array($res_sw)) {
      $igf_software[] = $row;
    }
  }

  //echo '$igf_count :'.$igf_count.'<br />';
  // Check IGF Released STATUS
  $igf_released = FALSE;
  $sql_igf_rel  =   " SELECT * ";
  $sql_igf_rel  .=  " FROM idc_igf_status ";
  $sql_igf_rel  .=  " WHERE ";
  $sql_igf_rel  .=  "   igf_id='".mysql_real_escape_string($igf_id)."' ";
  $sql_igf_rel  .=  "   AND ";
  $sql_igf_rel  .=  "   status_id='".mysql_real_escape_string($status_id)."' ";
  $sql_igf_rel  .=  " LIMIT 0, 1";

  $sql_igf_rel  = " SELECT
                      COUNT(isrs.igf_server_release_server_id) AS released_servers
                    FROM
                      idc_igf_server_release_server AS isrs
                      LEFT JOIN
                      idc_igf_server_release AS isr ON isrs.igf_server_release_id = isr.igf_server_release_id
                    WHERE
                     isr.igf_id = '".mysql_real_escape_string($igf_id)."'
                  ";
  //echo $sql_igf_rel.'<br />';
  $res_igf_rel   = mysql_query($sql_igf_rel, $pei_conn);
  if($res_igf_rel) {
    $released_server = mysql_fetch_array($res_igf_rel);
    if($released_server['released_servers'] == $igf_count){
      $igf_released = TRUE;
    }
  }


// Check if all server are checked or released
foreach ($igf_server as $key => $value) {
  if($value['igf_server_checked'] == '0') {
    $igf_server_all_checked = FALSE;
  }

  if($value['igf_server_release_at'] == NULL || empty($value['igf_server_release_at'])) {
    $igf_server_all_released = FALSE;
  }

  if($value['igf_server_release_id'] && !in_array($value['igf_server_release_id'], $igf_server_released)) {
    $igf_server_released[] = $value['igf_server_release_id'];
  }

  if($value['igf_server_checked'] == '1' && $value['igf_server_release_id'] == NULL) {
    $server_release_count++;
  }
}

// Sort Server Released ID
sort($igf_server_released);
// Flip Server Release ID with release count
$igf_server_released_ver  = array_flip($igf_server_released);
// Define Release Number for each server release ID accordingly
switch(count($igf_server_released_ver)) {
  case '0':
    break;
  case '1':
    // If IGF is Fully released in single release then add 'R'
    // Else add 'R' with release no.
    foreach ($igf_server_released_ver as $key => $value) {
      $igf_server_released_ver[$key] = ($igf_server_all_released) ? 'F' : 'R1';
    }
    break;
  default:
    $ver = 1;
    foreach ($igf_server_released_ver as $key => $value) {
      $igf_server_released_ver[$key] = 'R'.$ver;
      $ver++;
    }
}

//var_dump($mail_released);
//var_dump($igf_released);
//echo '</pre>';
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
    <?php if($igf_request) {
      ?>
      <h5> #<?php echo $req_id;?> <small><?php echo strtoupper($igf_request['req_title']);?></small></h5>
      <?php
      }
      ?>
      <form enctype="multipart/form-data" action="" method="POST">
        <ul class="nav nav-tabs" role="tablist" id="myTab">
        <li role="presentation" class="active"><a href="#contact" aria-controls="contact" role="tab" data-toggle="tab">CONTACT & BUDGET INFORMATION</a></li>
        <li role="presentation"><a href="#server" aria-controls="server" role="tab" data-toggle="tab"><?php echo $igf_header_3;?></a></li>
        <li role="presentation"><a href="#equipment" aria-controls="equipment" role="tab" data-toggle="tab">EQUIPMENT LIST</a></li>
        <li role="presentation"><a href="#software" aria-controls="software" role="tab" data-toggle="tab">SOFTWARE LIST</a></li>

        <div style="float:right;">
          <a class="btn btn-primary" href="<?php echo $pei_config['urls']['baseUrl'];?>/igf/igf_download.php?igf_id=<?php echo $igf_id;?>" role="button">Download</a>
          <?php if($pei_access_sync) {?>
          <button type="submit" name="igf_sync" id="igf-sync-top" value="Sync" class="btn btn-primary btn-label-left">Sync & Save</button>
          <?php
            /* confirm('Before initiating RELEASE Mail have you verified Release IGF data.? \n OK will initiate the RELEASE Mail.') */
            if($pei_access_release && $mail_released && !$igf_released) {
          ?>
          <button type="submit" name="igf_release" id="igf-released-top" value="release" class="btn btn-primary btn-label-left" onclick="return validate_release();">Release (<?php echo $server_release_count;?>)</button>
          <?php
            }
          }
          ?>
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
            <th width="20%">REQUESTOR GROUP</th><td colspan="2" align="left"><?php echo strtoupper($igf['req_group_name']);?></td>
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
        <div role="tabpanel" class="tab-pane" id="server">
          <table class="table-igf-server table-bordered table-hover">
            <tr>
              <th width="60px">RELEASE<br />
                <div class="checkbox pei-igf-release-checkbox">
                  <label>
                    <input type="checkbox" id="igf-server-checked-all" class="igf-server-checked-all"  name="igf_server_checked_all" value="" <?php if($igf_server_all_checked) {?> checked <?php } ?> <?php if($igf_server_all_released) { ?> disabled <?php } ?>> Select All
                  </label>
                </div>
              </th>
              <th width="90px">RELEASED DATE</th>
              <th width="80px">RELEASED NUMBER</th>
              <th width="150px">SERVER NUMBER</th>
              <th width="150px">SERVER SERIAL NUMBER</th>
              <th width="300px">CLUSTER SPECIFIC INFORMATION / MISCELLANEOUS</th>
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
              <th width="150px">ENVIRONMENT</th>
              <th width="150px">LOCATION</th>
              <th width="150px">SERVER HALL</th>
              <th width="150px">ROW-RACK</th>
              <th width="150px">RACK NAME</th>
              <th width="150px">RACK "U"</th>
              <th width="150px">SLOT NO.</th>
              <th width="150px">SERVER TYPE</th>
              <th width="150px">HYPERVISOR</th>
              <th width="150px">SERVER ROLE</th>
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
              <th width="150px">APPLICATION NAME</th>
              <th width="150px">REQUESTOR GROUP</th>
              <th width="150px">REQUESTOR SUB-GROUP</th>
            </tr>
      <?php
      if($igf_server) {
        foreach ($igf_server as $key => $server) {
          # code...
          /* <?php echo $server[''];?>  */
      ?>
            <tr>
              <td><!-- RELEASE--><input type="checkbox" pei-data-type-id="<?php echo $server['igf_server_type_id'];?>" pei-data-serial="<?php echo $server['igf_server_serial_number'];?>" class="igf-server-checked" name="igf_server_checked[]" value="<?php echo $server['igf_server_id'];?>" <?php if($server['igf_server_checked'] == '1') {?> checked <?php } ?> <?php if($server['igf_server_release_at'] != NULL) { ?> disabled <?php } ?> ></td>
              <td><!-- RELEASED DATE --><?php echo ($server['igf_server_release_at']) ? pei_date_format($server['igf_server_release_at']) : '';?></td>
              <td><!-- RELEASED NUMBER -->
                <?php
                if($server['igf_server_release_id'] && $igf_server_released_ver) {
                  echo $igf_server_released_ver[$server['igf_server_release_id']];
                }
                ?>
              </td>
              <td><!-- SERVER NUMBER --><?php echo $server['igf_server_number'];?></td>
              <td><!-- SERVER SERIAL NUMBER --><?php echo $server['igf_server_serial_number'];?></td>
              <td><!-- CLUSTER SPECIFIC INFORMATION -->
              <?php
                $igf_server_misc = $server['igf_server_misc'];
                if(isset($_POST['igf_server_misc_'.$server['igf_server_id']])) {
                  $igf_server_misc = $_POST['igf_server_misc_'.$server['igf_server_id']];
                }
                $igf_server_misc_title = 'Miscellaneous  Info for '.$server['igf_server_number'].' '.$server['igf_server_serial_number'];

                if($server['igf_server_release_at'] != NULL) {
                  echo  nl2br($igf_server_misc);
                }
                else {
              ?>
                <textarea class="form-control" name="igf_server_misc_<?php echo $server['igf_server_id'];?>" placeholder="<?php echo $igf_server_misc_title;?>" data-toggle="tooltip" data-placement="bottom" title="<?php echo $igf_server_misc_title;?>" <?php if($server['igf_server_release_at'] != NULL) { ?> disabled <?php } ?>><?php echo $igf_server_misc;?></textarea>
              <?php
                }
              ?>
              </td>
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
<?php
  switch($igf_version){
    case 'v3':
    case 'v3.1':
      ?>
              <td><!-- OTHER IP --><?php echo $server['igf_server_other_ip'];?></td>
              <td><!-- SM --><?php echo $server['igf_server_other_ip_sm'];?></td>
              <td><!-- GW --><?php echo $server['igf_server_other_ip_gw'];?></td>
              <td><!-- PUBLIC IP --><?php echo $server['igf_server_public_ip'];?></td>
      <?php
      break;
    case 'v4':
    default:
?>
                <td><!-- PUBLIC IP --><?php echo $server['igf_server_public_ip'];?></td>
                <td><!-- EQUIPMENT (PRIVATE) LAN IP v4 --><?php echo $server['igf_server_public_ip'];?></td>
                <td><!-- EQUIPMENT (PRIVATE) SUBNET MASK v4 --><?php echo $server['igf_server_private_lan_sm'];?></td>
                <td><!-- RAC IP v4 --><?php echo $server['igf_server_rac_ip'];?></td>
                <td><!-- SCAN IP v4 --><?php echo $server['igf_server_scan_ip'];?></td>
                <td><!-- Heartbeat IP v4 --><?php echo $server['igf_server_heartbeat_ip'];?></td>
                <td><!-- Cluster Interconnect PRIVATE IP v4 --><?php echo $server['igf_server_cluster_ic_ip'];?></td>
                <td><!-- Oracle IP v4 --><?php echo $server['igf_server_oracle_vip'];?></td>
<?php
  }
?>

              <td><!-- ENVIRONMENT --><?php echo $server['igf_server_env'];?></td>
              <td><!-- LOCATION --><?php echo $server['igf_server_loc'];?></td>
              <td><!-- SERVER HALL--><?php echo $server['igf_server_server_hall'];?></td>
              <td><!-- ROW-RACK--><?php echo $server['igf_server_row_rack'];?></td>
              <td><!-- RACK NAME--><?php echo $server['igf_server_rack_name'];?></td>
              <td><!-- RACK "U" --><?php echo $server['igf_server_rack_u'];?></td>
              <td><!-- SLOT NO. --><?php echo $server['igf_server_slot_no'];?></td>
              <td><!-- SERVER TYPE --><?php echo $server['igf_server_type'];?></td>
              <td><!-- HYPERVISOR --><?php echo $server['igf_server_hypervisor'];?></td>
              <td><!-- SERVER ROLE --><?php echo $server['igf_server_role'];?></td>
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
                <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string( $server['igf_server_storage_ext_fs'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_storage_ext_fs']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_storage_ext_fs'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
              </td>
              <td><!-- VOLUME MANAGER --><?php echo $server['igf_server_volume_manager'];?></td>
              <td><!-- KERNEL PARAMETERS -->
                <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string($server['igf_server_kernel_parameter'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_kernel_parameter']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_kernel_parameter'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
              </td>
              <td><!-- ADDITIONAL PACKAGES -->
                <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string( $server['igf_server_additional_package'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_additional_package']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
                <span class="pei-content-more hide"><span class="pei-content-more-text"><?php echo $server['igf_server_additional_package'];?></span><span class="pei-content-more-link">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show Less</a></span></span>
              </td>
              <td><!-- USER ID : GROUP ID : HOME DIR --><?php echo $server['igf_server_user_id'];?></td>
              <td><!-- IDC SUPPORT REQUIREMENT --><?php echo $server['igf_server_idc_support'];?></td>
              <td><!-- REMARKS / ADDITIONAL NOTES -->
                <span class="pei-content-less"><span class="pei-content-less-text"><?php echo pei_display_string( $server['igf_server_remark'], $pei_less_word_count );?></span><span class="pei-content-less-link <?php if(strlen($server['igf_server_remark']) < $pei_less_word_count ) { echo 'hide'; }?>">&nbsp<a href="javascript:void(0);" class="pei-content-less-more-link">Show More</a></span></span>
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
              <td><!-- APPLICATION NAME --><?php echo $server['igf_server_app'];?></td>
              <td><!-- REQUESTOR GROUP --><?php echo $server['igf_server_req_group_name'];?></td>
              <td><!-- REQUESTOR SUB-GROUP --><?php echo $server['igf_server_req_sub_group_name'];?></td>
            </tr>
      <?php
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
      </div>

      </form>
<?php
} // END else $pei_page_access
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

    $('#myTab li:eq(1) a').tab('show');

    $("#igf-server-checked-all").change(function() {
      var checked_count = 0;
      var checked = $(this).is(":checked");
      $("input[name='igf_server_checked[]']").each(function(index, elem) {
        if (!$(elem).is(":disabled")) {
          $(elem).prop("checked", checked);
        }

        if (!$(elem).is(":disabled") && $(elem).is(":checked")) {
          checked_count = checked_count + 1;
        }
      });
      $("#igf-released-top").text('Release ('+checked_count+')');
    });

    $(".igf-server-checked").change(function() {
      var checked_count = 0;
      var checked       = true;
      $("input[name='igf_server_checked[]']").each(function(index, elem) {
        if (!$(elem).is(":disabled") && !$(elem).is(":checked")) {
          checked = false;
        }

        if (!$(elem).is(":disabled") && $(elem).is(":checked")) {
          checked_count = checked_count + 1;
        }

      });
      //igf-released-top
      $("#igf-released-top").text('Release ('+checked_count+')');
      $("#igf-server-checked-all").prop("checked", checked);
    });

  });

  function validate_release() {
    var ret = false;
    var checkboxValues    = [];
    var anyBoxesChecked   = false;
    var anyBoxesDisabled  = false;
    var checkedCount      = 0;
    var disabledCount     = 0;
    var confirmFlag       = false;
    $("input[name='igf_server_checked[]']").each(function(index, elem) {
      if ($(elem).is(":checked")) {
        checkedCount++;
        anyBoxesChecked = true;
      }
      if ($(elem).is(":disabled")) {
        disabledCount++;
        anyBoxesChecked = true;
      }
    });

    if(checkedCount > 0 ) {
      if(checkedCount == disabledCount){
        alert('Please Select Server to be released.');
        confirmFlag = false;
      }
      else {
        var phy_serial      = '';
        var phy_serial_last = '';
        var phy_serial_msg  = '';
        var checked_phy     = false;
        var checked_vir     = false;
        var checked_all_vir = true;
        // Check if all Virtual Server are selected for selected Physical server for release
        $("input[name='igf_server_checked[]']").each(function(index, elem) {
          if($(elem).attr("pei-data-type-id") == 3) {
            checked_phy = $(elem).is(":checked");
            phy_serial  = $(elem).attr("pei-data-serial");
          }
          else {
            if((!checked_phy && $(elem).is(":checked")) || (checked_phy && !$(elem).is(":checked")) ) {
              checked_all_vir = false;
              if(phy_serial_last != phy_serial) {
                phy_serial_last = phy_serial;
                phy_serial_msg  = phy_serial_msg+phy_serial+',';
              }
            }
          }
        });

        if(checked_all_vir){
          confirmFlag = true;
        }
        else {
          phy_serial_msg = phy_serial_msg.replace(/,\s*$/, "");
          if(confirm('Base Server(s) ['+phy_serial_msg+'] cannot be release before releasing all virtual server(s) for those base server(s).\n If you want to proceed with exception click OK else click cancel.')){
            confirmFlag = true;
          }
        }
      }
    }
    else {
      alert('Please Select Server to be released.');
      confirmFlag = false;
    }

    if(confirmFlag) {
      if(confirm('Before initiating RELEASE Mail have you verified Release IGF data.? \n OK will initiate the RELEASE Mail.')) {
        ret = true;
      }
    }
    return ret;
  }
</script>

