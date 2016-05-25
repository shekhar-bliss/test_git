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


  $igf_file_path = $pei_config['paths']['resources'].'/data/request/DDL-IGF-V4.xlsx';

  // Overwrite original file name to
  $igf_file_name = 'DDL-IGF-V4.xlsx';

  $objPHPExcel  = PHPExcel_IOFactory::createReader('Excel2007');
  $objPHPExcel  = $objPHPExcel->load($igf_file_path);


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





//echo '</pre>';
?>
