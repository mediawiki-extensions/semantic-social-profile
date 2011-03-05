<?php

//updating profile
$wgHooks['BasicProfileSaved'][] = 'wfPrintSalo';

function wfPrintSalo($name, $data){
	$text = $name." изменил профиль: \n";
	foreach ($data as $key => $value)
	{
		$text.="$key: $value <br />"; 
	}
	//this sends notification to the main page
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
	return true;
}

//Adding Friends
$wgHooks['NewFriendAccepted'][] = 'wfAcceptFriend';

function wfAcceptFriend($user_from, $user_in){
	$text = $user_in." теперь друзья c ".$user_from;
	// both profiles are to be updated
	//this sends notification to the main page
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
	return true;
}


//Removing friendship
$wgHooks['FriendShipRemovedByID'][] = 'wfRemoveFriend';

function wfRemoveFriend($userid1, $userid2){
	//returns not names but ids so far
	$text = $userid1." болше не друзья с ".$userid2;
	// both profiles are to be updated
	//this sends notification to the main page
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
	return true;
}

//todo AVATAR
?>
