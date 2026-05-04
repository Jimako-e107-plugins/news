<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Sitelinks configuration module - News
 *
 * $URL$
 * $Id$
 *
*/

if (!defined('e107_INIT')) { exit; }

//TODO Lans

class news_sitelink // include plugin-folder in the name.
{
	function config()
	{	
		$links = array();
		
		$links[] = array(
			'name'			=> "News Category List",
			'function'		=> "news_category_list",
			'description' 	=> ""
		);	
		
		$links[] = array(
			'name'			=> "News Category Pages",
			'function'		=> "news_category_page",
			'description' 	=> ""
		);	
			
		$links[] = array(
			'name'			=> "Last 10 News Items",
			'function'		=> "last_ten",
			'description' 	=> ""
		);

		
		return $links;
	}




	function news_category_page()
	{
		return $this->news_category_list('category');	
	}
	
	
	function news_cats() // BC
	{
		return $this->news_category_list();
	}


	function news_category_list($type=null) 
	{

		$ctree =  e107::getSingleton(NEWS_DEF_CATEGORY_MODEL_TREE,  e_PLUGIN . NEWS_DEF_PLUGIN_FOLDER . '/ehandlers/' . NEWS_DEF_CATEGORY_MODEL_FILE);
		$data = $ctree->loadActive();

		$sublinks = array();

		foreach ($ctree->getTree() as $cat)
		{
			//$row = (array) $cat;
			//$row['id'] = $cat->getId();
			$sublinks[] = array(
				'link_name'			=> $cat->sc_news_category_title(),
				'link_url'			=> $cat->sc_news_category_url(),
				'link_description'	=> $cat->sc_news_category_description(),
				'link_button'		=> '',
				'link_category'		=> '',
				'link_order'		=> $cat->sc_news_category_order(),
				'link_parent'		=> '',
				'link_open'			=> '',
				'link_class'		=> 0
			);
 
		}

		$sublinks[] = array(
			'link_name'			=> LAN_MORE,
			'link_url'			=> e107::url('news', 'all'),
			'link_description'	=> '',
			'link_button'		=> '',
			'link_category'		=> '',
			'link_order'		=> '',
			'link_parent'		=> '',
			'link_open'			=> '',
			'link_class'		=> 0
		);

		return $sublinks;
	}


	function last_ten()
	{
		$sql = e107::getDb();
		$sublinks = array();
		
		$nobody_regexp = "'(^|,)(".str_replace(",", "|", e_UC_NOBODY).")(,|$)'";
		$query = "SELECT * FROM #news WHERE news_class REGEXP '".e_CLASS_REGEXP."' AND NOT (news_class REGEXP ".$nobody_regexp.") ORDER BY news_datestamp DESC LIMIT 10";


		if($sql->gen($query))
		{		
			while($row = $sql->fetch())
			{
				$sublinks[] = array(
					'link_name'			=> $row['news_title'],
					'link_url'			=> e107::url('news', 'item', $row, array('full' => 1)), 
					'link_description'	=> $row['news_summary'],
					'link_button'		=> '',
					'link_category'		=> '',
					'link_order'		=> '',
					'link_parent'		=> '',
					'link_open'			=> '',
					'link_class'		=> intval($row['news_class'])
				);


			}
			
			$sublinks[] = array(
					'link_name'			=> LAN_MORE,
					'link_url'			=> e107::getUrl()->create('news/list/all'),  
					'link_description'	=> '',
					'link_button'		=> '',
					'link_category'		=> '',
					'link_order'		=> '',
					'link_parent'		=> '',
					'link_open'			=> '',
					'link_class'		=> 0
				);



				
			return $sublinks;
	    };
	}


	
}



