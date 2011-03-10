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

# Get request data from, e.g.
    $param = $wgRequest->getText('param');
    $action = $wgRequest->getText('action');
    if ($wgRequest->wasPosted()) {
      $this->wfsetUp();
      $this->wfUpdateUserProfiles($wgContLang->getNsText(NS_USER));
    }

# Do stuff
# ...
    $output=wfMsg('ssp-setupdesc');

    $html = '<form name="setupssp" action="" method="POST">' . "\n".
            '<input type="submit" value="'. wfMsg('ssp-setupsspbutton') .'"/>' . "\n".
	    '</form>';
    $wgOut->addWikiText( $output);
    $wgOut->addHtml( $html);
  
  }

  function wfsetUp(){
    global $wgOut, $wgContLang;
    $directory = dirname(__FILE__) . '/setup';
    $wgOut->addWikiText($directory);
    //echo ($directory);
    //get the list of all files in a setup directory
    $filenames = scandir($directory);
    $summary = 'Semantic Social Profile installation procedure';
    foreach ($filenames as $filename) {
      if(is_file($directory.'/'.$filename)) {
	$fn = explode('#',$filename,2);
	$wgOut->addWikiText($fn[1]);
	$pageTitle = Title::newFromText($wgContLang->getNsText($fn[0]).":".$fn[1]);
	$page = new Article($pageTitle);
	$page->doEdit(file_get_contents($directory.'/'.$filename), $summary);

      }
    }
    return true;
  }
  
	function wfUpdateUserProfiles($user_ns)
	{
		global $wgOut, $wgContLang;
		$dbr = wfGetDB( DB_SLAVE );
		$res = $dbr->select(
		'user',
		array( 'user_name' ),
		'user_id > 0',
		__METHOD__
		);
		if($res){
			//if users were found - prepare data
			$summary = 'the userpage has been created using SSP Setup';
			$id = Title::newFromText($wgContLang->getNsText( NS_TEMPLATE ).":Semantic_Social_Profile")->getArticleId();
			$templatearticle = Article::newFromId( $id )->getRawText();
			
			$template = preg_replace("/^.*(?:<pre>)(.*)(?:<\/pre>).*$/isU","$1",$templatearticle, 1);
			//$wgOut->addWikiText("users found");
		}
		
		foreach( $res as $row ){
			$pageTitle = Title::newFromText($user_ns.":".$row->user_name);
			$id = $pageTitle->getArticleId();
			if($id > 0){
				// if the userpage already exists
				// add template to the bottom
				$page = Article::newFromId( $id );
				if(strpos($page->getRawText(),"{{Semantic Social Profile") === false)
					$page->updateArticle($page->getRawText().$template, '', false, false );
			}
			else {
				// need to create a userpage with the SSP template
				$page = new Article($pageTitle);
				$page->doEdit($template, $summary );
			}
			//$wgOut->addWikiText($row->user_name." id: ".$id);
		}
		return true;
	}
}
