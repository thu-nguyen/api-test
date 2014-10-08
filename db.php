<?php
const HOST             =    "localhost";
const DB_USERNAME      =    "root";
const DB_PASSWORD      =    "password";
const DB_PETZZLE       =    "petzzle_test";
const DB_FLASHCARD     =    "flashcard_test";
const DB_VOCAB         =    "vocab_test";
const DATA_PATH        =    "data/";

function generateUser($number){	
	echo "inserting user...\n";
	$dbFlashcard = connectDb(DB_FLASHCARD);
	$dbPetzzle   = connectDb(DB_PETZZLE);
	$percent = $number/100;
	for($i = 1; $i <= $number; $i++){		
		$data = array(
			'language_id' => 1,
			'first_name' => generateCode(10),
			'last_name' => generateCode(10),
			'email' => generateEmail(),
			'gender' =>  randomArray(array('male', 'female')),
			'date_of_birth' => generateDateOfBirth(),
			'date_created' => generateDate()
		);				
		$userId = insert("user", $data, $dbFlashcard);		
		$group = array(
			'user_id' => $userId,
			'group_user' => "PETZZLE",			
			'date_created' => generateDate()
		);		
		insert("user_group", $group, $dbFlashcard);		
		$social = array(
			'user_id' => $userId,
			'provider' => 'Facebook',
			'identity' => generateCode(20),
			'token' => generateCode(50),
			'date_created' => generateDate()
		);
		
		insert("user_social_account", $social, $dbPetzzle);
		if ($i%$percent == 0) echo round($i/$number,2) * 100 . "%   ";
	}	
	echo "\n\n Done";
}
function generateFlashcard($number = 100){
	connectDb(DB_FLASHCARD);
	echo "\n Inserting flashcard ..";
	for($i = 1; $i <= $number; $i++){		
		$data = array(
			'is_approved' => 1,
			'is_disabled' => 0,
			'created_by' =>  rand(1,1000),
			'date_created' => generateDate()
		);
		$flashcardId = insert("flashcard", $data);
		$image = array(
			'flashcard_id' => $flashcardId,
			'image_style_id' => 1,
			'extension' => 'png',
			'created_by' => rand(1,1000),
			'date_created' => generateDate(),
			'md5' => generateCode(32)
		);
		insert("flashcard_image", $image);
		generateTerm($flashcardId,1);
		generateTerm($flashcardId,2);		
	}
	echo "\n\n Done \n\n";
}
function generateTerm($flashcardId, $languageId){
	$term = array(
		'language_id' => $languageId,
		'flashcard_id' => $flashcardId,
		'term' => generateCode(5)
	);
	$termId = insert("flashcard_term", $term);
	$termSound = array(
		'accent_id' => 1,
		'term_id' => $termId,
		'extension' => 'wav',
		'duration' => rand(1,2),
		'is_google_generated' => 1,
		'md5' => generateCode(32)
	);
	insert("flashcard_term_sound", $termSound);
	$sentence = array(
		'term_id' => $termId,
		'sentence' => generateCode(20)
	);
	$sentenceId = insert("flashcard_sentence", $sentence);
	$sentenceSound = array(
		'accent_id' => 1,
		'sentence_id' => $sentenceId,
		'extension' => 'wav',
		'duration'=> rand(1,2),
		'is_google_generated' => 1,
		'md5' => generateCode(32)
	);
	insert("flashcard_sentence_sound", $sentenceSound);
}
function generateChapter($number){
	connectDb(DB_PETZZLE);	
	$numberLevel = 20;
	$totalLevel  = 1;	
	echo "Inserting level...\n";
	for($i = 1; $i <= $number; $i++){		
		$data = array(
			'name' => generateCode(5),
			'number' => $i,
			'background' =>  "bg.jpg",
			'logo' =>  "logo.jpg",
			'logo_thumb' => "logo_thumb.jpg",
			'is_disabled' => 0,
			'created_by' => 1,
			'md5' => generateCode(32),
			'background_md5' => generateCode(32),
			'logo_md5' => generateCode(32),
			'logo_thumb_md5' => generateCode(32),
			'date_created' => generateDate(),
			'status' => "Approved"
		);
		$chapterId = insert("chapter", $data);
		for($level = 1; $level <= $numberLevel; $level++){
			$levelData = array(
				'chapter_id' => $chapterId,
				'number' => $level,
				'name' => $totalLevel++,
				'difficult_level' => rand(0,3),
				'level_design' => generateCode(100),
				'game_mode' => rand(0,3),
				'tatic' => rand(0,3),
				'is_disabled' => 0,
				'created_by' => 1,
				'date_created' => generateDate(),
				'status' => "Approved"
			);
			insert("level", $levelData);
		}		
	}
	echo "\n\n Done\n\n";
	
}
function generateWordlist($number){
	connectDb(DB_VOCAB);
	echo "\n Inserting wordlist ..";
	for($i = 1; $i <= $number; $i++){		
		$data = array(
			'user_id' => rand(1,1000),
			'name' => generateCode(5),
			'description' => generateCode(10),
			'image_extension' => 'png',
			'image_encrypt' => generateCode(5, "0123456789"),
			'image_size' => rand(100,500),
			'from_language_id' => 1,
			'to_language_id' => 2,
			'encrypt' => generateCode(32),			
			'is_disabled' => 0,
			'is_public' => 1,
			'is_approved' => 1,
			'is_featured' => rand(0,1),
			'view_count' => rand(0,1000),
			'download_count' => rand(0,1000),
			'play_count' => rand(0,1000),
			'download_code' => generateCode(6),
			'date_created' => generateDate()
		);
		$wordlistId = insert("wordlist", $data);
		$wordlistAccent = array(
			'wordlist_id' => $wordlistId,
			'accent_id' => 1			
		);
		insert("wordlist_accent", $wordlistAccent);	
		generateWord(rand(10,20), $wordlistId);	
	}
	echo "\n\n Done \n\n";
}
function generateWord($number, $wordlistId){
	for($i = 1; $i <= $number; $i++){
		$word = array(
			'wordlist_id' => $wordlistId,
			'play_count' => rand(0,1000),
			'is_disabled' => 0,
			'date_created' => generateDate()
		);
		$wordId = insert("word", $word);
		$image = array(
			'word_id' => $wordId,
			'image_style_id' => 1,
			'extension' => 'png',
			'size' => rand(100,500),
			'encrypt' => generateCode(5, "0123456789"),
		);
		for($languageId = 1; $languageId <=2; $languageId++ ){
			$term = array(
				'language_id' => $languageId,
				'word_id' => $wordId,
				'term' => generateCode(5),
			);
			$termId = insert('word_term', $term);
			$termSound = array(
				'accent_id' => 1,
				'term_id' => $termId,
				'extension' => 'wav',
				'size' => rand(100,500),
				'android_size' => rand(10,100),
				'encrypt' => generateCode(5, "0123456789"),
				'android_encrypt' => generateCode(5, "0123456789"),
				'duration' => rand(1,3),
				'is_google_generated' => 1
			);
			insert('word_term_sound', $termSound);	
		}		
	}
}
function generateUserFriend($number){
	connectDb(DB_FLASHCARD);
	echo "\n Inserting friend ..";
	for($i = 1; $i <= $number; $i++){
		$data = array(
			'user_id' => rand(1,1000000),
			'user_friend_id' => rand(1,1000000),
			'game_code' => 'PETZZLE',
			'date_created' => generateDate()
		);
		insert('user_friend', $data);
	}
	echo "\n Done";

}
function countRecords($table, $con = null){
    $result = mysql_query("SELECT * FROM $table", $con);
    $num_rows = mysql_num_rows($result);

	return $num_rows;
}
function connectDb($dbName, $username = DB_USERNAME, $password = DB_PASSWORD, $host = HOST) {
	$con = mysql_connect($host, $username, $password, true);
	mysql_select_db($dbName, $con);
	// Check connection
	if (mysqli_connect_errno()) {
	  	printMessage("Failed to connect to MySQL: " . mysqli_connect_error());
	}
	return $con;
}
function closeConnect($con){
    mysql_close($con);
}
function insert($tableName, $array, $con = null, $onUpdate = "") {
    $columns        = implode(", ", array_keys($array));
    $escaped_values = array_map('mysql_real_escape_string', array_values($array));
    foreach ($escaped_values as $idx=>$data) {
    	if (!is_numeric($data)){
    		$escaped_values[$idx] = "'".$data."'";
    	}
    }

    $values  = implode(", ", $escaped_values);
    $query   = "INSERT INTO $tableName ($columns) VALUES ($values) $onUpdate";
    if ($con){
    	mysql_query($query, $con) or die(mysql_error());	
    	return mysql_insert_id($con);
    }else{
    	mysql_query($query) or die(mysql_error());	
    	return mysql_insert_id();
    }
}
function update($tableName, $array, $where){
	$escaped_values = array();
    foreach ($array as $field=>$data) {
    	$data = mysql_real_escape_string($data);
    	if (!is_numeric($data)){
    		$escaped_values[] = "$field = '$data'";
    	}else{
    		$escaped_values[] = "$field = $data";
    	}
    }    

    $values  = implode(", ", $escaped_values);
    $query   = "UPDATE $tableName SET $values WHERE $where";
    return mysql_query($query) or die(mysql_error());    	
}
function fetchAll($tableName, $con = null) {
    $result = mysql_query("SELECT * from $tableName", $con);

    if(! $result) {
        die('Could not get data: ' . mysql_error());
    }

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $data[] = $row;
    }
    
    return $data;
}
function fetch($sql, $con = null) {
    $result = mysql_query("$sql", $con);

    if(! $result) {
        die('Could not get data: ' . mysql_error());
    }

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $data[] = $row;
    }
    
    return $data;
}
function fetchOne($sql, $con = null){
    $result = mysql_query("$sql", $con);

    if(! $result) {
        die('Could not get data: ' . mysql_error());
    }

    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
        $data = $row;
    }
    
    return $data;
}
function generateCode($length=3, $letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789"){
    $string   = "";
    for($i=0;$i < $length;$i++) {
        $char = $letters[mt_rand(0, strlen($letters)-1)];
        $string .= $char;
    }
    return $string;
}
function generateDate(){	
	$from = strtotime(date('Y-m-d'));
	$months = rand(1,10);
    $to = strtotime("- $months months");
    return date('Y-m-d H:i:s', rand($from, $to));
}
function generateDateOfBirth(){
	$from = strtotime("- 18 years");
	$to   = strtotime("- 50 years");	
    return date('Y-m-d', rand($from, $to));
}
function randomArray($array) {
	return $array[rand(0, count($array) - 1)];
}
function generateEmail() {
	$domains = array(
		'gmail.com',
		'yahoo.com',
		'hotmail.com',
	);

	return generateCode(rand(3,6)) . '@' . randomArray($domains);
}
function truncateAllData(){
	
	$flashcardDb = connectDb(DB_FLASHCARD);
	$tables = array(
		'flashcard',
		'flashcard_image',
		'flashcard_sentence',
		'flashcard_sentence_sound',
		'flashcard_term',
		'flashcard_term_sound',
		'user',
		'user_flashcard_term',
		'user_group',
	);

	mysql_query("SET foreign_key_checks = 0;", $flashcardDb) or die(mysql_error());
	foreach ($tables as $table) {
		$sql = "TRUNCATE TABLE `$table`;";
		mysql_query($sql, $flashcardDb) or die(mysql_error());
	}

	$tables = array(
		'chapter',
		'level',
		'level_map',
		'level_term',
		'package',
		'package_flashcard',
		'revise_chapter_play',
		'revise_stage',
		'revise_term_play',
		'user_active',
		'user_balance_transaction',
		'user_invite_friend',
		'user_message',
		'user_score',
		'user_send_life',
		'user_social_account',
		'user_state'
	);
	$petzzleDb = connectDb(DB_PETZZLE);	
	
	mysql_query("SET foreign_key_checks = 0;", $petzzleDb) or die(mysql_error());
	foreach ($tables as $table) {
		$sql = "TRUNCATE TABLE `$table`;";
		mysql_query($sql, $petzzleDb) or die(mysql_error());
	}
	echo "\n\nTruncate done\n\n";
}
function truncateTables($tables, $dbName){
	if (!is_array($tables)){
		$tables = explode(",", $tables);
	}
	$db = connectDb($dbName);
	mysql_query("SET foreign_key_checks = 0;", $db) or die(mysql_error());		
	echo "\nTruncate successfully tables:\n";
	foreach ($tables as $table) {
		mysql_query("TRUNCATE TABLE `$table`;", $db) or die(mysql_error());
		echo "  $table\n";
	}
	
	
}
function writeFacebookIds(){
	$petzzleDb = connectDb(DB_PETZZLE);
	$numberFile = 10;
	$numberRecords = 1000;
	for($i = 1; $i <= $numberFile; $i++){
		$result = mysql_query("SELECT identity FROM user_social_account ORDER BY RAND() LIMIT $numberRecords", $petzzleDb);
	    if(! $result) {
	        die('Could not get data: ' . mysql_error());
	    }
	    $fileName = DATA_PATH . "facebookId_{$i}.txt";
	    $content = "";
	    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
	    	$content .=  $row['identity'] ."\n";
	    }	
	    file_put_contents($fileName,$content, FILE_APPEND);
	}
	
    echo "Done";
}
function getFacebookIds($numberRecords){
	$petzzleDb = connectDb(DB_PETZZLE);
	$result = mysql_query("SELECT identity FROM user_social_account ORDER BY RAND() LIMIT 1000", $petzzleDb);    
    $facebookIds = array();
    while ($row = mysql_fetch_array($result, MYSQL_ASSOC)) {
    	$facebookIds[] = $row['identity'];
    }
    return $facebookIds;
}