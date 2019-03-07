<?php
/* <one line to give the program's name and a brief idea of what it does.>
 * Copyright (C) <2016>  <jamelbaz@gmail.com>
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */


// Load Dolibarr environment
if (false === (@include '../main.inc.php')) {  // From htdocs directory
	require '../../main.inc.php'; // From "custom" directory
}

dol_include_once('/dolifullcalendar/class/dolifullcalendar.class.php');

	$event = new Dolifullcalendar($db);
    
$id = GETPOST('id');
$start = GETPOST('start');
$end = GETPOST('end');
$color = GETPOST('color');



	$event->id = $id;
	$event->start = $start;
	$event->end = $end;
	$event->color = $color;
	
	$k = $event->updateDate($user);
	if(!empty($k)){
		print 'OK';
	}
	
	

	
?>
