<?php
class SpecialSemanticSocialProfile extends SpecialPage {
  function __construct() {
    parent::__construct( 'SemanticSocialProfile' );
    wfLoadExtensionMessages('SemanticSocialProfile');
  }

  function execute( $par ) {
    global $wgRequest, $wgOut;

    $this->setHeaders();

# Get request data from, e.g.
    $param = $wgRequest->getText('param');
    $action = $wgRequest->getText('action');
    if ($action == 'setupssp') {
      $this->wfsetUp('bla');
    }

# Do stuff
# ...
    $output="Hello world!";

    $html = '<form name="setupssp" action="" method="POST">' .
	    '<input type="hidden" name="action" value="setupssp" />' . "\n" .
            '<input type="submit" value="'. wfMsg('ssp_setupsspbutton') .'"/>' . "\n".
	    '</form>';
    $wgOut->addWikiText( $output);
    $wgOut->addHtml( $html);
  
  }

  function wfsetUp($name){
    global $wgOut;
    $directory = dirname(__FILE__) . '/setup';
    $wgOut->addWikiText($directory);
    echo ($directory);
    //get the list of all files in a setup directory
    $filenames = scandir($directory);
    $summary = 'Semantic Social Profile installation procedure';
    foreach ($filenames as $filename) {
      if(is_file($directory.'/'.$filename)) {
	$fn = urldecode($filename);
	$wgOut->addWikiText($fn);
	$pageTitle = Title::newFromText($fn);
	$page = new Article($pageTitle);
	$page->doEdit(file_get_contents($directory.'/'.$filename), $summary);

      }
    }
    return true;
  }}
