<?php
include_once("function.php");
include_once("db.php");
$numberUsers          =  NUMBER_USER_PER_REQUEST;
$numberConcurrency    =  CONCURRENCY;
$totalMessages        =  TOTAL_SUB_REQUEST;
$totalUsers           =  TOTAL_USER; 

$filePath             =  LOCK_PATH . SEND_MESSAGE ."_running.txt";
$startUserId          =  getUserIdFromLockFile($filePath, $totalUsers, $numberUsers);
$endUserId            =  $startUserId + $numberUsers;
$action               =  '[{"action":"userSendMessage","provider":"%s","identity":"%s","key":"%s","user_id":"%d"}]';

$messages             = array('Petzzle_Request_Life', 'Petzzle_Send_Life','Petzzle_Invite_Friend');
$facebookFile         = "data/facebookId_". rand(1,10) . ".txt";
$facebookIds          = getFacebookIdsFromFile($facebookFile);

$result               =  array();
$totalFailedRequest   =  0;

echo "send message is running\n";
echo "user_id from $startUserId to $endUserId\n";
echo "start requesting ..\n";

$start = microtime(true);

for($userId = $startUserId; $userId < $endUserId; $userId++){	
	
	$numberMessage = 1;
	$numberOfFailedRequest = 0;
	$startLoop = microtime(true);
	while($numberMessage < $totalMessages){
		$data = array();
		for($i = 1; $i <= $numberConcurrency; $i++){
			$key = randomArray($messages);
			if ($key == 'Petzzle_Invite_Friend'){
				$identity  =  generateCode(14);
			}else{
				$identity  = randomArray($facebookIds);
			}
			$postData = sprintf($action, 'Facebook', $identity, $key, $userId);
			$data[]   = array('url' => API_URL,'post' => array('json' => $postData));
			$numberMessage++;
		}
		
		$result[] = multiRequest($data, array(), $numberOfFailedRequest);		
	}

	$endLoop = microtime(true);
	writeResult(SEND_MESSAGE,'message', $startLoop, $endLoop, $totalMessages, $numberConcurrency, $numberOfFailedRequest);
	$totalFailedRequest += $numberOfFailedRequest;
}

$end = microtime(true);
$totalRequests = $numberUsers * $totalMessages;
showReport(SEND_MESSAGE, 'userSendMessage', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
writeResult(SEND_MESSAGE,'user', $start, $end, $totalRequests, $numberConcurrency, $totalFailedRequest);
// print_r($result);