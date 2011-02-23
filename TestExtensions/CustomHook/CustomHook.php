<?php

$wgHooks['BasicProfileSaved'][] = 'wfPrintSalo';

function wfPrintSalo($name){
	$text = $name." з'їв сала";
	$id = Title::newMainPage()->getArticleId();
	$ar = Article::newFromId($id);
	$ar->updateArticle($text, '', false, false );
	return true;
}
?>
