<?php
class SpecialSemanticSocialProfile extends SpecialPage {
  function __construct() {
    parent::__construct( 'SemanticSocialProfile', 'editinterface' );
    wfLoadExtensionMessages('SemanticSocialProfile');
  }

  function execute( $par ) {
    global $wgRequest, $wgOut, $wgUser, $wgContLang;
    
    //checks if the user has a right to access the page
		if ( !$this->userCanExecute($wgUser) ) {
			$this->displayRestrictionError();
			return;
		}
	
    $this->setHeaders();

	if ($wgRequest->wasPosted()) {
		//id hidden1 true show
		$hid = $wgRequest->getText('hiddenform');
		if($hid=='1'){
			//display results of the 1st step
			$this->wfsetUp();
			
			//prepare form to do step 2 
			$form = '<form name="syncssp" action="" method="POST">' . "\n".
				'<input type = "hidden" name = "hiddenform" value = "2">'. "\n".
				'<input type="submit" value="'.wfMsg('ssp-syncbutton').'"/>' . "\n".
			'</form>';
			$wgOut->addWikiText( "=== ".wfMsg('ssp-syncdesc')." ===");
			$wgOut->addWikiText( wfMsg('ssp-syncabout'));
			$wgOut->addHtml($form);
		}
		if($hid=='2'){
			$this->wfSynchronize();
			$gotomain = '<form name="setupssp" action="'.Title::newMainPage()->getFullURL().'" method="POST">' . "\n".
						'<input type = "submit" value = "'.wfMsg('main-page').'">'. "\n".
						'</form>';
			$wgOut->addWikiText( "\n '''".wfMsg('ssp-done')." '''" );
			$wgOut->addHtml($gotomain);
		}
	}
	else{
		$html = '<form name="setupssp" action="" method="POST">' . "\n".
				'<input type = "hidden" name = "hiddenform" value = "1">'. "\n".
				'<input type="submit" value="'.wfMsg('ssp-setupsspbutton').'"/>' . "\n".
			'</form>';
		$wgOut->addWikiText( "=== ".wfMsg('ssp-setupdesc')." ===");
		$wgOut->addWikiText( wfMsg('ssp-setupabout') );
		$wgOut->addHtml( $html);
	}
	//display 1st form
}

  function wfsetUp(){
	global $wgOut, $wgArticlePath, $wgServer;
    $directory = dirname(__FILE__) . '/setup';
    $wgOut->addWikiText("''' ".wfMsg('ssp-setupdone')." '''");

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
    $wgOut->addWikiText(implode(', ', $text));
    return true;
  }
  
	function wfSynchronize(){
		global $wgOut;
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
			$au = SSPAdmin::getProfile($nm);
			$au->syncWithDB();
			$au->syncFriendList();
			$au->save();
			$text[] = Title::makeTitle( NS_USER, $nm);
		}
		$wgOut->addWikiText("''' ".wfMsg('ssp-syncdone')." '''");
		$list = '[['.implode(']], [[', $text).']]';
		$wgOut->addWikiText($list);
		
		return true;
	}
}
