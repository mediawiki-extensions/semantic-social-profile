<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
	echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
require_once( "\$IP/extensions/RDFLoader/RDFLoader.php" );
EOT;
	exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
	'name' => 'RDFLoader',
	'author' => 'Dmitry Pokoptsev',
	'url' => 'http://www.mediawiki.org/wiki/Extension:RDFLoader',
	'description' => 'This extencion loads an rdf file from a given link',
	'descriptionmsg' => 'RDFLoader-desc',
	'version' => '0.0.1',
);

$dir = dirname(__FILE__) . '/';

$wgAutoloadClasses['SpecialRDFLoader'] = $dir . 'RDFLoader_body.php';
$wgExtensionMessagesFiles['RDFLoader'] = $dir . 'RDFLoader.i18n.php';
//$wgExtensionAliasesFiles['RDFLoader'] = $dir . 'RDFLoader.alias.php';
$wgSpecialPages['RDFLoader'] = 'SpecialRDFLoader';

//special page gets put under the right heading
$wgSpecialPageGroups['RDFLoader'] = 'smw_group';



?>
