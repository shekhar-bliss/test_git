<?php

/* IGF VERSION */
  if(!function_exists('validation_equipment_type')) {
    function validation_equipment_type($version = '') {
      switch($version) {
        case 'v3':
        case 'v3.1':
          $ret = array(
              'OTHER',
              'NA',
              'PHYSICAL',
              'VIRTUAL',
              'CHASSIS',
              'BLADE',
              'STORAGE',
              'SAN SWITCH',
              'NW SWITCH',
              'FIREWALL',
              'LB',
              'ROUTER',
            );
          break;
        case 'v4':
        default:
          $ret = array(
              'SERVER-PHYSICAL',
              'SERVER-VIRTUAL',
              'SERVER-CHASSIS',
              'SERVER-BLADE',
              'APPLIANCE',
              'NW-FIREWALL',
              'NW-LB',
              'NW-ROUTER',
              'NW-SWITCH',
              'NW-IPS',
              'SAN SWITCH',
              'SAN ROUTER',
              'STORAGE ARRAY',
              'STORAGE-FLASH',
              'STORAGE-SVC',
              'TAPE LIBRARY',
              'OTHER',
              'NA',
            );
          break;
      }

      return $ret;
    }
  }


  if(!function_exists('igf_v3_location_distinct_name')) {
    function igf_v3_location_distinct_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                 DISTINCT TRIM(UPPER(l.loc_name)) AS loc_name
                FROM
                  idc_location AS l
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $matches = '';
          if(preg_match('/^AG3-/', $row['loc_name'], $matches)) {
            $temp = $row['loc_name'];
            $row['loc_name'] = substr($temp, 4).'-AG3';
          }
          $ret[] = $row['loc_name'];
        }
      }

      return $ret;
    }
  }

  if(!function_exists('validation_eqpt_serial_number_status')) {
    function validation_eqpt_serial_number_status($sr_no, $vendor = '', $item = '', $loc = '',  $status = '') {
      //echo __FUNCTION__.'()<br />';
      $msg    = '';
      $ret    = '';
      $where  = ' 1 ';
      $limit  = '';

      if($sr_no) {
        $where  .= " AND serial_number = '".mysql_real_escape_string($sr_no)."' ";
      }

      if($vendor) {
        $where  .= " AND UPPER(vendor_name) = UPPER('".mysql_real_escape_string($vendor)."') ";
      }

      if($item) {
        $where  .= " AND UPPER(item_name) = UPPER('".mysql_real_escape_string($item)."') ";
      }

      if($loc) {
        $where  .= " AND TRIM(UPPER(location)) LIKE UPPER('".mysql_real_escape_string($loc)."%') ";
      }

      if($status) {
        $where  .= " AND updated_qty = '".mysql_real_escape_string($status)."' ";
      }

      $sql = "SELECT
                id,
                req_id,
                item_name,
                vendor_name,
                serial_number,
                status,
                qty,
                updated_qty
              FROM
                idc_stores
              WHERE
                ".$where."
              ORDER BY
                id DESC
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        if (mysql_num_rows($res)) {
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }
      return $ret;
    }
  }

  if(!function_exists('validation_eqpt_serial_number')) {
    function validation_eqpt_serial_number($sr_no, $vendor = '', $item = '', $loc = '') {
      //echo __FUNCTION__.'()<br />';
      $msg    = '';
      $ret    = '';
      $where  = ' 1 ';
      $limit  = '';

      $eqpt = validation_eqpt_serial_number_status($sr_no, $vendor, $item, $loc, 1);
      if($eqpt) {
        if(count($eqpt) == 1) {
          $ret = TRUE;
        }
      }

      if($ret !== TRUE) {
        // Check if item is already issued or not
        $eqpt_issued = validation_eqpt_serial_number_status($sr_no, $vendor, $item, $loc, 0);
        if($eqpt_issued) {
          if(count($eqpt_issued) == 1) {
            $ret = 'ALREADY ISSUED';
          }
        }
        else {
          $ret = FALSE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('igf_eqpt_type_find')) {
    function igf_eqpt_type_find($type) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = ' LIMIT 0,1';
      if($type) {
        $where  .= " AND TRIM(dt.dev_type_name) = TRIM('".mysql_real_escape_string($type)."') ";
      }

      $sql = " SELECT dt.dev_type_id
               FROM
                idc_device_type AS dt
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        $row = mysql_fetch_array($res);
        $ret = $row['dev_type_id'];
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }

  if(!function_exists('igf_request_epqt_count')) {
    function igf_request_epqt_count($req_id, $type = '') {
      $ret    = '';
      $where  = ' 1 AND i.igf_deleted=0 ';
      if($req_id) {
        $where .= " AND i.req_id='".mysql_real_escape_string($req_id)."' ";
      }

      switch (strtoupper($type)) {
        case 'PHYSICAL':
          $where .= " AND i_ser.igf_server_type_id IN (3,5,6,30,41,42,43,44,7,8,9,10,11,12,51,71,72,73,74)";
          break;
        case 'VIRTUAL':
          $where .= " AND i_ser.igf_server_type_id IN (4)";
          break;
        default:
          # code...
          break;
      }

      $sql =  " SELECT COUNT(i_ser.igf_server_id) AS server_count ";
      $sql .= " FROM
                idc_igf_server AS i_ser
                LEFT JOIN
                idc_igf AS i ON i_ser.igf_id = i.igf_id
                ";
      $sql .= " WHERE ".$where."  ";
      $sql .= " LIMIT 0,1";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['server_count'];
        }
      }
      return $ret;
    }
  }


  if(!function_exists('igf_request_epqt_detail')) {
    function igf_request_epqt_detail($req_id) {
      //echo __FUNCTION__.'()<br />';
      $msg    = '';
      $ret    = '';
      $where  = ' 1 AND i.igf_deleted=0 ';
      if($req_id) {
        $where .= " AND i.req_id='".mysql_real_escape_string($req_id)."' ";
      }

      $sql =  " SELECT
                  i.req_id,
                  i_ser.*,
                  l.loc_name,
                  l.loc_cmdb_city,
                  l.loc_cmdb_fac,
                  l.loc_cmdb_fac_id,
                  IF(ISNULL(i_ser.igf_server_release_at),'NOT RELEASED','RELEASED') AS server_release_status
                FROM
                  idc_igf_server AS i_ser
                  LEFT JOIN
                  idc_igf AS i ON i_ser.igf_id = i.igf_id
                  LEFT JOIN
                  idc_location AS l ON i_ser.igf_server_loc_id = l.loc_id
                WHERE ".$where."  ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

/* IGF */

  if(!function_exists('igf_detail')) {
    function igf_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND i.igf_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT i.*
               FROM
                idc_igf AS i
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($id){
          $ret = mysql_fetch_array($res);
        }
        else{
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('igf_unique')) {
    function igf_unique($data, $id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $result = '';
      $where = ' 1 ';
      if($id) {
        $where .= " AND igf_id != '".mysql_real_escape_string($id)."'";
      }
      if($data) {
        foreach ($data as $key => $value) {
          if(trim($value) && strtoupper($value) != 'NULL' ) {
            $where .= " AND LOWER(".$key.") = LOWER(TRIM('".mysql_real_escape_string($value)."'))  ";
          }
        }

        $sql = "SELECT
                  igf_id
                FROM
                  idc_igf
                WHERE
                  ".$where."
                LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if($res) {
          if (mysql_num_rows($res)) {
            //var_dump(mysql_fetch_array($res));
            $result = mysql_fetch_array($res);
            $ret = $result['igf_id'];
          }
          else {
            $ret = TRUE;
          }
        }

      }
      return $ret;
    }
  }


  if(!function_exists('igf_request_igf')) {
    function igf_request_igf($req_id, $del_status = '1') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($req_id) {
        $where  .= " AND i.req_id = '".mysql_real_escape_string($req_id)."' ";
      }

      if($del_status !== '' || $del_status !== NULL ) {
        $where  .= " AND i.igf_deleted = '".mysql_real_escape_string($del_status)."' ";
      }

      $sql = " SELECT i.*
               FROM
                idc_igf AS i
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('igf_request_igf_contact')) {
    function igf_request_igf_contact($req_id, $contact_type_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($req_id) {
        $where  .= " AND i.req_id = '".mysql_real_escape_string($req_id)."' ";
      }



      if($contact_type_id) {
        $where  .= " AND ic.igf_contact_type_id = '".mysql_real_escape_string($contact_type_id)."' ";
        $limit  = 'LIMIT 0, 1';
      }

      $where  .= " AND i.igf_deleted = '0' ";

      $sql = " SELECT
                ic.*,
                i.req_id
               FROM
                idc_igf AS i
                LEFT JOIN
                idc_igf_contact AS ic ON i.igf_id = ic.igf_id
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {

        if($contact_type_id){
          $ret = mysql_fetch_array($res);
        }
        else {
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }

      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('igf_add')) {
    function igf_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_igf (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .=" NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .=" NOW(), ";
              break;
            default:
              $sql .="'".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=");";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = mysql_insert_id();
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_update')) {
    function igf_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_igf SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .= $key."= NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .= $key."= NOW(), ";
              break;
            default:
              $sql .= $key."='".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=" WHERE igf_id='".mysql_real_escape_string($id)."' ";
        $sql .=";";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = $id;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_save')) {
    function igf_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = igf_update($data, $id);
      }
      else {
        $ret = igf_add($data);
      }

      return $ret;
    }
  }



/* IGF BUDGET */

  if(!function_exists('igf_budget_detail')) {
    function igf_budget_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ib.igf_budget_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }

      $sql = " SELECT ib.*
               FROM
                idc_igf_budget AS ib
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($id){
          $ret = mysql_fetch_array($res);
        }
        else{
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('igf_budget_unique')) {
    function igf_budget_unique($data, $id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $result = '';
      $where = ' 1 ';
      if($id) {
        $where .= " AND igf_budget_id != '".mysql_real_escape_string($id)."'";
      }
      if($data) {
        foreach ($data as $key => $value) {
          if(trim($value) && strtoupper($value) != 'NULL' ) {
            $where .= " AND LOWER(".$key.") = LOWER(TRIM('".mysql_real_escape_string($value)."'))  ";
          }
        }

        $sql = "SELECT
                  igf_budget_id
                FROM
                  idc_igf_budget
                WHERE
                  ".$where."
                LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if($res) {
          if (mysql_num_rows($res)) {
            //var_dump(mysql_fetch_array($res));
            $result = mysql_fetch_array($res);
            $ret = $result['igf_budget_id'];
          }
          else {
            $ret = TRUE;
          }
        }

      }
      return $ret;
    }
  }

  if(!function_exists('igf_budget_add')) {
    function igf_budget_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_igf_budget (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .=" NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .=" NOW(), ";
              break;
            default:
              $sql .="'".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=");";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = mysql_insert_id();
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_budget_update')) {
    function igf_budget_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_igf_budget SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .= $key."= NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .= $key."= NOW(), ";
              break;
            default:
              $sql .= $key."='".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=" WHERE igf_budget_id='".mysql_real_escape_string($id)."' ";
        $sql .=";";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = $id;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_budget_save')) {
    function igf_budget_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = igf_budget_update($data, $id);
      }
      else {
        $ret = igf_budget_add($data);
      }

      return $ret;
    }
  }

/* IGF CONTACT */

  if(!function_exists('igf_contact_detail')) {
    function igf_contact_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ic.igf_contact_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }

      $sql = " SELECT ic.*
               FROM
                idc_igf_contact AS ic
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($id){
          $ret = mysql_fetch_array($res);
        }
        else{
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('igf_contact_unique')) {
    function igf_contact_unique($data, $id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $result = '';
      $where = ' 1 ';
      if($id) {
        $where .= " AND igf_contact_id != '".mysql_real_escape_string($id)."'";
      }
      if($data) {
        foreach ($data as $key => $value) {
          if(trim($value) && strtoupper($value) != 'NULL' ) {
            $where .= " AND LOWER(".$key.") = LOWER(TRIM('".mysql_real_escape_string($value)."'))  ";
          }
        }

        $sql = "SELECT
                  igf_contact_id
                FROM
                  idc_igf_contact
                WHERE
                  ".$where."
                LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if($res) {
          if (mysql_num_rows($res)) {
            //var_dump(mysql_fetch_array($res));
            $result = mysql_fetch_array($res);
            $ret = $result['igf_contact_id'];
          }
          else {
            $ret = TRUE;
          }
        }

      }
      return $ret;
    }
  }

  if(!function_exists('igf_contact_add')) {
    function igf_contact_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_igf_contact (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .=" NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .=" NOW(), ";
              break;
            default:
              $sql .="'".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=");";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = mysql_insert_id();
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_contact_update')) {
    function igf_contact_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_igf_contact SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .= $key."= NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .= $key."= NOW(), ";
              break;
            default:
              $sql .= $key."='".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=" WHERE igf_contact_id='".mysql_real_escape_string($id)."' ";
        $sql .=";";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = $id;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_contact_save')) {
    function igf_contact_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = igf_contact_update($data, $id);
      }
      else {
        $ret = igf_contact_add($data);
      }

      return $ret;
    }
  }

/* IGF SERVER */

  if(!function_exists('igf_server_detail')) {
    function igf_server_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND iser.igf_server_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }

      $sql = " SELECT iser.*
               FROM
                idc_igf_server AS iser
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($id){
          $ret = mysql_fetch_array($res);
        }
        else{
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('igf_server_unique')) {
    function igf_server_unique($data, $id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $result = '';
      $where = ' 1 ';
      if($id) {
        $where .= " AND igf_server_id != '".mysql_real_escape_string($id)."'";
      }
      if($data) {
        foreach ($data as $key => $value) {
          if(trim($value) && strtoupper($value) != 'NULL' ) {
            $where .= " AND LOWER(".$key.") = LOWER(TRIM('".mysql_real_escape_string($value)."'))  ";
          }
        }

        $sql = "SELECT
                  igf_server_id
                FROM
                  idc_igf_server
                WHERE
                  ".$where."
                LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if($res) {
          if (mysql_num_rows($res)) {
            //var_dump(mysql_fetch_array($res));
            $result = mysql_fetch_array($res);
            $ret = $result['igf_server_id'];
          }
          else {
            $ret = TRUE;
          }
        }

      }
      return $ret;
    }
  }

  if(!function_exists('igf_server_add')) {
    function igf_server_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_igf_server (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .=" NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .=" NOW(), ";
              break;
            default:
              $sql .="'".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=");";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = mysql_insert_id();
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_server_update')) {
    function igf_server_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_igf_server SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .= $key."= NULL, ";
              break;
            case 'NOW':
            case 'NOW()':
              $sql .= $key."= NOW(), ";
              break;
            default:
              $sql .= $key."='".mysql_real_escape_string($value)."', ";
              break;
          }
        }
        $sql = rtrim(trim($sql), ',');
        $sql .=" WHERE igf_server_id='".mysql_real_escape_string($id)."' ";
        $sql .=";";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = $id;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('igf_server_save')) {
    function igf_server_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = igf_server_update($data, $id);
      }
      else {
        $ret = igf_server_add($data);
      }

      return $ret;
    }
  }


  if(!function_exists('request_igf_save')) {
    function request_igf_save($igf_doc, $req_id, $igf_keys) {
      //echo __FUNCTION__.'()<br />';
      $msg    = '';
      $ret    = FALSE;
      $uname  = '';
      $data_igf         = '';
      $data_igf_budget  = '';

      if(isset($_SESSION['pei_user'])) {
        $uname = strtolower($_SESSION['pei_user']);
      }

      if($igf_doc) {
        $igf_doc_id = '';
        // RENAME uploaded doc as per IGF doc name standard
        // Update igf document file name with Request ID prefix


        $data_doc_file_name = ltrim(trim($igf_doc['name']), $req_id.'-');
        $data_doc_name      = $req_id.'-'.$data_doc_file_name;
        $data_doc_file_path = strstr($igf_doc['path'], '---', TRUE);
        $data_doc_file_path .= '---'.$data_doc_name;
        //echo 'RENAME :'.$igf_doc['path'].'  => '.$igf_doc['path'].'<br />';
        $rename = rename($igf_doc['path'], $data_doc_file_path);
        if($rename){
          // SAVE DOC IGF
          $data_doc = array(
                        'doc_name' => $data_doc_name,
                        'doc_file_name' => $data_doc_name,
                        'doc_file_path' => $data_doc_file_path,
                        'created_by'  => $uname,
                        'doc_type_id' => 3 // IG2 Uploaded Excel
                      );
          $igf_doc_id = pei_doc_save($data_doc);

        }


        $igf_id                 = '';
        $igf_file_name          = '';
        $igf_file_name          = $igf_doc['name'];
        $igf_doc_name           = '';
        $igf_doc_path           = $igf_doc['path'];
        $igf_req_group_id       = '';
        $igf_req_sub_group_id   = '';
        $igf_name               = '';
        $igf_data_contact       = array();
        $igf_data_budget        = array();

        // Read the uploaded IGF excel file
        $igf_v4 = new SpreadsheetReader($data_doc_file_path);
        // Fetch data form Sheet 2 => CONTACT & BUDGET INFORMATION
        $igf_v4->ChangeSheet(2);
        foreach ($igf_v4 as $Key => $Row) {

          if($Key == $igf_keys['contact_budget']['req_group_name']['index']) {
            $data_igf['req_group_name'] = trim(preg_replace('/\s+/', ' ', $Row[2]));
            if($data_igf['req_group_name']) {
              $igf_req_group_id = find_requestor_group_id($data_igf['req_group_name']);
              if($igf_req_group_id) {
                $data_igf['req_group_id'] = $igf_req_group_id;
              }
            }
          }

          if($Key == $igf_keys['contact_budget']['req_sub_group_name']['index']) {
            $data_igf['req_sub_group_name'] = trim(preg_replace('/\s+/', ' ', $Row[2]));
            if($data_igf['req_sub_group_name']) {
              $data_igf['req_sub_group_id'] = find_requestor_sub_group_id($igf_req_group_id, $data_igf['req_sub_group_name']);
            }
          }

          if($Key == $igf_keys['contact_budget']['igf_name']['index']) {
            $data_igf['igf_name'] = trim(preg_replace('/\s+/', ' ', $Row[2]));
          }

          if($Key == $igf_keys['contact_budget']['igf_contact_name']['index']) {
            $igf_data_contact['spoc']['name'] = trim($Row[2]);
            $igf_data_contact['hod']['name'] = trim($Row[3]);
          }
          if($Key == $igf_keys['contact_budget']['igf_contact_mobile']['index']) {
            $igf_data_contact['spoc']['mobile'] = trim($Row[2]);
            $igf_data_contact['hod']['mobile'] = trim($Row[3]);
          }
          if($Key == $igf_keys['contact_budget']['igf_contact_email']['index']) {
            $igf_data_contact['spoc']['email'] = trim($Row[2]);
            $igf_data_contact['hod']['email'] = trim($Row[3]);
          }

          if(isset($igf_keys['contact_budget']['igf_contact_name_ops']) && $Key == $igf_keys['contact_budget']['igf_contact_name_ops']['index']) {
            $igf_data_contact['contact_1']['name'] = trim($Row[2]);
            $igf_data_contact['contact_2']['name'] = trim($Row[3]);
          }
          if(isset($igf_keys['contact_budget']['igf_contact_mobile_ops']) && $Key == $igf_keys['contact_budget']['igf_contact_mobile_ops']['index']) {
            $igf_data_contact['contact_1']['mobile'] = trim($Row[2]);
            $igf_data_contact['contact_2']['mobile'] = trim($Row[3]);
          }
          if(isset($igf_keys['contact_budget']['igf_contact_email_ops']) && $Key == $igf_keys['contact_budget']['igf_contact_email_ops']['index']) {
            $igf_data_contact['contact_1']['email'] = trim($Row[2]);
            $igf_data_contact['contact_2']['email'] = trim($Row[3]);
          }
          if($Key == $igf_keys['contact_budget']['igf_budget_fund_center']['index']) {
            $data_igf_budget['igf_budget_fund_center'] = trim(preg_replace('/\s+/', ' ', $Row[2]));
          }
          if($Key == $igf_keys['contact_budget']['igf_budget_gl']['index']) {
            $data_igf_budget['igf_budget_gl'] = trim(preg_replace('/\s+/', ' ', $Row[2]));
          }
          if($Key == $igf_keys['contact_budget']['igf_budget_wbs']['index']) {
            $data_igf_budget['igf_budget_wbs'] = trim(preg_replace('/\s+/', ' ', $Row[2]));
          }
        }

        // Save IGF file details
        if($igf_doc_id) {
          $data_igf['igf_doc_id'] = $igf_doc_id;
        }
        $data_igf['req_id']     = $req_id;
        $data_igf['created_by'] = $uname;
        $data_igf['created_at'] = 'NOW';
        $igf_id = igf_save($data_igf);

        if($igf_id) {
          // Save igf contact detail in database

          if($igf_data_contact) {
            foreach ($igf_data_contact as $key => $contact) {
              if(isset($contact['name']) && $contact['name'] != '') {
                save_igf_contact($igf_id, $contact, $key, $uname);
              }
            }
          }

          // Save IGF budget detail
          $data_igf_budget['igf_id']      = $igf_id;
          $data_igf_budget['created_by']  = $uname;
          $data_igf_budget['created_at']  = 'NOW';

          $igf_budget_id = igf_budget_save($data_igf_budget);

          // Save Sheet 3 => SERVER DETAILS
          $igf_v4->ChangeSheet(3);
          foreach ($igf_v4 as $Key => $Row) {
            if($Key > 1) {
              $data_igf_eqpt = '';
              $igf_eqpt_app               = '';
              $igf_eqpt_user_group        = '';
              $igf_eqpt_user_sub_group    = '';
              $igf_eqpt_env               = '';
              $igf_eqpt_loc               = '';
              $igf_eqpt_sh                = '';
              $igf_eqpt_rr                = '';
              $igf_eqpt_type              = '';
              $igf_eqpt_hypervisor        = '';
              $igf_eqpt_role              = '';
              $igf_eqpt_serial_number     = '';
              $igf_eqpt_make              = '';
              $igf_eqpt_make_id           = '';
              $igf_eqpt_model             = '';
              $igf_eqpt_cpu_type          = '';


              $data_igf_eqpt['igf_id']    = $igf_id;
              $igf_eqpt_app               = trim(preg_replace('/\s+/', ' ', $Row[0]));
              $data_igf_eqpt['igf_server_app'] = $igf_eqpt_app;
              $igf_eqpt_user_group        = trim(preg_replace('/\s+/', ' ', $Row[1]));
              $data_igf_eqpt['igf_server_req_group_name'] = $igf_eqpt_user_group;
              $igf_eqpt_user_sub_group    = trim(preg_replace('/\s+/', ' ', $Row[2]));
              $data_igf_eqpt['igf_server_req_sub_group_name'] = $igf_eqpt_user_sub_group;
              $igf_eqpt_env               = trim(preg_replace('/\s+/', ' ', $Row[2]));
              $data_igf_eqpt['igf_server_env'] = $igf_eqpt_env;
              if($igf_eqpt_app != '' && $igf_eqpt_user_group != '' && $igf_eqpt_user_sub_group != '' && $igf_eqpt_env != '') {

                if($igf_eqpt_user_group) {
                  $igf_eqpt_user_group_id = find_requestor_group_id($igf_eqpt_user_group);
                  $data_igf_eqpt['igf_server_req_group_id']     = $igf_eqpt_user_group_id;
                }

                if($igf_eqpt_user_sub_group) {
                  $data_igf_eqpt['igf_server_req_sub_group_id'] = find_requestor_sub_group_id($igf_eqpt_user_group_id, $igf_eqpt_user_sub_group);
                }

                if($igf_eqpt_env) {
                  $data_igf_eqpt['igf_server_env_id'] = find_env_id($igf_eqpt_env);
                }

                $igf_eqpt_loc = trim(preg_replace('/\s+/', ' ', $Row[4]));
                $data_igf_eqpt['igf_server_loc'] = $igf_eqpt_loc;
                if($igf_eqpt_loc) {
                  $data_igf_eqpt['igf_server_loc_id'] = find_loc_id($igf_eqpt_loc);
                }

                $igf_eqpt_sh = trim(preg_replace('/\s+/', ' ', $Row[5]));
                $data_igf_eqpt['igf_server_server_hall'] = $igf_eqpt_sh;
                if($igf_eqpt_sh) {
                  $data_igf_eqpt['igf_server_server_hall_id']  = find_server_hall_id($igf_eqpt_sh);
                }

                $igf_eqpt_rr = trim(preg_replace('/\s+/', ' ', $Row[6]));
                $data_igf_eqpt['igf_server_row_rack'] = $igf_eqpt_rr;
                if($igf_eqpt_rr) {
                  $data_igf_eqpt['igf_server_row_rack_id'] = find_row_rack_id($igf_eqpt_rr);
                }

                $data_igf_eqpt['igf_server_rack_name']  = trim(preg_replace('/\s+/', ' ', $Row[7]));
                $data_igf_eqpt['igf_server_rack_u']     = trim(preg_replace('/\s+/', ' ', $Row[8]));
                $data_igf_eqpt['igf_server_slot_no  ']  = trim(preg_replace('/\s+/', ' ', $Row[9]));
                $data_igf_eqpt['igf_server_number']     = trim(preg_replace('/\s+/', ' ', $Row[10]));

                $igf_eqpt_type = trim(preg_replace('/\s+/', ' ', $Row[11]));
                $data_igf_eqpt['igf_server_type']  = $igf_eqpt_type;
                if($igf_eqpt_type) {
                  $data_igf_eqpt['igf_server_type_id'] = igf_eqpt_type_find($igf_eqpt_type);

                  // TODO
                  // Add Device Info to idc_device
                }

                $igf_eqpt_hypervisor        = trim(preg_replace('/\s+/', ' ', $Row[12]));
                $data_igf_eqpt['igf_server_hypervisor']  = $igf_eqpt_hypervisor;
                if($igf_eqpt_hypervisor) {
                  $data_igf_eqpt['igf_server_hypervisor_id']   = find_server_hypervisor_id($igf_eqpt_hypervisor);
                }

                $igf_eqpt_role              = trim(preg_replace('/\s+/', ' ', $Row[13]));
                $data_igf_eqpt['igf_server_role']  = $igf_eqpt_role;
                if($igf_eqpt_role) {
                  $data_igf_eqpt['igf_server_role_id']  = find_server_role_id($igf_eqpt_role);
                }

                $data_igf_eqpt['igf_server_serial_number']  = trim(preg_replace('/\s+/', ' ', $Row[14]));

                $igf_eqpt_make              = trim(preg_replace('/\s+/', ' ', $Row[15]));
                $data_igf_eqpt['igf_server_make']  = $igf_eqpt_make;
                if($igf_eqpt_make) {
                  $igf_eqpt_make_id         = vendor_name_unique($igf_eqpt_make);
                  $data_igf_eqpt['igf_server_make_id'] = $igf_eqpt_make_id;
                }

                $igf_eqpt_model             = trim(preg_replace('/\s+/', ' ', $Row[16]));
                $data_igf_eqpt['igf_server_model']  = $igf_eqpt_model;
                if($igf_eqpt_model) {
                  $data_vendor_model = '';
                  $data_vendor_model['vendor_model_name'] = $igf_eqpt_model;
                  if($igf_eqpt_make_id) {
                    $data_vendor_model['vendor_id'] = $igf_eqpt_make_id;
                  }
                  $igf_eqpt_model_id         = vendor_model_unique($data_vendor_model);
                  $data_igf_eqpt['igf_server_model_id'] = $igf_eqpt_make_id;
                }

                $igf_eqpt_cpu_type          = trim(preg_replace('/\s+/', ' ', $Row[17]));
                $data_igf_eqpt['igf_server_cpu_type']  = $igf_eqpt_cpu_type;
                if($igf_eqpt_cpu_type) {
                  $data_igf_eqpt['igf_server_cpu_type_id'] = find_server_cpu_type_id($igf_eqpt_cpu_type);
                }


                $data_igf_eqpt['igf_server_cpu_no']  = trim(preg_replace('/\s+/', ' ', $Row[18]));
                $data_igf_eqpt['igf_server_cpu_cores']  = trim(preg_replace('/\s+/', ' ', $Row[19]));
                $data_igf_eqpt['igf_server_ram']  = trim(preg_replace('/\s+/', ' ', $Row[20]));

                $data_igf_eqpt['igf_server_storage_int_no']  = trim(preg_replace('/\s+/', ' ', $Row[21]));
                $data_igf_eqpt['igf_server_storage_int_size']  = trim(preg_replace('/\s+/', ' ', $Row[22]));
                $data_igf_eqpt['igf_server_storage_int_raid_config']  = trim(preg_replace('/\s+/', ' ', $Row[23]));

                $data_igf_eqpt['igf_server_nic_1g']  = trim(preg_replace('/\s+/', ' ', $Row[24]));
                $data_igf_eqpt['igf_server_nic_10g']  = trim(preg_replace('/\s+/', ' ', $Row[25]));

                $data_igf_eqpt['igf_server_fc_hba_card']  = trim(preg_replace('/\s+/', ' ', $Row[26]));
                $data_igf_eqpt['igf_server_fc_hba_port']  = trim(preg_replace('/\s+/', ' ', $Row[27]));
                $data_igf_eqpt['igf_server_fc_hba_port_speed']  = trim(preg_replace('/\s+/', ' ', $Row[28]));

                $data_igf_eqpt['igf_server_dl_port']  = trim(preg_replace('/\s+/', ' ', $Row[29]));
                $data_igf_eqpt['igf_server_dl_type']  = trim(preg_replace('/\s+/', ' ', $Row[30]));
                $data_igf_eqpt['igf_server_dl_speed']  = trim(preg_replace('/\s+/', ' ', $Row[31]));

                $data_igf_eqpt['igf_server_sl_port']  = trim(preg_replace('/\s+/', ' ', $Row[32]));
                $data_igf_eqpt['igf_server_sl_type']  = trim(preg_replace('/\s+/', ' ', $Row[33]));
                $data_igf_eqpt['igf_server_sl_speed']  = trim(preg_replace('/\s+/', ' ', $Row[34]));

                $data_igf_eqpt['igf_server_cl_port']  = trim(preg_replace('/\s+/', ' ', $Row[35]));
                $data_igf_eqpt['igf_server_cl_type']  = trim(preg_replace('/\s+/', ' ', $Row[36]));
                $data_igf_eqpt['igf_server_cl_speed']  = trim(preg_replace('/\s+/', ' ', $Row[37]));

                $data_igf_eqpt['igf_server_network_zone']  = trim(preg_replace('/\s+/', ' ', $Row[38]));
                $data_igf_eqpt['igf_server_network_sub_zone']  = trim(preg_replace('/\s+/', ' ', $Row[39]));
                $data_igf_eqpt['igf_server_load_balancer']  = trim(preg_replace('/\s+/', ' ', $Row[40]));

                $data_igf_eqpt['igf_server_ha_cluster']  = trim(preg_replace('/\s+/', ' ', $Row[41]));
                $data_igf_eqpt['igf_server_ha_cluster_type']  = trim(preg_replace('/\s+/', ' ', $Row[42]));
                $data_igf_eqpt['igf_server_ha_cluster_pair']  = trim(preg_replace('/\s+/', ' ', $Row[43]));

                $data_igf_eqpt['igf_server_os']  = trim(preg_replace('/\s+/', ' ', $Row[44]));
                $data_igf_eqpt['igf_server_os_version']  = trim(preg_replace('/\s+/', ' ', $Row[45]));
                $data_igf_eqpt['igf_server_db']  = trim(preg_replace('/\s+/', ' ', $Row[46]));
                $data_igf_eqpt['igf_server_db_version']  = trim(preg_replace('/\s+/', ' ', $Row[47]));

                $data_igf_eqpt['igf_server_storage_ext_type']  = trim(preg_replace('/\s+/', ' ', $Row[48]));
                $data_igf_eqpt['igf_server_storage_ext_iops']  = trim(preg_replace('/\s+/', ' ', $Row[49]));
                $data_igf_eqpt['igf_server_storage_ext_array']  = trim(preg_replace('/\s+/', ' ', $Row[50]));
                $data_igf_eqpt['igf_server_storage_ext_raid_config']  = trim(preg_replace('/\s+/', ' ', $Row[51]));
                $data_igf_eqpt['igf_server_storage_ext_p_vol_space']  = trim(preg_replace('/\s+/', ' ', $Row[52]));
                $data_igf_eqpt['igf_server_storage_ext_s_vol']  = trim(preg_replace('/\s+/', ' ', $Row[53]));
                $data_igf_eqpt['igf_server_storage_ext_s_vol_space']  = trim(preg_replace('/\s+/', ' ', $Row[54]));

                $data_igf_eqpt['igf_server_storage_int_fs']  = trim(preg_replace('/\s+/', ' ', $Row[55]));
                $data_igf_eqpt['igf_server_storage_ext_fs']  = trim(preg_replace('/\s+/', ' ', $Row[56]));


                $data_igf_eqpt['igf_server_volume_manager']  = trim(preg_replace('/\s+/', ' ', $Row[57]));
                $data_igf_eqpt['igf_server_kernel_parameter']  = trim(preg_replace('/\s+/', ' ', $Row[58]));
                $data_igf_eqpt['igf_server_additional_package']  = trim(preg_replace('/\s+/', ' ', $Row[59]));
                $data_igf_eqpt['igf_server_user_id']  = trim(preg_replace('/\s+/', ' ', $Row[60]));
                $data_igf_eqpt['igf_server_idc_support']  = trim(preg_replace('/\s+/', ' ', $Row[61]));
                $data_igf_eqpt['igf_server_remark']  = trim(preg_replace('/\s+/', ' ', $Row[62]));

                $data_igf_eqpt['igf_server_reconfig_rm_ram']  = trim(preg_replace('/\s+/', ' ', $Row[63]));
                $data_igf_eqpt['igf_server_reconfig_rm_hdd']  = trim(preg_replace('/\s+/', ' ', $Row[64]));
                $data_igf_eqpt['igf_server_reconfig_rm_nic']  = trim(preg_replace('/\s+/', ' ', $Row[65]));
                $data_igf_eqpt['igf_server_reconfig_rm_fc_hba']  = trim(preg_replace('/\s+/', ' ', $Row[66]));

                $data_igf_eqpt['igf_server_reconfig_add_ram']  = trim(preg_replace('/\s+/', ' ', $Row[67]));
                $data_igf_eqpt['igf_server_reconfig_add_hdd']  = trim(preg_replace('/\s+/', ' ', $Row[68]));
                $data_igf_eqpt['igf_server_reconfig_add_nic']  = trim(preg_replace('/\s+/', ' ', $Row[69]));
                $data_igf_eqpt['igf_server_reconfig_add_fc_hba']  = trim(preg_replace('/\s+/', ' ', $Row[70]));

                $data_igf_eqpt['igf_server_hostname']  = trim(preg_replace('/\s+/', ' ', $Row[71]));
                $data_igf_eqpt['igf_server_console_ip']  = trim(preg_replace('/\s+/', ' ', $Row[72]));
                $data_igf_eqpt['igf_server_console_ip_sm']  = trim(preg_replace('/\s+/', ' ', $Row[73]));
                $data_igf_eqpt['igf_server_console_ip_gw']  = trim(preg_replace('/\s+/', ' ', $Row[74]));
                $data_igf_eqpt['igf_server_data_ip_1']  = trim(preg_replace('/\s+/', ' ', $Row[75]));
                $data_igf_eqpt['igf_server_data_ip_2']  = trim(preg_replace('/\s+/', ' ', $Row[76]));
                $data_igf_eqpt['igf_server_vip']  = trim(preg_replace('/\s+/', ' ', $Row[77]));
                $data_igf_eqpt['igf_server_data_ip_sm']  = trim(preg_replace('/\s+/', ' ', $Row[78]));
                $data_igf_eqpt['igf_server_data_ip_gw']  = trim(preg_replace('/\s+/', ' ', $Row[79]));

                $data_igf_eqpt['igf_server_lb_ip']  = trim(preg_replace('/\s+/', ' ', $Row[80]));
                $data_igf_eqpt['igf_server_public_ip']  = trim(preg_replace('/\s+/', ' ', $Row[81]));
                $data_igf_eqpt['igf_server_private_lan_ip']  = trim(preg_replace('/\s+/', ' ', $Row[82]));
                $data_igf_eqpt['igf_server_private_lan_sm']  = trim(preg_replace('/\s+/', ' ', $Row[83]));
                $data_igf_eqpt['igf_server_rac_ip']  = trim(preg_replace('/\s+/', ' ', $Row[84]));
                $data_igf_eqpt['igf_server_scan_ip']  = trim(preg_replace('/\s+/', ' ', $Row[85]));
                $data_igf_eqpt['igf_server_heartbeat_ip']  = trim(preg_replace('/\s+/', ' ', $Row[86]));
                $data_igf_eqpt['igf_server_cluster_ic_ip']  = trim(preg_replace('/\s+/', ' ', $Row[87]));
                $data_igf_eqpt['igf_server_oracle_vip']  = trim(preg_replace('/\s+/', ' ', $Row[88]));

                if(isset($Row[89])) {
                  $data_igf_eqpt['igf_server_misc']  = trim(preg_replace('/\s+/', ' ', $Row[89]));
                }

                if(isset($Row[90])) {
                  $data_igf_eqpt['igf_server_app_owner_name']  = trim(preg_replace('/\s+/', ' ', $Row[90]));
                }
                if(isset($Row[91])) {
                  $data_igf_eqpt['igf_server_app_owner_email']  = trim(preg_replace('/\s+/', ' ', $Row[91]));
                }
                if(isset($Row[92])) {
                  $data_igf_eqpt['igf_server_app_owner_mobile']  = trim(preg_replace('/\s+/', ' ', $Row[92]));
                }

                $data_igf_eqpt['created_by']  = $uname;
                $data_igf_eqpt['created_at']  = 'NOW';
                // $igf_server_id
                $igf_eqpt_id = igf_server_save($data_igf_eqpt);
              }
            }
          }


        }// END IF $igf_id
      } // END IF $igf_doc

      if($igf_id) {
        $ret = $igf_id;
      }
      return $ret;
    }
  }
