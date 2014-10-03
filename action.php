<?php
include_once("function.php");
$actions = array(	
	'advanceModeLeaderboard'     =>  '[{"action":"advanceModeLeaderboard","user_id":"2","wordlist_id":"1"}]',
	'advanceModeBestStage'       =>  '[{"action":"advanceModeBestStage","wordlist_id":"1","user_id":"2"}]',
	'advanceModePopularWordlist' =>  '[{"action":"advanceModePopularWordlist","start":"0","limit":"20"}]',
	'advanceModeTrackDownload'   =>  '[{"action":"advanceModeTrackDownload","wordlist_id":"1","user_id":"1"}]',
	'advanceModeUpdateStage'     =>  '[{"action":"advanceModeUpdateStage","wordlist_id":"1","user_id":"2","word_collected":"1,2,3"}]',
	'advanceModeWordlistDetail'  =>  '[{"action":"advanceModeWordlistDetail","download_code":"CIA596"}]',
	'mainGameFlashcardCollected' =>  '[{"action":"mainGameFlashcardCollected","user_id":"1","word_id":"1"}]',
	'mainGameFlashcardUnlocked'  =>  '[{"action":"mainGameFlashcardUnlocked","user_id":"1","word_id":"1"}]',
	'mainGameLeaderboard'        =>  '[{"action":"mainGameLeaderboard","user_id":"1","level_id":"1"}]',
	'mainGameUpdateScore'        =>  '[{"action":"mainGameUpdateScore","user_id":"1","level_id":"1","score":"10000"}]',
	'messageTemplate'            =>  '[{"action":"messageTemplate"}]',
	'reviseGameBestStage'        =>  '[{"action":"reviseGameBestStage","chapter_id":"1","user_id":"1"}]',
	'reviseGameUpdateStage'      =>  '[{"action":"reviseGameUpdateStage","chapter_id":"1","user_id":"1","word_collected":"1,2,4,5"}]',
	'userAcceptMessage'          =>  '[{"action":"userAcceptMessage","message_id":"1","user_id":"1"}]',
	'userGetData'                =>  '[{"action":"userGetData","user_id":"1"}]',
	'userLogin'                  =>  '[{"action":"userLogin","provider":"Facebook","identity":"123456","token":"123456789","user_id":"1","email":""}]',
	'userMessage'                =>  '[{"action":"userMessage","user_id":"1"}]',
	'userRegisterNewDevice'      =>  '[{"action":"userRegisterNewDevice"}]',
	'userSendMessage'            =>  '[{"action":"userSendMessage","provider":"Facebook","identity":"10152484522977244","key":"Petzzle_Request_Life","user_id":"2"}]',
	'userUpdateCoin'             =>  '[{"action":"userUpdateCoin","user_id":"1","amount":"-100","type":"BuyLife","description":"Package1"}]',
	'userUpdateProfile'          =>  '[{"action":"userUpdateProfile","user_id":"1","first_name":"Van","last_name":"Dao","email":"vandao@gmail.com","gender":"male","date_of_birth":"26/7/1986","provider":"Facebook","identity":"123456","token":"asdfasdfasdf"}]',	
);
$numberOfFailedRequest = 0;
foreach ($actions as $action => $params) {
	$start    = microtime(true);
	// for($i = 1; $i <= 100; $i++){
		
		$data     = array('url' => API_URL,'post' => array('json' => $params));
		$result[] = multiRequest(array($data), array(), $numberOfFailedRequest);		
	// }
	$end      = microtime(true);
	$duration = round($end-$start, 3);
	// $duration = round(($end-$start)/100,3);
	writeResultAction("actions_db","action", $action, $duration);
}
// print_r($result);
