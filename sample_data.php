<?php
include_once ("db.php");
// truncateAllData();
// 1000 flashcards
// generateFlashcard(1000);
// 1000 levels
// generateChapter(50);
// 1000000 users
// generateUser(10000);
// writeFacebookIds();
$flashcardTables = array(	
	'user_flashcard_term',	
);
$petzzleTables = array(
	'revise_chapter_play',
	'revise_stage',
	'revise_term_play',
	'user_active',
	'user_balance_transaction',
	'user_invite_friend',
	'user_message',
	'user_score',
	'user_send_life',	
	'user_state'
);
$vocabTables = array(
	'wordlist',
	'wordlist_accent',
	'word',
	'word_image',
	'word_term',
	'word_term_sound',
	'wordlist_download',
	'wordlist_play',
	'wordlist_stage',
	'word_play'
);
truncateTables($petzzleTables, DB_PETZZLE);
truncateTables($flashcardTables, DB_FLASHCARD);
truncateTables($vocabTables, DB_VOCAB);
// generateWordlist(10);
// generateUserFriend(1000);