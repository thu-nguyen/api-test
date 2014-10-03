<?php
include_once("function.php");
$totalFlashcards     =  TOTAL_SUB_REQUEST;
$totalUsers          =  TOTAL_USER;
$numberUsers         =  NUMBER_USER_PER_REQUEST;
$numberConcurrency   =  CONCURRENCY;

$filePath            =  LOCK_PATH . UPDATE_REVISE_STAGE ."_running.txt";
$startUserId         =  getUserIdFromLockFile($filePath, $totalUsers, $numberUsers);
$endUserId           =  $startUserId + $numberUsers;
$action              =  '[{"action":"reviseGameUpdateStage","chapter_id":"%d","user_id":"%d","word_collected":"%s"}]';

$result              =  array();
$totalFailedRequest  =  0;

echo "update revise stage is running\n";
echo "user_id from $startUserId to $endUserId \n";
echo "start requesting ..\n";


$start = microtime(true);

for($userId = $startUserId; $userId < $endUserId; $userId++){	
	$numberFlashcard = 1;
	$numberOfFailedRequest = 0;
	$startLoop = microtime(true);

	while($numberFlashcard < $totalFlashcards){
		$data = array();
		for($i = 1; $i <= $numberConcurrency; $i++){
			$chapterId = ceil($numberFlashcard/20);
			$termId = getTermId(rand(1,10), $totalFlashcards);
			$postData = sprintf($action, $chapterId, $userId, $termId);			
			$data[] = array('url' => API_URL,'post' => array('json' => $postData));
			$numberFlashcard++;
		}
		
		$result[] = multiRequest($data, array(), $numberOfFailedRequest);		
	}

	$endLoop = microtime(true);
	writeResult(UPDATE_REVISE_STAGE,'stage', $startLoop, $endLoop, $totalFlashcards, $numberConcurrency, $numberOfFailedRequest);
	$totalFailedRequest += $numberOfFailedRequest;
}

$end = microtime(true);
$totalRequests = $numberUsers * $totalFlashcards;
showReport(UPDATE_REVISE_STAGE, 'reviseGameUpdateStage', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
writeResult(UPDATE_REVISE_STAGE,'user', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
print_r($result);