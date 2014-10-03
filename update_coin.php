<?php
include_once("function.php");
$numberUsers         =  NUMBER_USER_PER_REQUEST;
$numberConcurrency   =  CONCURRENCY;
$totalLevels         =  TOTAL_SUB_REQUEST;
$totalUsers          =  TOTAL_USER;

$filePath            =  LOCK_PATH . UPDATE_COIN ."_running.txt";
$startUserId         =  getUserIdFromLockFile($filePath, $totalUsers, $numberUsers);
$endUserId           =  $startUserId + $numberUsers;
$action              =  '[{"action":"userUpdateCoin","user_id":"%d","amount":"%d","type":"%s","description":"%s"}]';

$result              =  array();
$totalFailedRequest  =  0;

echo "update coin is running\n";
echo "user_id from $startUserId to $endUserId\n";
echo "start requesting ..\n";

$start = microtime(true);

for($userId = $startUserId; $userId < $endUserId; $userId++){
	$levelId = 1;
	$numberOfFailedRequest = 0;
	$startLevel = microtime(true);

	while($levelId < $totalLevels){
		$data = array();
		for($i = 1; $i <= $numberConcurrency; $i++){
			$postData = sprintf($action, $userId, 100, 'Collected Flashcard', '');
			$data[] = array('url' => API_URL,'post' => array('json' => $postData));
			$levelId++;
		}
		$result[] = multiRequest($data, array(), $numberOfFailedRequest);		
	}

	$endLevel = microtime(true);
	writeResult(UPDATE_COIN,'level', $startLevel, $endLevel, $totalLevels, $numberConcurrency, $numberOfFailedRequest);
	$totalFailedRequest += $numberOfFailedRequest;
}

$end = microtime(true);
$totalRequests = $numberUsers * $totalLevels;
showReport(UPDATE_COIN, 'userUpdateCoin', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
writeResult(UPDATE_COIN,'user', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
