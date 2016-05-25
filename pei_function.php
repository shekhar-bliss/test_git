<?php
  // Last synchronized Dev and LIVE on 30-MAR-2016

  if(!function_exists('pei_date_format')) {
    function pei_date_format($date_time, $format = 'd-M-Y') {
      $ret = date($format, strtotime($date_time));
      return $ret;
    }
  }

  if(!function_exists('pei_datetime_format')) {
    function pei_datetime_format($date_time, $format = 'd-M-Y H:i:s') {
      $ret = date($format, strtotime($date_time));
      return $ret;
    }
  }

  if(!function_exists('pei_datetime_convert')) {
    // $format_input  13-JUN-2015 16:30:00
    // $format_output 2015-06-13 16:30:00
    function pei_datetime_convert($date_time, $format_input = 'd-M-Y H:i:s', $format_output = 'Y-m-d H:i:s') {
      switch($format_input){
        case 'd-M-Y H:i:s':
          $m = date('m', strtotime($date_time[3].$date_time[4].$date_time[5]));
          $date_time[3] = $m[0];
          $date_time[4] = $m[1];
          $date_time = substr_replace($date_time, '', 5, 1);
          break;
        case 'Y-m-d H:i:s':
          break;
        default:
      }
      $ret = date($format_output, strtotime($date_time));
      return $ret;
    }
  }

  if(!function_exists('pei_display_string')) {
    function pei_display_string($str, $len = 20, $suffix = '..') {
      $ret      = $str;
      $str_len  = strlen($str);
      if($str_len > $len) {
        $ret = substr($str, 0, ($len - 2));
        $ret = $ret.$suffix;
      }
      return $ret;
    }
  }
  if(!function_exists('display_string')) {
    function display_string($str, $len = 20) {
      $ret      = $str;
      $str_len  = strlen($str);
      if($str_len > $len) {
        $ret = substr($str, 0, ($len - 2));
        $ret = $ret.'..';
      }
      return $ret;
    }
  }

  if(!function_exists('pei_fetch_mail_from_string')) {
    function pei_fetch_mail_from_string($str, $separator = ',') {
      $ret = '';
      $mail_arr = explode($separator, $str);
      if($mail_arr){
        foreach ($mail_arr as $key => $mail) {
          $ret[]['mail'] = trim($mail);
        }
      }
      return $ret;
    }
  }

  /*
    Find extra values in array by array key
  */
  if(!function_exists('pei_array_diff_by_key')) {
    function pei_array_diff_by_key($array, $array_against, $array_key) {
      //echo __FUNCTION__.'() <hr /> <br />';
      $msg  = '';
      $ret = FALSE;
      $from_array[] = '00';
      if($array_against) {
        foreach ($array_against as $key => $value) {
          if(isset($value[$array_key])){
            $from_array[] = $value[$array_key];
          }
        }
      }

      if($array){
        foreach ($array as $key => $value) {
          if(!in_array($value[$array_key], $from_array)){
            $ret[] = $value;
          }
        }
      }
      return $ret;
    }
  }

  if(!function_exists('pei_priority')) {
    function pei_priority() {
      $ret = array(
        array('value' => 10, 'name' => 'P1'),
        array('value' => 20, 'name' => 'P2'),
        array('value' => 30, 'name' => 'P3'),
        );
      return $ret;
    }
  }


  if(!function_exists('support_distinct_name')) {
    function support_distinct_name() {
      $ret = array(
              'PS',
              'PS/NW',
              'PS/HW',
              'PS/NW/HW',
              'PS/NW/HW/OSA',
              'PS/NW/HW/OSA/DB',
              'PS/NW/HW/OSA/APP',
              'PS/NW/HW/OSA/BKP',
              'PS/NW/HW/OSA/DB/BKP',
              'PS/NW/HW/OSA/APP/BKP',
              'PS/NW/HW/OSA/DB/APP/BKP',
              'NA',
            );
      return $ret;
    }
  }

  if(!function_exists('rack_unit_distinct_name')) {
    function rack_unit_distinct_name() {
      $ret = '';
      for($i=1;$i<=45;$i++){
        $ret[] = $i;
      }
      return $ret;
    }
  }

  if(!function_exists('rack_slot_distinct_name')) {
    function rack_slot_distinct_name() {
      $ret = '';
      for($i=1;$i<=14;$i++){
        $ret[] = $i;
      }
      return $ret;
    }
  }

  if(!function_exists('validation_user_group')) {
    function validation_user_group() {
      $ret = array(
              'NETWORK',
              'SYSTEMS',
              'PLATFORMS',
              'ENTERPRISE IT',
              'OTHERS'
        );
      return $ret;
    }
  }

  if(!function_exists('validation_ha_type')) {
    function validation_ha_type() {
      $ret = array(
              'ESXi CLUSTER',
              'VCS-HA',
              'VCS-CFS',
              'VCS-RAC',
              'MSCS',
              'ORACLE RAC',
              'OTHER',
              'NA',
        );
      return $ret;
    }
  }






  if(!function_exists('get_all_portal_links')) {
    function get_all_portal_links() {
      $ret = '';
      $sql = " SELECT *
               FROM portal_link
               ORDER BY LINK_GROUP, LINKNAME
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }



/* CONFIGURATION */

  if(!function_exists('config_detail')) {
    function config_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND c.config_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }

      $sql = " SELECT c.*
               FROM
                idc_config AS c
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


  if(!function_exists('config_unique')) {
    function config_unique($data, $id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $result = '';
      $where = ' 1 ';
      if($id) {
        $where .= " AND config_id != '".mysql_real_escape_string($id)."'";
      }
      if($data) {
        foreach ($data as $key => $value) {
          if(trim($value) && strtoupper($value) != 'NULL' ) {
            $where .= " AND LOWER(".$key.") = LOWER(TRIM('".mysql_real_escape_string($value)."'))  ";
          }
        }

        $sql = "SELECT
                  config_id
                FROM
                  idc_config
                WHERE
                  ".$where."
                LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if($res) {
          if (mysql_num_rows($res)) {
            //var_dump(mysql_fetch_array($res));
            $result = mysql_fetch_array($res);
            $ret = $result['config_id'];
          }
          else {
            $ret = TRUE;
          }
        }

      }
      return $ret;
    }
  }

  if(!function_exists('config_info')) {
    function config_info($type = '', $key = '', $value = '', $ver = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($type) {
        $where  .= " AND UPPER(c.config_type) = UPPER('".mysql_real_escape_string($type)."') ";
      }

      if($key) {
        $where  .= " AND UPPER(c.config_key) = UPPER('".mysql_real_escape_string($key)."') ";
      }

      if($value) {
        $where  .= " AND c.config_value = '".mysql_real_escape_string($value)."' ";
      }

      if($ver) {
        $where  .= " AND UPPER(c.config_version) = UPPER('".mysql_real_escape_string($ver)."') ";
      }

      $sql = " SELECT c.*
               FROM
                idc_config AS c
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

  if(!function_exists('config_value')) {
    function config_value($type = '', $key = '', $ver = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($type) {
        $where  .= " AND UPPER(c.config_type) = UPPER('".mysql_real_escape_string($type)."') ";
      }

      if($key) {
        $where  .= " AND UPPER(c.config_key) = UPPER('".mysql_real_escape_string($key)."') ";
      }

      if($ver) {
        $where  .= " AND UPPER(c.config_version) = UPPER('".mysql_real_escape_string($ver)."') ";
      }

      $sql = " SELECT c.config_value
               FROM
                idc_config AS c
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['config_value'];
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

  if(!function_exists('config_add')) {
    function config_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_config (";
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

  if(!function_exists('config_update')) {
    function config_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_config SET ";
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
        $sql .=" WHERE config_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('config_save')) {
    function config_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = config_update($data, $id);
      }
      else {
        $ret = config_add($data);
      }

      return $ret;
    }
  }

/* STATUS */

  if(!function_exists('status_unique')) {
    function status_unique($name, $par_id= '', $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';
      $where  .= " AND LOWER(s.status_name) = LOWER('".mysql_real_escape_string($name)."') ";

      if($par_id) {
        if($par_id == 'NULL'){
          $where  .= " AND s.status_parent_id IS NULL ";
        }
        else {
          $where  .= " AND s.status_parent_id ='".mysql_real_escape_string($par_id)."' ";
        }
      }

      if($id){
        $where  .= " AND s.status_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT s.status_id
                FROM idc_status AS s
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('status_detail')) {
    function status_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND s.status_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT s.*
               FROM
                idc_status AS s
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


  if(!function_exists('status_children')) {
    function status_children($id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      if($id === NULL) {
        $where  .= " AND s.status_parent_id IS NULL ";
      }
      else {
        $where  .= " AND s.status_parent_id = '".mysql_real_escape_string($id)."' ";
      }

      $sql = " SELECT s.*
               FROM
                idc_status AS s
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

  if(!function_exists('status_request_hold_status')) {
    function status_request_hold_status() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND s.status_status=1 ";
      $where  .= " AND ( UPPER(s.status_name) ='WIP'  OR s.status_parent_id = (SELECT status_id FROM idc_status WHERE UPPER(status_name) ='ON-HOLD' AND (status_parent_id IS NULL OR status_parent_id=0) ) ) ";
      $sql = " SELECT s.*
               FROM
                idc_status AS s
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


  if(!function_exists('status_request_status')) {
    function status_request_status() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND s.status_id IN (2,3,4,5) ";
      $sql = " SELECT s.*
               FROM
                idc_status AS s
               WHERE ".$where."
               ORDER BY
                s.status_position
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

  if(!function_exists('status_add')) {
    function status_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_status (";
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

  if(!function_exists('status_update')) {
    function status_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_status SET ";
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
        $sql .=" WHERE status_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('status_save')) {
    function status_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = status_update($data, $id);
      }
      else {
        $ret = status_add($data);
      }

      return $ret;
    }
  }

/* PHONE */

  if(!function_exists('phone_type_unique')) {
    function phone_type_unique($data) {
      $ret = FALSE;
      $phone_type = '';
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT phone_type_id
                FROM idc_phone_type
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $phone_type = mysql_fetch_array($res);
          $ret = $phone_type['phone_type_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('phone_type_save')) {
    function phone_type_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $phone_type_id  = '';
      $sql = "INSERT INTO idc_phone_type (";
      if($data) {
        // Check if data is unique or not
        $phone_type_id = phone_type_unique($data);
        if($phone_type_id) {
          $ret = $phone_type_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }

  if(!function_exists('phone_unique')) {
    function phone_unique($data) {
      $ret    = FALSE;
      $phone  = '';
      $where  = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT phone_id
                FROM idc_phone
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $phone = mysql_fetch_array($res);
          $ret = $phone['phone_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('phone_save')) {
    function phone_save($data,  $user = '') {
      $msg        = '';
      $ret        = FALSE;
      $phone_id   = '';
      $sql        = "INSERT INTO idc_phone (";
      if($data) {
        // Check if data is unique or not
        $phone_id = phone_unique($data);
        if($phone_id) {
          $ret = $phone_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }

/* MAIL */

  if(!function_exists('mail_address_type_unique')) {
    function mail_address_type_unique($data) {
      $ret = FALSE;
      $mail_address_type = '';
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT mail_address_type_id
                FROM idc_mail_address_type
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $mail_address_type = mysql_fetch_array($res);
          $ret = $mail_address_type['mail_address_type_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('mail_address_type_save')) {
    function mail_address_type_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $mail_address_type_id  = '';
      $sql = "INSERT INTO idc_mail_address_type (";
      if($data) {
        // Check if data is unique or not
        $mail_address_type_id = mail_address_type_unique($data);
        if($mail_address_type_id) {
          $ret = $mail_address_type_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }



  if(!function_exists('mail_address_unique')) {
    function mail_address_unique($data) {
      $ret          = FALSE;
      $mail_address = '';
      $where        = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT mail_address_id
                FROM idc_mail_address
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $mail_address = mysql_fetch_array($res);
          $ret = $mail_address['mail_address_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('mail_address_save')) {
    function mail_address_save($data,  $user = '') {
      $msg  = '';
      $ret  = FALSE;
      $mail_address_id  = '';
      $sql  = "INSERT INTO idc_mail_address (";
      if($data) {
        // Check if data is unique or not
        $mail_address_id = mail_address_unique($data);
        if($mail_address_id) {
          $ret = $mail_address_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


/* CONTACT */

  if(!function_exists('contact_type_unique')) {
    function contact_type_unique($data) {
      $ret = FALSE;
      $contact_type = '';
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT contact_type_id
                FROM idc_contact_type
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $contact_type = mysql_fetch_array($res);
          $ret = $contact_type['contact_type_id'];
        }
      }
      return $ret;
    }
  }


  if(!function_exists('contact_type_save')) {
    function contact_type_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $contact_type_id  = '';
      $sql = "INSERT INTO idc_contact_type (";
      if($data) {
        // Check if data is unique or not
        $contact_type_id = contact_type_unique($data);
        if($contact_type_id) {
          $ret = $contact_type_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }

  if(!function_exists('contact_unique')) {
    function contact_unique($data) {
      $ret      = FALSE;
      $contact  = '';
      $where    = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT contact_id
                FROM idc_contact
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $contact = mysql_fetch_array($res);
          $ret = $contact['contact_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('contact_save')) {
    function contact_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $contact_id  = '';
      $sql = "INSERT INTO idc_contact (";
      if($data) {
        // Check if data is unique or not
        $contact_id = contact_unique($data);
        if($contact_id) {
          $ret = $contact_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


if(!function_exists('contact_phone_unique')) {
    function contact_phone_unique($data) {
      $ret            = FALSE;
      $contact_phone  = '';
      $where    = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT contact_phone_id
                FROM idc_contact_phone
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $contact_phone = mysql_fetch_array($res);
          $ret = $contact_phone['contact_phone_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('contact_phone_save')) {
    function contact_phone_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $contact_phone_id  = '';
      $sql = "INSERT INTO idc_contact_phone (";
      if($data) {
        // Check if data is unique or not
        $contact_phone_id = contact_phone_unique($data);
        if($contact_phone_id) {
          $ret = $contact_phone_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


if(!function_exists('contact_mail_address_unique')) {
    function contact_mail_address_unique($data) {
      $ret            = FALSE;
      $contact_mail_address  = '';
      $where    = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT contact_mail_address_id
                FROM idc_contact_mail_address
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $contact_mail_address = mysql_fetch_array($res);
          $ret = $contact_mail_address['contact_mail_address_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('contact_mail_address_save')) {
    function contact_mail_address_save($data,  $user = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg = '';
      $ret = FALSE;
      $contact_mail_address_id  = '';
      $sql = "INSERT INTO idc_contact_mail_address (";
      if($data) {
        // Check if data is unique or not
        $contact_mail_address_id = contact_mail_address_unique($data);
        if($contact_mail_address_id) {
          $ret = $contact_mail_address_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


  if(!function_exists('save_contact_detail')) {
    function save_contact_detail($data,  $user = '') {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $data_contact   = '';
      $contact_mails  = '';
      $contact_phones = '';
      // First Save Contact Name
      foreach ($data as $contact_key => $contact_value) {
        switch ($contact_key) {
          case 'contact_mails':
            //echo __FUNCTION__.' contact_mails <br />';
            //var_dump($contact_value);
            foreach ($contact_value as $mail_key => $mail_value){
              $contact_mails[] =  mail_address_save($mail_value, $user);
            }
            break;

          case 'contact_phones':
            //echo __FUNCTION__.' contact_phones <br />';
            //var_dump($contact_value);
            foreach ($contact_value as $phone_key => $phone_value){
              $contact_phones[] =  phone_save($phone_value, $user);
            }
            break;
          default:
            $data_contact[$contact_key] = $contact_value;
            break;
        }
      }

      $ret = contact_save($data_contact, $user);

      // Save Contact Mails
      if($contact_mails) {
        foreach ($contact_mails as $contact_mail_key => $contact_mail){
          $data_contact_mail = array(
                            'contact_id' => $ret,
                            'mail_address_id' => $contact_mail,
                          );
          contact_mail_address_save($data_contact_mail, $user);
        }
      }

      // Save Contact Phones
      if($contact_phones) {
        foreach ($contact_phones as $contact_phone_key => $contact_phone){
          $data_contact_phone = array(
                            'contact_id' => $ret,
                            'phone_id' => $contact_phone,
                          );
          contact_phone_save($data_contact_phone, $user);
        }
      }
      return $ret;
    }
  }


/* DOCUMENT */

  if(!function_exists('doc_type_unique')) {
    function doc_type_unique($data) {
      $ret = FALSE;
      $doc_type = '';
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT doc_type_id
                FROM idc_doc_type
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $doc_type = mysql_fetch_array($res);
          $ret = $doc_type['doc_type_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('doc_type_save')) {
    function doc_type_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $doc_type_id  = '';
      $sql = "INSERT INTO idc_doc_type (";
      if($data) {
        // Check if data is unique or not
        $doc_type_id = doc_type_unique($data);
        if($doc_type_id) {
          $ret = $doc_type_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


  if(!function_exists('doc_unique')) {
    function doc_unique($data) {
      $ret    = FALSE;
      $doc    = '';
      $where  = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT doc_id
                FROM idc_doc
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $doc_type = mysql_fetch_array($res);
          $ret = $doc_type['doc_type_id'];
        }
      }
      return $ret;
    }
  }


  if(!function_exists('doc_save')) {
    function doc_save($data) {
      $msg      = '';
      $ret      = FALSE;
      $doc_id   = '';
      $sql      = "INSERT INTO idc_doc (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('doc_save_unique')) {
    function doc_save_unique($data,  $user = '') {
      $msg      = '';
      $ret      = FALSE;
      $doc_id   = '';
      $sql      = "INSERT INTO idc_doc (";
      if($data) {
        // Check if data is unique or not
        $doc_id = doc_unique($data);
        if($doc_id) {
          $ret = $doc_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }

/* DOCUMENT DOC TYPE*/
  if(!function_exists('doc_doc_type_unique')) {
    function doc_doc_type_unique($data) {
      $ret    = FALSE;
      $doc    = '';
      $where  = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT doc_id
                FROM idc_doc_doc_type
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $doc_type = mysql_fetch_array($res);
          $ret = $doc_type['doc_type_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('doc_doc_type_detail')) {
    function doc_doc_type_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ddt.doc_doc_type_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT ddt.*
               FROM
                idc_doc_doc_type AS ddt
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


  if(!function_exists('doc_doc_type_add')) {
    function doc_doc_type_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_doc_doc_type (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('doc_doc_type_update')) {
    function doc_doc_type_update($data, $id, $search_field = 'doc_doc_type_id') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_doc_doc_type SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE ".$search_field."='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('doc_doc_type_save')) {
    function doc_doc_type_save($data, $id = '', $search_field = 'doc_doc_type_id') {
     //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;

      if($id){
        $ret = doc_doc_type_update($data, $id, $search_field);
      }
      else {
        $id   = doc_doc_type_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }


  // pei_doc_save
  if(!function_exists('pei_doc_save')) {
    function pei_doc_save($data) {
     //echo __FUNCTION__.'()<br />';
      $msg      = '';
      $ret      = FALSE;

      if($data) {
        $data_doc       = '';
        $data_doc_doc_type  = '';
        // Seperate data
        foreach ($data as $key => $value) {
          switch($key){
            case 'doc_type_id':
              $data_doc_doc_type[$key] = $value;
              break;
            case 'created_by':
            case 'created_at':
            case 'updated_by':
            case 'updated_at':
              $data_doc_doc_type[$key] = $value;
              $data_doc[$key] = $value;
              break;
            default:
              $data_doc[$key] = $value;
          }
        }
        // Save doc
        $ret = doc_save($data_doc);
        if($ret){
          $data_doc_doc_type['doc_id'] = $ret;

          // Sace doc doc type
          doc_doc_type_save($data_doc_doc_type);
        }
      }
      return $ret;
    }
  }

/* ROW-RACK */

  if(!function_exists('row_rack_distinct_name')) {
    function row_rack_distinct_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                 DISTINCT TRIM(UPPER(rr.rr_name)) AS rr_name
                FROM
                  idc_row_rack AS rr
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['rr_name'];
        }
      }

      return $ret;
    }
  }

/* FIND */

  if(!function_exists('find_server_make_id')) {
    function find_server_make_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(vendor_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT vendor_id ";
      $sql .= "FROM idc_vendor ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['vendor_id'];
        }
        else {
          $ret = '1';
        }
      }
      return $ret;
    }

  }

/* Material Code */

  if(!function_exists('material_code_unique')) {
    function material_code_unique($code, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';
      $where  .= " AND LOWER(m.mat_code) = LOWER('".mysql_real_escape_string($code)."') ";
      if($id){
        $where  .= " AND m.mat_code_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT m.mat_code_id
                FROM idc_material_code AS m
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('material_code_detail')) {
    function material_code_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND m.mat_code_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT m.*
               FROM
                idc_material_code AS m
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


  if(!function_exists('material_code_add')) {
    function material_code_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_material_code (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('material_code_update')) {
    function material_code_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_material_code SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE mat_code_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('material_code_save')) {
    function material_code_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = material_code_update($data, $id);
      }
      else {
        $ret = material_code_add($data);
      }

      return $ret;
    }
  }


/* ACTIVITY TYPE */

  if(!function_exists('activity_type_unique')) {
    function activity_type_unique($name, $par_id = '', $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';
      $where  .= " AND LOWER(a.act_type_name) = LOWER('".mysql_real_escape_string($name)."') ";

      if($par_id) {
        if($par_id == 'NULL'){
          $where  .= " AND a.act_type_parent_id IS NULL ";
        }
        else {
          $where  .= " AND a.act_type_parent_id ='".mysql_real_escape_string($par_id)."' ";
        }
      }

      if($id){
        $where  .= " AND a.act_type_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT a.act_type_id
                FROM idc_activity_type AS a
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('activity_type_detail')) {
    function activity_type_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND a.act_type_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT a.*
               FROM
                idc_activity_type AS a
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

  if(!function_exists('activity_type_children')) {
    function activity_type_children($id, $status = 1) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $where  .= " AND a.act_type_parent_id = '".mysql_real_escape_string($id)."' ";
      if($status){
        $where  .= " AND a.act_type_status = '".mysql_real_escape_string($status)."' ";
      }
      $sql = " SELECT a.*
               FROM
                idc_activity_type AS a
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


  if(!function_exists('activity_type_children_children')) {
    function activity_type_children_children($id, $status = 1) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $children = activity_type_children($id, $status);
      if($children){
        foreach ($children as $key => $value) {
          $parent = $value;
          // Find children children
          $child = activity_type_children($parent['act_type_id']);
          if($child){
            $parent['children'] = $child;
          }

          $ret[] = $parent;
        }
      }
      return $ret;
    }
  }
  if(!function_exists('activity_type_search')) {
    function activity_type_search($name, $par_id = '', $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';
      $where  .= " AND LOWER(a.act_type_name) = LOWER('".mysql_real_escape_string($name)."') ";

      if($par_id) {
        if($par_id == 'NULL'){
          $where  .= " AND a.act_type_parent_id IS NULL ";
        }
        else {
          $where  .= " AND a.act_type_parent_id ='".mysql_real_escape_string($par_id)."' ";
        }
      }

      if($id){
        $where  .= " AND a.act_type_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT a.*
                FROM idc_activity_type AS a
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          while ( $row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }

      return $ret;
    }
  }
  if(!function_exists('activity_type_add')) {
    function activity_type_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_activity_type (";
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

  if(!function_exists('activity_type_update')) {
    function activity_type_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_activity_type SET ";
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
        $sql .=" WHERE act_type_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('activity_type_save')) {
    function activity_type_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = activity_type_update($data, $id);
      }
      else {
        $ret = activity_type_add($data);
      }

      return $ret;
    }
  }


/* PORTAL LINK */

  if(!function_exists('portal_link_type_unique')) {
    function portal_link_type_unique($name, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $where  .= " AND LOWER(plt.portal_link_type_name) = LOWER('".mysql_real_escape_string(trim($name))."') ";
      if($id){
        $where  .= " AND plt.portal_link_type_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "  SELECT plt.portal_link_type_id
                FROM idc_portal_link_type AS plt
                WHERE
                  ".$where."
                LIMIT 0, 1
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $portal_link_type = mysql_fetch_array($res);
          $ret    = $portal_link_type['portal_link_type_id'];
        }
      }
      return $ret;
    }
  }


  if(!function_exists('portal_link_type_detail')) {
    function portal_link_type_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND plt.portal_link_type_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT plt.*
               FROM
                idc_portal_link_type AS plt
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


  if(!function_exists('portal_link_type_add')) {
    function portal_link_type_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_portal_link_type (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('portal_link_type_update')) {
    function portal_link_type_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_portal_link_type SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE portal_link_type_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('portal_link_type_save')) {
    function portal_link_type_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $user = '';
      // Get created by user information form input data
      if(isset($data['created_by'])){
        $user = $data['created_by'];
      }

      if($id){
        $ret = portal_link_type_update($data, $id);
      }
      else {
        $id   = portal_link_type_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }

  if(!function_exists('portal_link_parents')) {
    function portal_link_parents() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND pl.portal_link_parent_id IS NULL ";

      $sql = " SELECT pl.*
               FROM
                idc_portal_link AS pl
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

  if(!function_exists('portal_link_detail')) {
    function portal_link_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND pl.portal_link_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT pl.*
               FROM
                idc_portal_link AS pl
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($id){
          $ret = mysql_fetch_array($res);
          // get portal link roles
          if($ret){
            $ret['roles'] = fetch_portal_link_role($ret['portal_link_id']);
          }
        }
        else{
          while ( $row = mysql_fetch_array($res)) {
            if($row){
              $row['roles'] = fetch_portal_link_role($row['portal_link_id']);
            }
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


  if(!function_exists('portal_link_add')) {
    function portal_link_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_portal_link (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('portal_link_update')) {
    function portal_link_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_portal_link SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE portal_link_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('portal_link_save')) {
    function portal_link_save($data, $id = '', $roles = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $user = '';
      // Get created by user information form input data
      if(isset($data['created_by'])){
        $user = $data['created_by'];
      }

      if($id){
        $ret = portal_link_update($data, $id);
      }
      else {
        $id   = portal_link_add($data);
        $ret  = $id;
      }

      // Save User Roles
      // Fetch Previous Portal Link Role's and compare with current values
      $previous_roles = fetch_portal_link_role($id);
      $data_previous_roles = array();
      if($previous_roles){
        foreach ($previous_roles as $key => $value) {
          $data_previous_roles[] =  array('role_id' => $value['role_id']);
        }
      }
      // To remove extra role
      $remove_role = pei_array_diff_by_key($data_previous_roles, $roles, 'role_id');
      $data_remove_role = '';
      if($remove_role) {
        foreach ($remove_role as $key => $value) {
          if(isset($value['role_id'])){
            $data_remove_role[] = $value['role_id'];
          }
        }
        portal_link_role_delete($id, $data_remove_role);
      }

      // To add new addition role
      $add_role = pei_array_diff_by_key($roles, $data_previous_roles, 'role_id');
      $data_add_role = '';
      if($add_role) {
        foreach ($add_role as $key => $value) {
          if(isset($value['role_id'])){
            $data_add_role[] = $value['role_id'];
          }
        }
        portal_link_role_add($id, $data_add_role, $user);
      }
      return $ret;
    }
  }


  if(!function_exists('fetch_portal_link_role')) {
    function fetch_portal_link_role($id) {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND pl.portal_link_id = '".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT DISTINCT pl.role_id
              FROM
                idc_role_portal_link AS pl
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
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

  if(!function_exists('role_portal_link_add')) {
    function role_portal_link_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_role_portal_link (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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


  if(!function_exists('role_portal_link_update')) {
    function role_portal_link_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_role_portal_link SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE role_portal_link_id='".mysql_real_escape_string($id)."' ";
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


  if(!function_exists('role_portal_link_save')) {
    function role_portal_link_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      if($id) {
        $ret = role_portal_link_update($data, $id);
      }
      else {
        $id   = role_portal_link_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }


  if(!function_exists('portal_link_role_delete')) {
    function portal_link_role_delete($id, $roles = array()) {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = FALSE;
      if($id) {
        $sql = " DELETE FROM idc_role_portal_link WHERE ";
        $sql .=" portal_link_id='".mysql_real_escape_string($id)."'";
        if($roles) {
          $sql .=" AND role_id IN (";
          foreach ($roles as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql = rtrim($sql, ',');
          $sql .=") ";
        }
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('portal_link_role_add')) {
    function portal_link_role_add($id, $roles, $user = '') {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = FALSE;
      if($id && $roles) {
        $sql = "INSERT INTO idc_role_portal_link (portal_link_id, role_id, created_by,
                          created_at) VALUE ";
        foreach ($roles as $key => $value) {
          $sql .="(";
          $sql .="'".mysql_real_escape_string($id)."',";
          $sql .="'".mysql_real_escape_string($value)."',";
          $sql .="'".mysql_real_escape_string($user)."',";
          $sql .="NOW()";
          $sql .="),";
        }

        $sql = rtrim($sql, ',');
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }



/* ENVIRONMENT */

  if(!function_exists('environment_detail')) {
    function environment_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND e.env_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT e.*
               FROM
                idc_environment AS e
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

  if(!function_exists('environment_distinct_name')) {
    function environment_distinct_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                 DISTINCT TRIM(UPPER(e.env_name)) AS env_name
                FROM
                  idc_environment AS e
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['env_name'];
        }
      }

      return $ret;
    }
  }


  if(!function_exists('get_all_env')) {
    function get_all_env() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_environment
               ORDER BY env_position
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('get_env_id')) {
    function get_env_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND LOWER(env_name_abbrev) =LOWER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT env_id ";
      $sql .= "FROM idc_environment ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['env_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_env_id')) {
    function find_env_id($name) {
      $ret = '';
      if($name) {
        switch (trim(strtolower($name))) {
          case 'dev':
            $ret = get_env_id('dev');
            break;
          case 'st':
            $ret = get_env_id('st');
            break;
          case 'sit':
            $ret = get_env_id('sit');
            break;
          case 'qa':
            $ret = get_env_id('qa');
            break;
          case 'rep':
            $ret = get_env_id('rep');
            break;
          case 'bf':
            $ret = get_env_id('bf');
            break;
          case 'preprod':
            $ret = get_env_id('preprod');
            break;
          case 'prod':
            $ret = get_env_id('prod');
            break;
          case 'dr':
            $ret = get_env_id('dr');
            break;
          case 'other':
          case 'others':
            $ret = get_env_id('others');
            break;
          default:
            $ret = '';
            break;
        }
      }
      return $ret;
    }

  }
  if(!function_exists('environment_add')) {
    function environment_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_environment (";
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

  if(!function_exists('environment_update')) {
    function environment_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_environment SET ";
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
        $sql .=" WHERE env_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('environment_save')) {
    function environment_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = environment_update($data, $id);
      }
      else {
        $ret = environment_add($data);
      }

      return $ret;
    }
  }


/* LOCATION */

  if(!function_exists('location_detail')) {
    function location_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND l.loc_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT l.*
               FROM
                idc_location AS  l
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

  if(!function_exists('location_by_key_value')) {
    function location_by_key_value($key, $val, $order_by = '',  $order = 'ASC') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $sql_order  = ' ';
      $limit  = '';
      $where  .= " AND l.".mysql_real_escape_string($key)." = '".mysql_real_escape_string($val)."' ";

      if($order_by == '') {
        $order_by = $key;
      }
      $sql_order  .= " ".mysql_real_escape_string($order_by)." ".mysql_real_escape_string($order);
      $sql = " SELECT l.*
               FROM
                idc_location AS  l
               WHERE ".$where."
               ORDER BY ".$sql_order."
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

  if(!function_exists('location_distinct_name')) {
    function location_distinct_name() {
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
          $ret[] = $row['loc_name'];
        }
      }

      return $ret;
    }
  }

  if(!function_exists('get_all_location')){
    function get_all_location() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_location
               ORDER BY loc_position
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_location_name_by_id')){
    function get_location_name_by_id($id) {
      $ret = '';
      $sql = " SELECT *
               FROM idc_location
               WHERE loc_id='".mysql_real_escape_string($id)."'
               LIMIT 0, 1
              ";
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['loc_name'];
      }
      return $ret;
    }
  }

  if(!function_exists('get_all_loc')) {
    function get_all_loc() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_location
               WHERE loc_position IS NOT NULL
               ORDER BY loc_position
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_loc_id')) {
    function find_loc_id($name) {
      $ret    = '6';
      $where  = ' 1 ';
      $where .= " AND UPPER(loc_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT loc_id ";
      $sql .= "FROM idc_location ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row){
          $ret = $row['loc_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('location_unique')) {
    function location_unique($data, $id= '') {
      //echo __FUNCTION__.'()<br />';
      $ret = FALSE;
      $result = '';
      $where = ' 1 ';

      if($id) {
        $where  .= " AND loc_id !='".mysql_real_escape_string($id)."' ";
      }

      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
      }
      $sql = "SELECT
                loc_id
              FROM
                idc_location
              WHERE
                ".$where."
              LIMIT 0,1";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $result = mysql_fetch_array($res);
          $ret = $result['loc_id'];
        }
        else {
          $ret = TRUE;
        }
      }


      return $ret;
    }
  }
  if(!function_exists('location_add')) {
    function location_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_location (";
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

  if(!function_exists('location_update')) {
    function location_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_location SET ";
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
        $sql .=" WHERE loc_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('location_save')) {
    function location_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = location_update($data, $id);
      }
      else {
        $ret = location_add($data);
      }

      return $ret;
    }
  }

/* SERVER HALL */

  if(!function_exists('server_hall_detail')) {
    function server_hall_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND sh.sh_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT sh.*
               FROM
                idc_server_hall AS  sh
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

  if(!function_exists('server_hall_distinct_name')) {
    function server_hall_distinct_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                 DISTINCT TRIM(UPPER(sh.sh_name)) AS sh_name
                FROM
                  idc_server_hall AS sh
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['sh_name'];
        }
      }

      return $ret;
    }
  }


  if(!function_exists('find_server_hall_id')) {
    function find_server_hall_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(sh_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT sh_id ";
      $sql .= "FROM idc_server_hall ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['sh_id'];
        }
      }
      return $ret;
    }
  }


  if(!function_exists('get_all_server_hall')){
    function get_all_server_hall() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_server_hall
               ORDER BY sh_id
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_server_hall_name_by_id')){
    function get_server_hall_name_by_id($id) {
      $ret = '';
      $sql = " SELECT *
               FROM idc_server_hall
               WHERE sh_id='".mysql_real_escape_string($id)."'
               LIMIT 0, 1
              ";
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['sh_name'];
      }
      return $ret;
    }
  }

  if(!function_exists('server_hall_add')) {
    function server_hall_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_server_hall (";
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

  if(!function_exists('server_hall_update')) {
    function server_hall_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_server_hall SET ";
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
        $sql .=" WHERE sh_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('server_hall_save')) {
    function server_hall_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = server_hall_update($data, $id);
      }
      else {
        $ret = server_hall_add($data);
      }

      return $ret;
    }
  }


/* OS */

  if(!function_exists('os_detail')) {
    function os_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND os.os_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT os.*
               FROM
                idc_os AS  os
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


  if(!function_exists('os_distinct_name')) {
    function os_distinct_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                 DISTINCT TRIM(UPPER(os.os_name)) AS os_name
                FROM
                  idc_os AS os
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['os_name'];
        }
      }

      return $ret;
    }
  }

  if(!function_exists('os_version_distinct_name')) {
    function os_version_distinct_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                 DISTINCT TRIM(UPPER(osv.os_ver_name)) AS os_ver_name
                FROM
                  idc_os_version AS osv
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['os_ver_name'];
        }
      }


      return $ret;
    }
  }

  if(!function_exists('os_add')) {
    function os_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_os (";
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

  if(!function_exists('os_update')) {
    function os_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_os SET ";
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
        $sql .=" WHERE sh_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('os_save')) {
    function os_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = os_update($data, $id);
      }
      else {
        $ret = os_add($data);
      }

      return $ret;
    }
  }






/* USER */

  if(!function_exists('user_detail')) {
    function user_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND u.user_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT u.*
               FROM
                idc_user AS u
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($id){
          $ret = mysql_fetch_array($res);
          // get portal link roles
          if($ret){
            $ret['roles'] = fetch_user_role_by_id($ret['user_id']);
          }
        }
        else{
          while ( $row = mysql_fetch_array($res)) {
            if($row){
              $row['roles'] = fetch_user_role_by_id($row['user_id']);
            }
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


  if(!function_exists('user_add')) {
    function user_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_user (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('user_update')) {
    function user_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_user SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE user_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('user_save')) {
    function user_save($data, $id = '', $roles = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $user = '';
      // Get created by user information form input data
      if(isset($data['created_by'])){
        $user = $data['created_by'];
      }

      if($id){
        $ret = user_update($data, $id);
      }
      else {
        $id   = user_add($data);
        $ret  = $id;
      }

      // Save User Roles
      // Fetch Previous User Role's and compare with current values
      $previous_roles = fetch_user_role_by_id($id);
      $data_previous_roles = array();
      if($previous_roles){
        foreach ($previous_roles as $key => $value) {
          $data_previous_roles[] =  array('role_id' => $value['role_id']);
        }
      }
      // To remove extra role
      $remove_role = pei_array_diff_by_key($data_previous_roles, $roles, 'role_id');
      $data_remove_role = '';
      if($remove_role) {
        foreach ($remove_role as $key => $value) {
          if(isset($value['role_id'])){
            $data_remove_role[] = $value['role_id'];
          }
        }
        user_role_delete($id, $data_remove_role);
      }

      // To add new addition role
      $add_role = pei_array_diff_by_key($roles, $data_previous_roles, 'role_id');
      $data_add_role = '';
      if($add_role) {
        foreach ($add_role as $key => $value) {
          if(isset($value['role_id'])){
            $data_add_role[] = $value['role_id'];
          }
        }
        user_role_add($id, $data_add_role, $user);
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_user_role_by_id')) {
    function fetch_user_role_by_id($id) {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND u.user_id = '".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT DISTINCT u.role_id
              FROM
                idc_user_role AS u
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
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

  if(!function_exists('user_role_delete')) {
    function user_role_delete($id, $roles = array()) {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = FALSE;
      if($id) {
        $sql = " DELETE FROM idc_user_role WHERE ";
        $sql .=" user_id='".mysql_real_escape_string($id)."'";
        if($roles) {
          $sql .=" AND role_id IN (";
          foreach ($roles as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql = rtrim($sql, ',');
          $sql .=") ";
        }
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('user_role_add')) {
    function user_role_add($id, $roles, $user = '') {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = FALSE;
      if($id && $roles) {
        $sql = "INSERT INTO idc_user_role (user_id, role_id, created_by,
                          created_at) VALUE ";
        foreach ($roles as $key => $value) {
          $sql .="(";
          $sql .="'".mysql_real_escape_string($id)."',";
          $sql .="'".mysql_real_escape_string($value)."',";
          $sql .="'".mysql_real_escape_string($user)."',";
          $sql .="NOW()";
          $sql .="),";
        }

        $sql = rtrim($sql, ',');
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }











  if(!function_exists('update_user_logout')) {
    function update_user_logout($user_id) {
      $ret = FALSE;
      $sql = " UPDATE idc_user SET last_login=NOW() ";
      $sql .=" WHERE user_id='".mysql_real_escape_string($user_id)."';";
      $res = mysql_query($sql);
      if($res){
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('get_all_user')) {
    function get_all_user() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_user
               ORDER BY user_name
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_all_user_by_role')) {
    function get_all_user_by_role($role_id = '', $distinct = '') {
      $ret = '';
      $where = ' 1  ';
      if($role_id){
        $where .= " AND r.role_id ='".mysql_real_escape_string($role_id)."' ";
      }
      $sql = "  SELECT
                  ".$distinct." u.*
                FROM
                  idc_role_permission AS rp
                  LEFT JOIN
                  idc_role AS r ON rp.role_id = r.role_id
                  LEFT JOIN
                  idc_permission AS p ON rp.perm_id = p.perm_id
                  LEFT JOIN
                  idc_user_role AS ur ON ur.role_id = rp.role_id
                  LEFT JOIN
                  idc_user AS u ON ur.user_id = u.user_id
                WHERE
                  ".$where."
                ORDER BY
                  u.user_login, r.role_id, p.perm_id
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }
  if(!function_exists('get_all_spoc_user')) {
    function get_all_spoc_user() {
      // PEI role user only
      $ret = '';
      $sql = "  SELECT
                  DISTINCT u.*
                FROM
                  idc_role_permission AS rp
                  LEFT JOIN
                  idc_role AS r ON rp.role_id = r.role_id
                  LEFT JOIN
                  idc_permission AS p ON rp.perm_id = p.perm_id
                  LEFT JOIN
                  idc_user_role AS ur ON ur.role_id = rp.role_id
                  LEFT JOIN
                  idc_user AS u ON ur.user_id = u.user_id
                WHERE
                  p.perm_id IN ('3')
                ORDER BY
                  u.user_login, r.role_id, p.perm_id
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('valid_user_id')){
    function valid_user_id($id) {
      $ret = FALSE;
      $sql = "SELECT user_id
              FROM idc_user
              WHERE user_id = '".mysql_real_escape_string($id)."'";
      $res = mysql_query($sql);
      if (mysql_num_rows($res)) {
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_user')) {
    function fetch_user() {
      $msg        = '';
      $idc_user   = '';
      $sql_user   = " SELECT *
                      FROM idc_user
                      ORDER BY user_name ";

      $res_user = mysql_query($sql_user);
      if (!$res_user) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql_user;
        die($msg);
      }
      else {
        $i = 0;
        while ($row_user = mysql_fetch_array($res_user)) {
           $idc_user[$i]['user_id']       = $row_user['user_id'];
           $idc_user[$i]['user_name']     = $row_user['user_name'];
           $idc_user[$i]['user_login']    = $row_user['user_login'];
           $i++;
        }
      }
      return $idc_user;
    }
  }

  if(!function_exists('fetch_role')) {
    function fetch_role() {
      $msg = '';
      $ret = '';
      $sql = "SELECT *
              FROM idc_role
              ORDER BY role_position
              ";

      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }



  if(!function_exists('fetch_user_role')) {
    function fetch_user_role($user_login) {
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($user_login) {
        $sql_where  .= " AND LOWER(u.user_login) = '".mysql_real_escape_string(strtolower($user_login))."' ";
      }
      $sql = "SELECT DISTINCT r.role_id, ur.*, u.user_name, r.role_name
              FROM
                idc_user_role AS ur
                LEFT JOIN
                idc_user AS u ON ur.user_id = u.user_id
                LEFT JOIN
                idc_role AS r ON ur.role_id = r.role_id
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
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

  if(!function_exists('fetch_user_permission')) {
    function fetch_user_permission($user_login) {
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($user_login) {
        $sql_where  .= " AND LOWER(u.user_login) = '".mysql_real_escape_string(strtolower($user_login))."' ";
      }
      $sql = "SELECT
                DISTINCT p.perm_id, rp.*, p.perm_key, p.perm_name,
                u.user_id
              FROM
                idc_role_permission AS rp
                LEFT JOIN
                idc_user_role AS ur ON rp.role_id = ur.role_id
                LEFT JOIN
                idc_permission AS p ON rp.perm_id =p.perm_id
                LEFT JOIN
                idc_user AS u ON ur.user_id = u.user_id
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
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


  if(!function_exists('fetch_user_access_permission')) {
    function fetch_user_access_permission($user_login) {
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($user_login) {
        $sql_where  .= " AND LOWER(u.user_login) = '".mysql_real_escape_string(strtolower($user_login))."' ";
      }
      $sql = "SELECT
                DISTINCT p.perm_key
              FROM
                idc_role_permission AS rp
                LEFT JOIN
                idc_user_role AS ur ON rp.role_id = ur.role_id
                LEFT JOIN
                idc_permission AS p ON rp.perm_id =p.perm_id
                LEFT JOIN
                idc_user AS u ON ur.user_id = u.user_id
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['perm_key'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_user_detail_from_user_id')) {
    function get_user_detail_from_user_id($user_id) {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($user_id) {
        $sql_where  .= " AND u.user_id = '".mysql_real_escape_string($user_id)."' ";
      }
      $sql = " SELECT u.*
               FROM
                idc_user AS u
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if(mysql_num_rows($res) == 1) {
          $ret = mysql_fetch_array($res);
          // get user roles
          if($ret){
            $ret['roles'] = fetch_user_role($ret['user_login']);
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

  if(!function_exists('get_user_detail_from_user_login')) {
    function get_user_detail_from_user_login($user_login, $user_id = '') {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($user_id) {
        $sql_where  .= " AND u.user_id != '".mysql_real_escape_string($user_id)."' ";
      }
      if($user_login) {
        $sql_where  .= " AND LOWER(u.user_login) = '".mysql_real_escape_string(strtolower($user_login))."' ";
      }
      $sql = " SELECT u.*
               FROM
                idc_user AS u
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if(mysql_num_rows($res) == 1) {
          $ret = mysql_fetch_array($res);
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

   //Store Funtion
   if(!function_exists('get_user_loc_detail_from_store_user_loc')) {
    function get_user_loc_detail_from_store_user_loc($user_login) {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($user_login) {
        $sql_where  .= " AND LOWER(u.username) = '".mysql_real_escape_string(strtolower($user_login))."' ";
      }
      $sql = " SELECT u.*
               FROM
               store_user_loc AS u
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if(mysql_num_rows($res) == 1) {
          $ret = mysql_fetch_array($res);
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

  if(!function_exists('get_user_detail_from_user_email')) {
    function get_user_detail_from_user_email($user_email, $user_id = '') {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($user_id) {
        $sql_where  .= " AND u.user_id != '".mysql_real_escape_string($user_id)."' ";
      }
      if($user_email) {
        $sql_where  .= " AND LOWER(u.user_email) = '".mysql_real_escape_string(strtolower($user_email))."' ";
      }
      $sql = " SELECT u.*
               FROM
                idc_user AS u
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if(mysql_num_rows($res) == 1) {
          $ret = mysql_fetch_array($res);
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


  if(!function_exists('save_users')) {
    function save_users($name, $email, $login, $roles, $phone, $user_id = '', $user = '') {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = FALSE;

      if($user_id) {
        $sql = "UPDATE idc_user SET ";
        $sql .=" user_name = '".mysql_real_escape_string($name)."', ";
        $sql .=" user_email = '".mysql_real_escape_string($email)."', ";
        $sql .=" user_login = '".mysql_real_escape_string($login)."', ";
        $sql .=" user_mobile = '".mysql_real_escape_string($phone)."' ";
        $sql .=" WHERE ";
        $sql .=" user_id = '".mysql_real_escape_string($user_id)."'";
        //echo 'SQL :'.$sql.'<br />';

        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      else {
        if($name && $email && $login && $roles) {
          $sql = "INSERT INTO idc_user (user_name, user_email, user_login, user_mobile ) VALUE ";
          $sql .="(";
          $sql .="'".mysql_real_escape_string($name)."', ";
          $sql .="'".mysql_real_escape_string($email)."', ";
          $sql .="'".mysql_real_escape_string($login)."', ";
          $sql .="'".mysql_real_escape_string($phone)."'";
          $sql .=");";

          $res = mysql_query($sql);
          if(!$res) {
            $msg .= 'Invalid query: ' . mysql_error() . "\n";
            $msg .= 'Whole query: ' . $sql;
            die($msg);
          }
          else {
            $user_id = mysql_insert_id();
            $ret = TRUE;
          }
        }
      }

      // Save User Roles
      if($user_id && $roles){
        // Fetch Previous save user role and compare with current values
        $previous_user_role = get_user_role($user_id);
        if(!is_array($previous_user_role)){
          $previous_user_role = array();
        }

        // To remove extra role
        $remove_role = array_diff($previous_user_role, $roles);
        if($remove_role){
          delete_user_role($user_id, $remove_role);
        }

        // To add new addition role
        $add_role = array_diff($roles, $previous_user_role);
        if($add_role) {
          save_user_role($user_id, $add_role, $user);
        }
      }

      return $ret;
    }
  }



  if(!function_exists('get_user_role')) {
    function get_user_role($user_id = '') {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($user_id) {
        $sql_where  .= " AND ur.user_id = '".mysql_real_escape_string($user_id)."' ";
      }
      $sql = " SELECT ur.*
               FROM
                idc_user_role AS ur
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['role_id'];
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



  if(!function_exists('save_user_role')) {
    function save_user_role($user_id, $roles, $user = '') {
      $msg = '';
      $ret = FALSE;
      if($user_id && $roles) {
        $sql = "INSERT INTO idc_user_role (user_id, role_id, created_by,
                          created_at) VALUE ";
        foreach ($roles as $key => $value) {
          $sql .="(";
          $sql .="'".mysql_real_escape_string($user_id)."',";
          $sql .="'".mysql_real_escape_string($value)."',";
          $sql .="'".mysql_real_escape_string($user)."',";
          $sql .="NOW()";
          $sql .="),";
        }

        $sql = rtrim($sql, ',');
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('delete_user_role')) {
    function delete_user_role($user_id, $roles = array()) {
      $msg = '';
      $ret = FALSE;
      if($user_id) {
        $sql = " DELETE FROM idc_user_role WHERE ";
        $sql .=" user_id='".mysql_real_escape_string($user_id)."'";
        if($roles) {
          $sql .=" AND role_id IN (";
          foreach ($roles as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql = rtrim($sql, ',');
          $sql .=") ";
        }
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_user_portal_link')) {
    function fetch_user_portal_link($user_login, $parent_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($parent_id){
        $sql_where    .= " AND pl.portal_link_parent_id='".mysql_real_escape_string($parent_id)."' ";
      }
      else {
       $sql_where    .= " AND (pl.portal_link_parent_id='".mysql_real_escape_string($parent_id)."' OR pl.portal_link_parent_id IS NULL )";
      }

      $sql_where    .= " AND pl.portal_link_status='1' ";
      if($user_login) {
        $sql_where  .= " AND LOWER(u.user_login) = '".mysql_real_escape_string(strtolower($user_login))."' ";
      }
      $sql = "SELECT
                DISTINCT pl.*
              FROM
                idc_portal_link AS pl
                LEFT JOIN
                idc_role_portal_link AS rpl ON pl.portal_link_id = rpl.portal_link_id
                LEFT JOIN
                idc_user_role AS ur ON rpl.role_id = ur.role_id
                LEFT JOIN
                idc_user AS u ON ur.user_id = u.user_id
              WHERE
                ".$sql_where."
              ORDER BY pl.portal_link_position
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        $pre_module = '';
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('fetch_user_header_menu_link')) {
    function fetch_user_header_menu_link($user_login) {
      //echo __FUNCTION__.'()<br />';
      $msg = '';
      $ret = '';
      // First get all main menu links
      $main_menus = fetch_user_portal_link($user_login);
      // Now get all sub menu links for each main menu links
      if($main_menus){
        foreach ($main_menus as $key => $main_menu) {
          if($main_menu['portal_link_id']){
            $sub_menu  = fetch_user_portal_link($user_login, $main_menu['portal_link_id']);
            if($sub_menu){
              $main_menu['portal_link_children'] = $sub_menu;
            }
          }
          $ret[] = $main_menu;
        }
      }
      return $ret;
    }
  }


/* PERMISSION */

  if(!function_exists('get_permission')) {
    function get_permission($perm_id = '') {
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($perm_id) {
        $where  .= " AND p.perm_id = '".mysql_real_escape_string($perm_id)."' ";
         $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT p.*
               FROM
                idc_permission AS p
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if($perm_id){
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

  if(!function_exists('get_permission_role')) {
    function get_permission_role($perm_id = '') {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($perm_id) {
        $sql_where  .= " AND rp.perm_id = '".mysql_real_escape_string($perm_id)."' ";
      }
      $sql = " SELECT rp.*
               FROM
                idc_role_permission AS rp
               WHERE ".$sql_where."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['role_id'];
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

  if(!function_exists('get_perm_detail_from_perm_key')) {
    function get_perm_detail_from_perm_key($perm_key, $perm_id = '') {
      $msg  = '';
      $ret  = '';

      $sql_where    = ' 1 ';
      if($perm_key) {
        $sql_where  .= " AND LOWER(p.perm_key) = '".mysql_real_escape_string(strtolower($perm_key))."' ";
      }
      if($perm_id) {
        $sql_where  .= " AND p.perm_id != '".mysql_real_escape_string($perm_id)."' ";
      }
      $sql = " SELECT p.*
               FROM
                idc_permission AS p
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if(mysql_num_rows($res) == 1) {
          $ret = mysql_fetch_array($res);
          // get user roles
          if($ret){
            $ret['roles'] = get_permission_role($ret['perm_id']);
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

  if(!function_exists('save_permission')) {
    function save_permission($name, $key, $roles, $perm_id = '', $user = '') {
      $msg = '';
      $ret = FALSE;

      if($perm_id) {
        $sql = "UPDATE idc_permission SET ";
        $sql .=" perm_name  = '".mysql_real_escape_string($name)."', ";
        $sql .=" perm_key   = '".mysql_real_escape_string($key)."' ";
        $sql .=" WHERE ";
        $sql .=" perm_id = '".mysql_real_escape_string($perm_id)."'";
        //echo 'SQL :'.$sql.'<br />';

        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      else {
        if($name && $key) {
          $sql = "INSERT INTO idc_permission (perm_name, perm_key ) VALUE ";
          $sql .="(";
          $sql .="'".mysql_real_escape_string($name)."', ";
          $sql .="'".mysql_real_escape_string($key)."' ";
          $sql .=");";

          $res = mysql_query($sql);
          if(!$res) {
            $msg .= 'Invalid query: ' . mysql_error() . "\n";
            $msg .= 'Whole query: ' . $sql;
            die($msg);
          }
          else {
            $perm_id = mysql_insert_id();
            $ret = TRUE;
          }
        }
      }

      // Save User Roles
      if($perm_id && $roles){
        // Fetch Previous save user role and compare with current values
        $previous_perm_role = get_permission_role($perm_id);
        //var_dump($previous_perm_role);
        if(!is_array($previous_perm_role)){
          $previous_perm_role = array();
        }

        // To remove extra role
        $remove_role = array_diff($previous_perm_role, $roles);
        if($remove_role){
          delete_permission_role($perm_id, $remove_role);
        }

        // To add new addition role
        $add_role = array_diff($roles, $previous_perm_role);
        if($add_role) {
          save_permission_role($perm_id, $add_role, $user);
        }
      }

      return $ret;
    }
  }


  if(!function_exists('save_permission_role')) {
    function save_permission_role($perm_id, $roles, $user = '') {
      $msg = '';
      $ret = FALSE;
      if($perm_id && $roles) {
        $sql = "INSERT INTO idc_role_permission (perm_id, role_id, created_by,
                          created_at) VALUE ";
        foreach ($roles as $key => $value) {
          $sql .="(";
          $sql .="'".mysql_real_escape_string($perm_id)."',";
          $sql .="'".mysql_real_escape_string($value)."',";
          $sql .="'".mysql_real_escape_string($user)."',";
          $sql .="NOW()";
          $sql .="),";
        }

        $sql = rtrim($sql, ',');
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('delete_permission_role')) {
    function delete_permission_role($perm_id, $roles = array()) {
      $msg = '';
      $ret = FALSE;
      if($perm_id) {
        $sql = " DELETE FROM idc_role_permission WHERE ";
        $sql .=" perm_id='".mysql_real_escape_string($perm_id)."'";
        if($roles) {
          $sql .=" AND role_id IN (";
          foreach ($roles as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql = rtrim($sql, ',');
          $sql .=") ";
        }
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }












  if(!function_exists('get_all_igf_doc_type')) {
    function get_all_igf_doc_type() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_doc_type
               WHERE doc_type_parent_id='2'
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_all_doc_type')) {
    function get_all_doc_type($parent_id = '') {
      $ret = '';
      $where = ' 1 ';
      if($parent_id){
        $where .= " AND dt.doc_type_parent_id='".mysql_real_escape_string($parent_id)."'";
      }
      $sql = " SELECT dt.*
               FROM idc_doc_type AS dt
               WHERE ".$where."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_all_status')) {
    function get_all_status() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_status
               WHERE status_position IS NOT NULL
               ORDER BY status_position
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }






























/* USER GROUP */
  if(!function_exists('user_group_unique')) {
    function user_group_unique($name, $par_id = '', $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';
      $where  .= " AND LOWER(ug.user_group_name) = LOWER('".mysql_real_escape_string($name)."') ";

      if($par_id) {
        if($par_id == 'NULL'){
          $where  .= " AND ug.user_group_parent_id IS NULL ";
        }
        else {
          $where  .= " AND ug.user_group_parent_id ='".mysql_real_escape_string($par_id)."' ";
        }
      }

      if($id){
        $where  .= " AND ug.user_group_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT ug.user_group_id
                FROM idc_user_group AS ug
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('user_group_detail')) {
    function user_group_detail($id = '', $parent_id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ug.user_group_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      if($parent_id){
        $where  .= " AND ug.user_group_parent_id = '".mysql_real_escape_string($parent_id)."' ";
      }
      $sql = " SELECT ug.*
               FROM
                idc_user_group AS ug
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

  if(!function_exists('user_group_distinct')) {
    function user_group_distinct() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $where  .= " AND ug.user_group_parent_id IS NOT NULL ";
      $sql = "  SELECT
                  ug.*
                FROM
                  idc_user_group AS ug
                WHERE
                  ".$where."
                GROUP BY UPPER(ug.user_group_name)
                ORDER BY ug.user_group_name
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('user_group_distinct_sub_group_name')) {
    function user_group_distinct_sub_group_name() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $where  .= " AND ug.user_group_parent_id IS NOT NULL ";
      $sql = "  SELECT
                  TRIM(UPPER(ug.user_group_name)) AS user_group_name
                FROM
                  idc_user_group AS ug
                WHERE
                  ".$where."
                GROUP BY UPPER(ug.user_group_name)
                ORDER BY ug.user_group_name
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row['user_group_name'];
        }
      }

      return $ret;
    }
  }


  if(!function_exists('user_group_other_group')) {
    function user_group_other_group($id) {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $where  .= " AND TRIM(ug.user_group_name) = (
                    SELECT
                      user_group_name
                    FROM
                      idc_user_group
                    WHERE
                      user_group_id = '".mysql_real_escape_string($id)."'
                   ) ";
      $sql = "  SELECT
                  ug.*
                FROM
                  idc_user_group AS ug
                WHERE
                  ".$where."
                ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('user_group_add')) {
    function user_group_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_user_group (";
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

  if(!function_exists('user_group_update')) {
    function user_group_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_user_group SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
            case 'NULL':
              $sql .= $key."= NULL, ";
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
        $sql .=" WHERE user_group_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('user_group_save')) {
    function user_group_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $user = '';
      // Get created by user information form input data
      if(isset($data['created_by'])){
        $user = $data['created_by'];
      }

      if($id){
        $ret = user_group_update($data, $id);
      }
      else {
        $id   = user_group_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }






























  if(!function_exists('get_requestor_user_group')) {
    function get_requestor_user_group() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_user_group
               WHERE user_group_parent_id IS NULL
               ORDER BY user_group_position
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('save_sub_group_other')) {
    function save_sub_group_other($req_group, $req_group_sub_other) {
      $req_group_sub = FALSE;
      $sql_insert_other   = "INSERT INTO idc_user_group (user_group_parent_id, ";
      $sql_insert_other   .= "user_group_name) VALUE (";
      $sql_insert_other   .="'".mysql_real_escape_string($req_group)."',";
      $sql_insert_other   .="'".mysql_real_escape_string($req_group_sub_other)."')";
      //echo $sql_insert_other.'<br />';
      $res_insert_other = mysql_query($sql_insert_other);
      if(!$res_insert_other) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_insert_other;
        die($req_msg);
      }
      else {
        $req_group_sub = mysql_insert_id();
      }
      return $req_group_sub;
    }
  }

  if(!function_exists('fetch_requestor_group')) {
    function fetch_requestor_group() {
      $group = '';
      $sql_group  = " SELECT *
                      FROM idc_user_group
                      WHERE user_group_parent_id IS NULL
                      ORDER BY user_group_position";

      $res_group = mysql_query($sql_group);
      if (!$res_group) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_group;
        die($req_msg);
      }
      else {
        $i = 0;
        while ($row_group = mysql_fetch_array($res_group)) {
           $group[$row_group['user_group_id']] = $row_group['user_group_name'];
           $i++;
        }
      }
      return $group;
    }
  }

  if(!function_exists('find_user_group_id')) {
    function find_user_group_id($name, $parent = NULL) {
      $ret    = '';
      $where  = ' 1 ';
      if($parent) {
        $where .= " AND user_group_parent_id='".mysql_real_escape_string($parent)."' ";
      }
      $where .= " AND LOWER(user_group_name) =LOWER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT user_group_id ";
      $sql .= "FROM idc_user_group ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['user_group_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_requestor_group_id')) {
    function find_requestor_group_id($requestor_group_name) {
      $ret = '';
      if($requestor_group_name) {
        switch (trim(strtolower($requestor_group_name))) {
          case 'it':
            $ret = find_user_group_id('it');
            break;
          case 'platforms':
          case 'platform':
            $ret = find_user_group_id('platforms');
            break;
          case 'network':
            $ret = find_user_group_id('Network');
            break;
          case 'security':
            $ret = find_user_group_id('security');
            break;
          case 'system':
          case 'systems':
          case 'iso/system':
            $ret = find_user_group_id('iso/system');
            break;
          case 'idc':
            $ret = find_user_group_id('idc');
            break;
          case 'other':
          case 'others':
            $ret = find_user_group_id('other');
            break;
          default:
            $ret = '';
            break;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_requestor_sub_group_id')) {
    function find_requestor_sub_group_id($group_id, $sub_group_name) {
      $ret = '';
      if($group_id && $sub_group_name) {
        switch($group_id){
          case '3':
            switch (trim(strtolower($sub_group_name))) {
              case 'system (iso)':
                $ret = find_user_group_id('system (iso)', $group_id);
                break;
              case 'oss-bss':
              case 'iso-oss/bss':
                $ret = find_user_group_id('oss-bss', $group_id);
                break;
            }
            break;
        }
      }
      return $ret;
    }
  }






























/* REQUEST */

  if(!function_exists('request_detail')) {
    function request_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND r.req_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT r.*, rm.*
               FROM
                idc_request AS r
                LEFT JOIN
                idc_request_material AS rm ON r.req_id = rm.req_id
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

  if(!function_exists('request_add')) {
    function request_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
          $new_req_id = mysql_insert_id();
          $new_result = mysql_query("SELECT req_id FROM idc_request WHERE request_id='".mysql_real_escape_string($new_req_id)."' LIMIT 0,1");
          if(!$new_result) {
            $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
            $req_msg .= 'Whole query: ';
            die($req_msg);
          }
          else {
            $new_request = mysql_fetch_array($new_result);
            $ret = $new_request['req_id'];
          }
        }
      }
      return $ret;
    }
  }

  if(!function_exists('request_update')) {
    function request_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE req_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_save')) {
    function request_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $user = '';
      // Get created by user information form input data
      if(isset($data['created_by'])){
        $user = $data['created_by'];
      }

      if($id){
        $ret = request_update($data, $id);
      }
      else {
        $id   = request_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }

/* REQUEST PROJECT MANAGER */

  if(!function_exists('request_pm_detail')) {
    function request_pm_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND rp.req_pm_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT rp.*
               FROM
                idc_request_pm AS rp
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

  if(!function_exists('request_pm_active')) {
    function request_pm_active($req_id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $where  .= " AND rp.req_pm_status='1' ";
      if($req_id) {
        $where  .= " AND rp.req_id = '".mysql_real_escape_string($req_id)."' ";
      }
      $limit  = 'LIMIT 0, 1';
      $sql = " SELECT
                rp.*,
                u.user_name AS req_pm_name
               FROM
                idc_request_pm AS rp
                LEFT JOIN
                idc_user AS u ON LOWER(rp.req_pm) = LOWER(u.user_login)
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('request_pm_add')) {
    function request_pm_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_pm (";
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

  if(!function_exists('request_pm_update')) {
    function request_pm_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_pm SET ";
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
        $sql .=" WHERE req_pm_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_pm_save')) {
    function request_pm_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_pm_update($data, $id);
      }
      else {
        $ret = request_pm_add($data);
      }

      return $ret;
    }
  }


  if(!function_exists('request_pm_save_detail')) {
    function request_pm_save_detail($req_id, $req_pm, $uname = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;

      // Fetch Previous save project manager detial and compare with current values
      $previous_req_pm = request_pm_active($req_id);
      if($previous_req_pm){
        if($req_pm){
          if(strtolower($previous_req_pm['req_pm']) != strtolower($req_pm)){
            // Make previous pm status to '0'
            // And add new pm details
            $data_req_pm = '';
            $data_req_pm['req_pm_status'] = '0';
            $data_req_pm['updated_by']    = $uname;
            $data_req_pm['updated_at']    = 'NOW';
            request_pm_save($data_req_pm, $previous_req_pm['req_pm_id']);

            //
            $data_req_pm  = '';
            $data_req_pm['req_id']        = $req_id;
            $data_req_pm['req_pm']        = $req_pm;
            $data_req_pm['req_pm_status'] = 1;
            $data_req_pm['created_by']    = $uname;
            $data_req_pm['created_at']    = 'NOW';
            request_pm_save($data_req_pm);

            // First Fetch all PM / IM activities
            $pmim_activity = activity_type_search('PM / IM');
            if($pmim_activity) {
              $pmim_activity_id = $pmim_activity[0]['act_type_id'];
              // Get Implementaion Child Activities
              if($pmim_activity_id) {
                $pmim_activities = activity_type_children_children($pmim_activity_id);

                if($pmim_activities) {
                  foreach ($pmim_activities as $key => $activity) {
                    //var_dump($activity);
                    // CHILDREN Activities
                    if(isset($activity['children'])) {
                      // Find previous activity spoc detail
                      foreach ($activity['children'] as $key => $children_activity) {
                        $pm_act_detail = request_activities($req_id, 1, $children_activity['act_type_id']);
                        //var_dump($pm_act_detail);
                        if($pm_act_detail) {
                          $req_act_id = $pm_act_detail[0]['req_act_id'];
                          if($req_act_id) {
                            $pm_act_spoc_detail = request_activity_spoc($req_act_id, 1);
                            if($pm_act_spoc_detail && $pm_act_spoc_detail[0]) {
                              if($pm_act_spoc_detail[0]['req_act_spoc'] && strtolower($pm_act_spoc_detail[0]['req_act_spoc']) != strtolower($req_pm) ) {
                                // Change Activity Spoc
                                // SELECT * FROM idc_request_activity_spoc ORDER BY req_act_spoc_id DESC LIMIT 5;
                                // FIRST Deactivite previous PM SPOC
                                $data_prev_spoc = array(
                                                    'req_act_spoc_status' => 0,
                                                    'updated_by'  => strtolower($uname),
                                                    'updated_at' => 'NOW'
                                                  );
                                request_activity_spoc_save($data_prev_spoc, $pm_act_spoc_detail[0]['req_act_spoc_id']);

                                // SECOND Add new PM SPOC detail

                                $data_spoc = array(
                                                  'req_act_id'          => $req_act_id,
                                                  'req_act_spoc'        => strtolower($req_pm),
                                                  'req_act_spoc_status' => 1,
                                                  'created_by'          => strtolower($uname),
                                                  'created_at'          => 'NOW'
                                                );
                                request_activity_spoc_save($data_spoc);
                              }
                            }
                            else {
                              $data_spoc = array(
                                                'req_act_id'          => $req_act_id,
                                                'req_act_spoc'        => strtolower($req_pm),
                                                'req_act_spoc_status' => 1,
                                                'created_by'          => strtolower($uname),
                                                'created_at'          => 'NOW'
                                              );
                              request_activity_spoc_save($data_spoc);
                            }
                          }
                        }// END IF
                      }// END FOREACH
                    }// END IF $activity['children']
                  }// END FOREACH $pmim_activities
                }
              }// END IF $pmim_activity_id
            }// END IF $pmim_activity

          }
        }
        else {
          // Update request_pm status to '0'
          $data_req_pm = '';
          $data_req_pm['req_pm_status'] = '0';
          $data_req_pm['updated_by']    = $uname;
          $data_req_pm['updated_at']    = 'NOW';
          request_pm_save($data_req_pm, $previous_req_pm['req_pm_id']);
        }
      }
      else {
        $data_req_pm  = '';
        $data_req_pm['req_id']        = $req_id;
        $data_req_pm['req_pm']        = $req_pm;
        $data_req_pm['req_pm_status'] = 1;
        $data_req_pm['created_by']    = $uname;
        $data_req_pm['created_at']    = 'NOW';
        request_pm_save($data_req_pm);
      }

      return $ret;
    }
  }

/* REQUEST PRIORITY */

  if(!function_exists('request_priority_detail')) {
    function request_priority_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND rp.req_priority_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT rp.*
               FROM
                idc_request_priority AS rp
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

  if(!function_exists('request_priority_active')) {
    function request_priority_active($req_id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $where  .= " AND rp.req_priority_status='1' ";
      if($req_id) {
        $where  .= " AND rp.req_id = '".mysql_real_escape_string($req_id)."' ";
      }
      $limit  = 'LIMIT 0, 1';
      $sql = " SELECT rp.*
               FROM
                idc_request_priority AS rp
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('request_priority_add')) {
    function request_priority_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_priority (";
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

  if(!function_exists('request_priority_update')) {
    function request_priority_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_priority SET ";
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
        $sql .=" WHERE req_priority_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_priority_save')) {
    function request_priority_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_priority_update($data, $id);
      }
      else {
        $ret = request_priority_add($data);
      }

      return $ret;
    }
  }


  if(!function_exists('request_priority_save_detail')) {
    function request_priority_save_detail($req_id, $req_priority, $uname = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;

      // Fetch Previous save request priority detial and compare with current values
      $previous_req_priority = request_priority_active($req_id);
      if($previous_req_priority){
        if($req_priority){
          if(strtolower($previous_req_priority['req_priority']) != strtolower($req_priority)){
            // Make previous pm status to '0'
            // And add new pm details
            $data_req_priority = '';
            $data_req_priority['req_priority_status']   = '0';
            $data_req_priority['updated_by']            = $uname;
            $data_req_priority['updated_at']            = 'NOW';
            request_priority_save($data_req_priority, $previous_req_priority['req_priority_id']);

            //
            $data_req_priority  = '';
            $data_req_priority['req_id']                = $req_id;
            $data_req_priority['req_priority']          = $req_priority;
            $data_req_priority['req_priority_status']   = 1;
            $data_req_priority['created_by']            = $uname;
            $data_req_priority['created_at']            = 'NOW';
            request_priority_save($data_req_priority);
          }
        }
        else {
          // Update request_priority status to '0'
          $data_req_priority = '';
          $data_req_priority['req_priority_status'] = '0';
          $data_req_priority['updated_by']          = $uname;
          $data_req_priority['updated_at']          = 'NOW';
          request_priority_save($data_req_priority, $previous_req_priority['req_priority_id']);
        }
      }
      else {
        $data_req_priority  = '';
        $data_req_priority['req_id']                = $req_id;
        $data_req_priority['req_priority']          = $req_priority;
        $data_req_priority['req_priority_status']   = 1;
        $data_req_priority['created_by']            = $uname;
        $data_req_priority['created_at']            = 'NOW';
        request_priority_save($data_req_priority);
      }

      return $ret;
    }
  }

/* REQUEST ETA */

  if(!function_exists('request_eta_detail')) {
    function request_eta_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND re.req_eta_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT re.*
               FROM
                idc_request_eta AS re
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

  if(!function_exists('request_eta_active')) {
    function request_eta_active($req_id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $where  .= " AND re.req_eta_status='1' ";
      if($req_id) {
        $where  .= " AND re.req_id = '".mysql_real_escape_string($req_id)."' ";
      }
      $limit  = 'LIMIT 0, 1';
      $sql = " SELECT re.*
               FROM
                idc_request_eta AS re
               WHERE ".$where."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('request_eta_add')) {
    function request_eta_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_eta (";
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

  if(!function_exists('request_eta_update')) {
    function request_eta_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_eta SET ";
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
        $sql .=" WHERE req_eta_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_eta_save')) {
    function request_eta_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_eta_update($data, $id);
      }
      else {
        $ret = request_eta_add($data);
      }

      return $ret;
    }
  }


  if(!function_exists('request_eta_save_detail')) {
    function request_eta_save_detail($req_id, $req_eta, $uname = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;

      // Fetch Previous save request priority detial and compare with current values
      $previous_req_eta = request_eta_active($req_id);
      if($previous_req_eta){
        if($req_eta){
          if(strtolower($previous_req_eta['req_eta']) != strtolower($req_eta)){
            // Make previous pm status to '0'
            // And add new pm details
            $data_req_eta = '';
            $data_req_eta['req_eta_status'] = '0';
            $data_req_eta['updated_by']     = $uname;
            $data_req_eta['updated_at']     = 'NOW';
            request_eta_save($data_req_eta, $previous_req_eta['req_eta_id']);

            //
            $data_req_eta  = '';
            $data_req_eta['req_id']         = $req_id;
            $data_req_eta['req_eta']        = $req_eta;
            $data_req_eta['req_eta_status'] = 1;
            $data_req_eta['created_by']     = $uname;
            $data_req_eta['created_at']     = 'NOW';
            request_eta_save($data_req_eta);
          }
        }
        else {
          // Update request_eta status to '0'
          $data_req_eta = '';
          $data_req_eta['req_eta_status'] = '0';
          $data_req_eta['updated_by']     = $uname;
          $data_req_eta['updated_at']     = 'NOW';
          request_eta_save($data_req_eta, $previous_req_eta['req_eta_id']);
        }
      }
      else {
        $data_req_eta  = '';
        $data_req_eta['req_id']           = $req_id;
        $data_req_eta['req_eta']          = $req_eta;
        $data_req_eta['req_eta_status']   = 1;
        $data_req_eta['created_by']       = $uname;
        $data_req_eta['created_at']       = 'NOW';
        request_eta_save($data_req_eta);
      }

      return $ret;
    }
  }

/* REQUEST DOCUMENT */

  if(!function_exists('request_doc_detail')) {
    function request_doc_detail($id = '') {
     //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND rd.req_doc_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT rd.*, ddt.doc_doc_type_id
               FROM
                idc_request_doc AS rd
                LEFT JOIN
                idc_doc AS d ON rd.doc_id = d.doc_id
                LEFT JOIN
                idc_doc_doc_type AS ddt ON d.doc_id = ddt.doc_id
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


  if(!function_exists('request_doc_add')) {
    function request_doc_add($data) {
     //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_doc (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('request_doc_update')) {
    function request_doc_update($data, $id) {
     //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_doc SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE req_doc_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_doc_save')) {
    function request_doc_save($data, $id = '') {
     //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $user = '';
      // Get created by user information form input data
      if(isset($data['created_by'])){
        $user = $data['created_by'];
      }

      if($id){
        $ret = request_doc_update($data, $id);
      }
      else {
        $id   = request_doc_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }


/* REQUEST MATERIAL */

  if(!function_exists('request_material_add')) {
    function request_material_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_material (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('request_material_update')) {
    function request_material_update($data, $id, $search_field = 'req_id') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_material SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE ".$search_field."='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_material_save')) {
    function request_material_save($data, $id = '', $key = 'req_id') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;

      if($id){
        $ret = request_material_update($data, $id, $key);
      }
      else {
        $id   = request_material_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }


/* REQUEST STATUS */

  if(!function_exists('request_status_add')) {
    function request_status_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_status (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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

  if(!function_exists('request_status_update')) {
    function request_status_update($data, $id, $search_field = 'req_id') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_status SET ";
      if($data && $id) {
        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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
        $sql .=" WHERE ".$search_field."='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_status_save')) {
    function request_status_save($data, $id = '', $key = 'req_id') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;

      if($id) {
        $ret = request_status_update($data, $id, $key);
      }
      else {
        $id   = request_status_add($data);
        $ret  = $id;
      }

      return $ret;
    }
  }


/* REQUEST ACTIVITY */

  if(!function_exists('request_activity_unique')) {
    function request_activity_unique($req_id, $activity_id, $id='') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';

      if($req_id && $activity_id) {
        $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";
        $where  .= " AND ra.act_type_id = '".mysql_real_escape_string($activity_id)."' ";
      }

      if($id){
        $where  .= " AND ra.req_act_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT ra.req_act_id
                FROM idc_request_activity AS ra
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('request_activity_detail')) {
    function request_activity_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ra.req_act_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT ra.*
               FROM
                idc_request_activity AS ra
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

  if(!function_exists('request_activities')) {
    function request_activities($req_id, $applicable = NULL, $act_type_id = NULL) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      if($applicable){
        $where  .= " AND ra.req_act_applicable = '".mysql_real_escape_string($applicable)."' ";
      }

      if($act_type_id) {
        $where  .= " AND at.act_type_id = '".mysql_real_escape_string($act_type_id)."' ";
      }

      $limit  = '';
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";

      $sql = " SELECT ra.*, at.act_type_parent_id
               FROM
                idc_request_activity AS ra
                LEFT JOIN
                idc_activity_type AS at ON ra.act_type_id = at.act_type_id
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

  if(!function_exists('request_parent_activities_status')) {
    function request_parent_activities_status($req_id, $parent_activity_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ra.req_act_applicable =1 ";
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";


      if($parent_activity_id){
        $where  .= " AND at.act_type_parent_id = '".mysql_real_escape_string($parent_activity_id)."' ";
      }
      $sql = " SELECT
                ra.*,
                ras.status_id,
                s.status_parent_id,
                ras.req_act_status_remark,
                ras.created_at AS req_activity_status_created_at,
                ras.created_by AS req_activity_status_created_by
               FROM
                idc_request_activity AS ra
                LEFT JOIN
                idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                LEFT JOIN
                idc_request_activity_status AS ras
                ON
                ra.req_act_id = ras.req_act_id
                AND
                ras.created_at = (SELECT MAX(created_at) FROM idc_request_activity_status WHERE req_act_id = ra.req_act_id )
                LEFT JOIN
                idc_status AS s ON ras.status_id = s.status_id
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

  if(!function_exists('request_activity_by_act_type_id')) {
    function request_activity_by_act_type_id($req_id, $activity_id) {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';

      if($req_id && $activity_id) {
        $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";
        $where  .= " AND ra.act_type_id = '".mysql_real_escape_string($activity_id)."' ";
      }

      $sql = "SELECT ra.*
                FROM idc_request_activity AS ra
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        $ret = mysql_fetch_array($res);
        // Get SPOC detail
        $req_act_spoc = request_activity_spoc($ret['req_act_id'], 1);
        if($req_act_spoc) {
          $ret['spoc'] = $req_act_spoc;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('request_activity_activites_status_history')) {
    function request_activity_activites_status_history($req_id, $applicable = NULL) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $where  .= " AND at_par.act_type_parent_id = '1' ";
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";

      if($applicable){
        $where  .= " AND ra.req_act_applicable = '".mysql_real_escape_string($applicable)."' ";
      }

      $sql = " SELECT
                ra.req_act_id,
                ra.act_type_id,
                ra.req_act_applicable,
                ras.status_id,
                ras.created_at,
                at.act_type_name,
                at.act_type_parent_id,
                at_par.act_type_name AS par_name,
                at_par.act_type_parent_id AS par_par_id
              FROM
                idc_request_activity_status AS ras
                LEFT JOIN
                idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id
                LEFT JOIN
                idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                LEFT JOIN
                idc_activity_type AS at_par ON at.act_type_parent_id = at_par.act_type_id
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


  if(!function_exists('request_activity_activites_status_latest')) {
    function request_activity_activites_status_latest($req_id, $par_par_act_type_id=1, $applicable = NULL) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";

      if($par_par_act_type_id){
        $where  .= " AND at_par.act_type_parent_id = '".mysql_real_escape_string($par_par_act_type_id)."' ";
      }

      if($applicable){
        $where  .= " AND ra.req_act_applicable = '".mysql_real_escape_string($applicable)."' ";
      }

      $sql = " SELECT
                ra.req_act_id,
                ra.act_type_id,
                ra.req_act_applicable,
                ras.status_id,
                s.status_parent_id,
                ras.created_at,
                at.act_type_name,
                at.act_type_parent_id,
                at_par.act_type_name AS par_name,
                at_par.act_type_parent_id AS par_par_id
              FROM
                idc_request_activity_status AS ras
                LEFT JOIN
                idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id AND ras.created_at = (SELECT MAX(created_at) FROM idc_request_activity_status WHERE req_act_id = ra.req_act_id )
                LEFT JOIN
                idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                LEFT JOIN
                idc_activity_type AS at_par ON at.act_type_parent_id = at_par.act_type_id
                LEFT JOIN
                idc_status AS s ON ras.status_id = s.status_id
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

  if(!function_exists('request_activity_user_activites')) {
    function request_activity_user_activites($user_login) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_spoc_status = '1' ";
      $where  .= " AND at_par.act_type_parent_id = '1' ";

      $where  .= " AND LOWER(ras.req_act_spoc) ='".mysql_real_escape_string($user_login)."' ";

      $sql = " SELECT
                ras.req_act_spoc_id,
                ras.req_act_id,
                ras.req_act_spoc,
                ra.req_id,
                ras.spoc_weight,
                ras.req_act_spoc_status,
                u.user_name AS req_act_spoc_name,
                ra.act_type_id,
                ra_s.status_id,
                s.status_parent_id,
                s.status_name
              FROM
                idc_request_activity_spoc AS ras
                LEFT JOIN
                idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id
                LEFT JOIN
                idc_request_activity_status AS ra_s
                ON
                ra_s.req_act_id = ra.req_act_id
                AND
                ra_s.created_at = (
                  SELECT MAX(created_at)
                  FROM idc_request_activity_status
                  WHERE
                  req_act_id = ra.req_act_id
                )
                LEFT JOIN
                idc_status AS s ON ra_s.status_id = s.status_id
                LEFT JOIN
                idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                LEFT JOIN
                idc_activity_type AS at_par ON at.act_type_parent_id = at_par.act_type_id
                LEFT JOIN
                idc_user AS u ON LOWER(ras.req_act_spoc) = LOWER(u.user_login)
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


  if(!function_exists('request_activity_activites_status_completed')) {
    function request_activity_activites_status_completed() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where    = ' 1 ';
      $order_by = '';
      $limit    = '';

      $where  .= " AND ra.req_act_applicable = '1' ";
      $where  .= " AND r.req_status IN ('WIP', 'PARTIAL RELEASED') ";

      $order_by .= 'ra.req_id, ra.act_type_id';
      $sql = " SELECT
                ra.req_id,
                ra.req_act_id,
                ra.act_type_id,
                at.act_type_parent_id,
                ra.req_act_applicable,
                ras.status_id,
                s.status_parent_id
              FROM
                idc_request_activity AS ra
                LEFT JOIN
                idc_request AS r ON ra.req_id = r.req_id
                LEFT JOIN
                idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                LEFT JOIN
                idc_request_activity_status AS ras
                  ON
                  ra.req_act_id = ras.req_act_id
                  AND
                  ras.created_at = (
                    SELECT MAX(created_at)
                    FROM idc_request_activity_status
                    WHERE req_act_id = ra.req_act_id
                    )
                LEFT JOIN
                idc_status AS s ON ras.status_id = s.status_id
               WHERE ".$where."
               ORDER BY ".$order_by."
               ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $new_req_id     = '0';
        $req_completed  = TRUE;
        while ( $row = mysql_fetch_array($res)) {
          if($new_req_id != $row['req_id']){
            if($req_completed){
              $ret[] = $new_req_id;
            }
            $new_req_id     = $row['req_id'];
            $req_completed  = TRUE;
          }
          else {
            if($req_completed){
              if($row['act_type_parent_id'] != '1' && $row['status_id'] != 100) {
                $req_completed  = FALSE;
              }
            }
          }
        }// END while
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }


  if(!function_exists('request_activity_add')) {
    function request_activity_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_activity (";
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

  if(!function_exists('request_activity_update')) {
    function request_activity_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_activity SET ";
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
        $sql .=" WHERE req_act_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_activity_save')) {
    function request_activity_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_activity_update($data, $id);
      }
      else {
        $ret = request_activity_add($data);
      }

      return $ret;
    }
  }


/* REQUEST ACTIVITY SPOC */

  if(!function_exists('request_activity_spoc_unique')) {
    function request_activity_spoc_unique($req_act_id, $req_act_spoc, $id='') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';

      if($req_id && $activity_id) {
        $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
        $where  .= " AND LOWER(ras.req_act_spoc) = LOWER('".mysql_real_escape_string($req_act_spoc)."') ";
      }

      if($id){
        $where  .= " AND ras.req_act_spoc_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "  SELECT ras.req_act_spoc_id
                FROM idc_request_activity_spoc AS ras
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('request_activity_spoc_detail')) {
    function request_activity_spoc_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ras.req_act_spoc_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT ras.*
               FROM
                idc_request_activity_spoc AS ras
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


  if(!function_exists('request_activity_spoc')) {
    function request_activity_spoc($req_act_id, $status = NULL) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
      if($status) {
        $where  .= " AND ras.req_act_spoc_status = '".mysql_real_escape_string($status)."' ";
      }
      $sql = " SELECT ras.*, u.user_name AS req_act_spoc_name
               FROM
                idc_request_activity_spoc AS ras
                LEFT JOIN
                idc_user AS u ON LOWER(ras.req_act_spoc) = LOWER(u.user_login)
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

  if(!function_exists('request_activity_spoc_by_weight')) {
    function request_activity_spoc_by_weight($req_act_id, $weight, $status = NULL) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
      $where  .= " AND ras.spoc_weight = '".mysql_real_escape_string($weight)."' ";
      if($status){
        $where  .= " AND ras.req_act_spoc_status = '".mysql_real_escape_string($status)."' ";
      }
      $sql = " SELECT ras.*
               FROM
                idc_request_activity_spoc AS ras
                LEFT JOIN
                idc_user AS u ON LOWER(ras.req_act_spoc) = LOWER(u.user_login)
               WHERE ".$where."
               ORDER BY ras.created_at DESC
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }

  if(!function_exists('request_activity_spoc_by_req_id')) {
    function request_activity_spoc_by_req_id($req_id, $act_type_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";

      if($act_type_id) {
        $where  .= " AND ra.act_type_id = '".mysql_real_escape_string($act_type_id)."' ";
      }

      $sql = " SELECT ras.*, u.user_name AS req_act_spoc_name, ra.act_type_id
               FROM
                idc_request_activity_spoc AS ras
                LEFT JOIN
                idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id
                LEFT JOIN
                idc_user AS u ON LOWER(ras.req_act_spoc) = LOWER(u.user_login)
               WHERE ".$where."
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

  if(!function_exists('request_activity_spoc_by_req_id_parent_act_type_id')) {
    function request_activity_spoc_by_req_id_parent_act_type_id($req_id, $act_type_par_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";
      $where  .= " AND ras.req_act_spoc_status = '1' ";

      if($act_type_par_id) {
        $where  .= " AND at.act_type_parent_id = '".mysql_real_escape_string($act_type_par_id)."' ";
      }

      $sql = " SELECT
                  ras.*,
                  u.user_name AS req_act_spoc_name,
                  ra.act_type_id,
                  ra_s.status_id,
                  s.status_parent_id,
                  s.status_name AS req_act_spoc_activity_stats
               FROM
                  idc_request_activity_spoc AS ras
                  LEFT JOIN
                  idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id
                  LEFT JOIN
                  idc_request_activity_status AS ra_s
                  ON
                  ra_s.req_act_id = ra.req_act_id
                  AND
                  ra_s.created_at = (
                    SELECT MAX(created_at)
                    FROM idc_request_activity_status
                    WHERE
                    req_act_id = ra.req_act_id
                  )
                  LEFT JOIN
                  idc_status AS s ON ra_s.status_id = s.status_id
                  LEFT JOIN
                  idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                  LEFT JOIN
                  idc_user AS u ON LOWER(ras.req_act_spoc) = LOWER(u.user_login)
               WHERE ".$where."
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


  if(!function_exists('request_activity_spoc_add')) {
    function request_activity_spoc_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_activity_spoc (";
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

  if(!function_exists('request_activity_spoc_update')) {
    function request_activity_spoc_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_activity_spoc SET ";
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
        $sql .=" WHERE req_act_spoc_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_activity_spoc_save')) {
    function request_activity_spoc_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_activity_spoc_update($data, $id);
      }
      else {
        $ret = request_activity_spoc_add($data);
      }

      return $ret;
    }
  }


/* REQUEST ACTIVITY STATUS */

  if(!function_exists('request_activity_status_detail')) {
    function request_activity_status_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND ras.req_act_status_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT ras.*
               FROM
                idc_request_activity_status AS ras
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


  if(!function_exists('request_activity_status')) {
    function request_activity_status($req_act_id, $order = 'DESC') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
      $sql = " SELECT
                ras.*,
                s.status_name AS req_act_status_name,
                u.user_name AS req_act_status_remark_by
               FROM
                idc_request_activity_status AS ras
                LEFT JOIN
                idc_status AS s ON ras.status_id = s.status_id
                LEFT JOIN
                idc_user AS u ON LOWER(ras.created_by) = LOWER(u.user_login)
               WHERE ".$where."
               ORDER BY ras.req_act_status_id ".$order."
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

  if(!function_exists('request_activity_status_by_status')) {
    function request_activity_status_by_status($req_act_id, $status) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
      $where  .= " AND ras.status_id = '".mysql_real_escape_string($status)."' ";
      $sql = " SELECT ras.req_act_status_id
               FROM
                idc_request_activity_status AS ras
               WHERE ".$where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['req_act_status_id'];
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }

  if(!function_exists('request_activity_remark_by_req_id_act_type_id')) {
    function request_activity_remark_by_req_id_act_type_id($req_id, $act_type_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";
      $where  .= " AND ra.req_act_applicable = '1' ";

      $where  .= " AND TRIM(ras.req_act_status_remark) != '' ";

      if($act_type_par_id) {
        $where  .= " AND at.act_type_id = '".mysql_real_escape_string($act_type_id)."' ";
      }

      $sql = " SELECT
                  ras.*,
                  u.user_name AS req_act_status_remark_by
               FROM
                  idc_request_activity_status AS ras
                  LEFT JOIN
                  idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id
                  LEFT JOIN
                  idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                  LEFT JOIN
                  idc_user AS u ON LOWER(ras.created_by) = LOWER(u.user_login)
               WHERE ".$where."
               ORDER BY ras.req_act_status_id DESC
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


  if(!function_exists('request_activity_remark_by_req_id_parent_act_type_id')) {
    function request_activity_remark_by_req_id_parent_act_type_id($req_id, $act_type_par_id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ra.req_id = '".mysql_real_escape_string($req_id)."' ";
      $where  .= " AND ra.req_act_applicable = '1' ";

      $where  .= " AND TRIM(ras.req_act_status_remark) != '' ";

      if($act_type_par_id) {
        $where  .= " AND at.act_type_parent_id = '".mysql_real_escape_string($act_type_par_id)."' ";
      }

      $sql = " SELECT
                  ras.*,
                  u.user_name AS req_act_status_remark_by
               FROM
                  idc_request_activity_status AS ras
                  LEFT JOIN
                  idc_request_activity AS ra ON ras.req_act_id = ra.req_act_id
                  LEFT JOIN
                  idc_activity_type AS at ON ra.act_type_id = at.act_type_id
                  LEFT JOIN
                  idc_user AS u ON LOWER(ras.created_by) = LOWER(u.user_login)
               WHERE ".$where."
               ORDER BY ras.req_act_status_id DESC
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


  if(!function_exists('request_activity_start_datetime')) {
    function request_activity_start_datetime($req_act_id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
      $where  .= " AND ras.status_id = '6' ";
      $sql = " SELECT
                ras.created_at
               FROM
                idc_request_activity_status AS ras
               WHERE ".$where."
               ORDER BY ras.created_at ASC
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['created_at'];
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }

  if(!function_exists('request_activity_complete_datetime')) {
    function request_activity_complete_datetime($req_act_id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND ras.req_act_id = '".mysql_real_escape_string($req_act_id)."' ";
      $where  .= " AND ras.status_id = '100' ";
      $sql = " SELECT
                ras.created_at
               FROM
                idc_request_activity_status AS ras
               WHERE ".$where."
               ORDER BY ras.created_at DESC
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['created_at'];
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }

  if(!function_exists('request_activity_status_add')) {
    function request_activity_status_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_activity_status (";
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

  if(!function_exists('request_activity_status_update')) {
    function request_activity_status_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_activity_status SET ";
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
        $sql .=" WHERE req_act_status_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_activity_status_save')) {
    function request_activity_status_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_activity_status_update($data, $id);
      }
      else {
        $ret = request_activity_status_add($data);
      }

      return $ret;
    }
  }

/* REQUEST STAT*/


  if(!function_exists('request_imp_dashboard_top_level_summary')) {
    function request_imp_dashboard_top_level_summary($user_sub_group = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "  SELECT
                  'Count - Total Request' AS `igf_server_type`,
                  `idc_request`.`req_status` AS `req_status`,
                  NULL AS `Released`,
                  count(1) AS `count(1)`
                FROM
                  `idc_request`
                GROUP BY
                  NULL,
                  `idc_request`.`req_status`,
                  NULL
                UNION
                SELECT
                  `s`.`igf_server_type` AS `igf_server_type`,
                  `r`.`req_status` AS `req_status`,
                  if(isnull(`s`.`igf_server_release_at`),'Yet To Release','Already Released') AS `Released`,
                  count(1) AS `count(1)`
                FROM
                  (
                    (
                      `idc_igf_server` `s`
                      JOIN
                      `idc_igf` `i`
                    )
                    JOIN `idc_request` `r`
                  )
                WHERE
                  (
                    (`s`.`igf_id` = `i`.`igf_id`)
                    AND
                    (`i`.`req_id` = `r`.`req_id`)
                    AND
                    (`i`.`igf_deleted` = 0)
                    AND
                    (`s`.`igf_server_type` <> 'Appliance')
                  )
                GROUP BY
                  `s`.`igf_server_type`,
                  `r`.`req_status`,
                  if(isnull(`s`.`igf_server_release_at`),'Yet To Release','Already Released')
                UNION
                SELECT
                  `s`.`igf_server_type` AS `igf_server_type`,
                  NULL AS `req_status`,
                  if(isnull(`s`.`igf_server_release_at`),'Yet To Release','Already Released') AS `Released`,
                  sum(1) AS `sum(1)`
                FROM
                  (
                    (
                      `idc_igf_server` `s`
                      JOIN
                      `idc_igf` `i`
                    )
                    JOIN `idc_request` `r`
                  )
                WHERE
                  (
                    (`s`.`igf_id` = `i`.`igf_id`)
                    AND
                    (`i`.`req_id` = `r`.`req_id`)
                    AND
                    (`i`.`igf_deleted` = 0)
                    AND
                    (`s`.`igf_server_type` <> 'Appliance')
                  )
                GROUP BY
                  `s`.`igf_server_type`,
                  if(isnull(`s`.`igf_server_release_at`),'Yet To Release','Already Released')
                UNION
                SELECT
                  'Count - Total' AS `igf_server_type`,
                  NULL AS `req_status`,
                  if(isnull(`s`.`igf_server_release_at`),'Yet To Release','Already Released') AS `Released`,
                  sum(1) AS `sum(1)`
                FROM
                  (
                    (
                      `idc_igf_server` `s`
                      JOIN
                      `idc_igf` `i`
                    )
                    JOIN
                    `idc_request` `r`
                  )
                WHERE
                  (
                    (`s`.`igf_id` = `i`.`igf_id`)
                    AND
                    (`i`.`req_id` = `r`.`req_id`)
                    AND
                    (`i`.`igf_deleted` = 0)
                    AND
                    (`s`.`igf_server_type` <> 'Appliance')
                  )
                GROUP BY
                  if(isnull(`s`.`igf_server_release_at`),'Yet To Release','Already Released')
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


/*
OLD QUERY

SELECT `s`.`igf_server_type` AS `igf_server_type`,
       `r`.`req_status` AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`,
       count(1) AS `count(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (s.igf_server_storage_ext_type like 'SAN%'))
GROUP BY `s`.`igf_server_type`,
         `r`.`req_status`,
         if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
UNION
SELECT `s`.`igf_server_type` AS `igf_server_type`,
       NULL AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`,
       sum(1) AS `sum(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (s.igf_server_storage_ext_type like 'SAN%'))
GROUP BY `s`.`igf_server_type`,
         if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
UNION
SELECT 'Count - Total' AS `igf_server_type`,
       NULL AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`,
       sum(1) AS `sum(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (s.igf_server_storage_ext_type like 'SAN%'))
GROUP BY if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
*/

  if(!function_exists('request_imp_dashboard_top_level_summary_san')) {
    function request_imp_dashboard_top_level_summary_san() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "
select alis_a.igf_server_type, alis_a.req_status, alis_a.Released, alis_a.`count(1)`
from (
SELECT `s`.`igf_server_type` AS `igf_server_type`,
       `r`.`req_status` AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`
          ,case
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = 'NA' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('NA')
           then ''
          when TRIM(UPPER(s.igf_server_fc_hba_port)) > '0' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS', 'NA')
           then 'SAN'
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = '' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
           then 'SAN'
           ELSE ''
         END server_storage_type,
       count(1) AS `count(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
           AND (s.igf_server_storage_ext_type IN ('SAN', 'SAN + NAS', 'NA'))
          )
GROUP BY `s`.`igf_server_type`,
         `r`.`req_status`,
         if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released'),
          case
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = 'NA' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('NA')
           then ''
          when TRIM(UPPER(s.igf_server_fc_hba_port)) > '0' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS', 'NA')
           then 'SAN'
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = '' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
           then 'SAN'
           ELSE ''
         END
UNION
SELECT `s`.`igf_server_type` AS `igf_server_type`,
       NULL AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`
          ,case
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = 'NA' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('NA')
           then ''
          when TRIM(UPPER(s.igf_server_fc_hba_port)) > '0' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS', 'NA')
           then 'SAN'
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = '' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
           then 'SAN'
           ELSE ''
         END server_storage_type,
       sum(1) AS `sum(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (s.igf_server_storage_ext_type IN ('SAN', 'SAN + NAS', 'NA'))
          )
GROUP BY `s`.`igf_server_type`,
         if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
          ,case
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = 'NA' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('NA')
           then ''
          when TRIM(UPPER(s.igf_server_fc_hba_port)) > '0' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS', 'NA')
           then 'SAN'
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = '' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
           then 'SAN'
           ELSE ''
         END
UNION
SELECT 'Count - Total' AS `igf_server_type`,
NULL AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`
          ,case
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = 'NA' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('NA')
           then ''
          when TRIM(UPPER(s.igf_server_fc_hba_port)) > '0' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS', 'NA')
           then 'SAN'
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = '' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
           then 'SAN'
           ELSE ''
         END server_storage_type,
       sum(1) AS `sum(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (s.igf_server_storage_ext_type IN ('SAN', 'SAN + NAS', 'NA'))
          )
GROUP BY if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
          ,case
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = 'NA' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('NA')
           then ''
          when TRIM(UPPER(s.igf_server_fc_hba_port)) > '0' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS', 'NA')
           then 'SAN'
          when TRIM(UPPER(s.igf_server_fc_hba_port)) = '' AND TRIM(UPPER(s.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
           then 'SAN'
           ELSE ''
         END
         ) alis_a
         where UPPER(alis_a.server_storage_type) = 'SAN'

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

  if(!function_exists('request_imp_dashboard_top_level_summary_cluster')) {
    function request_imp_dashboard_top_level_summary_cluster() {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $where  = ' 1 ';
      $sql = "SELECT `s`.`igf_server_type` AS `igf_server_type`,
       `r`.`req_status` AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`,
       count(1) AS `count(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
        AND (UPPER(s.igf_server_ha_cluster) = 'YES'))
GROUP BY `s`.`igf_server_type`,
         `r`.`req_status`,
         if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
UNION
SELECT `s`.`igf_server_type` AS `igf_server_type`,
       NULL AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`,
       sum(1) AS `sum(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (UPPER(s.igf_server_ha_cluster) = 'YES'))
GROUP BY `s`.`igf_server_type`,
         if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
UNION
SELECT 'Count - Total' AS `igf_server_type`,
       NULL AS `req_status`,
       if(isnull(`s`.`igf_server_release_at`),
          'Yet To Release',
          'Already Released')
          AS `Released`,
       sum(1) AS `sum(1)`
  FROM ((`idc_igf_server` `s` JOIN `idc_igf` `i`) JOIN `idc_request` `r`)
WHERE (    (`s`.`igf_id` = `i`.`igf_id`)
        AND (`i`.`req_id` = `r`.`req_id`)
        AND (`i`.`igf_deleted` = 0)
        AND (`s`.`igf_server_type` <> 'Appliance')
          AND (UPPER(s.igf_server_ha_cluster) = 'YES'))
GROUP BY if(isnull(`s`.`igf_server_release_at`),
            'Yet To Release',
            'Already Released')
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
  if(!function_exists('request_stat_unique')) {
    function request_stat_unique($name, $par_id= '', $id = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';
      $where  .= " AND LOWER(rs.req_stat_name) = LOWER('".mysql_real_escape_string($name)."') ";

      if($par_id) {
        if($par_id == 'NULL'){
          $where  .= " AND rs.req_stat_parent_id IS NULL ";
        }
        else {
          $where  .= " AND rs.req_stat_parent_id ='".mysql_real_escape_string($par_id)."' ";
        }
      }

      if($id){
        $where  .= " AND s.req_stat_id !='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT rs.req_stat_id
                FROM idc_request_stat AS rs
                WHERE
                  ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        if (mysql_num_rows($res)) {
          $ret    = FALSE;
        }
        else {
          $ret    = TRUE;
        }
      }

      return $ret;
    }
  }

  if(!function_exists('request_stat_detail')) {
    function request_stat_detail($id = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      if($id) {
        $where  .= " AND rs.req_stat_id = '".mysql_real_escape_string($id)."' ";
        $limit  = 'LIMIT 0, 1';
      }
      $sql = " SELECT rs.*
               FROM
                idc_request_stat AS rs
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



  if(!function_exists('request_stat_latest')) {
    function request_stat_latest($as_on_date = '') {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';
      $parent_id = request_stat_latest_parent($as_on_date);
      //var_dump($parent_id);
      $where  = ' 1 ';
      $where  .= " AND rs.req_stat_parent_id = '".mysql_real_escape_string($parent_id)."' ";

      $sql = " SELECT rs.*
               FROM
                idc_request_stat AS rs
               WHERE ".$where."
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

  if(!function_exists('request_stat_latest_parent')) {
    function request_stat_latest_parent($as_on_date) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = ' LIMIT 0, 1';
      $where  .= " AND rs.req_stat_parent_id IS NULL ";
      if($as_on_date){
        $where  .= " AND rs.req_stat_name   = 'DASHBOAD AS ON' ";
        $where  .= " AND rs.req_stat_value  = '".mysql_real_escape_string($as_on_date)."' ";
      }

      $sql = " SELECT rs.req_stat_id
               FROM
                idc_request_stat AS rs
               WHERE ".$where."
               ORDER BY rs.created_at DESC
               ".$limit."

              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['req_stat_id'];
      }
      else {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      return $ret;
    }
  }

  if(!function_exists('request_stat_children')) {
    function request_stat_children($id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';
      $where  .= " AND rs.req_stat_parent_id = '".mysql_real_escape_string($id)."' ";
      $sql = " SELECT rs.*
               FROM
                idc_request_stat AS rs
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


  if(!function_exists('request_stat_add')) {
    function request_stat_add($data) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "INSERT INTO idc_request_stat (";
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

  if(!function_exists('request_stat_update')) {
    function request_stat_update($data, $id) {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = FALSE;
      $sql  = "UPDATE idc_request_stat SET ";
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
        $sql .=" WHERE req_stat_id='".mysql_real_escape_string($id)."' ";
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

  if(!function_exists('request_stat_save')) {
    function request_stat_save($data, $id = '') {
      //echo __FUNCTION__.'()<br />';
      //var_dump($data);
      $msg  = '';
      $ret  = FALSE;
      if($id){
        $ret = request_stat_update($data, $id);
      }
      else {
        $ret = request_stat_add($data);
      }

      return $ret;
    }
  }



/* REQUEST DASHBOARD */

  if(!function_exists('request_dashboard_user_group_wise')) {
    function request_dashboard_user_group_wise() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $sql = "SELECT
                r.req_group_id,
                ug.user_group_name,
                COUNT(r.req_group_id) AS count
              FROM
                idc_user_group AS ug
                LEFT JOIN
                idc_request AS r ON r.req_group_id = ug.user_group_id
              WHERE ".$where."
              GROUP BY
                r.req_group_id
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




  if(!function_exists('request_dashboard_user_sub_group_wise_req_count')) {
    function request_dashboard_user_sub_group_wise_req_count() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where    = ' 1 ';
      $order_by = ' ORDER BY name ';
      $limit    = '';



      $sql = "SELECT
                a.user_sub_group_name AS name,
                IF(b.count, b.count, 0) AS count
              FROM
                (
                  SELECT
                    DISTINCT UPPER(user_group_name) AS user_sub_group_name
                  FROM
                    idc_user_group
                  WHERE
                    user_group_parent_id IS NOT NULL
                ) AS a
                LEFT JOIN
                (
                  SELECT
                    ug.user_group_name AS user_sub_group_name,
                    COUNT(UPPER(ug.user_group_name)) AS count
                  FROM
                    idc_request AS r
                    LEFT JOIN
                    idc_user_group AS ug ON r.req_group_sub_id = ug.user_group_id
                  GROUP BY
                    ug.user_group_name
                ) AS b ON a.user_sub_group_name = b.user_sub_group_name
                ".$order_by."
                ".$limit."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[strtoupper($row['name'])] = $row;
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

  if(!function_exists('request_dashboard_user_sub_group_wise_server')) {
    function request_dashboard_user_sub_group_wise_server() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where  = ' 1 ';
      $limit  = '';

      $sql = "SELECT
                UPPER(ug.user_group_name) AS user_sub_group_name,
                st.server_type_name,
                IF(ISNULL(igf_ser.igf_server_release_at), 'NOT_RELEASED', 'RELEASED') AS server_released_status,
                COUNT(UPPER(ug.user_group_name)) AS count
              FROM
                idc_igf_server AS igf_ser
                LEFT JOIN
                idc_server_type AS st ON  igf_ser.igf_server_type_id = st.server_type_id
                LEFT JOIN
                idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
                LEFT JOIN
                idc_igf_server_release_server AS isrs ON igf_ser.igf_server_id = isrs.igf_server_id
                LEFT JOIN
                idc_request AS r ON igf.req_id = r.req_id
                LEFT JOIN
                idc_user_group AS ug ON r.req_group_sub_id = ug.user_group_id
              WHERE
                1
                AND
                igf.igf_deleted = '0'
              GROUP BY
                UPPER(ug.user_group_name),
                st.server_type_name,
                server_released_status
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

  if(!function_exists('request_dashboard_user_sub_group_wise')) {
    function request_dashboard_user_sub_group_wise() {
      //echo __FUNCTION__.'()<br />';
      $msg  = '';
      $ret  = '';

      $where    = ' 1 ';
      $order_by = ' ORDER BY user_sub_group_name, type, server_type_name, server_released_status ';
      $limit    = '';

      $sql = "SELECT
  UPPER(ug.user_group_name) AS user_sub_group_name,
  'SERVERS / EQUIPMENTS' AS type,
  st.server_type_name,
  IF(ISNULL(igf_ser.igf_server_release_at), 'NOT_RELEASED', 'RELEASED') AS server_released_status,
  COUNT(UPPER(ug.user_group_name)) AS count
FROM
  idc_igf_server AS igf_ser
  LEFT JOIN
  idc_server_type AS st ON  igf_ser.igf_server_type_id = st.server_type_id
  LEFT JOIN
  idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
  LEFT JOIN
  idc_request AS r ON igf.req_id = r.req_id
  LEFT JOIN
  idc_user_group AS ug ON r.req_group_sub_id = ug.user_group_id
WHERE
  1
  AND
  igf.igf_deleted = '0'
GROUP BY
  UPPER(ug.user_group_name),
  type,
  st.server_type_name,
  server_released_status
UNION
SELECT
  UPPER(ug.user_group_name) AS user_sub_group_name,
  IF(
      TRIM(LOWER(igf_ser.igf_server_fc_hba_port)) NOT IN ('', 'na', '0')
      ||
      TRIM(UPPER(igf_ser.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS') ,
      'SAN',
      TRIM(UPPER(igf_ser.igf_server_storage_ext_type))
    ) AS type,
  st.server_type_name,
  IF(ISNULL(igf_ser.igf_server_release_at), 'NOT_RELEASED', 'RELEASED') AS server_released_status,
  COUNT(UPPER(ug.user_group_name)) AS count
FROM
  idc_igf_server AS igf_ser
  LEFT JOIN
  idc_server_type AS st ON  igf_ser.igf_server_type_id = st.server_type_id
  LEFT JOIN
  idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
  LEFT JOIN
  idc_request AS r ON igf.req_id = r.req_id
  LEFT JOIN
  idc_user_group AS ug ON r.req_group_sub_id = ug.user_group_id
WHERE
  1
  AND
  igf.igf_deleted = '0'
  AND
  (UPPER(igf_ser.igf_server_type) <> 'APPLIANCE')
  AND
  (
    (
      (
        TRIM(UPPER(igf_ser.igf_server_fc_hba_port)) > '0'
      )
      AND
      (
        TRIM(UPPER(igf_ser.igf_server_fc_hba_port)) != 'NA'
      )
      AND
      (
        TRIM(UPPER(igf_ser.igf_server_storage_ext_type)) NOT IN ('DAS', 'NAS')
      )
    )
    OR
    TRIM(UPPER(igf_ser.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS')
  )
GROUP BY
  UPPER(ug.user_group_name),
  type,
  st.server_type_name,
  server_released_status
UNION
SELECT
  UPPER(ug.user_group_name) AS user_sub_group_name,
  IF((UPPER(igf_ser.igf_server_ha_cluster) = 'YES' ), 'CLUSTER', '') AS type,
  st.server_type_name,
  IF(ISNULL(igf_ser.igf_server_release_at), 'NOT_RELEASED', 'RELEASED') AS server_released_status,
  COUNT(UPPER(ug.user_group_name)) AS count
FROM
  idc_igf_server AS igf_ser
  LEFT JOIN
  idc_server_type AS st ON  igf_ser.igf_server_type_id = st.server_type_id
  LEFT JOIN
  idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
  LEFT JOIN
  idc_request AS r ON igf.req_id = r.req_id
  LEFT JOIN
  idc_user_group AS ug ON r.req_group_sub_id = ug.user_group_id
WHERE
  1
  AND
  igf.igf_deleted = '0'
  AND
  (UPPER(igf_ser.igf_server_type) <> 'APPLIANCE')
  AND
  TRIM(LOWER(igf_ser.igf_server_ha_cluster)) NOT IN ('', 'no', 'na')
GROUP BY
  UPPER(ug.user_group_name),
  type,
  st.server_type_name,
  server_released_status
               ".$order_by."
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


























  if(!function_exists('valid_request_id')){
    function valid_request_id($req_id) {
      $ret = FALSE;
      $sql = "SELECT req_id
              FROM idc_request
              WHERE req_id = '".mysql_real_escape_string($req_id)."'";
      $res = mysql_query($sql);
      if (mysql_num_rows($res)) {
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_req_env')) {
    function fetch_req_env($req_id) {
      $req_env = '';
      $sql_req_env = "SELECT *
              FROM idc_request_env
              WHERE req_id = '".mysql_real_escape_string($req_id)."'";
      $res_req_env = mysql_query($sql_req_env);
      if (!$res_req_env) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_env;
        die($req_msg);
      }
      else {
        while ($row_req_env = mysql_fetch_array($res_req_env)) {
           $req_env[] = $row_req_env['env_id'];
        }
      }
      return $req_env;
    }
  }


  if(!function_exists('save_req_env')) {
    function save_req_env($req_id, $req_env, $user) {
      $ret = FALSE;
      if($req_env) {
        $sql_insert_env = "INSERT INTO idc_request_env (req_id, env_id,created_by,
                          created_at) VALUE ";
        foreach ($req_env as $key => $value) {
          $sql_insert_env   .="(";
          $sql_insert_env   .="'".mysql_real_escape_string($req_id)."',";
          $sql_insert_env   .="'".mysql_real_escape_string($value)."',";
          $sql_insert_env   .="'".mysql_real_escape_string($user)."',";
          $sql_insert_env   .="NOW()";
          $sql_insert_env   .="),";
        }

        $sql_insert_env = rtrim($sql_insert_env, ',');
        $res_insert_env = mysql_query($sql_insert_env);
        if(!$res_insert_env) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_insert_env;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      else{
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('delete_req_env')) {
    function delete_req_env($req_id, $req_env = array()) {
      $ret = FALSE;
      if($req_id) {
        $sql_delete = " DELETE FROM idc_request_env WHERE ";
        $sql_delete .=" req_id='".mysql_real_escape_string($req_id)."'";
        if($req_env) {
          $sql_delete .=" AND env_id IN (";
          foreach ($req_env as $key => $value) {
            $sql_delete .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
          $sql_delete .=") ";
        }
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_req_loc')) {
    function fetch_req_loc($req_id) {
      $req_loc = '';
      $sql_req_loc = "SELECT *
              FROM idc_request_loc
              WHERE req_id = '".mysql_real_escape_string($req_id)."'";
      $res_req_loc = mysql_query($sql_req_loc);
      if (!$res_req_loc) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_loc;
        die($req_msg);
      }
      else {
        while ($row_req_loc = mysql_fetch_array($res_req_loc)) {
           $req_loc[] = $row_req_loc['loc_id'];
        }
      }
      return $req_loc;
    }
  }

  if(!function_exists('save_req_loc')) {
    function save_req_loc($req_id, $req_loc, $user) {
      $ret = FALSE;
      if($req_loc) {
        $sql_insert = "INSERT INTO idc_request_loc (req_id, loc_id,created_by,
                          created_at) VALUE ";
        foreach ($req_loc as $key => $value) {
          $sql_insert   .="(";
          $sql_insert   .="'".mysql_real_escape_string($req_id)."',";
          $sql_insert   .="'".mysql_real_escape_string($value)."',";
          $sql_insert   .="'".mysql_real_escape_string($user)."',";
          $sql_insert   .="NOW()";
          $sql_insert   .="),";
        }

        $sql_insert = rtrim($sql_insert, ',');
        $res_insert = mysql_query($sql_insert);
        if(!$res_insert) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_insert;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      else{
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('delete_req_loc')) {
    function delete_req_loc($req_id, $req_loc = array()) {
      $ret = FALSE;
      if($req_id) {
        $sql_delete = " DELETE FROM idc_request_loc WHERE ";
        $sql_delete .=" req_id='".mysql_real_escape_string($req_id)."' ";
        if($req_loc) {
          $sql_delete .=" AND loc_id IN (";
          foreach ($req_loc as $key => $value) {
            $sql_delete  .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
          $sql_delete .=")";
        }
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_req_sh')) {
    function fetch_req_sh($req_id) {
      $ret = '';
      $sql = "SELECT *
              FROM idc_request_sh
              WHERE req_id = '".mysql_real_escape_string($req_id)."'";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
           $ret[] = $row['sh_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('save_req_sh')) {
    function save_req_sh($req_id, $req_sh, $user) {
      $ret = FALSE;
      if($req_sh) {
        $sql_insert = "INSERT INTO idc_request_sh (req_id, sh_id,created_by,
                        created_at) VALUE ";
        foreach ($req_sh as $key => $value) {
          $sql_insert   .="(";
          $sql_insert   .="'".mysql_real_escape_string($req_id)."',";
          $sql_insert   .="'".mysql_real_escape_string($value)."',";
          $sql_insert   .="'".mysql_real_escape_string($user)."',";
          $sql_insert   .="NOW()";
          $sql_insert   .="),";
        }

        $sql_insert = rtrim($sql_insert, ',');
        $res_insert = mysql_query($sql_insert);
        if(!$res_insert) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_insert;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      else{
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('delete_req_sh')) {
    function delete_req_sh($req_id, $req_sh = array()) {
      $ret = FALSE;
      if($req_id) {
        $sql_delete = " DELETE FROM idc_request_sh WHERE ";
        $sql_delete .=" req_id='".mysql_real_escape_string($req_id)."' ";
        if($req_sh) {
          $sql_delete .=" AND sh_id IN (";
          foreach ($req_sh as $key => $value) {
            $sql_delete  .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
          $sql_delete .=")";
        }
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }






  if(!function_exists('save_request_doc')) {
    function save_request_doc($data) {
      $msg      = '';
      $ret      = FALSE;
      $doc_id   = '';
      $sql      = "INSERT INTO idc_request_doc (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }
        $sql = rtrim(trim($sql), ',');
        $sql .= " ) VALUE ( ";

        foreach ($data as $key => $value) {
          switch (strtoupper($value)) {
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


  if(!function_exists('get_request_doc')) {
    function get_request_doc($id) {
      $ret = '';
      $where = ' 1 ';
      if($id){
        $where .= " AND rd.req_doc_id='".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT
                rd.*,
                d.doc_name,
                d.doc_file_name,
                d.doc_file_path,
                dt.doc_type_id,
                dt.doc_type_name
              FROM
                idc_request_doc AS rd
                LEFT JOIN
                idc_doc AS d ON rd.doc_id = d.doc_id
                LEFT JOIN
                idc_doc_doc_type AS ddt ON d.doc_id = ddt.doc_id
                LEFT JOIN
                idc_doc_type AS dt ON ddt.doc_type_id = dt.doc_type_id
              WHERE
                ".$where."
              LIMIT 0,1
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        if(mysql_num_rows($res) == 1) {
          $ret = mysql_fetch_array($res);
        }
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_req_doc')) {
    function fetch_req_doc($id) {
      $msg = '';
      $ret = '';
      $sql = "SELECT
                rd.req_doc_id,
                d.doc_name,
                d.doc_file_name,
                d.doc_file_path,
                t.doc_type_id,
                t.doc_type_name
              FROM
                idc_request_doc AS rd
                LEFT JOIN
                idc_doc AS d ON rd.doc_id = d.doc_id
                LEFT JOIN
                idc_doc_doc_type AS ddt ON d.doc_id = ddt.doc_id
                LEFT JOIN
                idc_doc_type AS t ON ddt.doc_type_id = t.doc_type_id
              WHERE
                rd.req_id = '".mysql_real_escape_string($id)."'";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        $i = 0;
        while ($row = mysql_fetch_array($res)) {
           $ret[$i]['req_doc_id']         = $row['req_doc_id'];
           $ret[$i]['doc_type_id']        = $row['doc_type_id'];
           $ret[$i]['doc_type_name']      = $row['doc_type_name'];
           $ret[$i]['doc_name']           = $row['doc_name'];
           $ret[$i]['doc_path']           = $row['doc_file_path'];
           $i++;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('update_req_doc')) {
    function update_req_doc($req_id, $insert_id_req_doc){
      $str_sol = '';
      foreach ($insert_id_req_doc as $key => $value) {
        $str_sol .= $value.',';
      }
      $str_sol = rtrim($str_sol, ',');
      $sql_update_sol = "UPDATE idc_request_doc ";
      $sql_update_sol .="SET req_id='".mysql_real_escape_string($req_id)."'";
      $sql_update_sol .="WHERE req_doc_id IN (".mysql_real_escape_string($str_sol).")";
      $res_update_sol = mysql_query($sql_update_sol);
      if(!$res_update_sol) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_update_sol;
        die($req_msg);
      }
    }
  }

  if(!function_exists('update_req_doc_type')) {
    function update_req_doc_type($doc_id, $type_id){
      $msg = '';
      $sql = "UPDATE idc_doc_doc_type ";
      $sql .="SET doc_type_id='".mysql_real_escape_string($type_id)."'";
      $sql .="WHERE doc_id=".mysql_real_escape_string($doc_id)."";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if(!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
    }
  }

  if(!function_exists('delete_req_doc')) {
    function delete_req_doc($req_id, $req_doc = array()) {
      $ret = FALSE;
      if($req_id) {
        $sql_delete = " DELETE FROM idc_request_doc WHERE ";
        $sql_delete.=" req_id='".mysql_real_escape_string($req_id)."'";
        if($req_doc) {
          $sql_delete .=" AND req_doc_id IN (";
          foreach ($req_env as $key => $value) {
            $sql_delete  .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
          $sql_delete .=") ";
        }
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }



  if(!function_exists('fetch_req_scope')) {
    function fetch_req_scope($req_id) {
      $req_scope = '';
      $sql_req_scope = "SELECT *
                        FROM idc_request_scope
                        WHERE req_id = '".mysql_real_escape_string($req_id)."'
                        LIMIT 0, 1";
      $res_req_scope = mysql_query($sql_req_scope);
      if (!$res_req_scope) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_scope;
        die($req_msg);
      }
      else {
        $req_scope = mysql_fetch_array($res_req_scope);
      }
      return $req_scope;
    }
  }

  if(!function_exists('delete_req_scope')) {
    function delete_req_scope($req_id) {
      $ret = FALSE;
      if($req_id) {
        $sql_delete = " DELETE FROM idc_request_scope WHERE ";
        $sql_delete.=" req_id='".mysql_real_escape_string($req_id)."' ";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_update_value')) {
    function get_update_value($new, $pre){
      $new = ($new == 1) ? 1 : 0;
      $pre = ($pre == 1) ? 1 : 0;
      $ret = '';
      if( ($new == 0 && $pre == 0) || ($new == 1 && $pre == 1) ){
        $ret = '';
      }

      if($new == 0 && $pre == 1){
        $ret = 'NULL';
      }

      if($new == 1 && $pre == 0){
        $ret = date('Y-m-d');
      }
      return $ret;
    }
  }


  if(!function_exists('get_igf_budget')) {
    function get_igf_budget($igf_id) {
      $ret    = '';
      $where  = ' 1 ';
      if($igf_id) {
        $where .= " AND ib.igf_id='".mysql_real_escape_string($igf_id)."' ";
      }
      $sql = "SELECT ib.*
              FROM
                idc_igf_budget AS ib
              WHERE ".$where."
              ORDER BY igf_budget_id DESC
              LIMIT 0,1
              ";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $ret = mysql_fetch_array($res);
      }
      return $ret;
    }
  }

  if(!function_exists('save_igf_budget')) {
    function save_igf_budget($igf_id, $data, $user = '') {
      $ret = '';
      $sql  = "INSERT INTO idc_igf_budget ( ";
      $sql  .= "igf_id, igf_budget_fund_center, igf_budget_gl, igf_budget_wbs, ";
      $sql  .= "created_by, created_at ) VALUE ( ";
      $sql  .= "'".mysql_real_escape_string($igf_id)."', ";
      $sql  .= "'".mysql_real_escape_string($data['FUND CENTRE'])."', ";
      $sql  .= "'".mysql_real_escape_string($data['GL'])."', ";
      $sql  .= "'".mysql_real_escape_string($data['WBS'])."', ";
      $sql  .= "'".mysql_real_escape_string($user)."', ";
      $sql  .= "NOW() )";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg = 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $ret = mysql_insert_id();
      }
      return $ret;
    }
  }

  if(!function_exists('delete_igf_budget')) {
    function delete_igf_budget($igf_id) {
      $ret = FALSE;
      if($igf_id) {
        $sql_delete = " DELETE FROM idc_igf_budget WHERE ";
        $sql_delete .=" igf_id IN (";
        if(is_array($igf_id)) {
          foreach ($igf_id as $key => $value) {
            $sql_delete .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
        }
        else {
          $sql_delete .="'".mysql_real_escape_string($igf_id)."',";
        }
        $sql_delete .=") ";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_contact_type_id')) {
    function get_contact_type_id($key) {
      $ret = '';
      if($key){
        $sql= " SELECT igf_contact_type_id
                FROM idc_igf_contact_type
                WHERE igf_contact_type_key = '".mysql_real_escape_string($key)."'
                LIMIT 0,1
                ";
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          $row = mysql_fetch_array($res);
          if($row) {
            $ret = $row['igf_contact_type_id'];
          }
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_igf_contact_by_type_id')) {
    function get_igf_contact_by_type_id($igf_id, $type_id = NULL) {
      $ret = '';

      if($igf_id) {
        $where = ' 1 ';
        $limit = '';
        $where .= " AND igf_id='".mysql_real_escape_string($igf_id)."'  ";

        if($type_id){
          $where .= " AND igf_contact_type_id='".mysql_real_escape_string($type_id)."'  ";
          $limit = 'LIMIT 0,1';
        }

        $sql= " SELECT *
                FROM
                  idc_igf_contact
                WHERE
                ".$where."
                ".$limit." ";
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          if($type_id){
            $ret = mysql_fetch_array($res);
          }
          else {
            while ($row = mysql_fetch_array($res)) {
               $ret[] = $row;
            }
          }
        }
      }
      return $ret;
    }
  }

  if(!function_exists('save_igf_contact')) {
    function save_igf_contact($igf_id, $contact, $key = 'other', $user = '') {
      // Get contact type id
      $contact_type_id = get_contact_type_id($key);
      $ret = '';
      $sql_ins_igf_contact   = "INSERT INTO idc_igf_contact (";
      $sql_ins_igf_contact   .= "igf_id, igf_contact_type_id, igf_contact_name, ";
      $sql_ins_igf_contact   .= "igf_contact_mobile, igf_contact_email, ";
      $sql_ins_igf_contact   .= "created_by, created_at ) VALUE (";
      $sql_ins_igf_contact   .= "'".mysql_real_escape_string($igf_id)."', ";
      $sql_ins_igf_contact   .= "'".mysql_real_escape_string($contact_type_id)."', ";
      $sql_ins_igf_contact   .= "'".mysql_real_escape_string($contact['name'])."', ";
      $sql_ins_igf_contact   .= "'".mysql_real_escape_string($contact['mobile'])."', ";
      $sql_ins_igf_contact   .= "'".mysql_real_escape_string($contact['email'])."', ";
      $sql_ins_igf_contact   .= "'".mysql_real_escape_string($user)."', ";
      $sql_ins_igf_contact   .= "NOW() )";
      //echo $sql_ins_igf_contact.'<br />';
      $res_ins_igf_contact = mysql_query($sql_ins_igf_contact);
      if (!$res_ins_igf_contact) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_ins_igf_contact;
        die($req_msg);
      }
      else {
        $ret = mysql_insert_id();
      }
      return $ret;
    }
  }

  if(!function_exists('delete_igf_contact')) {
    function delete_igf_contact($igf_id) {
      $ret = FALSE;
      if($igf_id) {
        $sql_delete = " DELETE FROM idc_igf_contact WHERE ";
        $sql_delete .=" igf_id IN (";
        if(is_array($igf_id)) {
          foreach ($igf_id as $key => $value) {
            $sql_delete .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
        }
        else {
          $sql_delete .="'".mysql_real_escape_string($igf_id)."',";
        }
        $sql_delete .=") ";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('get_igf_equipment')) {
    function get_igf_equipment($igf_id) {
      $ret    = '';
      $where  = ' 1 ';
      if($igf_id) {
        $where .= " AND ie.igf_id='".mysql_real_escape_string($igf_id)."' ";
      }
      $sql = "SELECT ie.*, l.loc_name AS igf_eqpt_loc_name
              FROM
                idc_igf_equipment AS ie
                LEFT JOIN
                idc_location AS l ON ie.igf_eqpt_loc_id = l.loc_id
              WHERE ".$where."
              ORDER BY igf_eqpt_id DESC
              ";
      //echo 'SQL '.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('delete_igf_equipment')) {
    function delete_igf_equipment($igf_id) {
      $ret = FALSE;
      if($igf_id) {
        $sql_delete = " DELETE FROM idc_igf_equipment WHERE ";
        $sql_delete .=" igf_id IN (";
        if(is_array($igf_id)) {
          foreach ($igf_id as $key => $value) {
            $sql_delete .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
        }
        else {
          $sql_delete .="'".mysql_real_escape_string($igf_id)."',";
        }
        $sql_delete .=") ";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('count_igf_server_released_by_req_id')) {
    function count_igf_server_released_by_req_id($req_id, $type = '', $igf_id = '') {
      $ret    = '';
      $where  = ' 1 ';
      if($req_id) {
        $where .= " AND i.req_id='".mysql_real_escape_string($req_id)."' ";
      }
      if($type) {
        switch ($type) {
          case 3:
          case '3':
            $where .= " AND iser.igf_server_type_id IN (3,5,6,30,41,42,43,44,7,8,9,10,11,12,51) ";
            # code...
            break;
          default:
            $where .= " AND iser.igf_server_type_id='".mysql_real_escape_string($type)."' ";
            break;
        }

      }
      if($igf_id) {
        $where .= " AND isrs.igf_id='".mysql_real_escape_string($igf_id)."' ";
      }
      $sql =  " SELECT COUNT(isrs.igf_server_id) AS server_count ";
      $sql .= " FROM
                  idc_igf_server_release_server AS isrs
                  LEFT JOIN
                  idc_igf_server AS iser ON isrs.igf_server_id = iser.igf_server_id
                  LEFT JOIN
                  idc_igf AS i ON iser.igf_id = i.igf_id
                ";
      $sql .= " WHERE ".$where."  ";
      $sql .= " LIMIT 0,1";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
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

  if(!function_exists('delete_igf_server_release')) {
    function delete_igf_server_release($igf_id) {
      $msg = '';
      $ret = FALSE;
      if($igf_id) {
        $sql  = " DELETE FROM idc_igf_server_release WHERE ";
        $sql .=" igf_id IN (";
        if(is_array($igf_id)) {
          foreach ($igf_id as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql  = rtrim($sql, ',');
        }
        else {
          $sql .="'".mysql_real_escape_string($igf_id)."',";
        }
        $sql .=") ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('req_igf_has_ext_storage')) {
    function req_igf_has_ext_storage($id) {
      $ret    = '';
      $where  = ' 1 ';
      if($id) {
        $where .= " AND igf.req_id = '".mysql_real_escape_string($id)."'";
      }
      $sql = "SELECT DISTINCT iser.igf_server_storage_ext_type
              FROM
                idc_igf_server AS iser
                LEFT JOIN
                idc_igf AS igf ON iser.igf_id = igf.igf_id
              WHERE ".$where;
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ($row = mysql_fetch_array($res)) {
          switch (strtoupper($row['igf_server_storage_ext_type'])) {
            case 'NA':
              break;
            default:
              $ret = $row['igf_server_storage_ext_type'].',';
              break;
          }
        }
        $ret = rtrim(trim($ret), ',');
      }
      return $ret;
    }
  }

/*
SELECT iser.igf_server_type_id, iser.igf_server_storage_ext_type, count(*) AS node_count
FROM
  idc_igf_server AS iser
  LEFT JOIN
  idc_igf AS igf ON iser.igf_id = igf.igf_id
WHERE
  1
  AND
    iser.igf_server_storage_ext_type != 'NA'
  AND
    igf.igf_deleted = '0'
  AND
    igf.req_id = 'REQ15-0229'
GROUP BY iser.igf_server_type_id, iser.igf_server_storage_ext_type;


SELECT
  iser.igf_server_type_id,
  IF(
      TRIM(LOWER(iser.igf_server_fc_hba_port)) NOT IN ('', 'na', '0')
      ||
      TRIM(UPPER(iser.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS') ,
      'SAN',
      TRIM(UPPER(iser.igf_server_storage_ext_type))
    ) AS server_storage_type,
  iser.igf_server_type,
  count(*) AS node_count
FROM
  idc_igf_server AS iser
  LEFT JOIN
  idc_igf AS igf ON iser.igf_id = igf.igf_id
WHERE
  1
  AND
  igf.igf_deleted = '0'
  AND
  igf.req_id = 'REQ15-0004'
  AND
  AND TRIM(LOWER(server_storage_type)) NOT IN ('', 'na')
GROUP BY
  iser.igf_server_type_id, server_storage_type

*/
  if(!function_exists('req_igf_storage_node_count')) {
    function req_igf_storage_node_count($id) {
      //echo __FUNCTION__.'()<br />';
      $ret    = '';
      $ret['DAS']['PHYSICAL'] = 0;
      $ret['DAS']['VIRTUAL']  = 0;
      $ret['NAS']['PHYSICAL'] = 0;
      $ret['NAS']['VIRTUAL']  = 0;
      $ret['SAN']['PHYSICAL'] = 0;
      $ret['SAN']['VIRTUAL']  = 0;

      $where  = ' 1 ';
      $where .= " AND igf.igf_deleted = '0'";
      if($id) {
        $where .= " AND igf.req_id = '".mysql_real_escape_string($id)."'";
      }
      $sql = "SELECT
                iser.igf_server_type_id,
                IF(
                    TRIM(LOWER(iser.igf_server_fc_hba_port)) NOT IN ('', 'na', '0')
                    ||
                    TRIM(UPPER(iser.igf_server_storage_ext_type)) IN ('SAN', 'SAN + NAS') ,
                    'SAN',
                    TRIM(UPPER(iser.igf_server_storage_ext_type))
                  ) AS server_storage_type,
                iser.igf_server_type,
                count(*) AS node_count
              FROM
                idc_igf_server AS iser
                LEFT JOIN
                idc_igf AS igf ON iser.igf_id = igf.igf_id
              WHERE ".$where."
              GROUP BY
                iser.igf_server_type_id, server_storage_type
             ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ($row = mysql_fetch_array($res)) {
          switch($row['igf_server_type_id']){
            case '3':
              switch(strtoupper($row['server_storage_type'])){
                case 'DAS':
                  $ret['DAS']['PHYSICAL'] = $ret['DAS']['PHYSICAL'] + $row['node_count'];
                  break;
                case 'NAS':
                  $ret['NAS']['PHYSICAL'] = $ret['NAS']['PHYSICAL'] + $row['node_count'];
                  break;
                case 'SAN':
                case 'SAN + NAS':
                  $ret['SAN']['PHYSICAL'] = $ret['SAN']['PHYSICAL'] + $row['node_count'];
                  break;
                default:
              }
              break;
            case '4':
              switch(strtoupper($row['server_storage_type'])){
                case 'DAS':
                  $ret['DAS']['VIRTUAL'] = $ret['DAS']['VIRTUAL'] + $row['node_count'];
                  break;
                case 'NAS':
                  $ret['NAS']['VIRTUAL'] = $ret['NAS']['VIRTUAL'] + $row['node_count'];
                  break;
                case 'SAN':
                case 'SAN + NAS':
                  $ret['SAN']['VIRTUAL'] = $ret['SAN']['VIRTUAL'] + $row['node_count'];
                  break;
                default:
              }
              break;
            default:
          }
        }
      }
      return $ret;
    }
  }



  if(!function_exists('req_igf_has_ha_cluster')){
    function req_igf_has_ha_cluster($id) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND igf.igf_deleted = '0'";
      if($id) {
        $where .= " AND igf.req_id = '".mysql_real_escape_string($id)."'";
      }
      $sql = "SELECT
                DISTINCT iser.igf_server_ha_cluster
              FROM
                idc_igf_server AS iser
                LEFT JOIN
                idc_igf AS igf ON iser.igf_id = igf.igf_id
              WHERE ".$where;
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ($row = mysql_fetch_array($res)) {
          switch (strtoupper($row['igf_server_ha_cluster'])) {
            case 'NO':
            case 'NA':
              break;
            default:
              $ret = $row['igf_server_ha_cluster'].',';
              break;
          }
        }
        $ret = rtrim(trim($ret), ',');
      }
      return $ret;
    }
  }
/*
SELECT
  iser.igf_server_type_id,
  iser.igf_server_ha_cluster,
  count(*) AS node_count
FROM
  idc_igf_server AS iser
  LEFT JOIN
  idc_igf AS igf ON iser.igf_id = igf.igf_id
WHERE
  1
  AND
  igf.igf_deleted = '0'
  AND
  TRIM(LOWER(iser.igf_server_ha_cluster)) NOT IN ('', 'no', 'na')
  AND
  igf.req_id = 'REQ15-0228'
GROUP BY
  iser.igf_server_type_id,
  iser.igf_server_ha_cluster

*/
  if(!function_exists('req_igf_ha_cluster_node_count')){
    function req_igf_ha_cluster_node_count($id) {
      //echo __FUNCTION__.'()<br />';
      $ret    = '';
      $ret['PHYSICAL'] = 0;
      $ret['VIRTUAL']  = 0;
      $where  = ' 1 ';
      $where .= " AND igf.igf_deleted = '0'";
      $where .= " AND TRIM(LOWER(iser.igf_server_ha_cluster)) NOT IN ('', 'no', 'na')";
      if($id) {
        $where .= " AND igf.req_id = '".mysql_real_escape_string($id)."'";
      }
      $sql = "SELECT
                iser.igf_server_type_id,
                iser.igf_server_ha_cluster,
                count(*) AS node_count
              FROM
                idc_igf_server AS iser
                LEFT JOIN
                idc_igf AS igf ON iser.igf_id = igf.igf_id
              WHERE ".$where."
              GROUP BY
                iser.igf_server_type_id,
                iser.igf_server_ha_cluster
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ($row = mysql_fetch_array($res)) {
          switch($row['igf_server_type_id']){
            case '3':
              switch(strtoupper($row['igf_server_ha_cluster'])) {
                case 'Y':
                case 'YES':
                  $ret['PHYSICAL'] = $ret['PHYSICAL'] + $row['node_count'];
                  break;
                default:
              }
              break;
            case '4':
              switch(strtoupper($row['igf_server_ha_cluster'])){
                case 'Y':
                case 'YES':
                  $ret['VIRTUAL'] = $ret['VIRTUAL'] + $row['node_count'];
                  break;
                default:
              }
              break;
            default:
          }
        }
      }
      return $ret;
    }
  }


  if(!function_exists('delete_igf_server')) {
    function delete_igf_server($igf_id) {
      $ret = FALSE;
      if($igf_id) {
        // Delete IGF Server Releases
        delete_igf_server_release($igf_id);

        $sql_delete = " DELETE FROM idc_igf_server WHERE ";
        $sql_delete .=" igf_id IN (";
        if(is_array($igf_id)) {
          foreach ($igf_id as $key => $value) {
            $sql_delete .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
        }
        else {
          $sql_delete .="'".mysql_real_escape_string($igf_id)."',";
        }
        $sql_delete .=") ";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('update_igf_server_misc')) {
    function update_igf_server_misc($id, $value = '') {
      $ret = FALSE;
      if($id) {
        $sql = " UPDATE idc_igf_server SET ";
        $sql .=" igf_server_misc ='".mysql_real_escape_string($value)."' ";
        $sql .=" WHERE ";
        $sql .=" igf_server_id ='".mysql_real_escape_string($id)."' ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('check_igf_server_released_status')){
    function check_igf_server_released_status($id) {
      $ret = FALSE;
      $sql = "SELECT *
              FROM idc_igf_server_release_server
              WHERE igf_server_id = '".mysql_real_escape_string($id)."'";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if (mysql_num_rows($res)) {
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('update_igf_server_checked')) {
    function update_igf_server_checked($data, $status = 1) {
      $ret = FALSE;
      if($data) {
        $sql = " UPDATE idc_igf_server SET ";
        $sql .=" igf_server_checked ='".mysql_real_escape_string($status)."' ";
        $sql .=" WHERE ";
        if(is_array($data)) {
          $sql .=" igf_server_id IN (";
          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql = rtrim($sql, ',');
          $sql .=") ";
        }
        else {
          $sql .=" igf_id ='".mysql_real_escape_string($data)."' ";
        }
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('update_igf_server_released')) {
    function update_igf_server_released($data, $uname, $release_at = 'NOW') {
      $ret = FALSE;
      if($data) {
        $sql = " UPDATE idc_igf_server SET ";
        $sql .=" igf_server_release_by ='".mysql_real_escape_string($uname)."', ";
        if($release_at == 'NOW') {
          $sql .=" igf_server_release_at = NOW()";
        }
        else {
          $sql .=" igf_server_release_at = '".mysql_real_escape_string($release_at)."' ";
        }

        $sql .=" WHERE ";
        if(is_array($data)) {
          $sql .=" igf_server_id IN (";
          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."',";
          }
          $sql = rtrim($sql, ',');
          $sql .=") ";
        }
        else {
          $sql .=" igf_id ='".mysql_real_escape_string($data)."' ";
        }
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('save_igf_server_release')) {
    function save_igf_server_release($igf_id, $release_by, $release_at = 'NOW') {
      $ret = FALSE;
      $msg = '';
      $sql =  " INSERT INTO idc_igf_server_release (igf_id, release_by, release_at) ";
      $sql .= " VALUE (";
      $sql .= " '".mysql_real_escape_string($igf_id)."',";
      $sql .= " '".mysql_real_escape_string($release_by)."',";
      if($release_at == 'NOW') {
        $sql .=" NOW()";
      }
      else {
        $sql .=" '".mysql_real_escape_string($release_at)."' ";
      }
      $sql .= " ) ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if(!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        $ret = mysql_insert_id();
      }
      return $ret;
    }
  }

  if(!function_exists('save_igf_server_release_server')) {
    function save_igf_server_release_server($server_release_id, $server_id, $release_by, $release_at = 'NOW') {
      $ret = FALSE;
      $msg = '';
      $sql =  " INSERT INTO idc_igf_server_release_server (igf_server_release_id, igf_server_id, created_by, created_at) ";
      $sql .= " VALUE (";
      $sql .= " '".mysql_real_escape_string($server_release_id)."',";
      $sql .= " '".mysql_real_escape_string($server_id)."',";
      $sql .= " '".mysql_real_escape_string($release_by)."',";
      if($release_at == 'NOW') {
        $sql .=" NOW()";
      }
      else {
        $sql .=" '".mysql_real_escape_string($release_at)."' ";
      }
      $sql .= " ) ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if(!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        $ret = mysql_insert_id();
      }
      return $ret;
    }
  }

  if(!function_exists('get_igf_last_released')) {
    function get_igf_last_released($id) {
      $msg = '';
      $ret = NULL;
      if($id) {
        $sql= " SELECT
                  isr.*
                FROM
                  idc_igf_server_release AS isr
                WHERE
                 isr.igf_id = '".mysql_real_escape_string($id)."'
                ORDER BY isr.igf_server_release_id DESC
                LIMIT 0,1
                ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = mysql_fetch_array($res);
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_igf_server_released_history')) {
    function get_igf_server_released_history($id) {
      $ret = NULL;
      if($id) {
        $sql= " SELECT
                  iis.igf_server_type_id, isrs.igf_server_release_id, isr.release_at
                FROM
                  idc_igf_server_release_server AS isrs
                  LEFT JOIN
                  idc_igf_server_release AS isr ON isrs.igf_server_release_id = isr.igf_server_release_id
                  LEFT JOIN
                  idc_igf_server AS iis ON isrs.igf_server_id = iis.igf_server_id
                WHERE
                 isr.igf_id = '".mysql_real_escape_string($id)."'
                 ORDER BY isr.igf_server_release_id
                ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          $i = 0;
          $date = '';
          $total  = 0;
          $phy    = 0;
          $vir    = 0;
          while ($row = mysql_fetch_array($res)) {
            if($date == '') {
              $date = $row['igf_server_release_id'];
              $ret[$i]['date']    = $row['release_at'];
              $ret[$i]['total']   = 0;
              $ret[$i]['physical']= 0;
              $ret[$i]['virtual'] = 0;
            }
            if($row['igf_server_release_id'] == $date) {
              //echo $row['igf_server_release_id'].' == '.$date.'<br />';
              $ret[$i]['total'] = $ret[$i]['total'] + 1;
              // 3 Phyical
              //echo $row['igf_server_type_id'].' == 3<br />';
              if($row['igf_server_type_id'] == 3 ){
                $ret[$i]['physical'] = $ret[$i]['physical'] + 1;
              }
              else {
                $ret[$i]['virtual'] = $ret[$i]['virtual'] + 1;
              }
            }
            else {
              //echo 'CHANGE <br />';
              $date = $row['igf_server_release_id'];
              $total  = 0;
              $phy    = 0;
              $vir    = 0;
              $i++;
              $ret[$i]['date']    = $row['release_at'];
              $ret[$i]['total']   = 1;
              $ret[$i]['physical']= 0;
              $ret[$i]['virtual'] = 0;

              // 3 Phyical
              if($row['igf_server_type_id'] == 3 ){
                $ret[$i]['physical'] = $ret[$i]['physical'] + 1;
              }
              else {
                $ret[$i]['virtual'] = $ret[$i]['virtual'] + 1;
              }
            }
          }

        }
      }
      return $ret;
    }
  }

  if(!function_exists('req_igf_server_release_count')){
    function req_igf_server_release_count($id) {
     //echo __FUNCTION__.'()<br />';
      $ret    = '';
      $ret['PHYSICAL'] = 0;
      $ret['VIRTUAL']  = 0;
      $where  = ' 1 ';
      $where .= " AND igf.igf_deleted = '0'";
      if($id) {
        $where .= " AND igf.req_id = '".mysql_real_escape_string($id)."'";
      }
      $sql = "SELECT
                iser.igf_server_type_id,
                count(*) AS released_server_count
              FROM
                idc_igf_server_release_server AS isrs
                LEFT JOIN
                idc_igf_server AS iser ON isrs.igf_server_id = iser.igf_server_id
                LEFT JOIN
                idc_igf AS igf ON iser.igf_id = igf.igf_id
              WHERE ".$where."
              GROUP BY
                iser.igf_server_type_id
              ";
     //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ($row = mysql_fetch_array($res)) {
          switch($row['igf_server_type_id']){
            case '3':
              $ret['PHYSICAL'] = $ret['PHYSICAL'] + $row['released_server_count'];
              break;
            case '4':
              $ret['VIRTUAL'] = $ret['VIRTUAL'] + $row['released_server_count'];
              break;
            default:
          }
        }
      }
      return $ret;
    }
  }



  if(!function_exists('get_igf_server_released_history_temp')) {
    function get_igf_server_released_history_temp($id) {
      $ret = NULL;
      if($id) {
        $sql= " SELECT
                  igf_server_id, igf_id, igf_server_type_id,
                  igf_server_checked, igf_server_release_by,
                  igf_server_release_at,
                  DATE_FORMAT(igf_server_release_at, '%Y-%m-%d') AS igf_server_release_date
                FROM
                  idc_igf_server
                WHERE
                  igf_id = '".mysql_real_escape_string($id)."'
                  AND
                  igf_server_release_at IS NOT NULL
                ORDER BY igf_server_release_at ASC
                ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          $i = 0;
          $date = '';
          $total  = 0;
          $phy    = 0;
          $vir    = 0;
          while ($row = mysql_fetch_array($res)) {
            if($date == '') {
              $date = $row['igf_server_release_date'];
              $ret[$i]['total']   = 0;
              $ret[$i]['physical']= 0;
              $ret[$i]['virtual'] = 0;
            }
            if($row['igf_server_release_date'] == $date) {
              //echo $row['igf_server_release_date'].' == '.$date.'<br />';
              $ret[$i]['total'] = $ret[$i]['total'] + 1;
              // 3 Phyical
              //echo $row['igf_server_type_id'].' == 3<br />';
              if($row['igf_server_type_id'] == 3 ){
                $ret[$i]['physical'] = $ret[$i]['physical'] + 1;
              }
              else {
                $ret[$i]['virtual'] = $ret[$i]['virtual'] + 1;
              }
            }
            else {
              //echo 'CHANGE <br />';
              $date = $row['igf_server_release_date'];
              $total  = 0;
              $phy    = 0;
              $vir    = 0;
              $i++;
              $ret[$i]['total']   = 1;
              $ret[$i]['physical']= 0;
              $ret[$i]['virtual'] = 0;

              // 3 Phyical
              if($row['igf_server_type_id'] == 3 ){
                $ret[$i]['physical'] = $ret[$i]['physical'] + 1;
              }
              else {
                $ret[$i]['virtual'] = $ret[$i]['virtual'] + 1;
              }
            }
          }

        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_igf_software')) {
    function get_igf_software($igf_id) {
      $ret    = '';
      $where  = ' 1 ';
      if($igf_id) {
        $where .= " AND isw.igf_id='".mysql_real_escape_string($igf_id)."' ";
      }
      $sql = "SELECT isw.*
              FROM
                idc_igf_software AS isw
              WHERE ".$where."
              ORDER BY isw.igf_sw_id DESC
              ";
      //echo 'SQL '.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('delete_igf_software')) {
    function delete_igf_software($igf_id) {
      $ret = FALSE;
      if($igf_id) {
        $sql_delete = " DELETE FROM idc_igf_software WHERE ";
        $sql_delete .=" igf_id IN (";
        if(is_array($igf_id)) {
          foreach ($igf_id as $key => $value) {
            $sql_delete .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
        }
        else {
          $sql_delete .="'".mysql_real_escape_string($igf_id)."',";
        }
        $sql_delete .=") ";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_igf_patching')) {
    function get_igf_patching($igf_id) {
      $ret    = '';
      $where  = ' 1 ';
      if($igf_id) {
        $where .= " AND ip.igf_id='".mysql_real_escape_string($igf_id)."' ";
      }
      $sql = "SELECT ip.*
              FROM
                idc_igf_patching AS ip
              WHERE ".$where."
              ";
      //echo 'SQL '.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

//idc_igf_server
  if(!function_exists('get_igf_last_modified')) {
    function get_igf_last_modified($req_id, $igf_id = '') {
      $msg = '';
      $ret = NULL;
      $where = ' 1 ';
      if($req_id) {
        $where .= " AND i.req_id='".mysql_real_escape_string($req_id)."' ";

        if($igf_id){
          $where .= " AND i.igf_id='".mysql_real_escape_string($igf_id)."' ";
        }

        $sql= " SELECT u.user_name AS igf_last_modified_by, i_ser.updated_at
                FROM
                  idc_igf_server AS i_ser
                  LEFT JOIN
                  idc_igf AS i ON i_ser.igf_id = i.igf_id
                  LEFT JOIN
                  idc_user AS u ON i_ser.updated_by = u.user_login
                WHERE ".$where."
                ORDER BY i_ser.updated_at DESC
                LIMIT 0,1
                ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret = mysql_fetch_array($res);
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_row_rack_id')) {
    function find_row_rack_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(rr_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT rr_id ";
      $sql .= "FROM idc_row_rack ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['rr_id'];
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_server_type_id')) {
    function find_server_type_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(server_type_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT server_type_id ";
      $sql .= "FROM idc_server_type ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['server_type_id'];
        }
        else {
          $ret = '1';
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_server_hypervisor_id')) {
    function find_server_hypervisor_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(server_hypervisor_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT server_hypervisor_id ";
      $sql .= "FROM idc_server_hypervisor ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['server_hypervisor_id'];
        }
        else {
          $ret = '1';
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_server_role_id')) {
    function find_server_role_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(server_role_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT server_role_id ";
      $sql .= "FROM idc_server_role ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['server_role_id'];
        }
        else {
          $ret = '1';
        }
      }
      return $ret;
    }
  }

  if(!function_exists('find_server_model_id')) {
    function find_server_model_id($name, $make = '') {
      $ret    = '';
      $where  = ' 1 ';
      if($make) {
        $where .= " AND server_make_id = '".mysql_real_escape_string($make)."'  ";
      }
      $where .= " AND UPPER(server_model_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT server_model_id ";
      $sql .= "FROM idc_server_model ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['server_model_id'];
        }
        else {
          $ret = '1';
        }
      }
      return $ret;
    }

  }

  if(!function_exists('find_server_cpu_type_id')) {
    function find_server_cpu_type_id($name) {
      $ret    = '';
      $where  = ' 1 ';
      $where .= " AND UPPER(server_cpu_type_name) = UPPER('".mysql_real_escape_string($name)."')";
      $sql =  "SELECT server_cpu_type_id ";
      $sql .= "FROM idc_server_cpu_type ";
      $sql .= "WHERE ".$where."  ";
      $sql .= "LIMIT 0,1";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        $row = mysql_fetch_array($res);
        if($row) {
          $ret = $row['server_cpu_type_id'];
        }
        else {
          $ret = '1';
        }
      }
      return $ret;
    }
  }


  if(!function_exists('find_patch_type_id')) {
    function find_patch_type_id($name) {
      $ret    = 1;
      if($name) {
        // First remove spaces from string
        $name   = trim(preg_replace('/\s+/', ' ', $name));
        // Second remove special character from string
        $name   = preg_replace('/[^A-Za-z0-9\-]/', '',$name);

        $where  = ' 1 ';
        $where .= " AND UPPER(REPLACE(patch_type_name, ' ', '')) = UPPER('".mysql_real_escape_string($name)."')";
        $sql =  "SELECT patch_type_id ";
        $sql .= "FROM idc_patch_type ";
        $sql .= "WHERE ".$where."  ";
        $sql .= "LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          $row = mysql_fetch_array($res);
          if($row){
            $ret = $row['patch_type_id'];
          }
        }
      }

      return $ret;
    }
  }


  if(!function_exists('find_cable_type_id')) {
    function find_cable_type_id($name) {
      $ret    = 1;
      if($name) {
        // First remove spaces from string
        $name   = trim(preg_replace('/\s+/', ' ', $name));
        // Second remove special character from string
        $name   = preg_replace('/[^A-Za-z0-9\-]/', '',$name);

        $where  = ' 1 ';
        $where .= " AND UPPER(REPLACE(cable_type_name, ' ', '')) = UPPER('".mysql_real_escape_string($name)."')";
        $sql =  "SELECT cable_type_id ";
        $sql .= "FROM idc_cable_type ";
        $sql .= "WHERE ".$where."  ";
        $sql .= "LIMIT 0,1";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          $row = mysql_fetch_array($res);
          if($row){
            $ret = $row['cable_type_id'];
          }
        }
      }

      return $ret;
    }
  }

  if(!function_exists('get_all_igf')) {
    function get_all_igf() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_igf
               ORDER BY req_id
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_req_igf')) {
    function fetch_req_igf($id) {
      $msg = '';
      $ret = '';
      $where  = ' 1 ';
      $where   .= ' AND igf.igf_deleted=0 ';
      if($id){
         $where.= " AND igf.req_id = '".mysql_real_escape_string($id)."'";
      }
      $sql = "SELECT
                igf.igf_id,
                igf.igf_name,
                d.doc_file_name AS igf_file_name,
                d.doc_file_path AS igf_file_path
              FROM
                idc_igf AS igf
                LEFT JOIN
                idc_doc AS d ON igf.igf_doc_id = d.doc_id
              WHERE
                ".$where."
              ORDER BY igf.igf_id DESC
              ";
      //echo $sql.'<br/>';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        $i = 0;
        while ($row_req_igf = mysql_fetch_array($res)) {
           $ret[$i]['igf_id']         = $row_req_igf['igf_id'];
           $ret[$i]['igf_name']       = $row_req_igf['igf_name'];
           $ret[$i]['igf_file_name']  = $row_req_igf['igf_file_name'];
           $ret[$i]['igf_file_path']  = $row_req_igf['igf_file_path'];
           $i++;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('update_req_igf_delete')) {
    function update_req_igf_delete($id, $value = '1' , $uname) {
      $ret = FALSE;
      $sql = " UPDATE idc_igf  SET ";
      $sql .=" igf_deleted    ='".mysql_real_escape_string($value)."', ";
      $sql .=" updated_by     ='".mysql_real_escape_string($uname)."', ";
      $sql .=" updated_at     =NOW() ";
      $sql .=" WHERE igf_id   ='".mysql_real_escape_string($id)."';";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
         $ret = TRUE;
      }
      return $ret;
    }
  }


  if(!function_exists('delete_req_igf')) {
    function delete_req_igf($req_id, $req_igf = array()) {
      $ret = FALSE;
      if($req_id) {
        $sql_delete   = " DELETE FROM idc_igf WHERE ";
        $sql_delete   .=" req_id='".mysql_real_escape_string($req_id)."'";
        if($req_igf) {
          $sql_delete   .=" AND igf_id IN (";
          foreach ($req_igf as $key => $value) {
            $sql_delete   .="'".mysql_real_escape_string($value)."',";
          }
          $sql_delete = rtrim($sql_delete, ',');
          $sql_delete .=") ";
        }
        else {
          // Get All IGF for this Request
          $req_igf = '';
          $req_igf_fetch = fetch_req_igf($req_id);
          if($req_igf_fetch){
            foreach ($req_igf_fetch as $key => $value) {
              # code...['igf_id']
              $req_igf[] = $value['igf_id'];
            }
          }
        }

        // First delete all igf related detail form database
        if($req_igf) {
          // Delete Request IGF Budget Details
          delete_igf_budget($req_igf);

          // Delete Request IGF Conatct Details idc_igf_contact
          delete_igf_contact($req_igf);

          // Delete Request IGF Equipment Details idc_igf_equipment
          delete_igf_equipment($req_igf);

          // Delete Request IGF Server Details idc_igf_server
          // Also delete IGF Server Release Detals idc_igf_server_release
          delete_igf_server($req_igf);

          // Delete Request IGF Software Details idc_igf_software
          delete_igf_software($req_igf);


        }

        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('save_igf_docs')) {
    function save_igf_docs($igf_doc, $req_id, $igf_keys) {
      echo __FUNCTION__.'()<br />';
      $req_msg = '';
      // Save IGF detail
      if($igf_doc) {
        $uname = strtolower($_SESSION['usr']);
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
        //echo '$igf_doc_id :'.$igf_doc_id.'<br />';

        $igf_id                 = '';
        $igf_file_name          = '';
        $igf_file_name          = $igf_doc['name'];
        //$igf_file_name          = strstr($igf_doc, '----');
        //$igf_file_name          = ltrim($igf_file_name, '----');
        $igf_doc_name           = '';
        $igf_doc_path           = $igf_doc['path'];
        $igf_req_group_name     = '';
        $igf_req_group_id       = '';
        $igf_req_sub_group_name = '';
        $igf_req_sub_group_id   = '';
        $igf_name               = '';
        $igf_data_contact       = array();
        $igf_data_budget        = array();

        // Read the uploaded IGF excel file
        $spreadsheet = new SpreadsheetReader($data_doc_file_path);
        // Fetch data form Sheet 2 => CONTACT & BUDGET INFORMATION
        $spreadsheet -> ChangeSheet(2);

        foreach ($spreadsheet as $Key => $Row) {
          if($Key == $igf_keys['contact_budget']['req_group_name']['index']) {
            $igf_req_group_name = trim($Row[2]);
            $igf_req_group_id = find_requestor_group_id($igf_req_group_name);
          }

          if($Key == $igf_keys['contact_budget']['req_sub_group_name']['index']) {
            $igf_req_sub_group_name = trim($Row[2]);
            $igf_req_sub_group_id   = find_requestor_sub_group_id($igf_req_group_id, trim($Row[2]));
          }
          if($Key == $igf_keys['contact_budget']['igf_name']['index']) {
            $igf_name = trim($Row[2]);
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
          if($Key == $igf_keys['contact_budget']['igf_contact_name_ops']['index']) {
            $igf_data_contact['contact_1']['name'] = trim($Row[2]);
            $igf_data_contact['contact_2']['name'] = trim($Row[3]);
          }
          if($Key == $igf_keys['contact_budget']['igf_contact_mobile_ops']['index']) {
            $igf_data_contact['contact_1']['mobile'] = trim($Row[2]);
            $igf_data_contact['contact_2']['mobile'] = trim($Row[3]);
          }
          if($Key == $igf_keys['contact_budget']['igf_contact_email_ops']['index']) {
            $igf_data_contact['contact_1']['email'] = trim($Row[2]);
            $igf_data_contact['contact_2']['email'] = trim($Row[3]);
          }
          if($Key == $igf_keys['contact_budget']['igf_budget_fund_center']['index']) {
            $igf_data_budget['FUND CENTRE'] = trim($Row[2]);
          }
          if($Key == $igf_keys['contact_budget']['igf_budget_gl']['index']) {
            $igf_data_budget['GL'] = trim($Row[2]);
          }
          if($Key == $igf_keys['contact_budget']['igf_budget_wbs']['index']) {
            $igf_data_budget['WBS'] = trim($Row[2]);
          }
        }

        // Save IGF file details
        $sql_ins_igf   = "INSERT INTO idc_igf (";
        $sql_ins_igf   .="req_id, req_group_name,req_group_id,";
        $sql_ins_igf   .="req_sub_group_name,  req_sub_group_id, ";
        //$sql_ins_igf   .="igf_name, igf_file_name, igf_file_path, ";
        $sql_ins_igf   .="igf_name, ";
        $sql_ins_igf   .="igf_doc_id, ";
        $sql_ins_igf   .="created_by, created_at ) VALUE (";
        $sql_ins_igf   .="'".mysql_real_escape_string($req_id)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($igf_req_group_name)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($igf_req_group_id)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($igf_req_sub_group_name)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($igf_req_sub_group_id)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($igf_name)."', ";
        //$sql_ins_igf   .="'".mysql_real_escape_string($igf_file_name)."', ";
        //$sql_ins_igf   .="'".mysql_real_escape_string($igf_doc_path)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($igf_doc_id)."', ";
        $sql_ins_igf   .="'".mysql_real_escape_string($uname)."', ";
        $sql_ins_igf   .="NOW())";
        //echo '$sql_ins_igf :'.$sql_ins_igf.'<br />';
        $res_ins_igf = mysql_query($sql_ins_igf);
        if (!$res_ins_igf) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_ins_igf;
          die($req_msg);
        }
        else {
          // Get Insert ID
          $igf_id  = mysql_insert_id();
        }
        //echo '$igf_id :'.$igf_id.'<br />';

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
          save_igf_budget($igf_id, $igf_data_budget, $uname);

          // Save Sheet 3 => SERVER DETAILS
          $spreadsheet->ChangeSheet(3);
          foreach ($spreadsheet as $Key => $Row) {
            if($Key > 1) {
              // Check if igf server line item is empty or not
              if(trim($Row[0]) != '' && trim($Row[1]) != '' && trim($Row[2]) != '' && trim($Row[3]) != '') {
                $igf_server_user_group_id     = find_requestor_group_id($Row[1]);

                $igf_server_user_sub_group_id = find_requestor_sub_group_id($igf_server_user_group_id, $Row[2]);
                $igf_server_env_id            = find_env_id($Row[3]);
                $igf_server_loc_id            = find_loc_id($Row[4]);
                $igf_server_sh_id             = find_server_hall_id($Row[5]);
                $igf_server_rr_id             = find_row_rack_id($Row[6]);
                $igf_server_type_id           = find_server_type_id($Row[11]);
                $igf_server_hypervisor_id     = find_server_hypervisor_id($Row[12]);
                $igf_server_role_id           = find_server_role_id($Row[13]);
                $igf_server_make_id           = find_server_make_id($Row[15]);
                $igf_server_model_id          = find_server_model_id($Row[16], $igf_server_make_id);
                $igf_server_cpu_type_id       = find_server_cpu_type_id($Row[17]);

                $sql  = '';
                $sql  = "INSERT INTO idc_igf_server ( ";
                $sql  .= "";
                $sql  .= "igf_id, igf_server_app, ";
                $sql  .= "igf_server_req_group_name, igf_server_req_group_id, ";
                $sql  .= "igf_server_req_sub_group_name, igf_server_req_sub_group_id,";
                $sql  .= "igf_server_env, igf_server_env_id, ";
                $sql  .= "igf_server_loc, igf_server_loc_id, ";
                $sql  .= "igf_server_server_hall, igf_server_server_hall_id, ";
                $sql  .= "igf_server_row_rack, igf_server_row_rack_id, ";

                $sql  .= "igf_server_rack_name, igf_server_rack_u, igf_server_slot_no, ";
                $sql  .= "igf_server_number, igf_server_type, igf_server_type_id, ";
                $sql  .= "igf_server_hypervisor, igf_server_hypervisor_id, ";
                $sql  .= "igf_server_role, igf_server_role_id, ";
                $sql  .= "igf_server_serial_number, ";

                $sql  .= "igf_server_make, igf_server_make_id, ";
                $sql  .= "igf_server_model, igf_server_model_id, ";
                $sql  .= "igf_server_cpu_type, igf_server_cpu_type_id, ";

                $sql  .= "igf_server_cpu_no, igf_server_cpu_cores, igf_server_ram,";

                $sql  .= "igf_server_storage_int_no, igf_server_storage_int_size,";
                $sql  .= "igf_server_storage_int_raid_config, ";

                $sql  .= "igf_server_nic_1g, igf_server_nic_10g, ";
                $sql  .= "igf_server_fc_hba_card, ";
                $sql  .= "igf_server_fc_hba_port, igf_server_fc_hba_port_speed, ";

                $sql  .= "igf_server_dl_port, igf_server_dl_type, igf_server_dl_speed, ";
                $sql  .= "igf_server_sl_port, igf_server_sl_type, igf_server_sl_speed, ";
                $sql  .= "igf_server_cl_port, igf_server_cl_type, igf_server_cl_speed, ";

                $sql  .= "igf_server_network_zone, igf_server_network_sub_zone, igf_server_load_balancer, ";
                $sql  .= "igf_server_ha_cluster, igf_server_ha_cluster_type, igf_server_ha_cluster_pair, ";

                $sql  .= "igf_server_os, igf_server_os_version, ";
                $sql  .= "igf_server_db, igf_server_db_version, ";

                $sql  .= "igf_server_storage_ext_type, igf_server_storage_ext_iops, ";
                $sql  .= "igf_server_storage_ext_array, igf_server_storage_ext_raid_config, ";

                $sql  .= "igf_server_storage_ext_p_vol_space, ";

                $sql  .= "igf_server_storage_ext_s_vol, igf_server_storage_ext_s_vol_space,";

                $sql  .= "igf_server_storage_int_fs, igf_server_storage_ext_fs, ";

                $sql  .= "igf_server_volume_manager, ";

                $sql  .= "igf_server_kernel_parameter, igf_server_additional_package, ";
                $sql  .= "igf_server_user_id, ";
                // group_id infor is storeded in igf_server_user_id column
                //$sql  .= "igf_server_group_id, ";

                $sql  .= "igf_server_idc_support, igf_server_remark, ";

                $sql  .= "igf_server_reconfig_rm_ram, igf_server_reconfig_rm_hdd, ";
                $sql  .= "igf_server_reconfig_rm_nic, igf_server_reconfig_rm_fc_hba, ";
                $sql  .= "igf_server_reconfig_add_ram, igf_server_reconfig_add_hdd, ";
                $sql  .= "igf_server_reconfig_add_nic, igf_server_reconfig_add_fc_hba, ";
                $sql  .= "igf_server_hostname, ";
                $sql  .= "igf_server_console_ip, igf_server_console_ip_sm, igf_server_console_ip_gw, ";
                $sql  .= "igf_server_data_ip_1, igf_server_data_ip_2, igf_server_vip, ";

                $sql  .= "igf_server_data_ip_sm, igf_server_data_ip_gw, ";
                $sql  .= "igf_server_lb_ip, igf_server_other_ip, ";
                $sql  .= "igf_server_other_ip_sm, igf_server_other_ip_gw, igf_server_public_ip, ";

                if(isset($Row[85])){
                  $sql  .= " igf_server_misc, ";
                }

                $sql  .= "created_by, created_at ) VALUE ( ";
                $sql  .= "'".mysql_real_escape_string($igf_id)."', ";
                $sql  .= "'".mysql_real_escape_string($Row[0])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[1])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_user_group_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[2])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_user_sub_group_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[3])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_env_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[4])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_loc_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[5])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_sh_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[6])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_rr_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[7])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[8])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[9])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[10])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[11])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_type_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[12])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_hypervisor_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[13])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_role_id)."', ";
                // Server Serial Number
                $sql  .= "'".mysql_real_escape_string($Row[14])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[15])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_make_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[16])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_model_id)."', ";

                $sql  .= "'".mysql_real_escape_string($Row[17])."', ";
                $sql  .= "'".mysql_real_escape_string($igf_server_cpu_type_id)."', ";


                $sql  .= "'".mysql_real_escape_string($Row[18])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[19])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[20])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[21])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[22])."', ";
                //igf_server_storage_int_raid_config
                $sql  .= "'".mysql_real_escape_string($Row[23])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[24])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[25])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[26])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[27])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[28])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[29])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[30])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[31])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[32])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[33])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[34])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[35])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[36])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[37])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[38])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[39])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[40])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[41])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[42])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[43])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[44])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[45])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[46])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[47])."', ";


                $sql  .= "'".mysql_real_escape_string($Row[48])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[49])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[50])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[51])."', ";

                // igf_server_storage_ext_p_vol_space
                $sql  .= "'".mysql_real_escape_string($Row[52])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[53])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[54])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[55])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[56])."', ";

                //igf_server_volume_manager
                $sql  .= "'".mysql_real_escape_string($Row[57])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[58])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[59])."', ";
                //igf_server_user_id
                $sql  .= "'".mysql_real_escape_string($Row[60])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[61])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[62])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[63])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[64])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[65])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[66])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[67])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[68])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[69])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[70])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[71])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[72])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[73])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[74])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[75])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[76])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[77])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[78])."', ";


                $sql  .= "'".mysql_real_escape_string($Row[79])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[80])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[81])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[82])."', ";

                $sql  .= "'".mysql_real_escape_string($Row[83])."', ";
                $sql  .= "'".mysql_real_escape_string($Row[84])."', ";

                if(isset($Row[85])){
                  $sql  .= "'".mysql_real_escape_string($Row[85])."', ";
                }

                $sql  .= "'".mysql_real_escape_string($uname)."', ";
                $sql  .= "NOW() )";
                //echo '<br />'.$sql.'<br />';

                $res = mysql_query($sql);
                if (!$res) {
                  $req_msg = 'Invalid query: ' . mysql_error() . "\n";
                  $req_msg .= 'Whole query: ' . $sql;
                  die($req_msg);
                }
                else {
                  $ret = mysql_insert_id();
                }
              }
            }
          }

          // Save Sheet 4 => EQUIPMENT LIST
          $igf_ref_sheets_4 = array(
                                array(
                                  array('key' => 'igf_eqpt_loc', 'name' => 'LOCATION'),
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
                                ),
                              );
          $spreadsheet->ChangeSheet(4);
          foreach ($spreadsheet as $Key => $Row) {
            //var_dump($Row);
            //echo $Key.': <br />';
            if($Key ==  0) {
              // Header
              //var_dump($Row);
              foreach ($Row as $row_key => $row_value) {
                if(trim(preg_replace('/\s+/', ' ', $row_value))) {
                  //echo trim(preg_replace('/\s+/', ' ', $row_value)).'<br />';
                  foreach ($igf_ref_sheets_4[$Key] as $test_key => $test) {
                    //echo $test['name'].'<br />';
                    if($test['name'] == trim(preg_replace('/\s+/', ' ', $row_value))) {
                      $igf_ref_sheets_4_key[$igf_ref_sheets_4[$Key][$test_key]['key']] = $row_key;
                      break;
                    }
                  }
                }

                if(isset($igf_ref_sheets_4[$Key][$row_key]['name']) && $igf_ref_sheets_4[$Key][$row_key]['name'] != '' ) {
                  if($igf_ref_sheets_4[$Key][$row_key]['name'] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                     $req_msg .= 'Invalid IGF Template ('.$igf_file_name.'). EQUIPMENT LIST Sheet has missing cell '.$igf_ref_sheets_4[$Key][$row_key]['name'].'.\n';
                    break;
                  }
                  else {
                    $igf_ref_sheets_4[$Key][$row_key]['index'] = $row_key;
                    $igf_ref_sheets_4_key[$igf_ref_sheets_4[$Key][$row_key]['key']] = $row_key;
                  }
                }
              }
              if($req_msg != '') {
                break;
              }
            }
            else {
              // Fetch Data
              //var_dump($Row);
              // Check if row is empty or not
              $check_row_empty = TRUE;
              foreach ($Row as $row_key => $row_value) {
                if(trim($row_value) != ''){
                  $check_row_empty = FALSE;
                  break;
                }
              }
              if($check_row_empty == FALSE) {
                $sql  =   '';
                $sql  =   "INSERT INTO idc_igf_equipment ( ";
                $sql  .=  "";
                $sql  .=  "igf_id, ";
                foreach ($igf_ref_sheets_4_key as $key => $value) {
                  $sql  .= $key.',';
                }
                $sql  .=  "created_by, created_at ) VALUE ( ";
                $sql  .= "'".mysql_real_escape_string($igf_id)."', ";
                foreach ($igf_ref_sheets_4_key as $key => $value) {
                 $sql  .= "'".mysql_real_escape_string($Row[$value])."', ";
                }
                $sql  .= "'".mysql_real_escape_string($uname)."', ";
                $sql  .= "NOW() )";

                //echo $sql.'<br />';
                $res_igf_equi = mysql_query($sql);
                if (!$res_igf_equi) {
                  $req_msg = 'Invalid query: ' . mysql_error() . "\n";
                  $req_msg .= 'Whole query: ' . $sql;
                  die($req_msg);
                }
                else {
                  $ret_igf_equi[] = mysql_insert_id();
                }
              }
            }
          }

          // Save Sheet 5 => SOFTWARE LIST
          $igf_ref_sheets_5 = array(
                                array(
                                  array('key' => 'igf_sw_sr_no', 'name' => 'SR. NO.'),
                                  array('key' => 'igf_sw_category', 'name' => 'SOFTWARE CATEGORY (APPLICATION / WEB / DB)'),
                                  array('key' => 'igf_sw_vendor_name', 'name' => 'VENDOR NAME'),
                                  array('key' => 'igf_sw_product_name', 'name' => 'PRODUCT NAME'),
                                  array('key' => 'igf_sw_edition', 'name' => 'SOFTWARE EDITION'),
                                  array('key' => 'igf_sw_version', 'name' => 'SOFTWARE VERSION'),
                                  array('key' => 'igf_sw_base_os', 'name' => 'BASE OS'),
                                  array('key' => 'igf_sw_licence_type', 'name' => 'LICENSING TYPE (BASED ON USERS / CPU / CORE/ ANY OTHER)'),
                                  array('key' => 'igf_sw_licence_count', 'name' => '# OF LICENCES REQUIRED'),
                                  array('key' => 'igf_sw_support', 'name' => 'SOFTWARE SUPPORT REQUIRED (YES / NO)'),
                                ),
                              );
          $igf_ref_sheets_5_key = '';
          $spreadsheet->ChangeSheet(5);
          foreach ($spreadsheet as $Key => $Row) {
            //var_dump($Row);
            //echo $Key.': <br />';
            if($Key ==  0) {
              // Header
              //var_dump($Row);
              foreach ($Row as $row_key => $row_value) {
                //echo 'VALUE =>'.$row_value.'<br />';
                if(trim(preg_replace('/\s+/', ' ', $row_value))) {
                  //echo trim(preg_replace('/\s+/', ' ', $row_value)).'<br />';
                  foreach ($igf_ref_sheets_5[$Key] as $test_key => $test) {
                    //echo $test['name'].'<br />';
                    if($test['name'] == trim(preg_replace('/\s+/', ' ', $row_value))) {
                      $igf_ref_sheets_5[$igf_ref_sheets_5[$Key][$test_key]['key']] = $row_key;
                      break;
                    }
                  }
                }

                if(isset($igf_ref_sheets_5[$Key][$row_key]['name']) && $igf_ref_sheets_5[$Key][$row_key]['name'] != '' ) {
                  if($igf_ref_sheets_5[$Key][$row_key]['name'] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                     $req_msg .= 'Invalid IGF Template ('.$igf_file_name.'). SOFTWARE LIST Sheet has missing cell '.$igf_ref_sheets_5[$Key][$row_key]['name'].'.\n';
                    break;
                  }
                  else {
                    $igf_ref_sheets_5[$Key][$row_key]['index'] = $row_key;
                    $igf_ref_sheets_5_key[$igf_ref_sheets_5[$Key][$row_key]['key']] = $row_key;
                  }
                }
              }
              if($req_msg != '') {
                break;
              }
            }
            else {
              // Fetch Data
              //var_dump($Row);
              // Check if row is empty or not
              $check_row_empty = TRUE;
              foreach ($Row as $row_key => $row_value) {
                if(trim($row_value) != ''){
                  $check_row_empty = FALSE;
                  break;
                }
              }
              if($check_row_empty == FALSE) {
                $sql  =   '';
                $sql  =   "INSERT INTO idc_igf_software ( ";
                $sql  .=  "";
                $sql  .=  "igf_id, ";
                foreach ($igf_ref_sheets_5_key as $key => $value) {
                  $sql  .= $key.',';
                }
                $sql  .=  "created_by, created_at ) VALUE ( ";
                $sql  .= "'".mysql_real_escape_string($igf_id)."', ";
                foreach ($igf_ref_sheets_5_key as $key => $value) {
                 $sql  .= "'".mysql_real_escape_string($Row[$value])."', ";
                }
                $sql  .= "'".mysql_real_escape_string($uname)."', ";
                $sql  .= "NOW() )";

                //echo $sql.'<br />';
                $res_igf_sw = mysql_query($sql);
                if (!$res_igf_sw) {
                  $req_msg = 'Invalid query: ' . mysql_error() . "\n";
                  $req_msg .= 'Whole query: ' . $sql;
                  die($req_msg);
                }
                else {
                  $ret_igf_sw[] = mysql_insert_id();
                }
              }
            }
          }
          //var_dump($igf_ref_sheets_5_key);

          //var_dump($igf_keys);
          // 7.Patching Sheet
          // Save Sheet 7 => Patching Sheet
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
            $igf_ref_sheets_7[2][1]  = array('key' => 'igf_patching_cable_detail', 'name' => 'CABLE DETAILS', 'ignore_key' => TRUE);
            $igf_ref_sheets_7[2][3]  = array('key' => 'igf_patching_source_rack', 'name' => 'SOURCE RACK', 'ignore_key' => TRUE);
            $igf_ref_sheets_7[2][8]  = array('key' => 'igf_patching_destination_rack', 'name' => 'DESTINATION RACK', 'ignore_key' => TRUE);
            $igf_ref_sheets_7[3]  = array(
                                      array('key' => 'igf_patching_cable', 'name' => 'CABLE TYPE'),
                                      array('key' => 'igf_patching_cable_length', 'name' => 'LENGTH'),
                                      array('key' => 'igf_patching_src_rack', 'name' => 'SOURCE RACK'),
                                      array('key' => 'igf_patching_src_sr', 'name' => 'SERIAL NUMBER'),
                                      array('key' => 'igf_patching_src_label', 'name' => 'Label'),
                                      array('key' => 'igf_patching_src_port', 'name' => 'System / PORT'),
                                      array('key' => 'igf_patching_dst_rack', 'name' => 'DESTINATION RACK'),
                                      array('key' => 'igf_patching_dst_sr_u', 'name' => 'SERIAL NUMBER'),
                                      array('key' => 'igf_patching_dst_port', 'name' => 'Switch / PORT'),
                                      array('key' => 'igf_patching_dst_label', 'name' => 'LABEL'),
                                      array('key' => 'igf_patching_qty', 'name' => 'QTY'),
                                      array('key' => 'igf_patching_remark', 'name' => 'REMARKS'),
                                    );

            //var_dump($igf_ref_sheets_7);

            $igf_ref_sheets_7_key = '';
            $row_patch_type_text  = '';
            $patch_type_id        = '';
            $patch_type           = '';
            $ignore_patching_sh   = FALSE;
            $check_header         = FALSE;

            $spreadsheet->ChangeSheet($igf_keys['patching_sheet_key']);
            foreach ($spreadsheet as $Key => $Row) {
              //var_dump($Row);
              //echo $Key.': <br />';
              if($Key < 2 || $check_header) {
                // Header
                if($check_header){
                  $Key = 3;
                  $check_header = FALSE;
                  $igf_ref_sheets_7_key = '';
                }
                foreach ($Row as $row_key => $row_value) {
                  //echo 'VALUE =>'.$row_value.'<br />';
                  if(trim(preg_replace('/\s+/', ' ', $row_value))) {
                    //echo trim(preg_replace('/\s+/', ' ', $row_value)).'<br />';
                    foreach ($igf_ref_sheets_7[$Key] as $test_key => $test) {
                      //echo '$test[name] :'.$test['name'].'<br />';
                      if($test['name'] == trim(preg_replace('/\s+/', ' ', $row_value))) {
                        $igf_ref_sheets_7[$igf_ref_sheets_7[$Key][$test_key]['key']] = $row_key;
                        break;
                      }
                    }
                  }

                  //echo $igf_ref_sheets_7[$Key][$row_key]['name'].'<br />';
                  if(isset($igf_ref_sheets_7[$Key][$row_key]['name']) && $igf_ref_sheets_7[$Key][$row_key]['name'] != '' ) {
                    if($igf_ref_sheets_7[$Key][$row_key]['name'] != trim(preg_replace('/\s+/', ' ', $row_value))) {
                      if(isset($igf_ref_sheets_7[$Key][$row_key]['optional']) && $igf_ref_sheets_7[$Key][$row_key]['optional'] == TRUE){

                      }
                      else {
                        $req_msg .= 'Invalid IGF Template ('.$igf_file_name.'). Patching Sheet has missing cell '.$igf_ref_sheets_7[$Key][$row_key]['name'].'.\n';
                        break;
                      }
                    }
                    else {
                      $igf_ref_sheets_7[$Key][$row_key]['index'] = $row_key;
                      if(isset($igf_ref_sheets_7[$Key][$row_key]['ignore_key']) && $igf_ref_sheets_7[$Key][$row_key]['ignore_key']){

                      }
                      else {
                        $igf_ref_sheets_7_key[$igf_ref_sheets_7[$Key][$row_key]['key']] = $row_key;
                      }
                    }
                  }
                }
                if($req_msg != '') {
                  break;
                }
                //var_dump($igf_ref_sheets_7_key);
              }
              else {
                $cable_type_id      = NULL;
                $row_ignore         = FALSE;
                //var_dump($igf_ref_sheets_7_key);
                // Fetch Data
                //var_dump($Row);
                // Check if row is empty or not
                $check_row_empty  = TRUE;
                $check_row_type   = FALSE;
                foreach ($Row as $row_key => $row_value) {
                  if(trim($row_value) != ''){
                    $check_row_empty = FALSE;
                    break;
                  }
                }

                if($check_row_empty == FALSE) {
                  // Check for Patching Type
                  foreach ($Row as $row_key => $row_value) {
                    if($row_key == 0 ) {
                      if(trim($row_value) != '') {
                        switch(strtoupper(trim($row_value))){
                          case 'CABLE DETAILS':
                          case 'CABLE TYPE':
                            $check_header       = TRUE;
                            $row_ignore         = TRUE;
                            $ignore_patching_sh = TRUE;
                            break;
                          default:
                            $check_row_type       = TRUE;
                            $row_patch_type_text  = trim($row_value);
                        }// END SWITCH
                      }
                      else {
                        $check_row_type   = FALSE;
                        break;
                      }
                    }
                    else {
                      //echo 'ELSE '.trim($row_value).'<br />';
                      if(trim($row_value) != ''){
                        $check_row_type   = FALSE;
                      }
                    }
                  }
                  if($check_row_type) {
                    // Find patching type
                    $patch_type     = trim(preg_replace('/[^A-Za-z0-9\-\\s]/', '',$row_patch_type_text));
                    $patch_type_id  = find_patch_type_id($row_patch_type_text);
                  }

                  if($check_row_type == FALSE && $row_ignore == FALSE) {
                    // Find Cable Type ID
                    // igf_patching_cable
                    if(isset($igf_ref_sheets_7_key['igf_patching_cable'])) {
                      $cable_type_id = find_cable_type_id($Row[$igf_ref_sheets_7_key['igf_patching_cable']]);
                    }

                    $sql  =   '';
                    $sql  =   "INSERT INTO idc_igf_patching ( ";
                    $sql  .=  "";
                    $sql  .=  "igf_id, ";
                    $sql  .=  "igf_patching_type, ";
                    $sql  .=  "igf_patching_type_id, ";
                    $sql  .=  "igf_patching_cable_type_id, ";
                    foreach ($igf_ref_sheets_7_key as $key => $value) {
                      $sql  .= $key.',';
                    }
                    $sql  .= "created_by, created_at ) VALUE ( ";
                    $sql  .= "'".mysql_real_escape_string($igf_id)."', ";
                    $sql  .= "'".mysql_real_escape_string($patch_type)."', ";
                    $sql  .= "'".mysql_real_escape_string($patch_type_id)."', ";
                    $sql  .= "'".mysql_real_escape_string($cable_type_id)."', ";
                    foreach ($igf_ref_sheets_7_key as $key => $value) {
                      $sql  .= "'".mysql_real_escape_string($Row[$value])."', ";
                    }
                    $sql  .= "LOWER('".mysql_real_escape_string($uname)."'), ";
                    $sql  .= "NOW() )";

                    //echo $sql.'<br />';
                    $res_igf_patch = mysql_query($sql);
                    if (!$res_igf_patch) {
                      $req_msg = 'Invalid query: ' . mysql_error() . "\n";
                      $req_msg .= 'Whole query: ' . $sql;
                      die($req_msg);
                    }
                    else {
                      $ret_igf_patch[] = mysql_insert_id();
                    }
                  }
                }// END if($check_row_empty == FALSE)
              }
            }
            //var_dump($igf_ref_sheets_7_key);
          }
        // IF($igf_id) ends below
        }
      // IGF DOCS for loop ends on below line
      }
      // Save IGF detail END
    }

  }


  if(!function_exists('get_email_template_by_name')) {
    function get_email_template_by_name($name) {
      $ret = '';
      $sql = " SELECT *
               FROM idc_mail_template
               WHERE mail_template_name='".mysql_real_escape_string($name)."'
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        $ret = mysql_fetch_array($res);
      }
      return $ret;
    }
  }


  if(!function_exists('update_request_status')) {
    function update_request_status($req_id, $status = 'RFI', $status_id = NULL) {
      $ret = FALSE;
      $sql = " UPDATE idc_request  SET ";
      $sql .=" req_status     ='".mysql_real_escape_string($status)."', ";
      $sql .=" req_status_id  ='".mysql_real_escape_string($status_id)."'";
      $sql .=" WHERE req_id='".mysql_real_escape_string($req_id)."';";
      $res = mysql_query($sql);
      if($res) {
        $ret = TRUE;
      }
      return $ret;
    }
  }

  if(!function_exists('get_req_env_string')) {
    function get_req_env_string($req_id) {
      $ret = '';
      $sql = "SELECT e.env_name
              FROM idc_request_env AS re
              LEFT JOIN
              idc_environment AS e ON re.env_id = e.env_id
              WHERE re.req_id = '".mysql_real_escape_string($req_id)."'";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret .= strtoupper($row['env_name']).',';
        }
        $ret = rtrim($ret, ',');
      }
      return $ret;
    }
  }

  if(!function_exists('get_req_loc')) {
    function get_req_loc($req_id) {
      $ret = '';
      $sql = "SELECT l.*
              FROM idc_request_loc AS rl
              LEFT JOIN
              idc_location AS l ON rl.loc_id = l.loc_id
              WHERE rl.req_id = '".mysql_real_escape_string($req_id)."'";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_req_loc_string')) {
    function get_req_loc_string($req_id) {
      $ret = '';
      $sql = "SELECT l.loc_name
              FROM idc_request_loc AS rl
              LEFT JOIN
              idc_location AS l ON rl.loc_id = l.loc_id
              WHERE rl.req_id = '".mysql_real_escape_string($req_id)."'";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret .= strtoupper($row['loc_name']).',';
        }
        $ret = rtrim($ret, ',');
      }
      return $ret;
    }
  }

  if(!function_exists('get_req_sh_string')) {
    function get_req_sh_string($req_id) {
      $ret = '';
      $sql = "SELECT sh.sh_name
              FROM idc_request_sh AS rsh
              LEFT JOIN
              idc_server_hall AS sh ON rsh.sh_id = sh.sh_id
              WHERE rsh.req_id = '".mysql_real_escape_string($req_id)."'";
      $res = mysql_query($sql);
      if (!$res) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
          $ret .= strtoupper($row['sh_name']).',';
        }
        $ret = rtrim($ret, ',');
      }
      return $ret;
    }
  }

  if(!function_exists('get_req_igf_count')) {
    function get_req_igf_count($req_id, $inclued_delete = FALSE) {
      $igf_count = 0;
      // Calculate Request IGF count
      $where = ' 1 ';
      if(!$inclued_delete) {
        $where .= " AND igf_deleted='0' ";
      }

      if($req_id ) {
        $where .= " AND req_id='".mysql_real_escape_string($req_id)."' ";
      }
      $sql_count =  "SELECT COUNT(igf_id) AS count ";
      $sql_count .= "FROM idc_igf ";
      $sql_count .= "WHERE ".$where." ";
      $sql_count .= "LIMIT 0,1";
      //echo $sql_count.'<br />';
      $res_count = mysql_query($sql_count);

      if (!$res_count) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_count;
        die($req_msg);
      }
      else {
        $row_count = mysql_fetch_array($res_count);
        if($row_count) {
          $igf_count = $row_count['count'];
        }
      }
      //echo '$igf_count :'.$igf_count.'<br />';
      return $igf_count;
    }
  }

  if(!function_exists('flow_fetch_req_env')) {
    function flow_fetch_req_env($req_id) {
      $req_env = '';
      $sql_req_env = "SELECT re.*, e.*
              FROM
                idc_request_env AS re
                LEFT JOIN
                idc_environment AS e ON re.env_id = e.env_id
              WHERE re.req_id = '".mysql_real_escape_string($req_id)."'";
      //echo $sql_req_env;
      $res_req_env = mysql_query($sql_req_env);
      if (!$res_req_env) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_env;
        die($req_msg);
      }
      else {
        while ($row_req_env = mysql_fetch_array($res_req_env)) {
           $req_env[] = $row_req_env['env_name_abbrev'];
        }
      }
      return $req_env;
    }
  }

  if(!function_exists('flow_fetch_req_loc')) {
    function flow_fetch_req_loc($req_id) {
      $req_loc = '';
      $sql_req_loc = "SELECT rl.*, l.loc_name
              FROM
                idc_request_loc AS rl
                LEFT JOIN
                idc_location l ON rl.loc_id = l.loc_id
              WHERE req_id = '".mysql_real_escape_string($req_id)."'";
      $res_req_loc = mysql_query($sql_req_loc);
      if (!$res_req_loc) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_loc;
        die($req_msg);
      }
      else {
        while ($row_req_loc = mysql_fetch_array($res_req_loc)) {
           $req_loc[] = $row_req_loc['loc_name'];
        }
      }
      return $req_loc;
    }
  }

  // Fetch ALL Row Rack Detail
  if(!function_exists('get_all_row_rack')){
    function get_all_row_rack() {
      $ret = '';
      $sql = " SELECT *
               FROM idc_row_rack
               ORDER BY rr_id
              ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  // Fetch Row Rack Name By ID
  if(!function_exists('get_row_rack_name_by_id')){
    function get_row_rack_name_by_id($id) {
      $ret = '';
      $sql = " SELECT *
               FROM idc_row_rack
               WHERE rr_id='".mysql_real_escape_string($id)."'
               LIMIT 0, 1
              ";
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret = $row['rr_name'];
      }
      return $ret;
    }
  }

  // Fetch All IGF Server Details for that igf
  if(!function_exists('get_igf_server_by_igf_id')){
    function get_igf_server_by_igf_id($id) {
      $ret = '';
      $sql  = " SELECT s.*
                FROM
                idc_igf_server AS s
                WHERE s.igf_id='".mysql_real_escape_string($id)."' ";
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  // Fetch All IGF Server Details for that igf
  if(!function_exists('get_igf_server_detail_by_igf_id')){
    function get_igf_server_detail_by_igf_id($id) {
      $ret = '';
      $sql  = " SELECT
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
                WHERE i_ser.igf_id='".mysql_real_escape_string($id)."'
                ORDER BY i_ser.igf_server_id
                ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        while ( $row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('get_igf_server_detail')){
    function get_igf_server_detail($id) {
      $ret = '';
      $sql  = " SELECT
                  i_ser.*,
                  l.loc_name AS igf_server_loc_name,
                  sh.sh_name AS igf_server_server_hall_name,
                  rr.rr_name AS igf_server_row_rack_name
                FROM
                  idc_igf_server AS i_ser
                  LEFT JOIN
                  idc_location AS l ON i_ser.igf_server_loc_id = l.loc_id
                  LEFT JOIN
                  idc_server_hall AS sh ON i_ser.igf_server_server_hall_id = sh.sh_id
                  LEFT JOIN
                  idc_row_rack AS rr ON i_ser.igf_server_row_rack_id = rr.rr_id
                WHERE
                  i_ser.igf_server_id='".mysql_real_escape_string($id)."'
                ORDER BY i_ser.igf_server_id
                LIMIT 0, 1
                ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        $ret = mysql_fetch_array($res);
      }
      return $ret;
    }
  }



  // Fetch IGF Detail for that igf ID
  if(!function_exists('get_igf_detail_by_igf_id')){
    function get_igf_detail_by_igf_id($id) {
      $ret = '';
      $sql  = " SELECT
                  i.*, d.doc_name, d.doc_file_name, d.doc_file_path,
                  r.req_title, u.user_name AS igf_uploaded_by
                FROM
                  idc_igf AS i
                  LEFT JOIN
                  idc_doc AS d ON i.igf_doc_id = d.doc_id
                  LEFT JOIN
                  idc_request AS r ON i.req_id = r.req_id
                  LEFT JOIN
                  idc_user AS u ON i.created_by = u.user_login
                WHERE
                  i.igf_id='".mysql_real_escape_string($id)."'
                  LIMIT 0,1; ";
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      return $ret;
    }
  }

  if(!function_exists('determine_stage')) {
    function determine_stage($row_req){
      $phase = '';
      $ret  = '';

      $design         = '';
      $design_path    = '';
      $design_stages  = '';
      if($row_req['req_scope_design'] == '1') {
        $design = 'grey';
        if($row_req['req_scope_design_server'] == '1') {
          $design_stages['req_scope_design_server'] = 'grey';
        }
        if($row_req['req_scope_design_network'] == '1') {
          $design_stages['req_scope_design_network'] = 'grey';
        }
        if($row_req['req_scope_design_storage'] == '1') {
          $design_stages['req_scope_design_storage'] = 'grey';
        }
        if($row_req['req_scope_design_security'] == '1') {
          $design_stages['req_scope_design_security'] = 'grey';
        }
        if($row_req['req_scope_design_software'] == '1') {
          $design_stages['req_scope_design_software']  = 'grey';
        }
        if($row_req['req_scope_design_db'] == '1') {
          $design_stages['req_scope_design_db'] = 'grey';
        }

        if($design_stages) {
          foreach ($design_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $design = 'red';
              $design_stages[$key] = 'red';
            }
          }
          $design_complete_temp = TRUE;
          foreach ($design_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $design_complete_temp = FALSE;
            }
            else {
              $design_stages[$key] = 'green';
            }
          }
          if($design_complete_temp){
            $design = 'green';
          }
        }

        $phase[]  = array('phase' => 'design', 'status' => $design, 'group' => $row['user_group_name'], 'user_group_id' => $row['user_group_id']);
      }
      $design_path  = '/image/design_'.$design.'.png';


      $budget         = '';
      $budget_path    = '';
      $budget_stages  = '';
      if($row_req['req_scope_budget'] == '1') {
        $budget = 'grey';
        if($row_req['req_scope_budget_note'] == '1') {
          $budget_stages['req_scope_budget_note'] = 'grey';
        }
        if($row_req['req_scope_budget_approval'] == '1') {
          $budget_stages['req_scope_budget_approval'] = 'grey';
        }
        if($row_req['req_scope_budget_sap'] == '1') {
          $budget_stages['req_scope_budget_sap'] = 'grey';
        }
        if($budget_stages) {
          foreach ($budget_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $budget = 'red';
              $budget_stages[$key] = 'red';
            }
          }
          $budget_complete_temp = TRUE;
          foreach ($budget_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $budget_complete_temp = FALSE;
            }
            else {
              $budget_stages[$key] = 'green';
            }
          }
          if($budget_complete_temp){
            $budget = 'green';
          }
        }
        $phase[]  = array('phase' => 'budget', 'status' => $budget, 'group' => $row['user_group_name'], 'user_group_id' => $row['user_group_id']);
      }
      $budget_path  = '/image/budget_'.$budget.'.png';


      $boq         = '';
      $boq_path    = '';
      $boq_stages  = '';
      if($row_req['req_scope_boq'] == '1') {
        $boq = 'grey';
        if($row_req['req_scope_boq_server'] == '1') {
          $boq_stages['req_scope_boq_server'] = 'grey';
        }
        if($row_req['req_scope_boq_network'] == '1') {
          $boq_stages['req_scope_boq_network'] = 'grey';
        }
        if($row_req['req_scope_boq_storage'] == '1') {
          $boq_stages['req_scope_boq_storage'] = 'grey';
        }
        if($row_req['req_scope_boq_security'] == '1') {
          $boq_stages['req_scope_boq_security'] = 'grey';
        }
        if($row_req['req_scope_boq_software'] == '1') {
          $boq_stages['req_scope_boq_software'] = 'grey';
        }
        if($row_req['req_scope_boq_db'] == '1') {
          $boq_stages['req_scope_boq_db'] = 'grey';
        }

        if($boq_stages) {
          foreach ($boq_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $boq = 'red';
              $boq_stages[$key] = 'red';
            }
          }
          $boq_complete_temp = TRUE;
          foreach ($boq_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $boq_complete_temp = FALSE;
            }
            else {
              $boq_stages[$key] = 'green';
            }
          }
          if($boq_complete_temp){
            $boq = 'green';
          }
        }
        $phase[]  = array('phase' => 'boq', 'status' => $boq, 'group' => $row['user_group_name'], 'user_group_id' => $row['user_group_id']);
      }
      $boq_path  = '/image/boq_'.$boq.'.png';


      $procure         = '';
      $procure_path    = '';
      $procure_stages  = '';
      if($row_req['req_scope_procure'] == '1') {
        $procure = 'grey';
        if($row_req['req_scope_procure_server'] == '1') {
          $procure_stages['req_scope_procure_server'] = 'grey';
        }
        if($row_req['req_scope_procure_network'] == '1') {
          $procure_stages['req_scope_procure_network'] = 'grey';
        }
        if($row_req['req_scope_procure_storage'] == '1') {
          $procure_stages['req_scope_procure_storage'] = 'grey';
        }
        if($row_req['req_scope_procure_security'] == '1') {
          $procure_stages['req_scope_procure_security'] = 'grey';
        }
        if($row_req['req_scope_procure_software'] == '1') {
          $procure_stages['req_scope_procure_software'] = 'grey';
        }
        if($row_req['req_scope_procure_db'] == '1') {
          $procure_stages['req_scope_procure_db'] = 'grey';
        }
        if($row_req['req_scope_procure_service'] == '1') {
          $procure_stages['req_scope_procure_service'] = 'grey';
        }

        if($procure_stages) {
          foreach ($procure_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $procure = 'red';
              $procure_stages[$key] = 'red';
            }
          }
          $procure_complete_temp = TRUE;
          foreach ($procure_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $procure_complete_temp = FALSE;
            }
            else {
              $procure_stages[$key] = 'green';
            }
          }
          if($procure_complete_temp){
            $procure = 'green';
          }
        }

        $phase[]  = array('phase' => 'procure', 'status' => $procure, 'group' => $row['user_group_name'], 'user_group_id' => $row['user_group_id']);
      }
      $procure_path  = '/image/procure_'.$procure.'.png';

      $imp         = '';
      $imp_path    = '';
      $imp_stages  = '';
      if($row_req['req_scope_imp'] == '1') {
        $imp = 'grey';
        if($row_req['req_scope_imp_server'] == '1') {
          $imp_stages['req_scope_imp_server'] = 'grey';
        }
        if($row_req['req_scope_imp_network'] == '1') {
          $imp_stages['req_scope_imp_network'] = 'grey';
        }
        if($row_req['req_scope_imp_storage'] == '1') {
          $imp_stages['req_scope_imp_storage'] = 'grey';
        }
        if($row_req['req_scope_imp_security'] == '1') {
          $imp_stages['req_scope_imp_security'] = 'grey';
        }
        if($row_req['req_scope_imp_software'] == '1') {
          $imp_stages['req_scope_imp_software'] = 'grey';
        }
        if($row_req['req_scope_imp_db'] == '1') {
          $imp_stages['req_scope_imp_db'] = 'grey';
        }

        if($imp_stages) {
          foreach ($imp_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $imp = 'red';
              $imp_stages[$key] = 'red';
            }
          }
          $imp_complete_temp = TRUE;
          foreach ($imp_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $imp_complete_temp = FALSE;
            }
            else {
              $imp_stages[$key] = 'green';
            }
          }
          if($imp_complete_temp){
            $imp = 'green';
          }
        }
        $phase[]  = array('phase' => 'imp', 'status' => $imp, 'group' => $row['user_group_name'], 'user_group_id' => $row['user_group_id']);
      }
      $imp_path  = '/image/imple_'.$imp.'.png';

      $support         = '';
      $support_path    = '';
      $support_stages  = '';
      if($row_req['req_scope_support'] == '1') {
        $support = 'grey';
        if($row_req['req_scope_support_server'] == '1') {
          $support_stages['req_scope_support_server'] = 'grey';
        }
        if($row_req['req_scope_support_network'] == '1') {
          $support_stages['req_scope_support_network'] = 'grey';
        }
        if($row_req['req_scope_support_storage'] == '1') {
          $support_stages['req_scope_support_storage'] = 'grey';
        }
        if($row_req['req_scope_support_security'] == '1') {
          $support_stages['req_scope_support_security'] = 'grey';
        }
        if($row_req['req_scope_support_software'] == '1') {
          $support_stages['req_scope_support_software'] = 'grey';
        }
        if($row_req['req_scope_support_db'] == '1') {
          $support_stages['req_scope_support_db'] = 'grey';
        }

        if($support_stages) {
          foreach ($support_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $support = 'red';
              $support_stages[$key] = 'red';
            }
          }
          $support_complete_temp = TRUE;
          foreach ($support_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $support_complete_temp = FALSE;
            }
            else {
              $support_stages[$key] = 'green';
            }
          }
          if($support_complete_temp){
            $support = 'green';
          }
        }
        $phase[]  = array('phase' => 'support', 'status' => $support, 'group' => $row['user_group_name'], 'user_group_id' => $row['user_group_id']);
      }
      $support_path  = '/image/supp_'.$support.'.png';


      if($phase){
        //var_dump($phase);
        //echo count($phase).'<br />';
        foreach ($phase as $test) {
          if($test['status']  != 'green') {
            $ret = $test['phase'];
            break;
          }
        }
      }

      return $ret;
    }
  }

  if(!function_exists('determine_status')) {
    function determine_status($row_req){
      $phase = '';
      $ret  = '';

      $design         = '';
      $design_path    = '';
      $design_stages  = '';
      if($row_req['req_scope_design'] == '1') {
        $design = 'grey';
        if($row_req['req_scope_design_server'] == '1') {
          $design_stages['req_scope_design_server'] = 'grey';
        }
        if($row_req['req_scope_design_network'] == '1') {
          $design_stages['req_scope_design_network'] = 'grey';
        }
        if($row_req['req_scope_design_storage'] == '1') {
          $design_stages['req_scope_design_storage'] = 'grey';
        }
        if($row_req['req_scope_design_security'] == '1') {
          $design_stages['req_scope_design_security'] = 'grey';
        }
        if($row_req['req_scope_design_software'] == '1') {
          $design_stages['req_scope_design_software']  = 'grey';
        }
        if($row_req['req_scope_design_db'] == '1') {
          $design_stages['req_scope_design_db'] = 'grey';
        }

        if($design_stages) {
          foreach ($design_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $design = 'red';
              $design_stages[$key] = 'red';
            }
          }
          $design_complete_temp = TRUE;
          foreach ($design_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $design_complete_temp = FALSE;
            }
            else {
              $design_stages[$key] = 'green';
            }
          }
          if($design_complete_temp){
            $design = 'green';
          }
        }

        $phase[]  = array('phase' => 'design', 'status' => $design, 'req_group_id' => $row_req['req_group_id']);
      }
      $design_path  = '/image/design_'.$design.'.png';


      $budget         = '';
      $budget_path    = '';
      $budget_stages  = '';
      if($row_req['req_scope_budget'] == '1') {
        $budget = 'grey';
        if($row_req['req_scope_budget_note'] == '1') {
          $budget_stages['req_scope_budget_note'] = 'grey';
        }
        if($row_req['req_scope_budget_approval'] == '1') {
          $budget_stages['req_scope_budget_approval'] = 'grey';
        }
        if($row_req['req_scope_budget_sap'] == '1') {
          $budget_stages['req_scope_budget_sap'] = 'grey';
        }
        if($budget_stages) {
          foreach ($budget_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $budget = 'red';
              $budget_stages[$key] = 'red';
            }
          }
          $budget_complete_temp = TRUE;
          foreach ($budget_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $budget_complete_temp = FALSE;
            }
            else {
              $budget_stages[$key] = 'green';
            }
          }
          if($budget_complete_temp){
            $budget = 'green';
          }
        }
        $phase[]  = array('phase' => 'budget', 'status' => $budget, 'req_group_id' => $row_req['req_group_id']);
      }
      $budget_path  = '/image/budget_'.$budget.'.png';


      $boq         = '';
      $boq_path    = '';
      $boq_stages  = '';
      if($row_req['req_scope_boq'] == '1') {
        $boq = 'grey';
        if($row_req['req_scope_boq_server'] == '1') {
          $boq_stages['req_scope_boq_server'] = 'grey';
        }
        if($row_req['req_scope_boq_network'] == '1') {
          $boq_stages['req_scope_boq_network'] = 'grey';
        }
        if($row_req['req_scope_boq_storage'] == '1') {
          $boq_stages['req_scope_boq_storage'] = 'grey';
        }
        if($row_req['req_scope_boq_security'] == '1') {
          $boq_stages['req_scope_boq_security'] = 'grey';
        }
        if($row_req['req_scope_boq_software'] == '1') {
          $boq_stages['req_scope_boq_software'] = 'grey';
        }
        if($row_req['req_scope_boq_db'] == '1') {
          $boq_stages['req_scope_boq_db'] = 'grey';
        }

        if($boq_stages) {
          foreach ($boq_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $boq = 'red';
              $boq_stages[$key] = 'red';
            }
          }
          $boq_complete_temp = TRUE;
          foreach ($boq_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $boq_complete_temp = FALSE;
            }
            else {
              $boq_stages[$key] = 'green';
            }
          }
          if($boq_complete_temp){
            $boq = 'green';
          }
        }
        $phase[]  = array('phase' => 'boq', 'status' => $boq, 'req_group_id' => $row_req['req_group_id']);
      }
      $boq_path  = '/image/boq_'.$boq.'.png';


      $procure         = '';
      $procure_path    = '';
      $procure_stages  = '';
      if($row_req['req_scope_procure'] == '1') {
        $procure = 'grey';
        if($row_req['req_scope_procure_server'] == '1') {
          $procure_stages['req_scope_procure_server'] = 'grey';
        }
        if($row_req['req_scope_procure_network'] == '1') {
          $procure_stages['req_scope_procure_network'] = 'grey';
        }
        if($row_req['req_scope_procure_storage'] == '1') {
          $procure_stages['req_scope_procure_storage'] = 'grey';
        }
        if($row_req['req_scope_procure_security'] == '1') {
          $procure_stages['req_scope_procure_security'] = 'grey';
        }
        if($row_req['req_scope_procure_software'] == '1') {
          $procure_stages['req_scope_procure_software'] = 'grey';
        }
        if($row_req['req_scope_procure_db'] == '1') {
          $procure_stages['req_scope_procure_db'] = 'grey';
        }
        if($row_req['req_scope_procure_service'] == '1') {
          $procure_stages['req_scope_procure_service'] = 'grey';
        }

        if($procure_stages) {
          foreach ($procure_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $procure = 'red';
              $procure_stages[$key] = 'red';
            }
          }
          $procure_complete_temp = TRUE;
          foreach ($procure_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $procure_complete_temp = FALSE;
            }
            else {
              $procure_stages[$key] = 'green';
            }
          }
          if($procure_complete_temp){
            $procure = 'green';
          }
        }

        $phase[]  = array('phase' => 'procure', 'status' => $procure, 'req_group_id' => $row_req['req_group_id']);
      }
      $procure_path  = '/image/procure_'.$procure.'.png';

      $imp         = '';
      $imp_path    = '';
      $imp_stages  = '';
      if($row_req['req_scope_imp'] == '1') {
        $imp = 'grey';
        if($row_req['req_scope_imp_server'] == '1') {
          $imp_stages['req_scope_imp_server'] = 'grey';
        }
        if($row_req['req_scope_imp_network'] == '1') {
          $imp_stages['req_scope_imp_network'] = 'grey';
        }
        if($row_req['req_scope_imp_storage'] == '1') {
          $imp_stages['req_scope_imp_storage'] = 'grey';
        }
        if($row_req['req_scope_imp_security'] == '1') {
          $imp_stages['req_scope_imp_security'] = 'grey';
        }
        if($row_req['req_scope_imp_software'] == '1') {
          $imp_stages['req_scope_imp_software'] = 'grey';
        }
        if($row_req['req_scope_imp_db'] == '1') {
          $imp_stages['req_scope_imp_db'] = 'grey';
        }

        if($imp_stages) {
          foreach ($imp_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $imp = 'red';
              $imp_stages[$key] = 'red';
            }
          }
          $imp_complete_temp = TRUE;
          foreach ($imp_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $imp_complete_temp = FALSE;
            }
            else {
              $imp_stages[$key] = 'green';
            }
          }
          if($imp_complete_temp){
            $imp = 'green';
          }
        }
        $phase[]  = array('phase' => 'imp', 'status' => $imp, 'req_group_id' => $row_req['req_group_id']);
      }
      $imp_path  = '/image/imple_'.$imp.'.png';

      $support         = '';
      $support_path    = '';
      $support_stages  = '';
      if($row_req['req_scope_support'] == '1') {
        $support = 'grey';
        if($row_req['req_scope_support_server'] == '1') {
          $support_stages['req_scope_support_server'] = 'grey';
        }
        if($row_req['req_scope_support_network'] == '1') {
          $support_stages['req_scope_support_network'] = 'grey';
        }
        if($row_req['req_scope_support_storage'] == '1') {
          $support_stages['req_scope_support_storage'] = 'grey';
        }
        if($row_req['req_scope_support_security'] == '1') {
          $support_stages['req_scope_support_security'] = 'grey';
        }
        if($row_req['req_scope_support_software'] == '1') {
          $support_stages['req_scope_support_software'] = 'grey';
        }
        if($row_req['req_scope_support_db'] == '1') {
          $support_stages['req_scope_support_db'] = 'grey';
        }

        if($support_stages) {
          foreach ($support_stages as $key =>$value) {
            $stage_name = $key.'_start';
            if($row_req[$stage_name] == '1') {
              $support = 'red';
              $support_stages[$key] = 'red';
            }
          }
          $support_complete_temp = TRUE;
          foreach ($support_stages as $key =>$value) {
            $stage_name = $key.'_end';
            if($row_req[$stage_name] != '1') {
              $support_complete_temp = FALSE;
            }
            else {
              $support_stages[$key] = 'green';
            }
          }
          if($support_complete_temp){
            $support = 'green';
          }
        }
        $phase[]  = array('phase' => 'support', 'status' => $support, 'req_group_id' => $row_req['req_group_id']);
      }
      $support_path  = '/image/supp_'.$support.'.png';

      // -------------------------------------------------------------------------

      $test_stage = 'WIP';
      if($phase) {
        // Test For RFI status
        $test_rfi_stage = '';
        $test_imp_stage = '';
        foreach ($phase as $key => $value) {
          if(in_array($value['phase'], array('design', 'budget', 'boq', 'procure'))){
            $test_rfi_stage[] = $value;
          }

          if($value['phase'] == 'imp'){
            $test_imp_stage =  $value;
          }
        }

        if($test_rfi_stage) {
           $test_stage = 'RFI';
          foreach ($test_rfi_stage as $key => $value) {
            if($value['status']  != 'green'){
              $test_stage = 'WIP';
            }
          }
        }

        // Test for imp status
        if($test_stage == 'RFI' && $test_imp_stage){
          if($test_imp_stage['status'] != 'green'){
            $test_stage = 'IMP';
          }
        }
      }
      $ret = $test_stage;
      // -------------------------------------------------------------------------
      return $ret;
    }

  }


  if(!function_exists('get_user_group_sub_group')) {
    function get_user_group_sub_group($user_group_id = '',$user_group_name = '') {
      $ret = '';

      $sql_where    = ' 1 ';
      if($user_group_id) {
        $sql_where  .= " AND user_group_parent_id = '".mysql_real_escape_string($user_group_id)."'";
      }

      if($user_group_name) {
        $sql_where  .= " AND user_group_name LIKE '%".mysql_real_escape_string($user_group_name)."%'";
      }

      $sql_sub_group = " SELECT *
                          FROM idc_user_group
                          WHERE ".$sql_where."
                          ORDER BY user_group_position, user_group_name
                          ";
      //echo $sql_sub_group;
      $res_sub_group = mysql_query($sql_sub_group);

      while($sub_group = mysql_fetch_array($res_sub_group)) {
        $ret[] = $sub_group;
      }

      return $ret;
    }
  }

  if(!function_exists('save_imp_tat_for_req_id')) {
    function save_imp_tat_for_req_id($req_id, $user) {
      $ret = FALSE;
      if($req_id) {
        $sql_insert = "INSERT INTO idc_imp_tat (requestid, createby,
                          createddt) VALUE ";
        $sql_insert   .="(";
        $sql_insert   .="'".mysql_real_escape_string($req_id)."',";
        $sql_insert   .="'".mysql_real_escape_string($user)."',";
        $sql_insert   .="NOW()";
        $sql_insert   .=");";

        $res_insert = mysql_query($sql_insert);
        if(!$res_insert) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_insert;
          die($req_msg);
        }
        else {
          $ret =  mysql_insert_id();
        }
      }
      return $ret;
    }
  }


  if(!function_exists('count_igf_server_count')) {
    function count_igf_server_count($req_id, $type = '', $igf_id = '') {
      $ret    = '';
      $where  = ' 1 AND i.igf_deleted=0 ';
      if($req_id) {
        $where .= " AND i.req_id='".mysql_real_escape_string($req_id)."' ";
      }
      if($type) {
        $where .= " AND i_ser.igf_server_type_id='".mysql_real_escape_string($type)."' ";
      }
      if($igf_id) {
        $where .= " AND i_ser.igf_id='".mysql_real_escape_string($igf_id)."' ";
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
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql;
        die($req_msg);
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

  if(!function_exists('report_fetch_req_env')) {
    function report_fetch_req_env($req_id) {
      $req_env = '';
      $sql_req_env = "SELECT re.*, e.*
              FROM
                idc_request_env AS re
                LEFT JOIN
                idc_environment AS e ON re.env_id = e.env_id
              WHERE re.req_id = '".mysql_real_escape_string($req_id)."'";
      //echo $sql_req_env;
      $res_req_env = mysql_query($sql_req_env);
      if (!$res_req_env) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_env;
        die($req_msg);
      }
      else {
        while ($row_req_env = mysql_fetch_array($res_req_env)) {
           $req_env[] = $row_req_env['env_name'];
        }
      }
      return $req_env;
    }
  }

  if(!function_exists('report_fetch_req_loc')) {
    function report_fetch_req_loc($req_id) {
      $req_loc = '';
      $sql_req_loc = "SELECT rl.*, l.loc_name
              FROM
                idc_request_loc AS rl
                LEFT JOIN
                idc_location l ON rl.loc_id = l.loc_id
              WHERE req_id = '".mysql_real_escape_string($req_id)."'";
      $res_req_loc = mysql_query($sql_req_loc);
      if (!$res_req_loc) {
        $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
        $req_msg .= 'Whole query: ' . $sql_req_loc;
        die($req_msg);
      }
      else {
        while ($row_req_loc = mysql_fetch_array($res_req_loc)) {
           $req_loc[] = $row_req_loc['loc_name'];
        }
      }
      return $req_loc;
    }
  }

  if(!function_exists('get_all_request()')) {
    function get_all_request() {
      $ret = '';
      $sql = "  SELECT *
                FROM idc_request
                ORDER BY req_id DESC
              ";
      $res = mysql_query($sql);
      if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
      }
      else {
        while ($row = mysql_fetch_array($res)) {
           $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('delete_request')) {
    function delete_request($req_id) {
      $ret = FALSE;
      if($req_id) {
        // Delete Request Environment
        delete_req_env($req_id);

        // Delete Request Location
        delete_req_loc($req_id);

        // Delete Request Server Hall
        delete_req_sh($req_id);

        // Delete Request Document
        delete_req_doc($req_id);

        // Delete Request IGF
        // Also delete all igf related detail form database
        delete_req_igf($req_id);

        //Delete Request Scope
        delete_req_scope($req_id);



        // Delete Request Details
        $sql_delete = " DELETE FROM idc_request WHERE ";
        $sql_delete .=" req_id='".mysql_real_escape_string($req_id)."'";
        //echo $sql_delete.'<br />';
        $res_delete = mysql_query($sql_delete);
        if(!$res_delete) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql_delete;
          die($req_msg);
        }
        else {
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_request_detail_from_igf_id')) {
    function get_request_detail_from_igf_id($id) {
      $ret = '';

      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND i.igf_id = '".mysql_real_escape_string($id)."' ";
      }
      $sql = " SELECT r.*
               FROM
                idc_igf AS i
                LEFT JOIN
                idc_request AS r ON i.req_id = r.req_id
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      return $ret;
    }
  }


  if(!function_exists('get_request_detail_from_req_id')) {
    function get_request_detail_from_req_id($id) {
      $ret = '';

      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND r.req_id = '".mysql_real_escape_string($id)."' ";

        $sql = " SELECT r.*,rs.*,rm.*
                 FROM
                  idc_request AS r
                  LEFT JOIN
                  idc_request_scope  AS rs ON r.req_id = rs.req_id
                  LEFT JOIN
                  idc_request_material  AS rm ON r.req_id = rm.req_id
                 WHERE ".$sql_where."
                 LIMIT 0,1
              ";
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if($res){
          $ret = mysql_fetch_array($res);
        }
      }

      return $ret;
    }
  }

  if(!function_exists('get_req_igf_contact')) {
    function get_req_igf_contact($req_id, $igf_id = ''){
      $ret = '';
      if($req_id) {
        $sql_where    = ' 1 ';
        $sql_where    .= " AND i.req_id = '".mysql_real_escape_string($req_id)."' ";
        if($igf_id) {
          $sql_where  .= " AND i.igf_id = '".mysql_real_escape_string($igf_id)."' ";
        }
        $sql = "SELECT DISTINCT LOWER(ic.igf_contact_email), ic.*
                FROM
                  idc_igf_contact AS ic
                  LEFT JOIN
                  idc_igf AS i ON ic.igf_id = i.igf_id
                WHERE ".$sql_where;
        //echo $sql.'<br />';
        $res = mysql_query($sql);
        if (!$res) {
          $req_msg .= 'Invalid query: ' . mysql_error() . "\n";
          $req_msg .= 'Whole query: ' . $sql;
          die($req_msg);
        }
        else {
          while ($row = mysql_fetch_array($res)) {
            $ret[] = $row;
          }
        }
      }
      return $ret;
    }
  }

  if(!function_exists('get_req_igf_contact_email')) {
    function get_req_igf_contact_email($req_id){
      $ret = '';
      if($req_id) {
        $contacts = get_req_igf_contact($req_id);
        foreach($contacts as $row) {
          if($row['igf_contact_email']) {
            if($row['igf_contact_name']) {
              $ret .= $row['igf_contact_name'].'<'.$row['igf_contact_email'].'>';
            }
            else {
              $ret .= $row['igf_contact_email'];
            }
            $ret .=',';
          }
        }
        $ret = rtrim($ret,',');
      }
      return $ret;
    }
  }

  if(!function_exists('rename_request_ifg_files')) {
    function rename_request_ifg_files($files, $req_id) {
      $ret = TRUE;
      foreach ($files as $key => $file) {
        $file_name = ltrim(trim($file['name']), $req_id.'-');
        $new_name = $req_id.'-'.$file_name;
        $new_path = strstr($file['path'], '---', TRUE);
        $new_path .= '---'.$new_name;
        $rename = rename($file['path'], $new_path);
        if($rename) {
          $sql = "UPDATE idc_igf ";
          $sql .= "SET igf_file_name='".mysql_real_escape_string($new_name)."', ";
          $sql .= " igf_file_path='".mysql_real_escape_string($new_path)."' ";
          $sql .= " WHERE req_id='".mysql_real_escape_string($req_id)."' ";
          $sql .= " AND igf_file_path='".mysql_real_escape_string($file['path'])."';";
          $res = mysql_query($sql);
          if(!$res) {
            $ret = FALSE;
          }
        }
        else {
          $ret = FALSE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('save_request_status_for_req_id')) {
    function save_request_status_for_req_id($req_id, $status, $user, $datetime = 'NOW', $remark = '') {
      $msg = '';
      $ret = FALSE;
      if($req_id) {
        $sql = "INSERT INTO idc_request_status (req_id, status_id, status_remark, created_by,
                          created_at) VALUE ";
        $sql .="(";
        $sql .="'".mysql_real_escape_string($req_id)."',";
        $sql .="'".mysql_real_escape_string($status)."',";
        $sql .="'".mysql_real_escape_string($remark)."',";
        $sql .="'".mysql_real_escape_string($user)."',";
        if($datetime == 'NOW'){
          $sql .=" NOW() ";
        }
        else {
          $sql .="'".mysql_real_escape_string($datetime)."' ";
        }
        $sql .=");";
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if(!$res) {
          $msg .= 'Invalid query: ' . mysql_error() . "\n";
          $msg .= 'Whole query: ' . $sql;
          die($msg);
        }
        else {
          $ret =  mysql_insert_id();
        }
      }
      return $ret;
    }
  }


  if(!function_exists('get_request_status_for_req_id')) {
    function get_request_status_for_req_id($id, $status) {
      $ret = '';

      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND rs.req_id = '".mysql_real_escape_string($id)."' ";
      }
      if($status){
        $sql_where  .= " AND rs.status_id = '".mysql_real_escape_string($status)."' ";
      }
      $sql = " SELECT rs.*, s.status_name
               FROM
                idc_request_status AS rs
                LEFT JOIN
                idc_status AS s ON rs.status_id = s.status_id
               WHERE ".$sql_where."
               LIMIT 0,1
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $ret = mysql_fetch_array($res);
      }
      return $ret;
    }
  }

  if(!function_exists('get_request_status_history')) {
    function get_request_status_history($id, $status = '', $order_by = 'DESC') {
      //echo __FUNCTION__.'()<br />';
      $ret = '';

      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND rs.req_id = '".mysql_real_escape_string($id)."' ";
      }
      if($status){
        $sql_where  .= " AND rs.status_id = '".mysql_real_escape_string($status)."' ";
      }
      $sql = " SELECT rs.*, s.status_name, u.user_name AS status_remark_by
               FROM
                idc_request_status AS rs
                LEFT JOIN
                idc_status AS s ON rs.status_id = s.status_id
                LEFT JOIN
                idc_user AS u ON LOWER(rs.created_by) = LOWER(u.user_login)
               WHERE ".$sql_where."
               ORDER BY rs.req_status_id ".$order_by."
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('dashboard_wip_request_by_user_group')) {
    function dashboard_wip_request_by_user_group() {
      $ret = '';

      $sql_where    = ' 1 ';

      $sql = "  SELECT idc_user_group.user_group_id, idc_user_group.user_group_name, IFNULL(s.request_count, 0) AS request_count
                FROM idc_user_group
                LEFT JOIN (
                  SELECT req_group_id, COUNT(req_group_id) AS request_count
                  FROM idc_request
                  WHERE idc_request.req_status_id NOT IN (3,4)
                  GROUP BY req_group_id
                ) s ON (idc_user_group.user_group_id = s.req_group_id)
                WHERE
                idc_user_group.user_group_parent_id IS NULL
              ";
      //echo $sql.'<br />';
      $res = mysql_query($sql);
      if($res) {
        while ($row = mysql_fetch_array($res)) {
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }














  /* APPLICATION */

  if(!function_exists('app_contact_unique')){
    function app_contact_unique($data) {
      $ret = FALSE;
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT app_contact_detail_id
                FROM idc_app_contact_detail
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('app_contact_detail_save')) {
    function app_contact_detail_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $app_contact_id  = '';
      $sql = "INSERT INTO idc_app_contact_detail (";
      if($data) {
        // Check if data is unique or not
        $app_contact_id = app_contact_unique($data);
        if($app_contact_id){
          $ret = $app_contact_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


  if(!function_exists('app_save')) {
    function app_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $app_contact_id  = '';
      $sql = "INSERT INTO idc_app (";
      if($data) {
        foreach ($data as $key => $value) {
          $sql .= $key.", ";
        }

        $sql .= "created_by, ";
        $sql .= "created_at, ";
        $sql .= "updated_by, ";
        $sql .= "updated_at) VALUE ( ";

        foreach ($data as $key => $value) {
          $sql .="'".mysql_real_escape_string($value)."', ";
        }
        $sql .="'".mysql_real_escape_string($user)."', ";
        $sql .="NOW(),";
        $sql .="'".mysql_real_escape_string($user)."', ";
        $sql .="NOW() ";
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

  if(!function_exists('app_contact_save')) {
    function app_contact_save($app_id, $contact_id, $user = '') {
      $msg = '';
      $ret = FALSE;
      if($app_id && $contact_id) {
        $sql = "INSERT INTO idc_app_contact (app_id, app_contact_detail_id,
                  created_by, created_at, updated_by, updated_at ) VALUE ";
        $sql .="(";
        $sql .="'".mysql_real_escape_string($app_id)."', ";
        $sql .="'".mysql_real_escape_string($contact_id)."', ";
        $sql .="'".mysql_real_escape_string($user)."', ";
        $sql .="NOW(), ";
        $sql .="'".mysql_real_escape_string($user)."', ";
        $sql .="NOW() ";
        $sql .=");";

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


  if(!function_exists('app_server_detail_unique')){
    function app_server_detail_unique($data) {
      $ret = FALSE;
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT app_server_detail_id
                FROM idc_app_server_detail
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('app_server_detail_save')) {
    function app_server_detail_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $app_server_id  = '';
      $sql = "INSERT INTO idc_app_server_detail (";
      if($data) {
        // Check if data is unique or not
        $app_server_id = app_server_detail_unique($data);
        if($app_server_id){
          $ret = $app_server_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


  if(!function_exists('app_server_detail_ip_unique')){
    function app_server_detail_ip_unique($data) {
      $ret = FALSE;
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT app_server_detail_ip_id
                FROM idc_app_server_detail_ip
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('app_server_detail_ip_save')) {
    function app_server_detail_ip_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $app_server_detail_ip_id  = '';
      $sql = "INSERT INTO idc_app_server_detail_ip (";
      if($data) {
        // Check if data is unique or not
        $app_server_detail_ip_id = app_server_detail_ip_unique($data);
        if($app_server_detail_ip_id){
          $ret = $app_server_detail_ip_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }

  if(!function_exists('fetch_server_ip')) {
    function fetch_server_ip($id) {
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND srv.app_server_detail_id = '".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT
              FROM
                idc_app_server_detail AS srv
                LEFT JOIN
                idc_app_server_detail_ip  AS srv_ip ON srv.app_server_detail_id = srv_ip.app_server_detail_id
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
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

  if(!function_exists('app_server_unique')){
    function app_server_unique($data) {
      $ret = FALSE;
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT app_server_id
                FROM idc_app_server
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }


  if(!function_exists('app_server_save')) {
    function app_server_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $app_server_id  = '';
      $sql = "INSERT INTO idc_app_server (";
      if($data) {
        // Check if data is unique or not
        $app_server_id = app_server_unique($data);
        if($app_server_id){
          $ret = $app_server_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }


  if(!function_exists('fetch_server_detail_from_app_id')) {
    function fetch_server_detail_from_app_id($id) {
      $msg = '';
      $ret = '';
      $sql_where    = ' 1 ';
      if($id) {
        $sql_where  .= " AND app_ser.app_id = '".mysql_real_escape_string($id)."' ";
      }
      $sql = "SELECT
              FROM
                idc_app_server AS app_ser
                LEFT JOIN
                idc_app_server_detail AS asd ON asd.app_server_detail_id = app_ser.app_server_detail_id
              WHERE
                ".$sql_where."
              ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if (!$res) {
        $msg .= 'Invalid query: ' . mysql_error() . "\n";
        $msg .= 'Whole query: ' . $sql;
        die($msg);
      }
      else {
        while ( $row = mysql_fetch_array($res)) {
          // FIND all IP's associated with this server
          $server_ip = fetch_server_ip($row['app_server_detail_id']);
          //var_dump($server_ip);
          $row['server_ip'] = $server_ip;
          $ret[] = $row;
        }
      }
      return $ret;
    }
  }

  // idc_app_ip
  if(!function_exists('app_ip_unique')){
    function app_ip_unique($data) {
      $ret = FALSE;
      $where = ' 1 ';
      if($data) {
        foreach ($data as $key => $value) {
          $where .= " AND ".$key."= TRIM('".mysql_real_escape_string($value)."')  ";
        }
        $sql = "SELECT app_ip_id
                FROM idc_app_ip
                WHERE
                  ".$where;
        //echo 'SQL :'.$sql.'<br />';
        $res = mysql_query($sql);
        if (mysql_num_rows($res)) {
          //var_dump(mysql_fetch_array($res));
          $ret = TRUE;
        }
      }
      return $ret;
    }
  }

  if(!function_exists('app_ip_save')) {
    function app_ip_save($data,  $user = '') {
      $msg = '';
      $ret = FALSE;
      $app_ip_id  = '';
      $sql = "INSERT INTO idc_app_ip (";
      if($data) {
        // Check if data is unique or not
        $app_ip_id = app_ip_unique($data);
        if($app_ip_id){
          $ret = $app_ip_id;
        }
        else {
          foreach ($data as $key => $value) {
            $sql .= $key.", ";
          }

          $sql .= "created_by, ";
          $sql .= "created_at, ";
          $sql .= "updated_by, ";
          $sql .= "updated_at) VALUE ( ";

          foreach ($data as $key => $value) {
            $sql .="'".mysql_real_escape_string($value)."', ";
          }
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW(),";
          $sql .="'".mysql_real_escape_string($user)."', ";
          $sql .="NOW() ";
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
      }
      return $ret;
    }
  }








  if(!function_exists('request_server_make_model_count')) {
    function request_server_make_model_count($req_id = '', $type = '', $make= '', $model = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';

      $where  .= " AND igf.igf_deleted = '0' ";

      if($req_id) {
        $where  .= " AND r.req_id ='".mysql_real_escape_string($req_id)."' ";
      }

      if($type) {
        $where  .= " AND TRIM(UPPER(igf_ser.igf_server_type)) = '".mysql_real_escape_string(strtoupper($type))."' ";
      }

      if($make) {
        $where  .= " AND TRIM(UPPER(REPLACE(igf_ser.igf_server_make , ' ',''))) LIKE '".mysql_real_escape_string(strtoupper($make))."%' ";
      }

      if($model) {
        $where  .= " AND TRIM(UPPER(REPLACE(igf_ser.igf_server_model , ' ',''))) LIKE '".mysql_real_escape_string(strtoupper($model))."%' ";
      }

      $sql = "SELECT
                COUNT(igf_ser.igf_server_model) AS 'COUNT'
              FROM
                idc_igf_server AS igf_ser
                LEFT JOIN
                idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
                LEFT JOIN
                idc_request AS r ON igf.req_id = r.req_id
              WHERE
                ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret    = $row['COUNT'];
      }

      return $ret;
    }
  }


  if(!function_exists('request_server_make_model_count_DL380G9')) {
    function request_server_make_model_count_DL380G9($req_id = '', $type = '', $make= '', $model = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';

      $where  .= " AND igf.igf_deleted = '0' ";

      if($req_id) {
        $where  .= " AND r.req_id ='".mysql_real_escape_string($req_id)."' ";
      }

      if($type) {
        $where  .= " AND TRIM(UPPER(igf_ser.igf_server_type)) = '".mysql_real_escape_string(strtoupper($type))."' ";
      }

      if($make) {
        $where  .= " AND TRIM(UPPER(REPLACE(igf_ser.igf_server_make , ' ',''))) LIKE '".mysql_real_escape_string(strtoupper($make))."%' ";
      }

      if($model) {
        $where  .= " AND TRIM(UPPER(REPLACE(igf_ser.igf_server_model , ' ',''))) REGEXP '^DL380.G9|^DL380G9' ";
      }

      $sql = "SELECT
                COUNT(igf_ser.igf_server_model) AS 'COUNT'
              FROM
                idc_igf_server AS igf_ser
                LEFT JOIN
                idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
                LEFT JOIN
                idc_request AS r ON igf.req_id = r.req_id
              WHERE
                ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret    = $row['COUNT'];
      }

      return $ret;
    }
  }

  if(!function_exists('request_server_make_model_count_DL580G9')) {
    function request_server_make_model_count_DL580G9($req_id = '', $type = '', $make= '', $model = '') {
      //echo __FUNCTION__.'()<br />';
      $ret    = FALSE;
      $vendor = '';
      $where  = ' 1 ';

      $where  .= " AND igf.igf_deleted = '0' ";

      if($req_id) {
        $where  .= " AND r.req_id ='".mysql_real_escape_string($req_id)."' ";
      }

      if($type) {
        $where  .= " AND TRIM(UPPER(igf_ser.igf_server_type)) = '".mysql_real_escape_string(strtoupper($type))."' ";
      }

      if($make) {
        $where  .= " AND TRIM(UPPER(REPLACE(igf_ser.igf_server_make , ' ',''))) LIKE '".mysql_real_escape_string(strtoupper($make))."%' ";
      }

      if($model) {
        $where  .= " AND TRIM(UPPER(REPLACE(igf_ser.igf_server_model , ' ',''))) REGEXP '^DL580.G9|^DL580G9' ";
      }

      $sql = "SELECT
                COUNT(igf_ser.igf_server_model) AS 'COUNT'
              FROM
                idc_igf_server AS igf_ser
                LEFT JOIN
                idc_igf AS igf ON igf_ser.igf_id = igf.igf_id
                LEFT JOIN
                idc_request AS r ON igf.req_id = r.req_id
              WHERE
                ".$where." ";
      //echo 'SQL :'.$sql.'<br />';
      $res = mysql_query($sql);
      if($res){
        $row = mysql_fetch_array($res);
        $ret    = $row['COUNT'];
      }

      return $ret;
    }
  }



  if(!function_exists('test_123')) {

  }

?>
