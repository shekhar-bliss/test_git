<?php
  session_start();
  error_reporting(FALSE);
  ini_set('display_errors', FALSE);
  ini_set('display_startup_errors', FALSE);

  $pei_current_module = 'REQUEST';

  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");

  /** Include PHPExcel */
  //echo $pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel.php'.'<br />';
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel.php');

  //require_once dirname(__FILE__) . '/../Classes/PHPExcel/IOFactory.php';
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel/IOFactory.php');


//echo '<pre>';
//echo '<br /><br /><br /><br />';
//echo 'TEST123<br />';
//var_dump($_POST);

  // Initialize variables
  $pei_msg        = '';
  $req_msg        = '';
  $sql_where      = ' 1 ';
  $igf_id         = $_REQUEST['id'];
  $req_id         = '';
  $igf_server_released      = '';
  $igf_server_released_ver  = '';
  $igf_servers    = '';


  if($igf_id) {
    // Get Request Details from this igf
    $igf_request      = get_request_detail_from_igf_id($igf_id);
    if($igf_request){
      $req_id = $igf_request['req_id'];
    }
    $download_file_name   = 'CONSOLIDATED RELEASED - '.$req_id.'.xlsx';

    $rep_header   = array(
      'req_id'      => array('name' => 'Release ID','style_h_align' => 'center','db_col_name' => 'req_id' ),
      'release_no'      => array('name' => 'Release Number','style_h_align' => 'center'),
      'igf_server_release_at'    => array('name' => 'Release Date','style_h_align' => 'center', 'db_col_name' => 'igf_server_release_at'),
      'app_name'        => array('name' => 'APPLICATION NAME', 'db_col_name' => 'igf_server_app'),
      'user_group'      => array('name' => 'REQUESTOR GROUP', 'db_col_name' => 'igf_server_req_group_name'),
      'user_sub_group'   => array('name' => 'REQUESTOR SUB-GROUP', 'db_col_name' => 'igf_server_req_sub_group_name'),
      'env'        => array('name' => 'ENVIRONMENT', 'db_col_name' => 'igf_server_env'),
      'loc'        => array('name' => 'LOCATION', 'db_col_name' => 'igf_server_loc'),
      'sh'        => array('name' => 'SERVER HALL', 'db_col_name' => 'igf_server_server_hall'),
      'rr'        => array('name' => 'ROW-RACK', 'db_col_name' => 'igf_server_row_rack'),
      'rack'        => array('name' => 'RACK NAME', 'db_col_name' => 'igf_server_rack_name'),
      'rack_u'        => array('name' => 'RACK "U"', 'db_col_name' => 'igf_server_rack_u'),
      'slot'        => array('name' => 'SLOT NO.', 'db_col_name' => 'igf_server_slot_no'),
      'serv_num'        => array('name' => 'SERVER NUMBER', 'db_col_name' => 'igf_server_number'),
      'serv_type'        => array('name' => 'SERVER TYPE', 'db_col_name' => 'igf_server_type'),
      'hyper'        => array('name' => 'HYPERVISOR', 'db_col_name' => 'igf_server_hypervisor'),
      'serv_rol'        => array('name' => 'SERVER ROLE', 'db_col_name' => 'igf_server_role'),
      'serv_serial'        => array('name' => 'SERVER SERIAL NUMBER', 'db_col_name' => 'igf_server_serial_number'),
      'serv_make'        => array('name' => 'SERVER MAKE', 'db_col_name' => 'igf_server_make'),
      'serv_model'        => array('name' => 'SERVER MODEL', 'db_col_name' => 'igf_server_model'),
      'serv_cpu_type'        => array('name' => 'CPU TYPE', 'db_col_name' => 'igf_server_cpu_type'),
      'serv_cpu_no'        => array('name' => '# of CPU / vCPU', 'db_col_name' => 'igf_server_cpu_no'),
      'serv_cpu_core'        => array('name' => 'TOTAL # of COREs', 'db_col_name' => 'igf_server_cpu_cores'),
      'serv_cpu_ram'        => array('name' => 'RAM (GB)', 'db_col_name' => 'igf_server_ram'),
      'serv_storage_int_no'        => array('name' => '# of INTERNAL HDDs', 'db_col_name' => 'igf_server_storage_int_no'),
      'serv_storage_int_size'        => array('name' => 'SIZE - INTERNAL DISKS', 'db_col_name' => 'igf_server_storage_int_size'),
      'serv_storage_int_raid_config'        => array('name' => 'RAID CONFIG - INTERNAL DISKS (RAID 1 / RAID 5 / RAID 1+0)', 'db_col_name' => 'igf_server_storage_int_raid_config'),
      'serv_nic_1g'        => array('name' => '# of NICs - 1G', 'db_col_name' => 'igf_server_nic_1g'),
      'serv_nic_10g'        => array('name' => '# of NICs - 10G', 'db_col_name' => 'igf_server_nic_10g'),
      'serv_fc_hba_card'        => array('name' => '# of FC HBA CARDS', 'db_col_name' => 'igf_server_fc_hba_card'),
      'serv_fc_hba_port'        => array('name' => '# of FC HBA PORTS', 'db_col_name' => 'igf_server_fc_hba_port'),
      'serv_fc_hba_port_speed'        => array('name' => 'FC HBA PORT SPEED', 'db_col_name' => 'igf_server_fc_hba_port_speed'),
      'serv_dl_port'        => array('name' => '# of DATA LAN PORTS', 'db_col_name' => 'igf_server_dl_port'),
      'serv_dl_type'        => array('name' => 'DATA LAN INTERFACE TYPE', 'db_col_name' => 'igf_server_dl_type'),
      'serv_dl_speed'        => array('name' => 'DATA LAN INTERFACE SPEED', 'db_col_name' => 'igf_server_dl_speed'),
      'serv_sl_port'        => array('name' => '# of SERVER LAN PORTS', 'db_col_name' => 'igf_server_sl_port'),
      'serv_sl_type'        => array('name' => 'SERVER LAN INTERFACE TYPE', 'db_col_name' => 'igf_server_sl_type'),
      'serv_sl_speed'        => array('name' => 'SERVER LAN INTERFACE SPEED', 'db_col_name' => 'igf_server_sl_speed'),
      'serv_cl_port'        => array('name' => '# of CLUSTER LAN PORTS', 'db_col_name' => 'igf_server_cl_port'),
      'serv_cl_type'        => array('name' => 'CLUSTER LAN INTERFACE TYPE', 'db_col_name' => 'igf_server_cl_type'),
      'serv_cl_speed'        => array('name' => 'CLUSTER LAN INTERFACE SPEED', 'db_col_name' => 'igf_server_cl_speed'),
      'serv_network_zone'        => array('name' => 'NETWORK ZONE', 'db_col_name' => 'igf_server_network_zone'),
      'serv_network_sub_zone'        => array('name' => 'NETWORK SUB ZONE', 'db_col_name' => 'igf_server_network_sub_zone'),
      'serv_load_balancer'        => array('name' => 'LOAD BALANCER REQUIRED', 'db_col_name' => 'igf_server_load_balancer'),
      'serv_ha_cluster'        => array('name' => 'HA / CLUSTER', 'db_col_name' => 'igf_server_ha_cluster'),
      'serv_ha_cluster_type'        => array('name' => 'HA TYPE / CLUSTER SOFTWARE', 'db_col_name' => 'igf_server_ha_cluster_type'),
      'serv_ha_cluster_pair'        => array('name' => 'HA / CLUSTER PAIR NUMBER', 'db_col_name' => 'igf_server_ha_cluster_pair'),
      'serv_os'        => array('name' => 'OS', 'db_col_name' => 'igf_server_os'),
      'serv_os_ver'        => array('name' => 'OS VERSION', 'db_col_name' => 'igf_server_os_version'),
      'serv_db'        => array('name' => 'DB', 'db_col_name' => 'igf_server_db'),
      'serv_db_ver'        => array('name' => 'DB VERSION', 'db_col_name' => 'igf_server_db_version'),
      'serv_storage_ext_type'        => array('name' => 'EXTERNAL STORAGE TYPE', 'db_col_name' => 'igf_server_storage_ext_type'),
      'serv_storage_ext_iops'        => array('name' => 'STORAGE IOPS', 'db_col_name' => 'igf_server_storage_ext_iops'),
      'serv_storage_ext_array'        => array('name' => 'STORAGE ARRAY', 'db_col_name' => 'igf_server_storage_ext_array'),
      'serv_storage_ext_raid_config'        => array('name' => 'EXTERNAL STORAGE RAID CONFIG', 'db_col_name' => 'igf_server_storage_ext_raid_config'),
      'serv_storage_ext_p_vol_space'        => array('name' => 'EXTERNAL STORAGE USABLE SPACE- P-VOL (in GB)', 'db_col_name' => 'igf_server_storage_ext_p_vol_space'),
      'serv_storage_ext_s_vol'        => array('name' => 'S-VOL (BCV) REQUIRED', 'db_col_name' => 'igf_server_storage_ext_s_vol'),
      'serv_storage_ext_s_vol_space'        => array('name' => 'EXTERNAL STORAGE USABLE SPACE- S-VOL (in GB)', 'db_col_name' => 'igf_server_storage_ext_s_vol_space'),
      'serv_storage_int_fs'        => array('name' => 'FILE SYSTEM DETAILS - INTERNAL HDD', 'db_col_name' => 'igf_server_storage_int_fs'),
      'serv_storage_ext_fs'        => array('name' => 'FILE SYSTEM DETAILS - EXTERNAL STORAGE', 'db_col_name' => 'igf_server_storage_ext_fs'),
      'serv_volume_manager'        => array('name' => 'VOLUME MANAGER', 'db_col_name' => 'igf_server_volume_manager'),
      'serv_kernel_parameter'        => array('name' => 'KERNEL PARAMETERS', 'db_col_name' => 'igf_server_kernel_parameter'),
      'serv_additional_package'        => array('name' => 'ADDITIONAL PACKAGES', 'db_col_name' => 'igf_server_additional_package'),
      'serv_user_id'        => array('name' => 'USER ID : GORUP ID : HOME DIR', 'db_col_name' => 'igf_server_user_id'),
      'serv_idc_support'        => array('name' => 'IDC SUPPORT REQUIREMENT', 'db_col_name' => 'igf_server_idc_support'),
      'serv_remark'        => array('name' => 'REMARKS / ADDITIONAL NOTES', 'db_col_name' => 'igf_server_remark'),
      'serv_reconfig_rm_ram'        => array('name' => 'REMOVE - RAM', 'db_col_name' => 'igf_server_reconfig_rm_ram'),
      'serv_reconfig_rm_hdd'        => array('name' => 'REMOVE - HDD', 'db_col_name' => 'igf_server_reconfig_rm_hdd'),
      'serv_reconfig_rm_nic'        => array('name' => 'REMOVE - NIC', 'db_col_name' => 'igf_server_reconfig_rm_nic'),
      'serv_reconfig_rm_fc_hba'        => array('name' => 'REMOVE - FC HBA', 'db_col_name' => 'igf_server_reconfig_rm_fc_hba'),
      'serv_reconfig_add_ram'        => array('name' => 'ADD - RAM', 'db_col_name' => 'igf_server_reconfig_add_ram'),
      'serv_reconfig_add_hdd'        => array('name' => 'ADD - HDD', 'db_col_name' => 'igf_server_reconfig_add_hdd'),
      'serv_reconfig_add_nic'        => array('name' => 'ADD - NIC', 'db_col_name' => 'igf_server_reconfig_add_nic'),
      'serv_reconfig_add_fc_hba'        => array('name' => 'ADD - FC HBA', 'db_col_name' => 'igf_server_reconfig_add_fc_hba'),
      'serv_hostname'        => array('name' => 'HOSTNAME', 'db_col_name' => 'igf_server_hostname'),
      'serv_console_ip'        => array('name' => 'CONSOLE IP (iLO / RSC)', 'db_col_name' => 'igf_server_console_ip'),
      'serv_console_ip_sm'        => array('name' => 'SUBNET MASK', 'db_col_name' => 'igf_server_console_ip_sm'),
      'serv_console_ip_gw'        => array('name' => 'GATEWAY', 'db_col_name' => 'igf_server_console_ip_gw'),
      'serv_data_ip_1'        => array('name' => 'DATA IP 1', 'db_col_name' => 'igf_server_data_ip_1'),
      'serv_data_ip_2'        => array('name' => 'DATA IP 2', 'db_col_name' => 'igf_server_data_ip_2'),
      'serv_vip'        => array('name' => 'VIP', 'db_col_name' => 'igf_server_vip'),
      'serv_data_ip_sm'        => array('name' => 'SUBNET MASK', 'db_col_name' => 'igf_server_data_ip_sm'),
      'serv_data_ip_gw'        => array('name' => 'GATEWAY', 'db_col_name' => 'igf_server_data_ip_gw'),
      'serv_lb_ip'        => array('name' => 'LB IP', 'db_col_name' => 'igf_server_lb_ip'),
      'serv_other_ip'        => array('name' => 'OTHER IP', 'db_col_name' => 'igf_server_other_ip'),
      'serv_other_ip_sm'        => array('name' => 'SM', 'db_col_name' => 'igf_server_other_ip_sm'),
      'serv_other_ip_gw'        => array('name' => 'GW', 'db_col_name' => 'igf_server_other_ip_gw'),
      'serv_public_ip'        => array('name' => 'PUBLIC IP', 'db_col_name' => 'igf_server_public_ip'),
      'serv_misc'        => array('name' => 'CLUSTER SPECIFIC INFORMATION / MISCELLANEOUS', 'db_col_name' => 'igf_server_misc'),
    );

    $sql_where .= " AND i_ser.igf_id='".mysql_real_escape_string($igf_id)."'";


    $sql_limit    = ' ';
    $sql_order_by = ' ORDER BY i_ser.igf_server_id';
    $sql_search   = "  SELECT
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
                      WHERE ".$sql_where."
                      ".$sql_order_by.'  '.$sql_limit.';';
    //echo $sql_search.'<br />';
    $res_search = mysql_query($sql_search);

    if($res_search){
      while ($row=mysql_fetch_array($res_search)) {
        $igf_servers[] = $row;
      }
    }
    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("JioDC Portal")
               ->setLastModifiedBy("JioDC Portal")
               ->setTitle("Request IGF Consolidated")
               ->setSubject("Request IGF Consolidated")
               ->setDescription("Request IGF Consolidated.")
               ->setKeywords("office 2007 openxml php Request Status Report")
               ->setCategory("Request IGF Consolidated file");

    $objPHPExcel->setActiveSheetIndex(0);
    $objPHPExcel->getActiveSheet()->setTitle('3. SERVER DETAILS');

    $col = 0;
    // Create Header for xlsx
    foreach($rep_header as $key => $header_col) {
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($col, 1, $header_col['name']);
      // Style the column
      $columnLetter = PHPExcel_Cell::stringFromColumnIndex($col);
      $objPHPExcel->getActiveSheet()->getColumnDimension($columnLetter)->setAutoSize(true);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter.'1')->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter.'1')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCD5B4');

      if(isset($header_col['style_h_align'])){
        switch(strtoupper($header_col['style_h_align'])) {
          case 'CENTER':
            $objPHPExcel->getActiveSheet()->getStyle($columnLetter)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
            break;
        }
      }

      $rep_header[$key]['col'] = $col;
      $rep_header[$key]['col_letter'] = $columnLetter;
      $col++;
    }

    if($igf_servers) {
      $download_row = 2;
      $record_found = mysql_num_rows($res_search);
      foreach ($igf_servers as $key => $server) {
        foreach ($rep_header as $rep_header_key => $rep_header_value) {
          $igf_server_released[] = $server['igf_server_release_id'];
          switch($rep_header_key) {
            case 'req_id':
              $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header[$rep_header_key]['col'], $download_row, $req_id);
              break;
            case 'release_no':
              break;
            case 'igf_server_release_at':
              if($server['igf_server_release_at']){
                $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header[$rep_header_key]['col'], $download_row, pei_date_format($server['igf_server_release_at']));
              }
              break;
            default:
              $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header[$rep_header_key]['col'], $download_row, $server[$rep_header[$rep_header_key]['db_col_name']]);
          }
        }

        $download_row++;
      }

      // Sort Server Released ID
      sort($igf_server_released);

      // Flip Server Release ID with release count
      $igf_server_released_ver  = array_flip($igf_server_released);

      $ver = 1;
      foreach ($igf_server_released_ver as $key => $value) {
        $igf_server_released_ver[$key] = $ver;
        $ver++;
      }

      $excel_row = 1;
      foreach ($igf_servers as $key => $server) {
        $excel_row++;
        // Update RELEASED NUMBER
        if($server['igf_server_release_id'] && $igf_server_released_ver[$server['igf_server_release_id']]){
          $objPHPExcel->getActiveSheet()->setCellValue('A'.($excel_row), $igf_server_released_ver[$server['igf_server_release_id']]);
        }
      }
    }

    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$download_file_name.'"');
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
  }
//echo '</pre>';
?>
