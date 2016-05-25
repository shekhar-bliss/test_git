<?php
  session_start();
  //echo '<pre>';
  error_reporting(E_ALL); ini_set('display_errors', 1);
  //ini_set('memory_limit', '-1');
  ini_set('memory_limit', '2048M');
  ini_set('max_execution_time', 0);

  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");

  /** Include PHPExcel */
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel/IOFactory.php');
  require_once($pei_config['paths']['vendors'].'/PHPExcel/Classes/PHPExcel.php');

  //$_GET['igf_id'] = '';
  //echo '<pre>';

  // Initialize variables
  $pei_msg        = '';
  $igf_id         = '';
  $igf            = '';
  $igf_file_path  = '';
  $igf_file_name  = '';
  $igf_server     = '';
  $igf_version          = $pei_config['igf']['version'];
  // Request Number start from req_id number
  $igf_version_req_id   = $pei_config['igf']['after_req_id'];
  $req_id_num           = '';
  $igf_server_misc_col  = 'CL';
  $igf_server_public_col= 'CD';

  if(isset($_GET['igf_id'])) {
    $igf_id   = $_GET['igf_id'];
  }


  //echo '$igf_id :'.$igf_id.'<br />';
  // Validate IGF ID
  if($igf_id) {
    // Get IGF details
    $igf = get_igf_detail_by_igf_id($igf_id);
    //var_dump($igf);

    // Get Request Details from this igf
    $igf_request  = get_request_detail_from_igf_id($igf_id);
    //var_dump($igf_request);
    if($igf_request['req_id']) {
      $req_id = $igf_request['req_id'];
    }
    // Find IGF version
    $req_id_num = substr($req_id, -4);
    if($req_id_num <= $igf_version_req_id) {
      $igf_version  = 'v3.1';
    }

    // Get Server Details
    $igf_server = get_igf_server_detail_by_igf_id($igf_id);
    //var_dump($igf_server);

    $igf_file_path = $igf['doc_file_path'];

    $igf_file_name = $igf['doc_file_name'];
    // Overwrite original file name to
    $igf_file_name = $igf_request['req_id'].'-IGF.xlsx';

    $objPHPExcel  = PHPExcel_IOFactory::createReader('Excel2007');
    $objPHPExcel  = $objPHPExcel->load($igf_file_path);

    // Update Server Sheet
    $objPHPExcel->setActiveSheetIndex(3);

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


    if($igf_server) {
      foreach ($igf_server as $key => $server) {
        // Update LOCATION
        $objPHPExcel->getActiveSheet()->setCellValue('E'.($key+3), $server['igf_server_loc']);
        // Update SERVER HALL
        $objPHPExcel->getActiveSheet()->setCellValue('F'.($key+3), $server['igf_server_server_hall']);
        // Update ROW-RACK
        $objPHPExcel->getActiveSheet()->setCellValue('G'.($key+3), $server['igf_server_row_rack']);
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
        }

        // Update CLUSTER SPECIFIC INFORMATION
        $objPHPExcel->getActiveSheet()->setCellValue($igf_server_misc_col.($key+3), $server['igf_server_misc']);
      }
    }




    // Redirect output to a client’s web browser (Excel2007)
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$igf_file_name.'"');
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
  else {
    $pei_msg        = 'Invalid IGF ID';
  }
  //echo $pei_msg.'<br />';



//echo '</pre>';
?>
