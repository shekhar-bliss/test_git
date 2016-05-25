<?php
  session_start();
  //ini_set('display_errors', 0);
  // load up config file
  require_once(__dir__."/../pei_config.php");
  // load up database connection file
  require_once(__dir__."/../pei_db.php");
  // load up common functions file
  require_once(__dir__."/../pei_function.php");
  // load PHPMailer library
  require_once($pei_config['paths']['vendors'].'/PHPMailer/PHPMailerAutoload.php');

  //$_REQUEST['req_id'] = 'REQ15-0001';
  $uname              = strtolower($_SESSION['pei_user']);
  $pei_user_name      = '';
  $pei_msg            = '';
  $req_id             = '';
  $igf_id             = '';
  $req_loc            = '';
  $group_name         = '';
  $rfi_mail_sent      = FALSE;
  $mail_req_update_contact_row  = '';

  $data_req_status    = '';

  $pei_user = get_user_detail_from_user_login($uname);
  if($pei_user) {
    $pei_user_name = $pei_user['user_name'];
  }

  $mail_pei_recipient = array();
  if(isset($_REQUEST['req_id'])) {
    $req_id = $_REQUEST['req_id'];
  }

   // Status ID released
  $status_id_rfi = 2;

  //echo '$req_id :'.$req_id.'<br />';
  if(valid_request_id($req_id)) {
    // Get Request Details
    $req = get_request_detail_from_req_id($req_id);
    //var_dump($req);
    // Get Request Locations
    $req_loc = get_req_loc($req_id);

    $igf = fetch_req_igf($req_id);
    // var_dump($igf);
    if($igf){
      $igf_id = $igf[0]['igf_id'];
    }
    // Get Request RFI status detail
    $req_status_rfi =  get_request_status_for_req_id($req_id, $status_id_rfi);
    if($req_status_rfi) {
      $req_date = pei_date_format($req_status_rfi['created_at']);
    }
    else {
      $req_date = pei_date_format($req['req_date']);
    }

    // Send Email Notification
    // Get REQUEST RFI mail template
    $mail_template_pei = get_email_template_by_name('REQUEST RFI');
    //var_dump($email_template_pei);

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
    //echo '$mail_phy :'.$mail_phy.'<br />';
    $mail_vir          = count_igf_server_count($req_id, '4');
    //echo '$mail_vir :'.$mail_vir.'<br />';
    $mail_server       = $mail_phy + $mail_vir;
    //echo '$mail_server :'.$mail_server.'<br />';

    $mail_from_name     = $mail_template_pei['mail_template_from_name'];
    $mail_from_email    = $mail_template_pei['mail_template_from_mail'];
    $mail_recipient     = $mail_template_pei['mail_template_recipient'];


    $update_contact   = array();
    $update_contact_count         = 0;
    $mail_req_update_contact_row  = '';
    // First
    // Add Implementation Mail Recipient according to LOCATION selected
    if($req_loc){
      foreach ($req_loc as $key => $req_locaction) {
        if($req_locaction['loc_contact_mail']) {
          $loc_contact_name = $req_locaction['loc_contact_name'];
          $loc_contact_mail = pei_fetch_mail_from_string($req_locaction['loc_contact_mail']);
          if($loc_contact_mail){
            foreach ($loc_contact_mail as $key => $value_mail) {
              /*
              if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                $mail_pei_recipient[] = $value_mail['mail'];
              }
              */
              /* ----- */
              if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                // Add Implementation Mail Recipient
                $mail_pei_recipient[] = $value_mail['mail'];

                if(!in_array($value_mail['mail'], $update_contact)) {
                  $update_contact_count++;
                  $update_contact[] = $value_mail['mail'];
                  $contact_phone  = ($req_locaction['loc_contact_phone']) ? $req_locaction['loc_contact_phone'] : 'NA';
                  if(strtolower($value_mail['mail']) == strtolower('JioDC.ImplementationMumbai@ril.com')) {
                    $loc_contact_name = 'JioDC MUMBAI IMPLEMENTATION TEAM';
                  }
                  $mail_req_update_contact_row .= '<tr>
                    <td><b>CONTACT '.$update_contact_count.'</b></td>
                    <td>'.$loc_contact_name.'</td>
                    <td>'.$value_mail['mail'].'</td>
                  </tr>';
                }
              }
              /* ----- */

            }
          }
        }
      }
    }

    // Second
    // Add Stores Mail Recipient according to LOCATION selected
    if($req_loc){
      foreach ($req_loc as $key => $req_locaction) {
        if($req_locaction['loc_store_mail']) {
          $loc_store_mail = pei_fetch_mail_from_string($req_locaction['loc_store_mail']);
          if($loc_store_mail){
            foreach ($loc_store_mail as $key => $value_mail) {
              if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                $mail_pei_recipient[] = $value_mail['mail'];
              }
            }
          }
        }
      }
    }

    // Third
    // Add Mail Recipient form database
    if($mail_recipient){
      $mail_pei_recipient_temp = explode(",", $mail_recipient);
      if($mail_pei_recipient_temp) {
        foreach ($mail_pei_recipient_temp as $key => $value) {
          if (filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $mail_pei_recipient[] = $value;
          }
        }
      }
    }

    $mail_recipient_cc  = $mail_template_pei['mail_template_recipient_cc'];
    $mail_pei_recipient_cc = '';
    if($mail_recipient_cc){
      $mail_pei_recipient_cc = explode(",", $mail_recipient_cc);
    }

    // ADD CC Recipient according to LOCATION
    if($req_loc){
      foreach ($req_loc as $key => $req_locaction) {
        if($req_locaction['loc_contact_mail_cc']) {
          $loc_contact_mail_cc = pei_fetch_mail_from_string($req_locaction['loc_contact_mail_cc']);
          if($loc_contact_mail_cc){
            foreach ($loc_contact_mail_cc as $key => $value_mail) {
              if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                $mail_pei_recipient_cc[] = $value_mail['mail'];
              }
            }
          }
        }
      }
    }

    $mail_recipient_bcc  = $mail_template_pei['mail_template_recipient_bcc'];
    $mail_pei_recipient_bcc = '';
    if($mail_recipient_bcc){
      $mail_pei_recipient_bcc = explode(",", $mail_recipient_bcc);
    }

    // ADD BCC Recipient according to LOCATION
    if($req_loc){
      foreach ($req_loc as $key => $req_locaction) {
        if($req_locaction['loc_contact_mail_bcc']) {
          $loc_contact_mail_bcc = pei_fetch_mail_from_string($req_locaction['loc_contact_mail_bcc']);
          if($loc_contact_mail_bcc){
            foreach ($loc_contact_mail_bcc as $key => $value_mail) {
              if (filter_var($value_mail['mail'], FILTER_VALIDATE_EMAIL)) {
                $mail_pei_recipient_bcc[] = $value_mail['mail'];
              }
            }
          }
        }
      }
    }

    $mail_subject       = $mail_template_pei['mail_template_subject'];
    $mail_message       = $mail_template_pei['mail_template_message_text'];
    $mail_message_path  = $pei_config['paths']['templates'].'/'.ltrim($mail_template_pei['mail_template_message_path'], '/');
    //echo '$mail_message_path :'.$mail_message_path.'<br />';
    $mail_message_html  = file_get_contents($mail_message_path);

    $mail_serial_nos    = ($req['req_mat_serial_nos']) ? nl2br($req['req_mat_serial_nos']) : 'NA';
    $mail_ram           = ($req['req_mat_ram']) ? $req['req_mat_ram'] : 'NA';
    $mail_hdd           = ($req['req_mat_hdd']) ? $req['req_mat_hdd'] : 'NA';
    $mail_nic           = ($req['req_mat_nic']) ? $req['req_mat_nic'] : 'NA';
    $mail_fc_hba        = ($req['req_mat_fc_hba']) ? $req['req_mat_fc_hba'] : 'NA';
    $mail_add           = ($req['req_mat_additional']) ? $req['req_mat_additional'] : 'NA';
    $mail_ram_ret       = ($req['req_mat_ram_ret']) ? $req['req_mat_ram_ret'] : 'NA';
    $mail_hdd_ret       = ($req['req_mat_hdd_ret']) ? $req['req_mat_hdd_ret'] : 'NA';
    $mail_nic_ret       = ($req['req_mat_nic_ret']) ? $req['req_mat_nic_ret'] : 'NA';
    $mail_fc_hba_ret    = ($req['req_mat_fc_hba_ret']) ? $req['req_mat_fc_hba_ret'] : 'NA';


    $mail_remarks    = ($req['req_remarks']) ? nl2br($req['req_remarks']) : 'NA';


    $mail_variables     = array('{REQ_DATE}' => $req_date,
                            '{REQ_ID}' => $req_id,
                            '{REQ_ENV}' => $mail_env_str,
                            '{REQ_NAME}' => strtoupper($req['req_title']),
                            '{REQ_LOC}' => $mail_loc_str,
                            '{REQ_SH}' => $mail_loc_sh,
                            '{REQ_RFI_BY}' => $pei_user_name,
                            '{REQ_UPDATE_CONTACT}' => $mail_req_update_contact_row,
                            '{REQ_SERVER_COUNT}' => sprintf("%02d", $mail_server),
                            '{REQ_PHYSICAL_SERVERS}' => sprintf("%02d", $mail_phy),
                            '{REQ_VIRTUAL_SERVERS}' => sprintf("%02d", $mail_vir),
                            '{REQ_MAT_SERIAL}' => $mail_serial_nos,
                            '{REQ_MAT_RAM}' => $mail_ram,
                            '{REQ_MAT_HDD}' => $mail_hdd,
                            '{REQ_MAT_NIC}' => $mail_nic,
                            '{REQ_MAT_FC_HBA}' => $mail_fc_hba,
                            '{REQ_MAT_ADDITIONAL}' => $mail_add,
                            '{REQ_MAT_RAM_RETURN}' => $mail_ram_ret,
                            '{REQ_MAT_HDD_RETURN}' => $mail_hdd_ret,
                            '{REQ_MAT_NIC_RETURN}' => $mail_nic_ret,
                            '{REQ_MAT_FC_HBA_RETURN}' => $mail_fc_hba_ret,
                            '{REQ_REMARKS}' => $mail_remarks
                          );

    $mail_subject_pei       = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_subject);
    //echo $mail_subject_pei.'<br />';
    $mail_message_pei_text  = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_message);
    //echo $mail_message_pei_text.'<br />';
    $mail_message_pei_html  = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_message_html);
    //echo $mail_message_pei_html.'<br />';

    //Create a new PHPMailer instance
    $mail_pei = new PHPMailer;
    //Set who the message is to be sent from
    $mail_pei->setFrom($mail_from_email, $mail_from_name);
    //Set an alternative reply-to address
    //$mail_pei->addReplyTo($mail_from_email,  $mail_from_name);
    //Set who the message is to be sent to
    foreach ($mail_pei_recipient as $key => $value) {
      if(trim($value)) {
        //Set who the message is to be sent to
        $mail_pei->addAddress($value);
      }
    }
    // addCc
    if($mail_pei_recipient_cc) {
      foreach ($mail_pei_recipient_cc as $key => $value) {
        if(trim($value)) {
          //Set who the message is to be sent to
          $mail_pei->addCC($value);
        }
      }
    }
    // addBCC
    if($mail_pei_recipient_bcc) {
      foreach ($mail_pei_recipient_bcc as $key => $value) {
        if(trim($value)) {
          //Set who the message is to be sent to
          $mail_pei->addBCC($value);
        }
      }
    }

    //Set the subject line
    $mail_pei->Subject = $mail_subject_pei;
    //Read an HTML message body from an external file, convert referenced images to embedded,
    //convert HTML into a basic plain-text alternative body
    $mail_pei->msgHTML($mail_message_pei_html);
    //Replace the plain text body with one created manually
    $mail_pei->AltBody = $mail_message_pei_text;

    // Check IF RFI mail is already SENT or NOT
    $req_status_history = get_request_status_history($req_id);
    if($req_status_history){
      $pei_msg = 'ALREADY SENT';
    }
    else {
      //send the message, check for errors
      $mail_pei_sent =  $mail_pei->send();
      //var_dump($mail_pei_sent);
      if($mail_pei_sent){
        $pei_msg = 'REQUEST RFI SENT';

        // Get REQUEST RFI END USER mail template
        $mail_template_user = get_email_template_by_name('REQUEST RFI END USER');

        $mail_user_from_name      = $mail_template_user['mail_template_from_name'];
        $mail_user_from_email     = $mail_template_user['mail_template_from_mail'];
        $mail_user_recipient      = get_req_igf_contact($req_id, $igf_id);
        $mail_user_recipient_cc_temp   = $mail_template_user['mail_template_recipient_cc'];
        $mail_user_recipient_cc   = '';
        if($mail_user_recipient_cc_temp){
          $mail_user_recipient_cc = explode(",", $mail_user_recipient_cc_temp);
        }
        $mail_user_recipient_bcc_temp  = $mail_template_user['mail_template_recipient_bcc'];
        $mail_user_recipient_bcc = '';
        if($mail_user_recipient_bcc_temp){
          $mail_user_recipient_bcc = explode(",", $mail_user_recipient_bcc_temp);
        }
        $mail_user_subject       = $mail_template_user['mail_template_subject'];
        $mail_user_message       = $mail_template_user['mail_template_message_text'];
        $mail_user_message_path  = $pei_config['paths']['templates'].'/'.ltrim($mail_template_user['mail_template_message_path'], '/');
        //echo '$mail_user_message_path :'.$mail_user_message_path.'<br />';
        $mail_user_message_html  = file_get_contents($mail_user_message_path);

        $mail_subject_user       = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_user_subject);
        //echo $mail_subject_user.'<br />';
        $mail_message_user_text  = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_user_message);
        //echo $mail_message_user_text.'<br />';
        $mail_message_user_html  = str_replace(array_keys($mail_variables),array_values($mail_variables), $mail_user_message_html);
        //echo $mail_message_user_html.'<br />';

        // IGF path
        $mail_user_igf_path = $igf[0]['igf_file_path'];
        $mail_user_igf_name = $igf[0]['igf_file_name'];
        // Overwrite original file name to
        $mail_user_igf_name = $req_id.'-IGF.xlsx';
        //echo '$mail_user_igf_path :'.$mail_user_igf_path.'<br />';


        //Create a new PHPMailer instance for REQUEST RFI END USER
        $mail_user = new PHPMailer;
        //Set who the message is to be sent from
        $mail_user->setFrom($mail_user_from_email, $mail_user_from_name);

        //Set who the message is to be sent to
        foreach ($mail_user_recipient as $recipient) {
          if(trim($recipient['igf_contact_email'])) {
            //echo $recipient['igf_contact_email'].'<br />';
            //Set who the message is to be sent to

            // Check for multiple email address
            $recipient_mail = pei_fetch_mail_from_string($recipient['igf_contact_email']);
            if($recipient_mail) {
              $first_recipient = TRUE;
              foreach ($recipient_mail as $key => $value_mail) {
                if (filter_var(trim($value_mail['mail']), FILTER_VALIDATE_EMAIL) && $first_recipient) {
                  $mail_user->addAddress($value_mail['mail'], '');
                  $first_recipient = FALSE;
                }
              }
            }
            else {
              $recipient_name = ($recipient['igf_contact_name']) ? $recipient['igf_contact_name'] : '';
              $mail_user->addAddress($recipient['igf_contact_email'], $recipient_name);
            }
          }
        }


        // addC
        if($mail_user_recipient_cc) {
          foreach ($mail_user_recipient_cc as $key => $value) {
            if(trim($value)) {
              //Set who the message is to be sent to
              $mail_user->addCC($value);
            }
          }
        }
        // addBCC
        if($mail_user_recipient_bcc) {
          foreach ($mail_user_recipient_bcc as $key => $value) {
            if(trim($value)) {
              //Set who the message is to be sent to
              $mail_user->addBCC($value);
            }
          }
        }

        //Set the subject line
        $mail_user->Subject = $mail_subject_user;
        //Read an HTML message body from an external file, convert referenced images to embedded,
        //convert HTML into a basic plain-text alternative body
        $mail_user->msgHTML($mail_message_user_html);
        //Replace the plain text body with one created manually
        $mail_user->AltBody = $mail_message_user_text;
        //Attach an IGF file
        if($mail_user_igf_path) {
          $mail_user->addAttachment($mail_user_igf_path, $mail_user_igf_name);
        }

        //send the message, check for errors
        $mail_user_sent =  $mail_user->send();
        //var_dump($mail_user_sent);
        if($mail_user_sent) {
          // Save Request Status 'RFI'
          //'RFI' => status_id => 2
          // Change RFI to WIP
          $data_req_status['status_id']   = 6;
          $data_req_status['req_id']      = $req_id;
          $data_req_status['created_by']  = $uname;
          $data_req_status['created_at']  = 'NOW';
          request_status_save($data_req_status);

          // Update Request Status in idc_request
          update_request_status($req_id, 'WIP', 6);


          $pei_msg = 'Sent';


          $req_pm = '';
          // If PM / IM is set then assign PM / IM activities to him
          $req_pm = request_pm_active($req_id);
          //var_dump($req_pm);
          if($req_pm) {
            // First Fetch all PM / IM activities
            $pmim_activity = activity_type_search('PM / IM');
            //var_dump($pmim_activity);
            if($pmim_activity){
              $pmim_activity_id = $pmim_activity[0]['act_type_id'];
              //echo ' $pmim_activity_id :'.$pmim_activity_id.'<br />';
              // Get Implementaion Child Activities
              if($pmim_activity_id) {
                $pmim_activities = activity_type_children_children($pmim_activity_id);
                //var_dump($pmim_activities);
                if($pmim_activities) {
                  foreach ($pmim_activities as $key => $activity) {

                    // SAVE IMP ACTIVITY
                    $req_act_id     = '';
                    $data_activity  = '';
                    $data_activity['req_id']              = $req_id;
                    $data_activity['act_type_id']         = $activity['act_type_id'];
                    $data_activity['created_by']          = $uname;
                    $data_activity['created_at']          = 'NOW';
                    $data_activity['req_act_applicable']  = 1;

                    $req_act_id = request_activity_save($data_activity, $req_act_id);
                    //echo '$req_act_id :'.$req_act_id.'<br />';

                    // SAVE IMP ACTIVITY SPOC
                    if($req_act_id) {
                      $data_spoc = '';
                      $data_spoc['req_act_id']        = $req_act_id;
                      $data_spoc['req_act_spoc']      = strtolower($req_pm['req_pm']);
                      $data_spoc['spoc_weight']       = 1;
                      $data_spoc['created_by']        = $uname;
                      $data_spoc['created_at']        = 'NOW';
                      request_activity_spoc_save($data_spoc);
                    }

                    // CHILDREN Activities
                    if(isset($activity['children'])) {
                      foreach ($activity['children'] as $key => $children_activity) {
                        // SAVE IMP ACTIVITY
                        $req_act_id     = '';
                        $data_activity  = '';
                        $data_activity['req_id']              = $req_id;
                        $data_activity['act_type_id']         = $children_activity['act_type_id'];
                        $data_activity['created_by']          = $uname;
                        $data_activity['created_at']          = 'NOW';
                        $data_activity['req_act_applicable']  = 1;

                        $req_act_id = request_activity_save($data_activity, $req_act_id);
                        //echo 'CHILDREN $req_act_id :'.$req_act_id.'<br />';

                        // SAVE IMP ACTIVITY SPOC
                        if($req_act_id) {
                          $data_spoc = '';
                          $data_spoc['req_act_id']        = $req_act_id;
                          $data_spoc['req_act_spoc']      = strtolower($req_pm['req_pm']);
                          $data_spoc['spoc_weight']       = 1;
                          $data_spoc['created_by']        = $uname;
                          $data_spoc['created_at']        = 'NOW';
                          request_activity_spoc_save($data_spoc);
                        }
                      }
                    }// END CHILDREN Activities
                  }
                }
              }
            }
          }// END IF $req_pm





        }
        else {
          $pei_msg = 'ERROR';
        }
      }


    }

  }
  else {
    $pei_msg = 'Invalid Request Id.';
  }

  echo $pei_msg;

?>
