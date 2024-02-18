<?php
/**
 * Copyright (C) 2008-2011 e107 Inc (e107.org), Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
 * $Id$
 * 
 * News categories menu
 */

if (!defined('e107_INIT')) { exit; }

$cacheString = 'nq_' .  NEWS_DEF_CATEGORY_CACHE_STRING . '_menu_'.md5(serialize($parm).USERCLASS_LIST.e_LANGUAGE);
$cached = e107::getCache()->retrieve($cacheString);
$cached = false;
 
if(false === $cached)
{
	e107::plugLan(NEWS_DEF_PLUGIN_FOLDER);

	if(is_string($parm))
	{
		parse_str($parm, $parms);
	}
	else
	{
		$parms = $parm;
	}

	/** @var e_news_category_tree $ctree */
	//$ctree = e107::getObject('e_news2_category_tree', null, e_PLUGIN. 'news/ehandlers/e_news_category_tree.php');
	$ctree =  e107::getSingleton(NEWS_DEF_CATEGORY_MODEL_TREE,  e_PLUGIN . NEWS_DEF_PLUGIN_FOLDER .'/ehandlers/'. NEWS_DEF_CATEGORY_MODEL_FILE);
 
	$parms['tmpl']      = 'news_menu';
	$parms['tmpl_key']  = 'category';
 
	$template = e107::getTemplate(NEWS_DEF_PLUGIN_FOLDER, $parms['tmpl'], $parms['tmpl_key'], true, true);

	$cached = $ctree->loadActive()->render($template, $parms, "news_categories_menu");
 
	e107::getCache()->set($cacheString, $cached);
}

echo $cached;