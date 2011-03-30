<?php
/*
Class that manipulates properties on the userpage
*/
class SSPUser{
	protected $User;
	protected $UserPage;
	
	private $Name;
	private $Email;
	private $City;
	private $State;
	private $Country;
	private $HomeCity;
	private $HomeState;
	private $HomeCountry;
	private $Birthday;
	private $AboutMe;
	private $Occupation;
	private $Schools;
	private $Places;
	private $Websites;
	private $Avatar;
	protected $Friends;
	
	private $summary = 'This page has been changed by Semantic Social Profile extension';
	
	public static function getInstance($n = null){
		global $wgUser;
		if (is_null($n)) return new SSPUser($wgUser->getName());
		elseif ($wgUser->getName() == $n && User::isValidUserName($n))
			return new SSPUser($n);
		else return null;
	}
	
	protected function __construct($name){
		//global $wgUser;
		
		$this->User = $name;
		$articleid = Title::makeTitle( NS_USER, $name)->getArticleId();
		$this->UserPage = Article::newFromId($articleid);
		
		if(!is_null($this->UserPage)){
			$uptext = $this->UserPage->getRawText();
			$regex = '/^.*'
					.'(?:{{Semantic\sSocial\sProfile).*'
					.'(?:\|SSP\sName=\s*)(.*)\s*'
					.'(?:\|SSP\se-mail=\s*)(.*)\s*'
					.'(?:\|SSP\scity=\s*)(.*)\s*'
					.'(?:\|SSP\slocation\sstate=\s*)(.*)\s*'
					.'(?:\|SSP\slocation\scountry=\s*)(.*)\s*'
					.'(?:\|SSP\shome\scity=\s*)(.*)\s*'
					.'(?:\|SSP\shome\sstate=\s*)(.*)\s*'
					.'(?:\|SSP\shome\scountry=\s*)(.*)\s*'
					.'(?:\|SSP\sbirthday=\s*)(.*)\s*'
					.'(?:\|SSP\sabout\sme=\s*)(.*)\s*'
					.'(?:\|SSP\soccupation=\s*)(.*)\s*'
					.'(?:\|SSP\sschools=\s*)(.*)\s*'
					.'(?:\|SSP\splaces=\s*)(.*)\s*'
					.'(?:\|SSP\swebsites=\s*)(.*)\s*'
					.'(?:\|SSP\savatar=\s*)(.*)\s*'
					.'(?:\|SSP\sFriends=\s*)(.*)\s*(?:}})'
					.'.*$/isU';
		
			if (preg_match($regex, $uptext, $matches)){
				$this->Name = $matches[1];
				$this->Email = $matches[2];
				$this->City = $matches[3];
				$this->State = $matches[4];
				$this->Country = $matches[5];
				$this->HomeCity = $matches[6];
				$this->HomeState = $matches[7];
				$this->HomeCountry = $matches[8];
				$this->Birthday = $matches[9];
				$this->AboutMe = $matches[10];
				$this->Occupation = $matches[11];
				$this->Schools = $matches[12];
				$this->Places = $matches[13];
				$this->Websites = $matches[14];
				$this->Avatar = $matches[15];
				$this->Friends = $matches[16];
			}
		}
	}
	
	public function setName($var){
		$this->Name = $var;
	}
	
	public function setEmail($var){
		$this->Email = $var;
	}
	
	public function setCity($var){
		$this->City = $var;
	}
	
	public function setState($var){
		$this->State = $var;
	}
	
	public function setCountry($var){
		$this->Country = $var;
	}
	
	public function setHomeCity($var){
		$this->HomeCity = $var;
	}
	
	public function setHomeState($var){
		$this->HomeState = $var;
	}
	
	public function setHomeCountry($var){
		$this->HomeCountry = $var;
	}
	
	public function setBirthday($var){
		$this->Birthday = $var;
	}
	
	public function setAboutMe($var){
		$this->AboutMe = $var;
	}
	
	public function setOccupation($var){
		$this->Occupation = $var;
	}
	
	public function setSchools($var){
		$this->Schools = $var;
	}
	
	public function setPlaces($var){
		$this->Places = $var;
	}
	
	public function setWebsites($var){
		$this->Websites = $var;
	}
	
	public function setAvatar($var){
		$this->Avatar = $var;
	}
	
	public function addFriend($user){
		global $wgUser, $wgContLang;
		$friends = preg_split('/\s*,\s*/',$this->Friends);
		$friends[] = $wgContLang->getNsText( NS_USER ).":".$user;
		$this->Friends = implode(',',$friends);
		$this->save();
		
		//repeats the same for another user
		if($user!=$wgUser->getName()){
			$other = new SSPUser($user);
			$other->addFriend($wgUser->getName());
		}
	}
	
	public function removeFriend($user){
		global $wgUser, $wgContLang;
		$friends = preg_split('/\s*,\s*/',$this->Friends);
		$rem = $wgContLang->getNsText( NS_USER ).":".$user;
		for($i = 0; $i < count($friends); $i++){
			if($friends[$i] == $rem){
				unset($friends[$i]);
				break;
			}
		}
		$this->Friends = implode(',',$friends);
		$this->save();
		
		//repeats the same for another user
		if($user!=$wgUser->getName()){
			$other = new SSPUser($user);
			$other->removeFriend($wgUser->getName());
		}
	}
	
	public function save(){
	//	global $wgUser;
		if(!is_null($this->UserPage)){
			$info = " {{Semantic Social Profile\n"
					."    |SSP Name=$this->Name\n"
					."    |SSP e-mail=$this->Email\n"
					."    |SSP city=$this->City\n"
					."    |SSP location state=$this->State\n"
					."    |SSP location country=$this->Country\n"
					."    |SSP home city=$this->HomeCity\n"
					."    |SSP home state=$this->HomeState\n"
					."    |SSP home country=$this->HomeCountry\n"
					."    |SSP birthday=$this->Birthday\n"
					."    |SSP about me=$this->AboutMe\n"
					."    |SSP occupation=$this->Occupation\n"
					."    |SSP schools=$this->Schools\n"
					."    |SSP places=$this->Places\n"
					."    |SSP websites=$this->Websites\n"
					."    |SSP avatar=$this->Avatar\n"
					."    |SSP Friends=$this->Friends\n"
					." }}";
					
			$text = preg_replace("/(.*)(?:{{Semantic Social Profile).*(?:}})(.*)/isU","$1 \n $info $2",$this->UserPage->getRawText());
			$this->UserPage->doEdit($text, $this->summary );
		}
	}
	
	public function saveEmpty(){
		if(is_null($this->UserPage)){
			$id = Title::makeTitle( NS_TEMPLATE, 'Semantic_Social_Profile')->getArticleId();
			$template = Article::newFromId($id)->getRawText();
			$this->UserPage = new Article(Title::makeTitle( NS_USER, $this->User) );
			$this->UserPage->doEdit(preg_replace("/^.*(?:<pre>)(.*)(?:<\/pre>).*$/isU","$1",$template, 1),$this->summary);
		}
	}
}
