<?php
include_once("function.php");
$numberUsers         =  NUMBER_USER_PER_REQUEST;
$numberConcurrency   =  CONCURRENCY;
$totalFlashcards     =  TOTAL_SUB_REQUEST;
$totalUsers          =  TOTAL_USER;

$filePath            =  LOCK_PATH . UNLOCKED_FLASHCARD ."_running.txt";
$startUserId         =  getUserIdFromLockFile($filePath, $totalUsers, $numberUsers);
$endUserId           =  $startUserId + $numberUsers;
$action              =  '[{"action":"mainGameFlashcardUnlocked","user_id":"%d","word_id":"%d"}]';

$result              =  array();
$totalFailedRequest  =  0;

echo "Unlocked flashcard is running\n";
echo "user_id from $startUserId to $endUserId\n";
echo "Start requesting ..\n";


$start = microtime(true);

for($userId = $startUserId; $userId < $endUserId; $userId++){

	$termId = 1;
	$numberFlashcard = 1;
	$numberOfFailedRequest = 0;
	$startLoop = microtime(true);

	while($numberFlashcard < $totalFlashcards){
		$data = array();		
		for($i = 1; $i <= $numberConcurrency; $i++){
			$postData = sprintf($action, $userId, $termId);
			$data[] = array('url' => API_URL,'post' => array('json' => $postData));
			$numberFlashcard++;
			$termId +=2;
		}
		$result[] = multiRequest($data, array(), $numberOfFailedRequest);		
	}

	$endLoop = microtime(true);
	writeResult(UNLOCKED_FLASHCARD,'term', $startLoop, $endLoop, $totalFlashcards, $numberConcurrency, $numberOfFailedRequest);
	$totalFailedRequest += $numberOfFailedRequest;
}

$end = microtime(true);
$totalRequests = $numberUsers * $totalFlashcards;
showReport(UNLOCKED_FLASHCARD, 'mainGameFlashcardUnlocked', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
writeResult(UNLOCKED_FLASHCARD,'user', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);