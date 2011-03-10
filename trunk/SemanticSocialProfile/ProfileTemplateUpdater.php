<?php

//updating profile
$wgHooks['BasicProfileChanged'][] = 'wfAddSemantics';

function wfAddSemantics($login, $data){
	global $wgContLang;
	// 1) retreive curerent content of the page
	$userPageID = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$login)->getArticleId(); 
	$userArticle = Article::newFromId($userPageID);
	$content = $userArticle->getRawText();
	
	// 2) define replacement values
	$find = "/(?:\|SSP\sName).+(?:\|SSP\savatar)/isU";
	$replace = "|SSP Name=".$data['name']."
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
	global $wgContLang;
	// 1) retreive curerent content of both users pages
	$userPageID1 = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$user_from)->getArticleId(); 
	$userArticle1 = Article::newFromId($userPageID1);
	$content1 = $userArticle1->getRawText();
	
	$userPageID2 = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$user_in)->getArticleId(); 
	$userArticle2 = Article::newFromId($userPageID2);
	$content2 = $userArticle2->getRawText();
	
	// 2) define replacement values
	
	$pattern = '/(?:SSP\sFriends)\s*=\s*(.*)\s*}}/isU';
	//edit the user who sent invitation
	if(preg_match($pattern,$content1,$matches)){
		$friends1 = $matches[1];		
		if(strpos($friends1,$wgContLang->getNsText( NS_USER ))=== false) //check if he has no friends
			$text = preg_replace($pattern, "SSP Friends=".$wgContLang->getNsText( NS_USER ).":".$user_in."\n}}", $content1);
		else
			$text = preg_replace($pattern, "SSP Friends=".$friends1.", ".$wgContLang->getNsText( NS_USER ).":".$user_in."\n}}", $content1);
		
		// 4) save the template into the profile
		$userArticle1->updateArticle($text, '', false, false );
	}

	//edit the user who accepted the invitation
	if(preg_match($pattern,$content2,$matches)){
		$friends2 = $matches[1];
		if(strpos($friends2,$wgContLang->getNsText( NS_USER ))=== false)
			$text = preg_replace($pattern, "SSP Friends=".$wgContLang->getNsText( NS_USER ).":".$user_from."\n}}", $content2);
		else
			$text = preg_replace($pattern, "SSP Friends=".$friends2.", ".$wgContLang->getNsText( NS_USER ).":".$user_from."\n}}", $content2);
	 // 4) save the template into the profile
		$userArticle2->updateArticle($text, '', false, false );
	}
	return true;
}


//Removing friendship
$wgHooks['FriendShipRemovedByID'][] = 'wfRemoveFriend';

function wfRemoveFriend($user1, $user2){
	global $wgContLang;
	// 1) retreive curerent content of both users pages
	$userPageID1 = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$user1)->getArticleId(); 
	$userArticle1 = Article::newFromId($userPageID1);
	$content1 = $userArticle1->getRawText();
	
	$userPageID2 = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$user2)->getArticleId(); 
	$userArticle2 = Article::newFromId($userPageID2);
	$content2 = $userArticle2->getRawText();
	
	// 2) define replacement values
	
	$pattern = '/(?:SSP\sFriends)\s*=\s*(.*)\s*}}/isU';
	//edit the user who sent invitation
	if(preg_match($pattern,$content1,$matches)){
		//if there are several friends
		$friends_array = explode( ',',$matches[1]);
		if( !empty($friends_array) )
		{
			//erases the friend
			for($i = 0; $i < count($friends_array); $i++)
				if( trim($friends_array[$i]) == $wgContLang->getNsText( NS_USER ).":".$user2 )
				{
//					$text = $user2.' removed: ';
					unset($friends_array[$i]);
					break;
				}
			$text = preg_replace($pattern, "SSP Friends=".implode(',',$friends_array)."\n}}", $content1);
		}
		//updating the profile
		$userArticle1->updateArticle($text, '', false, false );
	}

	if(preg_match($pattern,$content2,$matches)){
		//if there are several friends
		$friends_array = explode( ',',$matches[1]);
		if( !empty($friends_array) )
		{
			//erases the friend
			for($i = 0; $i < count($friends_array); $i++)
				if( trim($friends_array[$i]) == $wgContLang->getNsText( NS_USER ).":".$user1 )
				{
//					$text = $user1.' removed: ';
					unset($friends_array[$i]);
					break;
				}
				$text = preg_replace($pattern, "SSP Friends=".implode(',',$friends_array)."\n}}", $content2);
		}
		//updating the profile
		$userArticle2->updateArticle($text, '', false, false );
	}
	
	return true;
}

//todo AVATAR
$wgHooks['NewAvatarUploaded'][] = 'wfUploadAvatar';

function wfUploadAvatar($login, $imageURL){
	global $wgContLang;
	// 1) retreive curerent content of the page
	$userPageID = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$login)->getArticleId(); 
	$userArticle = Article::newFromId($userPageID);
	$content = $userArticle->getRawText();

	// 2) define replacement values
	$find = "/(?:\|SSP\savatar).+(?:\|SSP\sFriends)/isU";
	$replace = "|SSP avatar=".$imageURL."
    |SSP Friends";
    
    $text = preg_replace($find, $replace, $content);

	// 4) save the template into the profile
	$userArticle->updateArticle($text, '', false, false );
	
	return true;
}

//creates a userpage for a newly created account
$wgHooks['AddNewAccount'][] = 'wfNewAccountUserpage';

function wfNewAccountUserpage($user, $byEmail){	
	global $wgContLang;
	$summary = 'the userpage has been created using SSP';
	$id = Title::newFromText($wgContLang->getNsText( NS_TEMPLATE ).":Semantic_Social_Profile")->getArticleId();
	$template = Article::newFromId( $id )->getRawText();
	
	$pageTitle = Title::newFromText($wgContLang->getNsText( NS_USER ).":".$user->getName()); 
	$page = new Article($pageTitle);
	$page->doEdit(preg_replace("/^.*(?:<pre>)(.*)(?:<\/pre>).*$/isU","$1",$template, 1),$summary);
	
	return true;
}
/*output to the main page
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
*/
