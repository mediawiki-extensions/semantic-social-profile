<?php
error_reporting(E_ALL);

if (!defined('MEDIAWIKI')) {
  echo <<<EOT
To install my extension, put the following line in LocalSettings.php:
  require_once( "\$IP/extensions/UkRusTag/UkRusTag.php" );
  To use the feature create an article and put some ukrainian text like this
  <ukr>the text goes here</ukr>
EOT;
  exit( 1 );
}

$wgExtensionCredits['tag'][] = array(
  'name' => 'UkRusTag',
  'author' => 'Dmitry Pokoptsev',
  'url' => 'http://www.mediawiki.org/wiki/Extension:UkRusTag',
  'description' => 'Allows to transcript Ukrainian text into Russian',
  'descriptionmsg' => 'UkRusTag-desc',  
  'version' => '0.0.1',
);

$wgHooks['ParserFirstCallInit'][] = 'efUkRusTagParserInit';

function efUkRusTagParserInit( &$parser ) {
  $parser->setHook( 'ukr', 'efUkRusTagRender' );
  return true;
}

function efUkRusTagRender( $input, $args, $parser, $frame ) {
$k = array ("и","е","і","ї","є","’","щ","И","Е","І","Ї","Є","'","Щ");
$v = array ("ы","э","и","йи","е","ъ","шч","Ы","Э","И","Йи","Е","ъ","Шч");
$res = str_replace($k, $v, $input);

return $res;
}

?>

