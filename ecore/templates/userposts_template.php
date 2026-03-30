<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2011 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * User posts page template
 *
 * $URL$
 * $Id$
 */

if (!defined('e107_INIT')) { exit; }

if (!defined("USER_WIDTH")){ define("USER_WIDTH", "width:95%"); }

$USERPOSTS_TEMPLATE['np_table'] = "<p class='nextprev'>{USERPOSTS_NEXTPREV}</p>";
$USERPOSTS_NP_TABLE = $USERPOSTS_TEMPLATE['np_table']; // BC, will be removed

// $USERPOSTS_NP_TABLE = "<div class='nextprev'>{USERPOSTS_NEXTPREV}</div>";

// ##### USERPOSTS_COMMENTS TABLE -----------------------------------------------------------------
	$USERPOSTS_TEMPLATE['comments_table_start'] = "
	<div id='up-comments-container'>
		{NEXTPREV}
		<table class='table fborder up-comments' id='up-comments'>
	";
	$USERPOSTS_COMMENTS_TABLE_START = $USERPOSTS_TEMPLATE['comments_table_start']; // BC, will be removed

	$USERPOSTS_TEMPLATE['comments_table'] = "
		<tr>
			<td class='fcaption'>
				{USERPOSTS_COMMENTS_HREF_PRE}<b>{USERPOSTS_COMMENTS_HEADING}</b></a>
				<span class='smalltext'>{USERPOSTS_COMMENTS_DATESTAMP} ({USERPOSTS_COMMENTS_TYPE})</span>
			</td>
		</tr>
		<tr>
			<td class='forumheader3'>
				{USERPOSTS_COMMENTS_COMMENT}&nbsp;
			</td>
		</tr>
	";
	$USERPOSTS_COMMENTS_TABLE = $USERPOSTS_TEMPLATE['comments_table']; // BC, will be removed

	$USERPOSTS_TEMPLATE['comments_table_end'] = "
		</table>
		{NEXTPREV}
	</div>";
	$USERPOSTS_COMMENTS_TABLE_END =  $USERPOSTS_TEMPLATE['comments_table'];

	$USERPOSTS_TEMPLATE['comments_table_empty'] = "
		<tr>
			<td class='forumheader3'>
				<span class='mediumtext'>".UP_LAN_7."</span>
			</td>
		</tr>
	";

 