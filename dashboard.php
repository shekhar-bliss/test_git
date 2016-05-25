<?php
  session_start();
  $pei_current_module = 'REQUEST';
  require_once(__dir__.'/../header.php');

  //echo error_reporting(E_ALL); ini_set('display_errors', 1);
  //echo '<pre><br /><br /><br /><br />';
  //var_dump($_REQUEST);
  // SELECT req_stat_id, req_stat_parent_id, req_stat_name, req_stat_value, created_at FROM  idc_request_stat;

  // Initialize variables
  $pei_messages     = array();
  $action           = 'View';
  $pei_page_access  = FALSE;
  $pei_access_group_wise  = FALSE;
  $pei_user         = $_SESSION['pei_user'];

  // CHECK access permission for
  if(in_array('view_request_dashboard', $pei_user_access_permission)) {
    $pei_page_access = TRUE;
  }

  // CHECK access permission for
  if(in_array('view_request_dashboard_user_sub_group_wise', $pei_user_access_permission)) {
    $pei_access_group_wise = TRUE;
  }

  $imp_as_on            = date('d-M-Y');
  $imp_as_on_from       = date('d-M-Y', strtotime('-1 week'));
  $imp_as_on_to         = date('d-M-Y');


  // SERVER
  $req_count            = 0;
  $server               = 0;
  $server_phy           = 0;
  $server_vir           = 0;
  $req_released         = 0;
  $server_release       = 0;
  $server_release_phy   = 0;
  $server_release_vir   = 0;
  $req_partial          = 0;
  $server_partial       = 0;
  $server_partial_phy   = 0;
  $server_partial_vir   = 0;
  $req_pending          = 0;
  $server_pending       = 0;
  $server_pending_phy   = 0;
  $server_pending_vir   = 0;
  $req_wip              = 0;
  $server_wip           = 0;
  $server_wip_phy       = 0;
  $server_wip_vir       = 0;
  $on_hold              = 0;
  $server_on_hold       = 0;
  $server_on_hold_phy   = 0;
  $server_on_hold_vir   = 0;
  $total_released       = 0;
  $total_wip            = 0;
  // SAN
  $san_req_count     = 0;
  $san               = 0;
  $san_phy           = 0;
  $san_vir           = 0;
  $san_req_released  = 0;
  $san_release       = 0;
  $san_release_phy   = 0;
  $san_release_vir   = 0;
  $san_req_partial   = 0;
  $san_partial       = 0;
  $san_partial_phy   = 0;
  $san_partial_vir   = 0;
  $san_req_pending   = 0;
  $san_pending       = 0;
  $san_pending_phy   = 0;
  $san_pending_vir   = 0;
  $san_req_wip       = 0;
  $san_wip           = 0;
  $san_wip_phy       = 0;
  $san_wip_vir       = 0;
  $san_req_on_hold   = 0;
  $san_on_hold       = 0;
  $san_on_hold_phy   = 0;
  $san_on_hold_vir   = 0;
  $san_total_wip     = 0;
  // CLUSTER
  $cluster_req_count     = 0;
  $cluster               = 0;
  $cluster_phy           = 0;
  $cluster_vir           = 0;
  $cluster_req_released  = 0;
  $cluster_release       = 0;
  $cluster_release_phy   = 0;
  $cluster_release_vir   = 0;
  $cluster_req_partial   = 0;
  $cluster_partial       = 0;
  $cluster_partial_phy   = 0;
  $cluster_partial_vir   = 0;
  $cluster_req_pending   = 0;
  $cluster_pending       = 0;
  $cluster_pending_phy   = 0;
  $cluster_pending_vir   = 0;
  $cluster_req_wip       = 0;
  $cluster_wip           = 0;
  $cluster_wip_phy       = 0;
  $cluster_wip_vir       = 0;
  $cluster_req_on_hold   = 0;
  $cluster_on_hold       = 0;
  $cluster_on_hold_phy   = 0;
  $cluster_on_hold_vir   = 0;
  $cluster_total_wip     = 0;

  // NEW
  $new_req_count          = 0;
  $new_server             = 0;
  $new_server_phy         = 0;
  $new_server_vir         = 0;
  $new_req_released       = 0;
  $new_server_release     = 0;
  $new_server_release_phy = 0;
  $new_server_release_vir = 0;

  $new_san_req_count      = 0;
  $new_san                = 0;
  $new_san_phy            = 0;
  $new_san_vir            = 0;
  $new_san_req_released   = 0;
  $new_san_release        = 0;
  $new_san_release_phy    = 0;
  $new_san_release_vir    = 0;

  $new_cluster_req_count      = 0;
  $new_cluster                = 0;
  $new_cluster_phy            = 0;
  $new_cluster_vir            = 0;
  $new_cluster_req_released   = 0;
  $new_cluster_release        = 0;
  $new_cluster_release_phy    = 0;
  $new_cluster_release_vir    = 0;

  $imp_dash = request_imp_dashboard_top_level_summary();
  if($imp_dash){
    foreach ($imp_dash as $row) {
      switch ($row['igf_server_type']) {
        case 'Count - Total Request':
          switch($row['req_status']){
            case 'ON-HOLD':
              $on_hold = $row['count(1)'];
              break;
            case 'PARTIAL RELEASED':
              $req_partial = $row['count(1)'];
              break;
            case 'RELEASED':
              $req_released = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $req_wip = $req_wip + $row['count(1)'];
              break;
          }
          break;
        case 'PHYSICAL':
          switch($row['req_status']) {
            case '': $server_phy = $server_phy + $row['count(1)'];
              break;
            case 'ON-HOLD':
              if($row['Released'] == 'Already Released'){
                $server_partial_phy = $server_partial_phy + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $server_on_hold_phy = $row['count(1)'];
              }
              break;
            case 'PARTIAL RELEASED':
              if($row['Released'] == 'Already Released'){
                $server_partial_phy = $server_partial_phy + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $server_pending_phy = $row['count(1)'];
              }
              break;
            case 'RELEASED':
              $server_release_phy = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $server_wip_phy = $server_wip_phy + $row['count(1)'];
              break;
          }
          break;
        case 'VIRTUAL':
          switch($row['req_status']){
            case '': $server_vir = $server_vir + $row['count(1)'];
              break;
            case 'ON-HOLD':
              if($row['Released'] == 'Already Released'){
                $server_partial_vir = $server_partial_vir + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $server_on_hold_vir = $server_on_hold_vir + $row['count(1)'];
              }
              break;
            case 'PARTIAL RELEASED':
              if($row['Released'] == 'Already Released'){
                $server_partial_vir = $server_partial_vir + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $server_pending_vir = $row['count(1)'];
              }
              break;
            case 'RELEASED':
              $server_release_vir = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $server_wip_vir = $server_wip_vir + $row['count(1)'];
              break;
          }
          break;
        default:
          # code...
          break;
      }// END switch
    }// END foreach

    $req_count      = $req_released + $req_partial + $req_wip + $on_hold;
    //$server_phy     = $server_release_phy + $server_partial_phy + $server_pending_phy + $server_wip_phy + $server_on_hold_phy;
    //$server_vir     = $server_release_vir + $server_partial_vir + $server_pending_vir + $server_wip_vir + $server_on_hold_vir;
    $server         = $server_phy + $server_vir;
    $server_release = $server_release_phy + $server_release_vir;
    $server_partial = $server_partial_phy + $server_partial_vir;
    $server_pending = $server_pending_phy + $server_pending_vir;
    $server_wip     = $server_wip_phy + $server_wip_vir;
    $server_on_hold = $server_on_hold_phy + $server_on_hold_vir;
    $total_released = $req_released;
    $total_wip      = $req_partial + $req_wip + $on_hold;
  }

  // SAN QUERY
  $imp_dash_san = request_imp_dashboard_top_level_summary_san();
  if($imp_dash_san){
    foreach ($imp_dash_san as $row) {
      switch ($row['igf_server_type']) {
        case 'Count - Total Request':
          switch($row['req_status']){
            case 'ON-HOLD':
              $san_on_hold = $row['count(1)'];
              break;
            case 'PARTIAL RELEASED':
              $san_req_partial = $row['count(1)'];
              break;
            case 'RELEASED':
              $san_req_released = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $san_req_wip = $san_req_wip + $row['count(1)'];
              break;
          }
          break;
        case 'PHYSICAL':
          switch($row['req_status']) {
            case '': $san_phy = $san_phy + $row['count(1)'];
              break;
            case 'ON-HOLD':
              if($row['Released'] == 'Already Released'){
                $san_partial_phy = $san_partial_phy + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $san_on_hold_phy = $row['count(1)'];
              }
              break;
            case 'PARTIAL RELEASED':
              if($row['Released'] == 'Already Released'){
                $san_partial_phy = $san_partial_phy + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $san_pending_phy = $row['count(1)'];
              }
              break;
            case 'RELEASED':
              $san_release_phy = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $san_wip_phy = $san_wip_phy + $row['count(1)'];
              break;
          }
          break;
        case 'VIRTUAL':
          switch($row['req_status']){
            case '': $san_vir = $san_vir + $row['count(1)'];
              break;
            case 'ON-HOLD':
              if($row['Released'] == 'Already Released'){
                $san_partial_vir = $san_partial_vir + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $san_on_hold_vir = $san_on_hold_vir + $row['count(1)'];
              }
              break;
            case 'PARTIAL RELEASED':
              if($row['Released'] == 'Already Released'){
                $san_partial_vir = $san_partial_vir + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $san_pending_vir = $row['count(1)'];
              }
              break;
            case 'RELEASED':
              $san_release_vir = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $san_wip_vir = $san_wip_vir + $row['count(1)'];
              break;
          }
          break;
        default:
          # code...
          break;
      }// END switch
    }// END foreach


    $san         = $san_phy + $san_vir;
    $san_release = $san_release_phy + $san_release_vir;
    $san_partial = $san_partial_phy + $san_partial_vir;
    $san_pending = $san_pending_phy + $san_pending_vir;
    $san_wip     = $san_wip_phy + $san_wip_vir;
    $san_on_hold = $san_on_hold_phy + $san_on_hold_vir;
  }// END IF $imp_dash_san

  // CLUSTER QUERY
  $imp_dash_cluster = request_imp_dashboard_top_level_summary_cluster();
  if($imp_dash_cluster){
    foreach ($imp_dash_cluster as $row) {
      switch (strtoupper($row['igf_server_type'])) {
        case 'Count - Total Request':
          switch(strtoupper($row['req_status'])) {
            case 'ON-HOLD':
              $cluster_on_hold = $row['count(1)'];
              break;
            case 'PARTIAL RELEASED':
              $cluster_req_partial = $row['count(1)'];
              break;
            case 'RELEASED':
              $cluster_req_released = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $cluster_req_wip = $cluster_req_wip + $row['count(1)'];
              break;
          }
          break;
        case 'PHYSICAL':
          switch(strtoupper($row['req_status'])) {
            case '':
              $cluster_phy = $cluster_phy + $row['count(1)'];
              break;
            case 'ON-HOLD':
              if($row['Released'] == 'Already Released'){
                $cluster_partial_phy = $cluster_partial_phy + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $cluster_on_hold_phy = $row['count(1)'];
              }
              break;
            case 'PARTIAL RELEASED':
              if($row['Released'] == 'Already Released'){
                $cluster_partial_phy = $cluster_partial_phy + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $cluster_pending_phy = $row['count(1)'];
              }
              break;
            case 'RELEASED':
              $cluster_release_phy = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $cluster_wip_phy = $cluster_wip_phy + $row['count(1)'];
              break;
          }
          break;
        case 'VIRTUAL':
          switch(strtoupper($row['req_status'])){
            case '':
              $cluster_vir = $cluster_vir + $row['count(1)'];
              break;
            case 'ON-HOLD':
              if($row['Released'] == 'Already Released'){
                $cluster_partial_vir = $cluster_partial_vir + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $cluster_on_hold_vir = $cluster_on_hold_vir + $row['count(1)'];
              }
              break;
            case 'PARTIAL RELEASED':
              if($row['Released'] == 'Already Released'){
                $cluster_partial_vir = $cluster_partial_vir + $row['count(1)'];
              }
              if($row['Released'] == 'Yet To Release'){
                $cluster_pending_vir = $row['count(1)'];
              }
              break;
            case 'RELEASED':
              $cluster_release_vir = $row['count(1)'];
              break;
            case 'RFI':
            case 'WIP':
              $cluster_wip_vir = $cluster_wip_vir + $row['count(1)'];
              break;
          }
          break;
        default:
          # code...
          break;
      }// END switch
    }// END foreach


    $cluster         = $cluster_phy + $cluster_vir;
    $cluster_release = $cluster_release_phy + $cluster_release_vir;
    $cluster_partial = $cluster_partial_phy + $cluster_partial_vir;
    $cluster_pending = $cluster_pending_phy + $cluster_pending_vir;
    $cluster_wip     = $cluster_wip_phy + $cluster_wip_vir;
    $cluster_on_hold = $cluster_on_hold_phy + $cluster_on_hold_vir;
  }// END IF $imp_dash_cluster



  // SEARCH
  $rfi_req_ids  = " '0' ";
  $sql_where    = ' 1 ';
  $sql_having   = ' 1 ';

  // Find request count
  if(isset($_GET['imp_as_on_from']) && $_GET['imp_as_on_from'] != '') {
    $imp_as_on_from = $_GET['imp_as_on_from'];
  }

  if(isset($_GET['imp_as_on_to']) && $_GET['imp_as_on_to'] != '') {
    $imp_as_on_to = $_GET['imp_as_on_to'];
  }

  $sql_where .= ' AND rs.req_status_id IS NOT NULL ';
  $sql_having .= " AND MIN(rs.created_at) ";
  $sql_having .= " AND rs.created_at  >= '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($imp_as_on_from)))."' ";
  $sql_having .= " AND rs.created_at  <= '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($imp_as_on_to)))."' ";


  $sql_total = "SELECT r.req_id, r.created_at, rs.req_status_id, rs.status_id, rs.created_at
                FROM
                  idc_request AS r
                  LEFT JOIN
                  idc_request_status rs ON r.req_id = rs.req_id
                WHERE
                  ".$sql_where."
                GROUP BY r.req_id
                HAVING ".$sql_having;
  //echo $sql_total.'<br />';
  $res_total  = mysql_query($sql_total);

  if($res_total) {
    while ($row = mysql_fetch_array($res_total)) {
      //echo $row['req_id'].'<br />';

      $rfi_req_ids .= ", '".$row['req_id']."' ";
      $new_req_count++;
    }// END while

    //echo '$rfi_req_ids :'.$rfi_req_ids.'<br />';

    // FIND NEW Server Types
    $sql_server = " SELECT i_ser.igf_server_type_id, st.server_type_name, COUNT(i_ser.igf_server_type_id) AS server_count
                        FROM
                          idc_igf_server AS i_ser
                          LEFT JOIN
                          idc_igf AS i ON i_ser.igf_id = i.igf_id
                          LEFT JOIN
                          idc_server_type AS st ON  i_ser.igf_server_type_id = st.server_type_id
                        WHERE
                          1 AND i.igf_deleted=0
                          AND
                          i.req_id IN (".$rfi_req_ids.")
                        GROUP BY
                          i_ser.igf_server_type_id, st.server_type_name
                       ";
    //echo $sql_server.'<br />';
    $res_server  = mysql_query($sql_server);
    if($res_server){
      while ($row = mysql_fetch_array($res_server)) {
        switch (strtoupper($row['server_type_name'])) {
          case 'PHYSICAL':
            $new_server_phy = $row['server_count'];
            break;
          case 'VIRTUAL':
            $new_server_vir = $row['server_count'];
            break;
          default:
            # code...
            break;
        }
      }
    }

    // FIND NEW SAN
    $sql_san = " SELECT i_ser.igf_server_type_id, st.server_type_name, COUNT(i_ser.igf_server_type_id) AS server_count
                        FROM
                          idc_igf_server AS i_ser
                          LEFT JOIN
                          idc_igf AS i ON i_ser.igf_id = i.igf_id
                          LEFT JOIN
                          idc_server_type AS st ON  i_ser.igf_server_type_id = st.server_type_id
                        WHERE
                          1
                          AND
                          i.igf_deleted=0
                          AND
                          i_ser.igf_server_storage_ext_type like 'SAN%'
                          AND
                          i.req_id IN (".$rfi_req_ids.")
                        GROUP BY
                          i_ser.igf_server_type_id, st.server_type_name
                       ";
    //echo $sql_san.'<br />';
    $res_san  = mysql_query($sql_san);
    if($res_san){
      while ($row = mysql_fetch_array($res_san)) {
        switch (strtoupper($row['server_type_name'])) {
          case 'PHYSICAL':
            $new_san_phy = $row['server_count'];
            break;
          case 'VIRTUAL':
            $new_san_vir = $row['server_count'];
            break;
          default:
            # code...
            break;
        }
      }
    }

    // FIND NEW CLUSTER
    $sql_cluster = " SELECT i_ser.igf_server_type_id, st.server_type_name, COUNT(i_ser.igf_server_type_id) AS server_count
                        FROM
                          idc_igf_server AS i_ser
                          LEFT JOIN
                          idc_igf AS i ON i_ser.igf_id = i.igf_id
                          LEFT JOIN
                          idc_server_type AS st ON  i_ser.igf_server_type_id = st.server_type_id
                        WHERE
                          1
                          AND
                          i.igf_deleted=0
                          AND
                          UPPER(i_ser.igf_server_ha_cluster) = 'YES'
                          AND
                          i.req_id IN (".$rfi_req_ids.")
                        GROUP BY
                          i_ser.igf_server_type_id, st.server_type_name
                       ";
    //echo $sql_cluster.'<br />';
    $res_cluster  = mysql_query($sql_cluster);
    if($res_cluster){
      while ($row = mysql_fetch_array($res_cluster)) {
        switch (strtoupper($row['server_type_name'])) {
          case 'PHYSICAL':
            $new_cluster_phy = $row['server_count'];
            break;
          case 'VIRTUAL':
            $new_cluster_vir = $row['server_count'];
            break;
          default:
            # code...
            break;
        }
      }
    }


  }// END IF $res_total



  // NEW RELEASED
  $rfr_ids = ' 0 ';
  $sql_rfr_ids = "SELECT
                    MAX(rs.req_status_id) AS req_status_id
                  FROM
                   idc_request_status AS rs
                  WHERE
                    1
                    AND
                    rs.created_at >= '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($imp_as_on_from)))."'
                    AND
                    rs.created_at  <= '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($imp_as_on_to)))."'
                  GROUP BY
                    rs.req_id
                  ";
  $res_rfr_ids  = mysql_query($sql_rfr_ids);
  if($res_rfr_ids) {
    while ($row = mysql_fetch_array($res_rfr_ids)) {
      $rfr_ids .= ', '.mysql_real_escape_string($row['req_status_id']);
    }
  }
  //echo '$rfr_ids :'.$rfr_ids.'<br/>';


  $rfr_req_ids  = " '0' ";
  $rfr_where    = ' 1 ';

  $rfr_where .= " AND isrs.created_at  >= '".mysql_real_escape_string(date('Y-m-d 00:00:00', strtotime($imp_as_on_from)))."' ";
  $rfr_where .= " AND isrs.created_at  <= '".mysql_real_escape_string(date('Y-m-d 23:59:59', strtotime($imp_as_on_to)))."' ";




  $sql_rfr = "SELECT COUNT(DISTINCT req_id) AS req_count
              FROM
               idc_request_status
              WHERE
                req_status_id IN (".$rfr_ids.")
                AND
                status_id IN (3, 4)
             ";
  //echo $sql_rfr.'<br />';
  $res_rfr  = mysql_query($sql_rfr);
  if($res_rfr) {
    $row = mysql_fetch_array($res_rfr);
    if($row) {
      $new_req_released = $row['req_count'];
    }
  }

  // FIND NEW RELEASED Server Types
  $sql_rfr_server = " SELECT i_ser.igf_server_type_id, st.server_type_name, COUNT(i_ser.igf_server_type_id) AS server_count
                      FROM
                        idc_igf_server_release_server AS isrs
                        LEFT JOIN
                        idc_igf_server AS i_ser ON isrs.igf_server_id = i_ser.igf_server_id
                        LEFT JOIN
                        idc_igf AS i ON i_ser.igf_id = i.igf_id
                        LEFT JOIN
                        idc_server_type AS st ON  i_ser.igf_server_type_id = st.server_type_id
                      WHERE
                        ".$rfr_where."
                      GROUP BY
                        i_ser.igf_server_type_id, st.server_type_name
                     ";
  //echo $sql_rfr_server.'<br />';
  $res_rfr_server  = mysql_query($sql_rfr_server);
  if($res_rfr_server){
    while ($row = mysql_fetch_array($res_rfr_server)) {
      switch (strtoupper($row['server_type_name'])) {
        case 'PHYSICAL':
          $new_server_release_phy = $row['server_count'];
          break;
        case 'VIRTUAL':
          $new_server_release_vir = $row['server_count'];
          break;
        default:
          # code...
          break;
      }
    }
  }

  // FIND NEW RELEASED SAN
  $sql_rfr_san = " SELECT i_ser.igf_server_type_id, st.server_type_name, COUNT(i_ser.igf_server_type_id) AS server_count
                      FROM
                        idc_igf_server_release_server AS isrs
                        LEFT JOIN
                        idc_igf_server AS i_ser ON isrs.igf_server_id = i_ser.igf_server_id
                        LEFT JOIN
                        idc_igf AS i ON i_ser.igf_id = i.igf_id
                        LEFT JOIN
                        idc_server_type AS st ON  i_ser.igf_server_type_id = st.server_type_id
                      WHERE
                        ".$rfr_where." AND i_ser.igf_server_storage_ext_type like 'SAN%'
                      GROUP BY
                        i_ser.igf_server_type_id, st.server_type_name
                     ";
  //echo $sql_rfr_san.'<br />';
  $res_rfr_san  = mysql_query($sql_rfr_san);
  if($res_rfr_san){
    while ($row = mysql_fetch_array($res_rfr_san)) {
      switch (strtoupper($row['server_type_name'])) {
        case 'PHYSICAL':
          $new_san_release_phy = $row['server_count'];
          break;
        case 'VIRTUAL':
          $new_san_release_vir = $row['server_count'];
          break;
        default:
          # code...
          break;
      }
    }
  }

  // FIND NEW RELEASED CLUSTER
  $sql_rfr_cluster = " SELECT i_ser.igf_server_type_id, st.server_type_name, COUNT(i_ser.igf_server_type_id) AS server_count
                      FROM
                        idc_igf_server_release_server AS isrs
                        LEFT JOIN
                        idc_igf_server AS i_ser ON isrs.igf_server_id = i_ser.igf_server_id
                        LEFT JOIN
                        idc_igf AS i ON i_ser.igf_id = i.igf_id
                        LEFT JOIN
                        idc_server_type AS st ON  i_ser.igf_server_type_id = st.server_type_id
                      WHERE
                        ".$rfr_where." AND UPPER(i_ser.igf_server_ha_cluster) = 'YES'
                      GROUP BY
                        i_ser.igf_server_type_id, st.server_type_name
                     ";
  //echo $sql_rfr_cluster.'<br />';
  $res_rfr_cluster  = mysql_query($sql_rfr_cluster);
  if($res_rfr_cluster){
    while ($row = mysql_fetch_array($res_rfr_cluster)) {
      switch (strtoupper($row['server_type_name'])) {
        case 'PHYSICAL':
          $new_cluster_release_phy = $row['server_count'];
          break;
        case 'VIRTUAL':
          $new_cluster_release_vir = $row['server_count'];
          break;
        default:
          # code...
          break;
      }
    }
  }

  $new_server   = $new_server_phy + $new_server_vir;
  $new_san      = $new_san_phy + $new_san_vir;
  $new_cluster  = $new_cluster_phy + $new_cluster_vir;

  $new_server_release   = $new_server_release_phy + $new_server_release_vir;
  $new_san_release      = $new_san_release_phy + $new_san_release_vir;
  $new_cluster_release  = $new_cluster_release_phy + $new_cluster_release_vir;

  //echo '</pre>';
?>
    <div class="container">
      <!-- breadcrumb -->
      <ol class="breadcrumb">
        <li><a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/index.php">User Requests</a></li>
        <li class="active">Dashboard</li>
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
        <div class="pei-breadcrumb-right">
        <?php
          if($pei_access_group_wise) {
          ?>
          <a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/dashboard_sub_group_wise.php">USER SUB GROUP WISE DASHBOARD</a>
          <?php
          }
        ?>
        </div>
        <div class="clearfix"></div>
        <div class="col-sm-12">
        <table class="table table-bordered table-hover">
          <tr valign="middle">
            <th colspan="14">
            <form id="form-imp-dashboard" class="form-inline" method="GET">
              <div class="form-group">
                AS ON : &nbsp
              </div>
              <div class="form-group">
                <div class="input-group date" id="imp-as-on-datetimepicker">
                  <input type="text" disabled="true" class="form-control" name="imp_as_on" value="<?php echo $imp_as_on;?>" placeholder="Date" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
            </form>

            </th>
          </tr>
          <tr>
            <th colspan="14" class="info">IMPLEMENTATION DASHBOARD - HIGH LEVEL SUMMARY</th>
          </tr>
          <tr>
            <th colspan="2" class="active">&nbsp</th>
            <th class="info" >&nbsp</th>
            <th colspan="3" class="text-center bg-primary">SERVERS / EQUIPMENT</th>
            <th class="info" >&nbsp</th>
            <th colspan="3" class="text-center">SAN</th>
            <th class="info" >&nbsp</th>
            <th colspan="3" class="text-center bg-danger">CLUSTER</th>
          </tr>
          <tr class="active">
            <th width="350px">&nbsp</th>
            <th class="text-center" width="120px">Request Count</th>
            <td class="info" >&nbsp</td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Physical</th>
            <th class="text-center" width="90px">Virtual</th>
            <td class="info" >&nbsp</td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Physical</th>
            <th class="text-center" width="90px">Virtual</th>
            <td class="info" >&nbsp</td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Physical</th>
            <th class="text-center" width="90px">Virtual</th>
          </tr>
          <tr class="success">
            <th class="active">NO. REQUESTS (RFI)</th>
            <td class="text-center"><?php
              if($pei_access_group_wise) {
              ?>
              <a href="<?php echo $pei_config['urls']['baseUrl'];?>/request/dashboard_sub_group_wise.php"><?php echo $req_count;?></a>
              <?php
              }
              else {
                echo $req_count;
              }
              ?>
            </td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $server;?></td>
            <td class="text-center"><?php echo $server_phy;?></td>
            <td class="text-center"><?php echo $server_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $san;?></td>
            <td class="text-center"><?php echo $san_phy;?></td>
            <td class="text-center"><?php echo $san_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $cluster;?></td>
            <td class="text-center"><?php echo $cluster_phy;?></td>
            <td class="text-center"><?php echo $cluster_vir;?></td>
          </tr>
          <tr>
            <th class="active">RELEASED</th>
            <td class="text-center"><?php echo $req_released;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $server_release;?></td>
            <td class="text-center"><?php echo $server_release_phy;?></td>
            <td class="text-center"><?php echo $server_release_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $san_release;?></td>
            <td class="text-center"><?php echo $san_release_phy;?></td>
            <td class="text-center"><?php echo $san_release_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $cluster_release;?></td>
            <td class="text-center"><?php echo $cluster_release_phy;?></td>
            <td class="text-center"><?php echo $cluster_release_vir;?></td>
          </tr>
          <tr>
            <th class="active">PARTIAL RELEASED (Including ON-HOLD)</th>
            <td class="text-center"><?php echo $req_partial;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $server_partial;?></td>
            <td class="text-center"><?php echo $server_partial_phy;?></td>
            <td class="text-center"><?php echo $server_partial_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $san_partial;?></td>
            <td class="text-center"><?php echo $san_partial_phy;?></td>
            <td class="text-center"><?php echo $san_partial_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $cluster_partial;?></td>
            <td class="text-center"><?php echo $cluster_partial_phy;?></td>
            <td class="text-center"><?php echo $cluster_partial_vir;?></td>
          </tr>
          <tr>
            <th class="active">PARTIAL PENDING</th>
            <td class="text-center"><?php echo $req_partial;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $server_pending;?></td>
            <td class="text-center"><?php echo $server_pending_phy;?></td>
            <td class="text-center"><?php echo $server_pending_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $san_pending;?></td>
            <td class="text-center"><?php echo $san_pending_phy;?></td>
            <td class="text-center"><?php echo $san_pending_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $cluster_pending;?></td>
            <td class="text-center"><?php echo $cluster_pending_phy;?></td>
            <td class="text-center"><?php echo $cluster_pending_vir;?></td>
          </tr>
          <tr>
            <th class="active">WIP</th>
            <td class="text-center"><?php echo $req_wip;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $server_wip;?></td>
            <td class="text-center"><?php echo $server_wip_phy;?></td>
            <td class="text-center"><?php echo $server_wip_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $san_wip;?></td>
            <td class="text-center"><?php echo $san_wip_phy;?></td>
            <td class="text-center"><?php echo $san_wip_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $cluster_wip;?></td>
            <td class="text-center"><?php echo $cluster_wip_phy;?></td>
            <td class="text-center"><?php echo $cluster_wip_vir;?></td>
          </tr>
          <tr>
            <th class="active">ON-HOLD</th>
            <td class="text-center"><?php echo $on_hold;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $server_on_hold;?></td>
            <td class="text-center"><?php echo $server_on_hold_phy;?></td>
            <td class="text-center"><?php echo $server_on_hold_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $san_on_hold;?></td>
            <td class="text-center"><?php echo $san_on_hold_phy;?></td>
            <td class="text-center"><?php echo $san_on_hold_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $cluster_on_hold;?></td>
            <td class="text-center"><?php echo $cluster_on_hold_phy;?></td>
            <td class="text-center"><?php echo $cluster_on_hold_vir;?></td>
          </tr>
          <tr>
            <th class="warning">TOTAL RELEASED</th>
            <td class="text-center warning"><?php echo $total_released;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center warning"><?php echo ($server_release + $server_partial);?></td>
            <td class="text-center warning"><?php echo ($server_release_phy + $server_partial_phy);?></td>
            <td class="text-center warning"><?php echo ($server_release_vir + $server_partial_vir);?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center warning"><?php echo ($san_release + $san_partial);?></td>
            <td class="text-center warning"><?php echo ($san_release_phy + $san_partial_phy);?></td>
            <td class="text-center warning"><?php echo ($san_release_vir + $san_partial_vir);?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center warning"><?php echo ($cluster_release + $cluster_partial);?></td>
            <td class="text-center warning"><?php echo ($cluster_release_phy + $cluster_partial_phy);?></td>
            <td class="text-center warning"><?php echo ($cluster_release_vir + $cluster_partial_vir);?></td>
          </tr>
          <tr>
            <th class="warning">TOTAL PENDING</th>
            <td class="text-center warning"><?php echo $total_wip;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center warning"><?php echo ($server_pending + $server_wip + $server_on_hold);?></td>
            <td class="text-center warning"><?php echo ($server_pending_phy + $server_wip_phy + $server_on_hold_phy);?></td>
            <td class="text-center warning"><?php echo ($server_pending_vir + $server_wip_vir + $server_on_hold_vir);?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center warning"><?php echo ($san_pending + $san_wip + $san_on_hold);?></td>
            <td class="text-center warning"><?php echo ($san_pending_phy + $san_wip_phy + $san_on_hold_phy);?></td>
            <td class="text-center warning"><?php echo ($san_pending_vir + $san_wip_vir + $san_on_hold_vir);?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center warning"><?php echo ($cluster_pending + $cluster_wip + $cluster_on_hold);?></td>
            <td class="text-center warning"><?php echo ($cluster_pending_phy + $cluster_wip_phy + $cluster_on_hold_phy);?></td>
            <td class="text-center warning"><?php echo ($cluster_pending_vir + $cluster_wip_vir + $cluster_on_hold_vir);?></td>
          </tr>
        </table>
        </div>

        <div class="clearfix"></div>
        <div class="col-sm-12">
        <table class="table table-bordered table-hover">
          <tr>
            <th colspan="14" class="info">IMPLEMENTATION DASHBOARD</th>
          </tr>
          <tr valign="middle">
            <th colspan="14">
            <form id="form-imp-dashboard-search" class="form-inline" method="GET">
              <div class="form-group">
                From : &nbsp
              </div>
              <div class="form-group">
                <div class="input-group date" id="imp-as-on-from-datetimepicker">
                  <input type="text" class="form-control" name="imp_as_on_from" value="<?php echo $imp_as_on_from;?>" placeholder="Date" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
              <div class="form-group">
                To : &nbsp
              </div>
              <div class="form-group">
                <div class="input-group date" id="imp-as-on-to-datetimepicker">
                  <input type="text" class="form-control" name="imp_as_on_to" value="<?php echo $imp_as_on_to;?>" placeholder="Date" />
                    <span class="input-group-addon">
                    <span class="glyphicon glyphicon-calendar"></span>
                  </span>
                </div>
              </div>
              <button type="submit" value="Search" name="search_from_to" class="btn btn-primary">Search</button>
            </form>
            </th>
          </tr>
          <tr>
            <th colspan="2">&nbsp</th>
            <th class="info" >&nbsp</th>
            <th colspan="3" class="text-center bg-primary">SERVERS / EQUIPMENT</th>
            <th class="info" >&nbsp</th>
            <th colspan="3" class="text-center">SAN</th>
            <th class="info" >&nbsp</th>
            <th colspan="3" class="text-center bg-danger">CLUSTER</th>
          </tr>
          <tr class="active">
            <th width="350px">&nbsp</th>
            <th class="text-center" width="120px">Request Count</th>
            <td class="info" >&nbsp</td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Physical</th>
            <th class="text-center" width="90px">Virtual</th>
            <td class="info" >&nbsp</td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Physical</th>
            <th class="text-center" width="90px">Virtual</th>
            <td class="info" >&nbsp</td>
            <th class="text-center" width="90px">Total</th>
            <th class="text-center" width="90px">Physical</th>
            <th class="text-center" width="90px">Virtual</th>
          </tr>
          <tr>
            <th class="active">NO. REQUESTS (RFI)</th>
            <td class="text-center"><?php echo $new_req_count;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $new_server;?></td>
            <td class="text-center"><?php echo $new_server_phy;?></td>
            <td class="text-center"><?php echo $new_server_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $new_san;?></td>
            <td class="text-center"><?php echo $new_san_phy;?></td>
            <td class="text-center"><?php echo $new_san_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $new_cluster;?></td>
            <td class="text-center"><?php echo $new_cluster_phy;?></td>
            <td class="text-center"><?php echo $new_cluster_vir;?></td>
          </tr>
          <tr>
            <th class="active">RELEASED (Including PARTIAL RELEASED)</th>
            <td class="text-center"><?php echo $new_req_released;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $new_server_release;?></td>
            <td class="text-center"><?php echo $new_server_release_phy;?></td>
            <td class="text-center"><?php echo $new_server_release_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $new_san_release;?></td>
            <td class="text-center"><?php echo $new_san_release_phy;?></td>
            <td class="text-center"><?php echo $new_san_release_vir;?></td>
            <td class="info" >&nbsp</td>
            <td class="text-center"><?php echo $new_cluster_release;?></td>
            <td class="text-center"><?php echo $new_cluster_release_phy;?></td>
            <td class="text-center"><?php echo $new_cluster_release_vir;?></td>
          </tr>
        </table>
        </div>
<?php
}
?>
      </div>
      <!-- /box-content -->

    </div> <!-- /container -->
<?php
  require_once(__dir__.'/../footer.php');
?>
<script type="text/javascript">
  $(document).ready(function() {
    $('#imp-as-on-datetimepicker, #imp-as-on-from-datetimepicker, #imp-as-on-to-datetimepicker').datetimepicker({
       format: 'DD-MMM-YYYY'
    });
    $("#imp-as-on-datetimepicker").on("dp.change", function (e) {
      //$('#datetimepicker7').data("DateTimePicker").minDate(e.date);
      $('#form-imp-dashboard').submit();
    });
  });
</script>
