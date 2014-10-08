<?php
const API_URL                 =  "http://petzzle.api";
const CONCURRENCY             =  1;
const REPORT_PATH             =  'report/';
const LOCK_PATH               =  'lock/';
const UPDATE_SCORE            =  'update_score';
const UNLOCKED_FLASHCARD      =  'unlocked_flashcard';
const COLLECTED_FLASHCARD     =  'collected_flashcard';
const UPDATE_COIN             =  'update_coin';
const SEND_MESSAGE            =  'send_message';
const UPDATE_REVISE_STAGE     =  'update_revise_stage';

const TOTAL_USER              =  1000000;
const TOTAL_SUB_REQUEST       =  100;
const NUMBER_USER_PER_REQUEST =  1000;

function multiRequest($data, $options = array(), &$numberOfFailedRequest) {
 
  // array of curl handles
  $curly = array();
  // data to be returned
  $result = array();
 
  // multi handle
  $mh = curl_multi_init();
 
  // loop through $data and create curl handles
  // then add them to the multi-handle
  foreach ($data as $id => $d) {
 
    $curly[$id] = curl_init();
 
    $url = (is_array($d) && !empty($d['url'])) ? $d['url'] : $d;
    curl_setopt($curly[$id], CURLOPT_URL,            $url);
    curl_setopt($curly[$id], CURLOPT_HEADER,         0);
    curl_setopt($curly[$id], CURLOPT_RETURNTRANSFER, 1);
 
    // post?
    if (is_array($d)) {
      if (!empty($d['post'])) {
        curl_setopt($curly[$id], CURLOPT_POST,       1);
        curl_setopt($curly[$id], CURLOPT_POSTFIELDS, $d['post']);
      }
    }
 
    // extra options?
    if (!empty($options)) {
      curl_setopt_array($curly[$id], $options);
    }
 
    curl_multi_add_handle($mh, $curly[$id]);
  }
 
  // execute the handles
  $running = null;
  do {
    curl_multi_exec($mh, $running);
  } while($running > 0);
 
 
  // get content and remove handles
  foreach($curly as $id => $c) {
  	$result[$id]['post']   = $data[$id]['post']['json'];
    $result[$id]['result'] = curl_multi_getcontent($c);
    $json = json_decode($result[$id]['result'], true);
    if (empty($json[0]['status'])){
    	$numberOfFailedRequest++;
    }
    curl_multi_remove_handle($mh, $c);
  }
 
  // all done
  curl_multi_close($mh);
 
  return $result;
}

function showReport($folder, $action, $start, $end, $numberOfRequest, $numberOfConcurrency, $numberOfFailedRequest){
  $duration = $end-$start;
  $average  = $duration/$numberOfRequest;
  $durationPerRequest = $numberOfRequest/$duration;

  $report = "\n\n\n-----------------Result---------------------\n";
  $report .= "Action:                  " . $action . "\n";
  $report .= "Concurrency requests:    " . $numberOfConcurrency . "\n";
  $report .= "Complete requests:       " . $numberOfRequest     ."\n";
  $report .= "Failed requests:         " . $numberOfFailedRequest . "\n";
  $report .= "Time taken for tests:    " . round($duration,3)   ." seconds (" . secondToTime($duration) . ")\n";
  $report .= "Requests per second:     " . round($durationPerRequest, 2) . " requests\n";
  $report .= "Time per request:        " . round($average, 3)   . " seconds\n";
  echo $report;

  $folder = REPORT_PATH . $folder;
  if (!is_dir($folder)){
    mkdir($folder, 0777, true);
  }
  file_put_contents("$folder/{$action}_log_". time() . ".txt", $report, FILE_APPEND);
}

function writeResult($folder, $fileName, $start, $end, $totalRequests, $numberOfConcurrency, $failed){
  $result = array(
    'start' => $start,
    'end' => $end,
    'total_request' => $totalRequests,
    'concurrency' => $numberOfConcurrency,
    'failed_request' => $failed
  );
  $result = json_encode($result) . "\n";
  $folder = REPORT_PATH . $folder;
  if (!is_dir($folder)){
    mkdir($folder, 0777, true);
  }
  file_put_contents("$folder/$fileName.json", $result, FILE_APPEND);
}

function readResult($filePath){  
  $reports = file_get_contents($filePath);
  $reports = explode("\n", $reports);  
  $data    = array();
  foreach ($reports as $row) {
    $report = json_decode($row, true);
    if (is_array($report)){
      $data[] = $report;
    }
  }
  return $data;
}
function secondToTime($seconds){
  $hours = floor($seconds / 3600);
  $minutes = floor(($seconds / 60) % 60);
  $seconds = $seconds % 60;
  return sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}
function chartData($data){
  $chart = array();
  $totalRequest = 0;  
  foreach ($data as $item) {  
    $totalRequest += $item['total_request'];
    $duration = $item['end'] - $item['start'];    
     $chart[] = array(
      'request' => $totalRequest,
      'duration' => round($duration,3),
      'average' => round($item['total_request']/$duration,3),
      'failed' => $item['failed_request'],
      'concurrency' => $item['concurrency']
     );
  }
  return $chart;
}
function getUserIdFromLockFile($filePath, $totalUsers, $numberUsers){
  $startUserId       = 1;
  $handle = fopen($filePath,"a+");
  //Lock File, error if unable to lock
  if(flock($handle, LOCK_EX)) {
      $content = @fread($handle, filesize($filePath));
      $content = explode("\n", $content);      
      $maxUserId = $content[count($content) - 1];
      if ($maxUserId){
        $startUserId = $maxUserId + $numberUsers;
      }
      if ($startUserId > $totalUsers) $startUserId = 1;      
      rewind($handle);           //Set write pointer to beginning of file            
      fwrite($handle, "\n$startUserId");
      flock($handle, LOCK_UN);    //Unlock File
  } else {
      echo "Could not Lock File!";
  }
  //Close Stream
  fclose($handle);
  return $startUserId;
}

function getFacebookIdsFromFile($filePath){
  $content = file_get_contents($filePath);
  $facebookIds = explode("\n", $content);  
  unset($facebookIds[count($facebookIds) - 1]);
  return $facebookIds;
}

function getTermId($numberTerm, $totalFlashcards){
  $termIds = array();
  for($i = 1; $i <= $numberTerm; $i++){
    $termId = rand(1,$totalFlashcards*2);
    if ($termId%2 == 0) $termId++;
    $termIds[] = $termId;
  }
  return implode(",", $termIds);
}

function writeResultAction($folder, $fileName, $action, $duration){
  // $duration = round($end - $start,3);
  $result = sprintf("| %35s | %10s |", $action, $duration) . "\n";
  echo $result .= sprintf("|%s|%10s|", str_repeat("_",37), str_repeat("_",12)) . "\n";
  $folder = REPORT_PATH . $folder;
  if (!is_dir($folder)){
    mkdir($folder, 0777, true);
  }
  file_put_contents("$folder/$fileName.txt", $result, FILE_APPEND);
}
function getOverviewData($data){  
  $totalRequest = 0;
  $duration = 0;
  $failed = 0;
  $concurrency = 0;
  $count = count($data);
  foreach ($data as $item) {  
    $totalRequest += $item['total_request'];
    $duration += $item['end'] - $item['start'];    
    $failed += $item['failed_request'];
    $concurrency += $item['concurrency'];     
  }
  $report = array(
    'total_request' => $totalRequest,
    'duration' => $duration,
    'request_per_second' => round($totalRequest/$duration,3),
    'time_per_request' => round($duration/$totalRequest,3),
    'failed_request' => $failed,
    'concurrency' => round($concurrency/$count)
  );
  return $report;
}