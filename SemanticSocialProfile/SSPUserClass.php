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
	private $Interests;
	protected $Avatar;
	protected $Friends;
	protected $Foes;
	
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
					.'(?:\|SSP\sname=\s*)(.*)\s*'
					.'(?:\|SSP\se-mail=\s*)(.*)\s*'
					.'(?:\|SSP\slocation\scity=\s*)(.*)\s*'
					.'(?:\|SSP\slocation\sstate=\s*)(.*)\s*'
					.'(?:\|SSP\slocation\scountry=\s*)(.*)\s*'
					.'(?:\|SSP\shome\scity=\s*)(.*)\s*'
					.'(?:\|SSP\shome\sstate=\s*)(.*)\s*'
					.'(?:\|SSP\shome\scountry=\s*)(.*)\s*'
					.'(?:\|SSP\shomeplace=).*'
					.'(?:\|SSP\sbirthday=\s*)(.*)\s*'
					.'(?:\|SSP\sabout\sme=\s*)(.*)\s*'
					.'(?:\|SSP\soccupation=\s*)(.*)\s*'
					.'(?:\|SSP\sschools=\s*)(.*)\s*'
					.'(?:\|SSP\splaces=\s*)(.*)\s*'
					.'(?:\|SSP\swebsites=\s*)(.*)\s*'
					.'(?:\|SSP\sinterests=\s*)(.*)\s*'
					.'(?:\|SSP\savatar=\s*)(.*)\s*'
					.'(?:\|SSP\sfoes=\s*)(.*)\s*'
					.'(?:\|SSP\sfriends=\s*)(.*)\s*(?:}})'
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
				$this->Interests = $matches[15];
				$this->Avatar = $matches[16];
				$this->Foes = new SSPUserList($matches[17]);
				$this->Friends = new SSPUserList($matches[18]);
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
	
	public function setInterests(&$int){
		$this->Interests = implode(',',$int);
	}
		
	public function getHomeplace(){
		$hp = array();
		if(!empty($this->HomeCity))$hp[] = $this->HomeCity;
		if(!empty($this->HomeState))$hp[] = $this->HomeState;
		if(!empty($this->HomeCountry))$hp[] = $this->HomeCountry;
		$text = implode(',',$hp);
		return $text;
	}
	
	public function updateAvatar(){
		global $wgUploadPath, $wgServer, $wgUser;
		$avatar = new wAvatar( $wgUser->getID(), 'l' );
		$imageURL = $wgServer.'/'.$wgUploadPath . '/avatars/' . $avatar->getAvatarImage();
		$this->Avatar = $imageURL;
	}
	
	public function addFriend($user,$repeat = null){
		global $wgUser;
		$this->Friends->add($user);
		$this->save();
		//repeats the same for another user
		if(is_null($repeat)){
			$other = new SSPUser($user);
			$other->addFriend($wgUser->getName(), 1);
		}
	}
	
	public function addFoe($user,$repeat = null){
		global $wgUser;
		$this->Foes->add($user);
		$this->save();
		//repeats the same for another user
		if(is_null($repeat)){
			$other = new SSPUser($user);
			$other->addFoe($wgUser->getName(), 1);
		}
	}
	
	public function removeRelationship($user,$repeat = null){
		global $wgUser;
		if($this->Friends->contains($user))
			$this->Friends->remove($user);
		elseif($this->Foes->contains($user))
			$this->Foes->remove($user);
		else return false;
		$this->save();
		//repeats the same for another user
		if(is_null($repeat)){
			$other = new SSPUser($user);
			$other->removeRelationship($wgUser->getName(),1);
		}
	}
	
	public function save(){
	//	global $wgUser;
		if(!is_null($this->UserPage)){
			$info = " {{Semantic Social Profile\n"
					."    |SSP name=$this->Name\n"
					."    |SSP e-mail=$this->Email\n"
					."    |SSP location city=$this->City\n"
					."    |SSP location state=$this->State\n"
					."    |SSP location country=$this->Country\n"
					."    |SSP home city=$this->HomeCity\n"
					."    |SSP home state=$this->HomeState\n"
					."    |SSP home country=$this->HomeCountry\n"
					."    |SSP homeplace=".$this->getHomeplace()."\n"
					."    |SSP birthday=$this->Birthday\n"
					."    |SSP about me=$this->AboutMe\n"
					."    |SSP occupation=$this->Occupation\n"
					."    |SSP schools=$this->Schools\n"
					."    |SSP places=$this->Places\n"
					."    |SSP websites=$this->Websites\n"
					."    |SSP interests=$this->Interests\n"
					."    |SSP avatar=$this->Avatar\n"
					."    |SSP foes=$this->Foes\n"
					."    |SSP friends=$this->Friends\n"
					." }}";
			if(preg_match("/^(.*)(?:\s*{{Semantic\sSocial\sProfile).*(?:}})\s*(.*)$/isU", $this->UserPage->getRawText(), $mtch))
				$this->UserPage->doEdit($mtch[1]."\n $info ".$mtch[2], $this->summary );
			else
				$this->UserPage->doEdit($info, $this->summary );
			$this->getHomeplace();
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

class SSPUserList{
	private $list = '';
	
	public function __construct($lst){
			$this->list = $lst;
	}
	
	public function __toString(){
		return $this->list;
	}
	
	public function add($uname){
		if(empty($this->list))
			$this->list = Title::makeTitle( NS_USER, $uname)->getPrefixedText();
		else{
			$addlst = preg_split('/\s*,\s*/',$this->list);
			$addlst[] = Title::makeTitle( NS_USER, $uname);
			$this->list = implode(',',$addlst);
		}
	}
	
	public function remove($uname){
		$remlst = preg_split('/\s*,\s*/',$this->list);
		$remelem = Title::makeTitle( NS_USER, $uname);
		for($i = 0; $i < count($remlst); $i++){
			if($remlst[$i] == $remelem){
				unset($remlst[$i]);
				break;
			}
		}
		$this->list = implode(',',$remlst);
	}
	
	public function contains($txt){
		return strpos($this->list, $txt)? true : false;
	}
}
