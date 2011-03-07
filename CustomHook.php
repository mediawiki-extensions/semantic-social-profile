<?php

//updating profile
$wgHooks['BasicProfileChanged'][] = 'wfAddSemantics';

function wfAddSemantics($login, $data){
	// 1) retreive curerent content of the page
	$userPageID = Title::newFromText("User:$login")->getArticleId(); 
	$userArticle = Article::newFromId($userPageID);
	$content = $userArticle->getRawText();
	
	// 2) define replacement values
	$find = '#SSP\ Name(.*)SSP\ avatar#isU';
	$replace = "SSP Name=".$data['name']."
    |SSP e-mail=".$data['e-mail']."
    |SSP city=".$data['city']."
    |SSP location state=".$data['location_state']."
    |SSP location country=".$data['location_country']."
    |SSP home city=".$data['home_city']."
    |SSP home state=".$data['home_state']."
    |SSP home country=".$data['home_country']."
    |SSP birthday=".$data['birthday']."
    |SSP about me=".$data['about_me']."
    |SSP ocupation=".$data['occupation']."
    |SSP schools=".$data['schools']."
    |SSP places=".$data['places']."
    |SSP websites=".$data['websites']."
    |SSP avatar";
	
	// 3) do replacement
	$text = preg_replace($find, $replace, $content);
	
	// 4) save the template into the profile
	$userArticle->updateArticle($text, '', false, false );

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
/*
$wgHooks['NewAvatarUploaded'][] = 'wfUploadAvatar';

function wfUploadAvatar($login, $imageURL){
	// 1) retreive curerent content of the page
	$userPageID = Title::newFromText("User:$login")->getArticleId(); 
	$userArticle = Article::newFromId($userPageID);
	$content = $userArticle->getRawText();
	
	// 2) define replacement values
	$find = '#SSP\ avatar(.*)}}#isU';
	$replace = "SSP avatar=".$imageURL."
}}";
    
	// 3) do replacement
	$text = preg_replace($find, $replace, $content);
	
	// 4) save the template into the profile
	$userArticle->updateArticle($text, '', false, false );

	return true;
}
*/
s
?>
