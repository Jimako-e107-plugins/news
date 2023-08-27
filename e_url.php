<?php
/*
 * e107 Bootstrap CMS
 *
 * Copyright (C) 2008-2015 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * IMPORTANT: Make sure the redirect script uses the following code to load class2.php:
 *
 * 	if (!defined('e107_INIT'))
 * 	{
 * 		require_once(__DIR__.'/../../class2.php');
 * 	}
 *
 */

if (!defined('e107_INIT')) { exit; }

// v2.x Standard  - Simple mod-rewrite module.

class news_url // plugin-folder + '_url'
{

	public $alias = 'news';

 

	function config()
	{
		$config = array();

		$pref = e107::pref('core','url_aliases'); // [en][news]

		$alias = null;

		if(!empty($pref[e_LAN]))
		{
			foreach($pref[e_LAN] as $k=>$v)
			{
				if($v === 'news' )
				{
					$alias = $k;
					break;
				}
			}
		}


		/* list  -  list/category  id */
		/* cat  - list/short id */
		/* day. month  id  list/day list/month '
		/* item, extend  view/item */
		/* default  list/items */

		/* all  list/all */ 
		/* default  list/items */ 

		/* news/ + route */
		$alias = 'blog';
		
		/* route:  news/list/author */

		$config['author'] = array(
			'alias'         => $alias,
			'regex'			=> '^{alias}/author/(\d*)-(.*)\/(?:\?)(.*)$',
			'sef'			=> '{alias}/author/{news_author}-{user_name}/',			 
			'redirect'		=> '{e_PLUGIN}news/news.php?action=author&id=$1&sef=$2'
		);


		$config['item'] = array(
			'alias'         => $alias,
			'regex'			=> '^{alias}/view/(\d*)-([\w-]*)\/?\??(.*)',
			'sef'			=> '{alias}/view/{news_id}-{news_sef}',			// {faq_info_sef} is substituted with database value when parsed by e107::url();
			'redirect'		=> '{e_PLUGIN}news/news.php?action=item&newsid=$1&sef=$2'
		);


		/* news/list/short */
		$config['category'] = array(
			'alias'         => $alias,
			'regex'			=> '^{alias}/category/(\d*)-([\w-]*)\/?\??(.*)',
			'sef'			=> '{alias}/category/{category_id}-{category_sef}/',			// {faq_info_sef} is substituted with database value when parsed by e107::url();
			'redirect'		=> '{e_PLUGIN}news/news.php?action=category&category=$1&sef=$2'
		);

/*
		$config['tag'] = array(
			'alias'         => $alias,
			'regex'         => 'blog\/([^\/]*)\/([\d]*)(?:\/|-)([\w-]*)/?\??(.*)',
			'sef'			=> 'blog/tag/1/{tag}/',			 
			'redirect'		=> '{e_PLUGIN}news/news.php?tag=$3&$4'
		);
*/

		$config['tag'] = array(
			'alias'         => $alias,
			'regex'         => '^{alias}/tag/(.*)(?:\/)(.*)(?:\/?)(.*)',
			'sef'			=> '{alias}/tag/{tag}/',			 
			'redirect'		=> '{e_PLUGIN}news/news.php?action=tag&tag=$1&$2'
		);
 
		$config['all'] = array(
			'alias'         => $alias,
			'regex'			=> '^{alias}/\?(.*)',
			'sef'			=> '{alias}/',			 
			'redirect'		=> '{e_PLUGIN}news/news.php?action=all&$1'
		);
		

		$config['index'] = array(
			'alias'         => $alias,
		 	'regex'			=> '^{alias}/$', 						// matched against url, and if true, redirected to 'redirect' below.
			'sef'			=>  '{alias}/', 	// used by e107::url(); to create a url from the db table.
			'redirect'		=> '{e_PLUGIN}news/news.php?action=index', 		// file-path of what to load when the regex returns true.

		);




		return $config;
	}



}