<?php
# Alert the user that this is not a valid entry point to MediaWiki if they try to access the special pages file directly.
if (!defined('MEDIAWIKI')) {
  echo (' To install my extension, put the following line in LocalSettings.php:
    require_once( "\$IP/extensions/SemanticSocialProfile/SemanticSocialProfile.php" );
  ');

  exit( 1 );
}

$wgExtensionCredits['specialpage'][] = array(
    'name' => 'SemanticSocialProfile',
    'author' => 'Yury Katkov, Dmitry Pokoptsev',
    'url' => 'http://www.mediawiki.org/wiki/Extension:SemanticSocialProfile',
    'description' => 'Default description message',
    'descriptionmsg' => 'ssp-desc',
    'version' => '0.0.0',
    );

$dir = dirname(__FILE__) . '/';

require_once( "$IP/extensions/SemanticSocialProfile/ProfileTemplateUpdater.php" ); 

$wgAutoloadClasses['SpecialSemanticSocialProfile'] = $dir . 'SemanticSocialProfile_body.php'; # Location of the SpecialSemanticSocialProfile class (Tell MediaWiki to load this file)
$wgExtensionMessagesFiles['SemanticSocialProfile'] = $dir . 'SemanticSocialProfile.i18n.php'; # Location of a messages file (Tell MediaWiki to load this file)
$wgExtensionAliasesFiles['SemanticSocialProfile'] = $dir . 'SemanticSocialProfile.alias.php'; # Location of an alias file (Tell MediaWiki to load this file)

$wgSpecialPages['SemanticSocialProfile'] = 'SpecialSemanticSocialProfile'; # Tell MediaWiki about the new special page and its class name

$wgSpecialPageGroups['SemanticSocialProfile'] = 'users';
