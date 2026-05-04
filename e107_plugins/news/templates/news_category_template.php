<?php
/**
 * Copyright (C) e107 Inc (e107.org), Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
 * $Id$
 * 
 * News categorz templates
 * it is the default layout for the category controller, not a variant of the list layout.
 */

if (!defined('e107_INIT'))  exit;

$NEWS_CATEGORY__TEMPLATE = array();
 
$NEWS_CATEGORY__INFO = array(
	'default' 	=> array('title' => LAN_DEFAULT, 	'description' => 'unused'),
);


$NEWS_CATEGORY_WRAPPER['default']['item']['NEWS_IMAGE: item=1'] = '<span class="news-images-main pull-left float-left col-xs-12 col-sm-6 col-md-6">{---}</span>';

$NEWS_CATEGORY_TEMPLATE['default']['caption'] = null; // add a value to user tablerender()
$NEWS_CATEGORY_TEMPLATE['default']['start']	= '<!-- Default News Template -->';
$NEWS_CATEGORY_TEMPLATE['default']['item'] = '
		{SETIMAGE: w=400&h=400}
		<div class="default-item">
		<h2 class="news-title">{NEWS_TITLE: link=1}</h2>

        <hr class="news-heading-sep">
         	<div class="row">
        		<div class="col-md-6"><small>{GLYPH=user} &nbsp;{NEWS_AUTHOR} &nbsp; {GLYPH=time} &nbsp;{NEWS_DATE=short} </small></div>
        		<div class="col-md-6 text-right text-end options"><small>{GLYPH=tags} &nbsp;{NEWS_TAGS} &nbsp; {GLYPH=folder-open} &nbsp;{NEWSCATEGORY} </small></div>
        	</div>
        <hr>
          {NEWS_IMAGE: item=1}

          <p class="lead">{NEWS_SUMMARY}</p>
          {NEWS_VIDEO: item=1}
          <div class="text-justify">
          {NEWS_BODY}
          </div>
          <div class="text-right text-end">
          {EXTENDED}
          </div>
		  <hr>
			<div class="options">
			<div class="btn-group hidden-print">{NEWSCOMMENTLINK: glyph=comments&class=btn btn-default btn-secondary}{PRINTICON: class=btn btn-default btn-secondary}{PDFICON}{SOCIALSHARE}{ADMINOPTIONS: class=btn btn-default btn-secondary}</div>
			</div>
		</div>
';

$NEWS_CATEGORY_TEMPLATE['default']['end']	= '{NEWS_PAGINATION}';