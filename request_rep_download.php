<?php
  session_start();
  error_reporting(FALSE);
  ini_set('display_errors', FALSE);
  ini_set('display_startup_errors', FALSE);

  $pei_current_module = 'REPORTS';

  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");

  require_once($pei_config['paths']['base'].'/igf/pei_igf.php');

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
  $req_status     = '';
  $req_rfi_from   = '';
  $req_rfi_to     = '';
  $req_group      = '';
  $req_user_group = '';
  $user_group     = '';
  $user_group_sub = '';
  $req_priority   = '';
  $req_name       = '';
  $sql_where     = ' 1 ';

  $req_status           = 'WIP/PARTIAL/ON-HOLD';
  $data                 = '';
  $data_priority        = pei_priority();
  $data_priority_val    = '';
  foreach ($data_priority as $key => $value) {
    $data_priority_val[$value['value']] = $value['name'];
  }
  $data_req_user_group  = get_requestor_user_group();
  $data_req_sub_group   = '';
  $data_req_status      = get_all_status();
  $req_user             = get_all_user();
  $download_file_name   = 'Request Status Report '.pei_date_format(date('d-M-Y')).'.xlsx';

  $project_count        = 0;
  $total_phy            = 0;
  $total_vir             = 0;


  $rep_header   = array(
    'sr_no'          => array('name' => 'Sr.','style_h_align' => 'center'),
    'req_id'          => array('name' => 'Request Number','style_h_align' => 'center'),
    'req_date'        => array('name' =>'Request Date','style_h_align' => 'center'),
    'req_created_by'  => array('name' =>'Created By'),
    'req_priority'    => array('name' =>'Priority','style_h_align' => 'center'),
    'req_pm'          => array('name' =>'PM / IM'),
    'req_eta'         => array('name' =>'Expected / Tentative ETA','style_h_align' => 'center'),
    'req_group'       => array('name' => 'Requestor Group'),
    'req_sub_group'   => array('name' => 'Requestor Sub-Group'),
    'req_title'       => array('name' => 'Request Title'),
    'req_env'         => array('name' => 'Environment'),
    'req_loc'         => array('name' => 'Location'),
    'req_sh'          => array('name' => 'Server Hall'),
    'req_total_server'=> array('name' => 'Total Equipment','style_h_align' => 'center'),
    'req_phy_server'  => array('name' => 'Physical Count','style_h_align' => 'center'),
    'req_vir_server'  => array('name' => 'Virtual Count','style_h_align' => 'center'),
    'req_san_node_count'  => array('name' => 'SAN (Node Count)','style_h_align' => 'center'),
    'req_san_phy_node_count'  => array('name' => 'SAN Physical (Node Count)','style_h_align' => 'center'),
    'req_san_vir_node_count'  => array('name' => 'SAN Virtual (Node Count)','style_h_align' => 'center'),
    'req_cluster_node_count'  => array('name' => 'CLUSTER (Node Count)','style_h_align' => 'center'),
    'req_cluster_phy_node_count'  => array('name' => 'CLUSTER Physical (Node Count)','style_h_align' => 'center'),
    'req_cluster_vir_node_count'  => array('name' => 'CLUSTER Virtual (Node Count)','style_h_align' => 'center'),
    'req_rfi_status'  => array('name' => 'RFI Status','style_h_align' => 'center'),
    'req_rfi_date'    => array('name' => 'RFI Date','style_h_align' => 'center'),
    'req_release_partial_server'  => array('name' => 'PARTIAL','style_h_align' => 'center'),
    'req_release_partial_phy_server'  => array('name' => 'PARTIAL Physical','style_h_align' => 'center'),
    'req_release_partial_vir_server'  => array('name' => 'PARTIAL Virtual','style_h_align' => 'center'),
    'req_release_pending_server'  => array('name' => 'PENDING','style_h_align' => 'center'),
    'req_release_pending_phy_server'  => array('name' => 'PENDING Physical','style_h_align' => 'center'),
    'req_release_pending_vir_server'  => array('name' => 'PENDING Virtual','style_h_align' => 'center'),
    'req_status_date'  => array('name' => 'Released Date','style_h_align' => 'center'),
    'req_status'  => array('name' => 'Status','style_h_align' => 'center'),
    'req_status_remark'          => array('name' => 'Remark'),
    'req_status_remark_by'          => array('name' => 'Remark By'),
  );

  $sql_where .= " AND r.req_status_id IS NOT NULL";

  // Search Query
  if(isset($_POST['download']) && $_POST['download'] != '') {

    if(isset($_POST['rep_status']) && $_POST['rep_status'] != '') {
      $req_status = $_POST['rep_status'];
      switch ($req_status) {
        case 'ALL':
          $sql_where .= " AND r.req_status_id !='3' ";
          break;
        case 'WIP/PARTIAL/ON-HOLD':
          $sql_where .= " AND r.req_status_id !='3' ";
          break;
        default:
          $sql_where .= " AND r.req_status_id = '".mysql_real_escape_string($req_status)."'";
          break;
      }
    }
    else{
      $sql_where .= " AND r.req_status_id !='3' ";
    }

    if(isset($_POST['rep_rfi_from']) && $_POST['rep_rfi_from'] != '') {
      $req_rfi_from = $_POST['rep_rfi_from'];

      if(isset($_POST['rep_rfi_to']) && $_POST['rep_rfi_to'] != '') {
        $req_rfi_to = $_POST['rep_rfi_to'];
        //echo date('Y-m-d H:i:s', strtotime($req_rfi_from)).'<br />';
        $sql_where .= " AND rs_rfi.rfi_date BETWEEN '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($req_rfi_from)))."' AND  '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($req_rfi_to)))."' ";
      }
      else {
        $sql_where .= " AND rs_rfi.rfi_date  >= '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($req_rfi_from)))."'  ";
      }
    }
    else {
      $req_rfi_to = $_POST['req_rfi_to'];
      if(isset($_POST['req_rfi_to']) && $_POST['req_rfi_to'] != '') {
        $sql_where .= " AND rs_rfi.rfi_date <= '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($req_rfi_to)))."'  ";
      }
    }

    if(isset($_POST['rep_req_group']) && $_POST['rep_req_group'] != '') {
      $req_group = $_POST['rep_req_group'];
      // Find all sub group of same name
      $sub_group_ids = " '0' ";
      $other_sub_group = user_group_other_group($req_group);
      if($other_sub_group){
        foreach ($other_sub_group as $key => $value) {
          if($value['user_group_id']){
            $sub_group_ids .= ", '".mysql_real_escape_string($value['user_group_id'])."' ";
          }
        }
      }
      $sql_where .= " AND r.req_group_sub_id IN (".$sub_group_ids." ) ";
    }

    if(isset($_POST['rep_user_group']) && $_POST['rep_user_group'] != '') {
      $req_user_group = $_POST['rep_user_group'];
      $sql_where .= " AND r.req_group_id = '".mysql_real_escape_string($req_user_group)."'";
      $data_req_sub_group = get_user_group_sub_group($req_user_group);
    }

    if (isset($_POST['rep_user_group_sub']) && $_POST['rep_user_group_sub'] != '') {
      $req_sub_group  = $_POST['rep_user_group_sub'];
      $sql_where .= " AND r.req_group_sub_id = '".mysql_real_escape_string($req_sub_group)."'";
    }

    if (isset($_POST['rep_req_priority']) && $_POST['rep_req_priority'] != '') {
      $req_priority      = $_POST['rep_req_priority'];
      $sql_where .= " AND rp.req_priority ='".mysql_real_escape_string($req_priority)."'";
    }

    if (isset($_POST['rep_req_name']) && $_POST['rep_req_name'] != '') {
      $req_name   = $_POST['req_name'];
      $sql_where .= " AND r.req_id LIKE '%".mysql_real_escape_string($req_name)."%'";
    }

    if (isset($_POST['rep_req_title']) && $_POST['rep_req_title'] != '') {
      $req_title  = $_POST['rep_req_title'];
      $sql_where .= " AND (
                            r.req_title LIKE '%".mysql_real_escape_string($req_title)."%'
                            OR
                            r.req_id LIKE '%".mysql_real_escape_string($req_title)."%'
                            OR
                            u_rpm.user_name LIKE '%".mysql_real_escape_string($req_title)."%'
                          )";
    }

    $sql_limit    = ' ';
    $sql_order_by = ' ORDER BY r.req_id DESC';
    $sql_search   = " SELECT r.*, DATE_FORMAT(r.req_date,'%d %M %Y') AS req_create_date,
                        ug.user_group_name AS req_group_name,
                        ugs.user_group_name AS req_group_name_sub,
                        u_c.user_name AS req_created_by,
                        u_u.user_name AS req_updated_by,
                        u_rpm.user_name AS req_pm_name,
                        rp.req_priority,
                        re.req_eta
                      FROM
                        idc_request AS r
                        LEFT JOIN
                        (
                          SELECT
                            req_status_id,
                            req_id,
                            status_id,
                            MIN(created_at) AS 'rfi_date'
                          FROM
                            idc_request_status
                          WHERE
                            1
                            AND
                            status_id IN (2, 6)
                          GROUP BY req_id
                        ) AS rs_rfi ON r.req_id = rs_rfi.req_id
                        LEFT JOIN
                        idc_user_group AS ug ON r.req_group_id = ug.user_group_id
                        LEFT JOIN
                        idc_user_group AS ugs ON r.req_group_sub_id = ugs.user_group_id
                        LEFT JOIN
                        idc_user AS u ON r.req_idc_spoc = u.user_login
                        LEFT JOIN
                        idc_user AS u_c ON r.created_by = u_c.user_login
                        LEFT JOIN
                        idc_user AS u_u ON r.updated_by = u_u.user_login
                        LEFT JOIN
                        idc_request_pm AS rpm ON r.req_id = rpm.req_id AND rpm.req_pm_status = 1
                        LEFT JOIN
                        idc_user AS u_rpm ON LOWER(rpm.req_pm) = LOWER(u_rpm.user_login)
                        LEFT JOIN
                        idc_request_priority AS rp ON r.req_id = rp.req_id AND rp.req_priority_status = 1
                        LEFT JOIN
                        idc_request_eta AS re ON r.req_id = re.req_id AND re.req_eta_status = 1
                      WHERE ".$sql_where."
                      ".$sql_order_by.'  '.$sql_limit.';';
    //echo $sql_search.'<br />';
    $res_search = mysql_query($sql_search);

    // Create new PHPExcel object
    $objPHPExcel = new PHPExcel();

    // Set document properties
    $objPHPExcel->getProperties()->setCreator("JioDC Portal")
               ->setLastModifiedBy("JioDC Portal")
               ->setTitle("Request Status Report")
               ->setSubject("Request Status Report")
               ->setDescription("Request Status Report.")
               ->setKeywords("office 2007 openxml php Request Status Report")
               ->setCategory("Request Status result file");


    $objPHPExcel->setActiveSheetIndex(0);

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

    if(!$res_search) {
      $pei_msg .= 'Something went wrong.';
    }
    else {
      $download_row = 2;
      $record_found = mysql_num_rows($res_search);
      while($row=mysql_fetch_array($res_search)) {

        $row['req_priority_val']  = '';
        if($row['req_priority']){
          $row['req_priority_val'] = $data_priority_val[$row['req_priority']];
        }
        $row['igf_count']         = 0;
        $row['server_total']      = 0;
        $row['server_physical']   = 0;
        $row['server_virtual']    = 0;
        $row['server_released']   = 0;
        $row['rfi_status']        = '';
        $row['rfi_date']          = '';
        $row['released_status']   = '';
        $row['released_date']     = '';
        $row['tat']               = 0;
        $row['has_ext_storage']   = '';
        $row['has_ha_cluster']    = '';

        $row['status_remark']         = '';
        $row['req_status_remark_by']  = '';
        $row['status_remark_name']    = '';

        $req_updated_at           = '';
        $row['igf_count'] = get_req_igf_count($row['req_id']);
        if($row['igf_count'] > 0) {
          // Server Information
          $row['server_physical']     = igf_request_epqt_count($row['req_id'], 'PHYSICAL');
          $row['server_virtual']      = igf_request_epqt_count($row['req_id'], 'VIRTUAL');
          $row['server_total']        = $row['server_physical'] + $row['server_virtual'];

          // RELEASED
          $row['server_released']     = count_igf_server_released_by_req_id($row['req_id']);
          $row['server_released_phy'] = count_igf_server_released_by_req_id($row['req_id'], 3);
          $row['server_released_vir'] = count_igf_server_released_by_req_id($row['req_id'], 4);
          // PENDING
          $row['server_pending']      = $row['server_total'] - $row['server_released'];
          $row['server_pending_phy']  = $row['server_physical'] - $row['server_released_phy'];
          $row['server_pending_vir']  = $row['server_virtual'] - $row['server_released_vir'];

          // SAN Node Count in Request IGF
          $storage_ext_type = req_igf_storage_node_count($row['req_id']);
          if($storage_ext_type) {
            $row['storage_node_count'] = $storage_ext_type;
          }

          // HA CLUSTER Node Count in Request IGF
          $ha_cluster = req_igf_ha_cluster_node_count($row['req_id']);
          if($ha_cluster) {
            $row['ha_cluster_node_count'] = $ha_cluster;
          }
        }// END IF $row['igf_count']

        $req_status_history = get_request_status_history($row['req_id']);
        if($req_status_history) {
          $row['req_status_remark_by']  = $req_status_history[0]['status_remark_by'];
          $row['status_remark']         = $req_status_history[0]['status_remark'];

          foreach ($req_status_history as $key => $value) {
            $row['rfi_status']  = 'RFI';
            $row['rfi_date']    = $value['created_at'];
          }
        }

        // Remark [Status Remark]
        switch ($row['req_status']) {
          case 'RELEASED':
            if($req_status_history){
              $row['released_date']       = $req_status_history[0]['created_at'];
              $row['status_remark']       = $req_status_history[0]['status_remark'];
              $row['status_remark_name']  = $req_status_history[0]['status_name'];

            }
            break;
          case 'RFI':
            // Then request is in WIP Stage
            $row['status_remark_name']  = 'WIP';
          case 'WIP':
            // Then request can be in WIP or PARTIAL WIP stage
            $row['status_remark_name']  = 'WIP';
            if( ($row['server_released'] > 0) && ($row['server_pending'] > 0) ){
              $row['status_remark_name']  = 'PARTIAL';
              if($req_status_history){
                foreach ($req_status_history as $key => $value) {
                  if($value['status_name'] == 'PARTIAL RELEASED'){
                    $row['released_date']       = $value['created_at'];
                    break;
                  }
                }
              }
            }
            break;
          case 'PARTIAL RELEASED':
            // Then request can be in PARTIAL ON-HOLD or PARTIAL WIP stage
            if($req_status_history){
              foreach ($req_status_history as $key => $value) {
                if($value['status_name'] == 'PARTIAL RELEASED'){
                  $row['released_date']       = $value['created_at'];
                  break;
                }
              }
            }
            $row['status_remark_name']  = 'PARTIAL';
            break;
          case 'ON-HOLD':
            // Then request can be in ON-HOLD or PARTIAL ON-HOLD stage
            $row['status_remark_name']  = 'ON-HOLD';
            if( ($row['server_released'] > 0) && ($row['server_pending'] > 0) ){
              $row['status_remark_name']  = 'PARTIAL ON-HOLD';
              if($req_status_history){
                foreach ($req_status_history as $key => $value) {
                  if($value['status_name'] == 'PARTIAL RELEASED'){
                    $row['released_date']       = $value['created_at'];
                    break;
                  }
                }
              }
            }
            break;
          default:

        }// END switch $row['req_status']


        $total_phy = $total_phy + $row['server_physical'];
        $total_vir = $total_vir + $row['server_virtual'];

        $data[] = $row;

        // sr_no
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['sr_no']['col'], $download_row, ($download_row - 1));
        // req_id
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_id']['col'], $download_row, strtoupper($row['req_id']));
        // req_date
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_date']['col'], $download_row, pei_date_format($row['req_date']));
        // req_created_by
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_created_by']['col'], $download_row, strtoupper($row['req_created_by']));
        // req_priority
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_priority']['col'], $download_row, $row['req_priority_val']);
        // req_pm
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_pm']['col'], $download_row, $row['req_pm_name']);
        // req_eta
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_eta']['col'], $download_row, $row['req_eta']);
        // req_group
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_group']['col'], $download_row, strtoupper($row['req_group_name']));
        // req_sub_group
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_sub_group']['col'], $download_row, strtoupper($row['req_group_name_sub']));
        // req_title
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_title']['col'], $download_row, strtoupper($row['req_title']));
        // req_env
        $loc_env = '';
        $loc_env = get_req_env_string($row['req_id']);
        if($loc_env) {
          $loc_env = str_replace(',', ', ', $loc_env);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_env']['col'], $download_row, strtoupper($loc_env));
        // req_loc
        $loc_str = '';
        $loc_str = get_req_loc_string($row['req_id']);
        if($loc_str) {
          $loc_str = str_replace(',', ', ', $loc_str);
        }
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_loc']['col'], $download_row, strtoupper($loc_str));
        // req_sh
        $sh_str = '';
        $sh_str = get_req_sh_string($row['req_id']);
        if($sh_str) {
          $sh_str = str_replace(',', ', ', $sh_str);
        }

        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_sh']['col'], $download_row, strtoupper($sh_str));
        // req_total_server
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_total_server']['col'], $download_row, $row['server_total']);
        // req_phy_server
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_phy_server']['col'], $download_row, $row['server_physical']);
        // req_vir_server
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_vir_server']['col'], $download_row, $row['server_virtual']);
        // req_san_node_count
        if($row['storage_node_count'] && ($row['storage_node_count']['SAN']['PHYSICAL'] + $row['storage_node_count']['SAN']['VIRTUAL']) != 0) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_san_node_count']['col'], $download_row, ($row['storage_node_count']['SAN']['PHYSICAL'] + $row['storage_node_count']['SAN']['VIRTUAL']));
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_san_phy_node_count']['col'], $download_row, $row['storage_node_count']['SAN']['PHYSICAL']);
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_san_vir_node_count']['col'], $download_row, $row['storage_node_count']['SAN']['VIRTUAL']);
        }
        // req_cluster_node_count
        if($row['ha_cluster_node_count'] && ($row['ha_cluster_node_count']['PHYSICAL'] + $row['ha_cluster_node_count']['VIRTUAL']) != 0) {
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_cluster_node_count']['col'], $download_row, ($row['ha_cluster_node_count']['PHYSICAL'] + $row['ha_cluster_node_count']['VIRTUAL']));
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_cluster_phy_node_count']['col'], $download_row, $row['ha_cluster_node_count']['PHYSICAL']);
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_cluster_vir_node_count']['col'], $download_row, $row['ha_cluster_node_count']['VIRTUAL']);
        }

        // req_rfi_status
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_rfi_status']['col'], $download_row, strtoupper($row['rfi_status']));
        // req_rfi_date
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_rfi_date']['col'], $download_row, (($row['rfi_date']) ? pei_date_format($row['rfi_date']) : '') );

        if( ($row['server_released'] > 0) && ($row['server_pending'] > 0) ) {
          // req_release_partial_server
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_release_partial_server']['col'], $download_row, $row['server_released'] );
          // req_release_partial_phy_server
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_release_partial_phy_server']['col'], $download_row, $row['server_released_phy'] );
          // req_release_partial_vir_server
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_release_partial_vir_server']['col'], $download_row, $row['server_released_vir'] );
        }
        if( $row['server_pending'] > 0) {
          // req_release_pending_server
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_release_pending_server']['col'], $download_row, $row['server_pending']);
          // req_release_pending_phy_server
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_release_pending_phy_server']['col'], $download_row, $row['server_pending_phy'] );
          // req_release_pending_vir_server
          $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_release_pending_vir_server']['col'], $download_row, $row['server_pending_vir'] );
        }

        // req_status_date
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_status_date']['col'], $download_row, (($row['released_date']) ? pei_date_format($row['released_date']) : '') );
        // req_status
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_status']['col'], $download_row, $row['status_remark_name']);

        // req_status_remark
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_status_remark']['col'], $download_row, ($row['status_remark'] ? $row['status_remark'] : ''));

        //req_status_remark_by
        $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($rep_header['req_status_remark_by']['col'], $download_row, $row['req_status_remark_by']);
        $download_row++;
      }

      $project_count        = $download_row - 2;

      // Count Summary
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($download_row + 2), 'COUNT SUMMARY');
      $columnLetter_index2 = PHPExcel_Cell::stringFromColumnIndex(2);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index2.($download_row + 2))->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index2.($download_row + 2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCD5B4');
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($download_row + 2), 'VALUE');
      $columnLetter_index3 = PHPExcel_Cell::stringFromColumnIndex(3);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 2))->getFont()->setBold(true);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 2))->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID)->getStartColor()->setRGB('FCD5B4');
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 2))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //Project Count
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($download_row + 3), 'Project Count');
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($download_row + 3), $project_count);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 3))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //Total Servers  (Total Physical Servers)
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($download_row + 4), 'Total Servers');
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($download_row + 4), $total_phy);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 4))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //Total VMs (Total Virtual Servers)
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($download_row + 5), 'Total VMs');
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($download_row + 5), $total_vir);
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 5))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
      //Servers + VM
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2, ($download_row + 6), 'Servers + VM');
      $objPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3, ($download_row + 6),  ($total_phy + $total_vir));
      $objPHPExcel->getActiveSheet()->getStyle($columnLetter_index3.($download_row + 6))->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

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
