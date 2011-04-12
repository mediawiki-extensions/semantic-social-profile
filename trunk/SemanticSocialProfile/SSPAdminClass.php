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
		}
		else{
			throw new SocProfException();
		}
	}
	
	public function syncFriendList(){
		//check if SocialProfile is installed
		if (class_exists('UserRelationship')){
			$rel = new UserRelationship($this->User);
			$frlist = $rel->getRelationshipIDs(1);

			for($i = 0; $i<count($frlist); $i++)
				$frlist[$i] = 'User:'.User::whoIs($frlist[$i]);
			
			$this->Friends = implode(',', $frlist);
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
