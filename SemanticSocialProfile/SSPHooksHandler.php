<?php

//updating basic profile information
//from SocialProfile/UserProfile/SpecialUpdateProfile.php
$wgHooks['BasicProfileChanged'][] = 'wfEditBasicProfileData';

function wfEditBasicProfileData($user_obj, $data){
	global $wgUser;
	if($user_obj == $wgUser){
		//if the logged in user changes his data himself
		$usr = SSPUser::getInstance();
	} elseif($wgUser->isAllowed("editinterface")){
		//if the data is changed by administrator
		$usr = SSPAdmin::getProfile($user_obj->getName());
	} else return false;
	
	$usr->setName($data['up_name']);
	$usr->setEmail($data['up_email']);
	$usr->setCity($data['up_location_city']);
	$usr->setState($data['up_location_state']);
	$usr->setCountry($data['up_location_country']);
	$usr->setHomeCity($data['up_hometown_city']);
	$usr->setHomeState($data['up_hometown_state']);
	$usr->setHomeCountry($data['up_hometown_country']);
	$usr->setBirthday($data['up_birthday']);
	$usr->setAboutMe($data['up_about']);
	$usr->setOccupation($data['up_occupation']);
	$usr->setSchools($data['up_schools']);
	$usr->setPlaces($data['up_places_lived']);
	$usr->setWebsites($data['up_websites']);
	$usr->save();

	return true;
}

//Adding interests
//from SocialProfile/UserProfile/SpecialUpdateProfile.php
$wgHooks['PersonalInterestsChanged'][] = 'wfEditInterests';

function wfEditInterests($user_obj, $data){
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
	}elseif($wgUser->getName() == $user_from){
		$user = SSPUser::getInstance();
		$user->addFriend($user_in);
	}else return false;
	return true;
}


//Removing friendship
//from SocialProfile/UserRelationship/UserRelationshipClass.php
$wgHooks['RelationshipRemovedByUserID'][] = 'wfRemoveFriend';

function wfRemoveFriend($user1, $user2){
	global $wgUser;
	$username1 = User::whoIs($user1);
	$username2 = User::whoIs($user2);
	if($wgUser->getName() == $username1){
		$user = SSPUser::getInstance();
		$user->removeFriend($username2);
	} elseif($wgUser->getName() == $username2){
		$user = SSPUser::getInstance();
		$user->removeFriend($username1);
	} else return false;
	return true;
}

//Changes avatar
//from SocialProfile/UserProfile/SpecialUploadAvatar.php
$wgHooks['NewAvatarUploaded'][] = 'wfUploadAvatar';

function wfUploadAvatar($user_obj){
	$user = SSPUser::getInstance();
	$user->updateAvatar();
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
