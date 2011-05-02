<?php
class SSPAdmin extends SSPUser{
	protected function __construct($usr){
		parent::__construct($usr);
		if(is_null($this->UserPage))		
			$this->UserPage = new Article(Title::makeTitle( NS_USER, $usr) );
	}

	public static function getProfile($usr){
		global $wgUser;
		if($wgUser->isAllowed("editinterface"))
			return new SSPAdmin($usr);
		else
			return null;
	}
	
	public function syncWithDB(){
		//check if socprof is installed
		if (class_exists('UserProfile')){
			$socprof = new UserProfile($this->User);
			$info = $socprof->getProfile();
			$this->setName($info['real_name']);
			$this->setEmail($info['email']);
			$this->setCity($info['location_city']);
			$this->setState($info['location_state']);
			$this->setCountry($info['location_country']);
			$this->setHomeCity($info['hometown_city']);
			$this->setHomeState($info['hometown_state']);
			$this->setHomeCountry($info['hometown_country']);
			$this->setBirthday($info['birthday']);
			$this->setAboutMe($info['about']);
			$this->setOccupation($info['occupation']);
			$this->setSchools($info['schools']);
			$this->setPlaces($info['places_lived']);
			$this->setWebsites($info['websites']);
			$this->updateAvatar();
			
			//setting interests
			$interests = array();
			if(!empty($info['companies']))
				$interests[] = $info['companies'];
			if(!empty($info['movies']))
				$interests[] = $info['movies'];
			if(!empty($info['music']))
				$interests[] = $info['music'];
			if(!empty($info['tv']))
				$interests[] = $info['tv'];
			if(!empty($info['books']))
				$interests[] = $info['books'];
			if(!empty($info['magazines']))
				$interests[] = $info['magazines'];
			if(!empty($info['video_games']))
				$interests[] = $info['video_games'];
			if(!empty($info['snacks']))
				$interests[] = $info['snacks'];
			if(!empty($info['drinks']))
				$interests[] = $info['drinks'];
			
			$this->setInterests($interests);
		}
		else{
			throw new SocProfException();
		}
	}
	
	public function syncRelationshipList(){
		//check if SocialProfile is installed
		if (class_exists('UserRelationship')){
			$rel = new UserRelationship($this->User);
			$frlist = $rel->getRelationshipIDs(1);
			$foelist = $rel->getRelationshipIDs(2);

			for($i = 0; $i<count($frlist); $i++)
				$frlist[$i] = 'User:'.User::whoIs($frlist[$i]);
			
			for($i = 0; $i<count($foelist); $i++)
				$foelist[$i] = 'User:'.User::whoIs($foelist[$i]);
			
			$friendlist = implode(',', $frlist);
			$listoffoes = implode(',', $foelist);
			
			$this->Friends = new SSPUserList($friendlist);
			$this->Foes = new SSPUserList($listoffoes);
		}
		else{
			throw new SocProfException();
		}
	}
	
	public function setBirthday($fbd){
		if($fbd == '' || strpos($fbd,'-')) parent::setBirthday($fbd);
		else{
			$timestamp = strtotime($fbd.' 2007');
			//OMG 2007 should be corrected!!!
			parent::setBirthday(date('Y-m-d',$timestamp));
		}
	}
}
