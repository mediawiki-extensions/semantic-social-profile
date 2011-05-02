<?php
class SpecialSemanticSocialProfile extends SpecialPage {
	private $_user;
	private $_output;
  function __construct() {
    parent::__construct( 'SemanticSocialProfile', 'editinterface' );
    wfLoadExtensionMessages('SemanticSocialProfile');
    
    $wiki = SpecialWikiVersion::getVersion($this);
	$this->_output = $wiki->out();
    $this->_user = $wiki->user();
  }

  function execute( $par ) {
    global $wgRequest;
    
    //checks if the user has a right to access the page
		if ( !$this->userCanExecute($this->_user) ) {
			$this->displayRestrictionError();
			return;
		}
	
    $this->setHeaders();

	if ($wgRequest->wasPosted()) {
		$hid = $wgRequest->getText('hiddenform');
		if($hid=='1'){
			//display results of the 1st step
			$this->_output->addWikiText("''' ".wfMsg('ssp-setupdone')." '''");
			//first setup the vocabulariess then properties
			$this->wfsetUp('vocabularies');
			$this->wfsetUp('properties');
			
			//prepare form to do step 2 
			$form = '<form name="syncssp" action="" method="POST">' . "\n".
				'<input type = "hidden" name = "hiddenform" value = "2">'. "\n".
				'<input type="submit" value="'.wfMsg('ssp-syncbutton').'"/>' . "\n".
			'</form>';
			$this->_output->addWikiText( "=== ".wfMsg('ssp-syncdesc')." ===");
			$this->_output->addWikiText( wfMsg('ssp-syncabout'));
			$this->_output->addHtml($form);
		}
		if($hid=='2'){
			if($this->wfSynchronize())
				$this->_output->addWikiText( "\n '''".wfMsg('ssp-done')." '''" );
			$gotomain = '<form name="setupssp" action="'.Title::newMainPage()->getFullURL().'" method="POST">' . "\n".
						'<input type = "submit" value = "'.wfMsg('main-page').'">'. "\n".
						'</form>';
			
			$this->_output->addHtml($gotomain);
		}
	}
	else{
		$html = '<form name="setupssp" action="" method="POST">' . "\n".
				'<input type = "hidden" name = "hiddenform" value = "1">'. "\n".
				'<input type="submit" value="'.wfMsg('ssp-setupsspbutton').'"/>' . "\n".
			'</form>';
		$this->_output->addWikiText( "=== ".wfMsg('ssp-setupdesc')." ===");
		$this->_output->addWikiText( wfMsg('ssp-setupabout') );
		$this->_output->addHtml( $html);
	}
	
}

  function wfsetUp($folder){
    $directory = dirname(__FILE__) . '/setup/' . $folder;

    $filenames = scandir($directory);
    $summary = 'Semantic Social Profile installation procedure';
    $text = array();
    foreach ($filenames as $filename) {
		if(is_file($directory.'/'.$filename)) {
			$fn = explode('#',$filename,2);
			$pageTitle = Title::makeTitle($fn[0],$fn[1]);
			$text[] = $pageTitle->getFullURL();
			$page = new Article($pageTitle);
			$page->doEdit(file_get_contents($directory.'/'.$filename), $summary);
      }
    }
    $this->_output->addWikiText(implode(', ', $text)."\n");
    return true;
  }
  
	function wfSynchronize(){
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
		'user',
		array( 'user_name' ),
		'user_id > 0',
		__METHOD__
		);
		
		$text = array();
		foreach( $res as $row ){
			$nm = $row->user_name;
			try{
				$au = SSPAdmin::getProfile($nm);
				$au->syncWithDB();
				$au->syncRelationshipList();
				$au->save();
			}
			catch(SocProfException $e){
				$this->_output->addWikiText($e->__toString());
				return false;
			}
			$text[] = Title::makeTitle( NS_USER, $nm);
		}
		$this->_output->addWikiText("''' ".wfMsg('ssp-syncdone')." '''");
		$list = '[['.implode(']], [[', $text).']]';
		$this->_output->addWikiText($list);
		
		return true;
	}
}

class SocProfException extends Exception {
	private $url = 'http://www.mediawiki.org/wiki/Extension:SocialProfile';
	public function __toString(){
		$out = "''' ".wfMsg('ssp-nosp')." ''' \n $this->url";
		return $out;
	}
}

abstract class SpecialWikiVersion{
	abstract public function out();
	abstract public function user();
	public static function getVersion(SpecialPage &$sp){
		global $wgVersion;
		if ( version_compare( $wgVersion, '1.18', '<' ))
			return new VersionPre1_18();
		else return new VersionPost1_18($sp);
	}
}

class VersionPre1_18 extends SpecialWikiVersion{
	public function out(){
		global $wgOut;
		return $wgOut;
	}
	public function user(){
		global $wgUser;
		return $wgUser;
	}
}

//this should work but NEEDS TESTING NOT TESTED YET
class VersionPost1_18 extends SpecialWikiVersion{
	private $spec;
	public function __construct(SpecialPage &$sp){
		$this->spec = $sp;
	}
	public function out(){
		return $this->spec->getOutput();
	}
	public function user(){
		return $this->spec->getUser();
	}
}
