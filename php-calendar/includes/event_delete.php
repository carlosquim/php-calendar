<?php
/*
   Copyright 2002 - 2005 Sean Proctor, Nathan Poiro

   This file is part of PHP-Calendar.

   PHP-Calendar is free software; you can redistribute it and/or modify
   it under the terms of the GNU General Public License as published by
   the Free Software Foundation; either version 2 of the License, or
   (at your option) any later version.

   PHP-Calendar is distributed in the hope that it will be useful,
   but WITHOUT ANY WARRANTY; without even the implied warranty of
   MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
   GNU General Public License for more details.

   You should have received a copy of the GNU General Public License
   along with PHP-Calendar; if not, write to the Free Software
   Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

if ( !defined('IN_PHPC') ) {
       die("Hacking attempt");
}

function remove_event($id)
{
	global $db;

	$sql = 'DELETE FROM '.SQL_PREFIX ."events WHERE id = '$id'";
	$result = $db->Execute($sql)
		or db_error(_('Error while removing an event.'), $sql);

	return ($db->Affected_Rows($result) > 0);
}

function event_delete()
{
	global $config;

	if(!check_user() && $config['anon_permission'] < 2) {
		soft_error(_('You do not have permission to delete events.'));
	}

	$del_array = explode('&', $_SERVER['QUERY_STRING']);

	$html = array();

	foreach($del_array as $del_value) {
		list($drop, $id) = explode("=", $del_value);

		if(preg_match('/^id$/', $drop) == 0) continue;

		if(remove_event($id)) {
			$html[] = tag('p', _('Removed item').": $id");
		} else {        
			$html[] = tag('p', _('Could not remove item').": $id");
		}
	}

	if(count($html) == 0) {
		$html[] = tag('p', _('No items selected.'));
	}

	return array_merge(tag('div', attributes('class="box"',
                                        'style="width: 50%"')), $html);
}

?>
