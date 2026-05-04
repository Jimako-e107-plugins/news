<?php
/**
 * Copyright (C) e107 Inc (e107.org), Licensed under GNU GPL (http://www.gnu.org/licenses/gpl.txt)
 * $Id$
 * 
 * News default templates
 */

if (!defined('e107_INIT'))  exit;


$NEWS_LIST_TEMPLATE = array();
 
$NEWS_LIST_INFO = array(
	'default' 	=> array('title' => LAN_DEFAULT, 	'description' => 'unused'),
	'list' 	    => array('title' => LAN_LIST, 		'description' => 'unused'),
);


// XXX The ListStyle template offers a listed summary of items with a minimum of 10 items per page. 
// As displayed by news.php?cat.1 OR news.php?all 
// {NEWS_BODY} should not appear in the LISTSTYLE as it is NOT the same as what would appear on news.php (no query) 

// Template/CSS to be reviewed for best bootstrap implementation 
$NEWS_LIST_TEMPLATE['list']['caption']	= LAN_PLUGIN_NEWS_NAME;
$NEWS_LIST_TEMPLATE['list']['start']	= '{SETIMAGE: w=400&h=350&crop=1}';
 

$NEWS_LIST_TEMPLATE['list']['end']	= '';
$NEWS_LIST_TEMPLATE['list']['item']	= '

		<div class="row row-fluid">
				<div class="span3 col-md-3">
                   <div class="thumbnail">
                        {NEWS_THUMBNAIL=placeholder}
                    </div>
				</div>
				<div class="span9 col-md-9">
                   <h3 class="media-heading">{NEWS_TITLE: link=1}</h3>
                      <p>
                       	{NEWS_SUMMARY}
					</p>
                    <p>
                       <a href="{NEWS_URL}" class="btn btn-small btn-primary">{LAN=READ_MORE}</a>
                   </p>
 				</div>
		</div>
		<hr class="visible-xs" />

';
 

$NEWS_LIST_WRAPPER['default']['item']['NEWS_IMAGE: item=1'] = '<span class="news-images-main pull-left float-left col-xs-12 col-sm-6 col-md-6">{---}</span>';

$NEWS_LIST_TEMPLATE['default']['caption'] = null; // add a value to user tablerender()
$NEWS_LIST_TEMPLATE['default']['start']	= '<!-- Default News Template -->';
$NEWS_LIST_TEMPLATE['default']['item'] = '
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

$NEWS_LIST_TEMPLATE['default']['end']	= '{NEWS_PAGINATION}';
 
