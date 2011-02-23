<?php
class SpecialRDFLoader extends SpecialPage {
	function __construct() {
		parent::__construct( 'RDFLoader', 'move' );
		wfLoadExtensionMessages('RDFLoader');
	}
 
	function execute( $par ) {
		global $wgRequest, $wgOut, $wgUser;;
		
		//checks if the user has a right to access the page
		if ( !$this->userCanExecute($wgUser) ) {
			$this->displayRestrictionError();
			return;
		}

		$this->setHeaders();

		# Do stuff
		# ...
		
		
		
		$form = '
	  <form method="post" action="">
	  <fieldset><legend>Enter a URL to your FOAF file</legend>
	  <input type="text" name = "url" size="50" />
	  <input type="submit" value="Obtain data" name="click" />
	  </legend></form>';
		
		if ($wgRequest->wasPosted()){
			$url = $wgRequest->getVal('url', 'fcuk');
			
			//uses curl to get data from an outer source
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL,$url);
			curl_setopt($ch, CURLOPT_HEADER, false);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($ch , CURLOPT_USERAGENT , 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
			$data = curl_exec($ch);
			curl_close($ch);

//  ******** variant 1 - shows some warnings if the xml is not valid

			try{ 
				$xml = simplexml_load_string($data);
				if($xml === false) throw new Exception("Bad input");
				$wgOut->addWikiText( $xml->asXML() );
			}
			catch(Exception $e){
				$wgOut->addWikiText($e->getMessage());
			}

			
//  ******** variant 2 - no warnings
			libxml_use_internal_errors(true);
			$xml = new DOMDocument('1.0', 'utf-8');
			$xml->loadXML($data);
			
			$errors = libxml_get_errors();
				if (empty($errors)) {
					$wgOut->addWikiText( $xml->saveXML() );
				}
				else $wgOut->addWikiText("Bad input");
		}
		else  $wgOut->addHTML( $form );
	}
}
?>
