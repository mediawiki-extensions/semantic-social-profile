<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/UkRusParser/UkRusParser.php" );
To use the feature create an article and put some ukrainian text like this
{{#ukrus:  the text goes here }}
EOT;
	exit( 1 );
}

$wgExtensionCredits['other'][] = array(
	'name' => 'UkRusParser',
	'author' => 'Dmitry Pokoptsev',
	'url' => 'http://www.mediawiki.org/wiki/...',
	'description' => 'This extencion makes ukrainian text transcripted to rusiian',
	'descriptionmsg' => 'UkRusParser-desc',
	'version' => '0.0.1',
);

$wgHooks['ParserFirstCallInit'][] = 'wfURP_Setup';
//setting up magic word hook

$wgHooks['LanguageGetMagic'][] = 'wfURP_Magic';

//defining the magic word
define('URPNAME','ukrus');

function wfURP_Setup($parser){
	//link the parser name with it's implementation
	$parser->setFunctionHook('URPNAME','wfURP_Render');
	return true;
}

function wfURP_Magic(&$magicWords, $langID){
	//declaring word as a magic one
	$magicWords['URPNAME'] = array(0,URPNAME);
	return true;
}

function wfURP_Render(&$parser, $text){
	$k = array ("и","е","і","ї","є","’","щ","И","Е","І","Ї","Є","'","Щ");
	$v = array ("ы","э","и","йи","е","ъ","шч","Ы","Э","И","Йи","Е","ъ","Шч");
	$res = str_replace($k, $v, $text);
	return $res;
}

?>
