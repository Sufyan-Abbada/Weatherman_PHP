<?php
  $date = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");

  if ($argv[1] == '-a' || $argv[1] == '-c') {
    $filedata = file_read($argv[3], (explode("/",$argv[2]))[0], $date[(explode("/",$argv[2]))[1] - 1]);

    if ($argv[1] == '-a'){
      $all_highest = find_relevant($filedata,1);
      $all_lowest = find_relevant($filedata,3);
      $all_humid = find_relevant($filedata,8);
      echo "Highest Average: ",(int)(array_sum($all_highest) / count($all_highest)),"C\n";
      echo "Lowest Average: ",(int)(array_sum($all_lowest) / count($all_lowest)),"C\n";
      echo "Average Humidity: ",(int)(array_sum($all_humid) / count($all_humid)),"%\n";
    }

    if ($argv[1] == '-c'){
      $count = 1;
      echo $date[(explode("/",$argv[2]))[1] - 1]," ";
      echo (explode("/",$argv[2]))[0];
      echo "\n";
      $all_highest = find_relevant($filedata,1);
      $all_lowest = find_relevant($filedata,3);
      for ($i=0; $i < count($all_highest); $i++) {
        echo $i+1," ",print_plus($all_highest[$i],"red")," ",$all_highest[$i];
        echo "\n";
        echo $i+1," ",print_plus($all_lowest[$i],"blue")," ",$all_lowest[$i];
        echo "\n";
      }
    }
  }
  elseif ($argv[1] == '-e') {
    $files_year = array();
    $myfiles = array_diff(scandir("$argv[3]"."_weather"), array('.', '..'));

    foreach ($myfiles as $filename) {
      if (str_contains($filename,(explode("/",$argv[2]))[0])){
        array_push($files_year, $filename);
      }
    }

    $filedata = folder_read($files_year,$argv[3]);

    $filtered_file_data = filter_data($filedata);

    find_comparison($filtered_file_data);
  }
  else{
    echo "Please Enter a Valid Input\n";
  }

  function find_comparison ($to_be_compared_array){
    $highest = 0;
    $lowest = -80;
    $humid = 0;
    for ($i = 0; $i < sizeof($to_be_compared_array); $i++) {
      $splitted = explode(',',$to_be_compared_array[$i]);
      if($splitted[1] > $highest){
        $highest = $splitted[1];
      }
      if($splitted[3] > $lowest){
        $lowest = $splitted[3];
      }
      if($splitted[8] > $humid){
        $humid = $splitted[8];
      }
      // if($splitted[$type] != ''){
      //   array_push($high_low_humid,$splitted[$type]);
      // }
    }
    echo "\nThe Highest Temperature was: ",$highest,"C";
    echo "\nThe Lowest Temperature was: ",$lowest,"C";
    echo "\nThe Highest Humidity was: ",$humid,"%\n";
  }

  function filter_data ($array){
    $filtered_array = array();
    foreach ($array as $item) {
      if (!is_bool($item)) {
        if($item[0] == 2){
          array_push($filtered_array,$item);
        }
      }
    }
    return $filtered_array;
  }

  function print_plus($times, $color){
    for ($i=0; $i < $times; $i++) {
      if ($color == 'red'){
        echo "\e[0;31m+\e[0m";
      }
      if ($color == 'blue'){
        echo "\e[0;34m+\e[0m";
      }
    }
  }

  function find_relevant($data, $type){
    $high_low_humid = array();
    foreach ($data as $record) {
      if(!is_bool($record)){
        if($record[0] == 2){
          $splitted = explode(',',$record);
          if($splitted[$type] != ''){
            array_push($high_low_humid,$splitted[$type]);
          }
        }
      }
    }
    return $high_low_humid;
  }

  function folder_read ($all_files,$city_name) {
    $file_contents = array();
    foreach ($all_files as $each_file) {
      $myfile = fopen("$city_name"."_weather/".$each_file, "r") or die("Unable to open file!\n");

      while(!feof($myfile)) {
        array_push($file_contents, fgets($myfile));
      }
      fclose($myfile);
    }
    return $file_contents;
  }

  function file_read ($city_name, $year, $month) {
    $file_contents = array();
    $myfile = fopen("$city_name"."_weather/$city_name"."_weather_$year"."_$month.txt", "r") or die("Unable to open file!\n");
    while(!feof($myfile)) {
      array_push($file_contents, fgets($myfile));
    }
    fclose($myfile);
    return $file_contents;
  }

?>