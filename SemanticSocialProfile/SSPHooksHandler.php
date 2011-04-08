<?php

//updating basic profile information
//from SocialProfile/UserProfile/SpecialUpdateProfile.php
$wgHooks['BasicProfileChanged'][] = 'wfEditBasicProfileData';

function wfEditBasicProfileData($login, $data){
	$esmg = SSPUser::getInstance();
	$esmg->setName($data['up_name']);
	$esmg->setEmail($data['up_email']);
	$esmg->setCity($data['up_location_city']);
	$esmg->setState($data['up_location_state']);
	$esmg->setCountry($data['up_location_country']);
	$esmg->setHomeCity($data['up_hometown_city']);
	$esmg->setHomeState($data['up_hometown_state']);
	$esmg->setHomeCountry($data['up_hometown_country']);
	$esmg->setBirthday($data['up_birthday']);
	$esmg->setAboutMe($data['up_about']);
	$esmg->setOccupation($data['up_occupation']);
	$esmg->setSchools($data['up_schools']);
	$esmg->setPlaces($data['up_places_lived']);
	$esmg->setWebsites($data['up_websites']);
	$esmg->save();

	return true;
}

//Adding interests
//from SocialProfile/UserProfile/SpecialUpdateProfile.php
$wgHooks['PersonalInterestsChanged'][] = 'wfEditInterests';

function wfEditInterests($login, $data){
	$text = '';
	foreach($data as $key => $val)
		$text .= $key.': '.$val.', ';
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
	return true;
}

//Adding Friends
//from SocialProfile/UserRelationship/UserRelationshipClass.php
$wgHooks['NewFriendAccepted'][] = 'wfAcceptFriend';

function wfAcceptFriend($user_from, $user_in){
	global $wgUser;
	if($wgUser->getName() == $user_in){
		$user = SSPUser::getInstance();
		$user->addFriend($user_from);
	}
	return true;
}


//Removing friendship
//from SocialProfile/UserRelationship/UserRelationshipClass.php
$wgHooks['FriendShipRemoved'][] = 'wfRemoveFriend';

function wfRemoveFriend($user1, $user2){
	global $wgUser;
	if($wgUser->getName() == $user1 || $wgUser->getName() == $user2){
		$user = SSPUser::getInstance();
		$user->removeFriend($user1);
	}
	return true;
}

//Changes avatar
//from SocialProfile/UserProfile/SpecialUploadAvatar.php
$wgHooks['NewAvatarUploaded'][] = 'wfUploadAvatar';

function wfUploadAvatar($login, $imageURL){
	$user = SSPUser::getInstance();
	$user->setAvatar($imageURL);
	$user->save();
	return true;
}

//creates a userpage for a newly created account
$wgHooks['AddNewAccount'][] = 'wfNewAccountUserpage';

function wfNewAccountUserpage($user, $byEmail){	
	$user = SSPUser::getInstance();
	$user->saveEmpty();
	return true;
}
/*output to the main page
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
*/
