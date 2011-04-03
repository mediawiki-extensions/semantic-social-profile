<?php

//updating profile
$wgHooks['BasicProfileChanged'][] = 'wfAddSemantics';

function wfAddSemantics($login, $data){
	$esmg = SSPUser::getInstance();
	$esmg->setName($data['name']);
	$esmg->setEmail($data['e-mail']);
	$esmg->setCity($data['city']);
	$esmg->setState($data['location_state']);
	$esmg->setCountry($data['location_country']);
	$esmg->setHomeCity($data['home_city']);
	$esmg->setHomeState($data['home_state']);
	$esmg->setHomeCountry($data['home_country']);
	$esmg->setBirthday($data['birthday']);
	$esmg->setAboutMe($data['about_me']);
	$esmg->setOccupation($data['occupation']);
	$esmg->setSchools($data['schools']);
	$esmg->setPlaces($data['places']);
	$esmg->setWebsites($data['websites']);
	$esmg->save();

	return true;
}

//Adding Friends
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
$wgHooks['FriendShipRemovedByID'][] = 'wfRemoveFriend';

function wfRemoveFriend($user1, $user2){
	global $wgUser;
	if($wgUser->getName() == $user1 || $wgUser->getName() == $user2){
		$user = SSPUser::getInstance();
		$user->removeFriend($user1);
	}
	return true;
}

// changes avatar
$wgHooks['NewAvatarUploaded'][] = 'wfUploadAvatar';

function wfUploadAvatar($login, $imageURL){
	$user = SSPUser::getInstance();
	$user->setAvatar($imageURL);
	$user->save();
	return true;
}

//removes avatar
$wgHooks['UserAvatarRemoved'][] = 'wfRemoveAvatar';

function wfRemoveAvatar($name){
	global $wgUser;
	if($wgUser->getName() == $name){
		$user = SSPUser::getInstance();
		$user->setAvatar('');
		$user->save();
	}
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
