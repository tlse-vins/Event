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
    
$langs->load('dolifullcalendar@dolifullcalendar');



/************************/
$action = GETPOST('action', 'alpha');
$title = GETPOST('title');
$start = GETPOST('start');
$end = GETPOST('end');
$color = GETPOST('color');
$id = GETPOST('id');
$delete = GETPOST('delete');
$delete = GETPOST('delete');

	// Filter
$year = $_GET["year"];
$today = 0;
if ($year == 0) {
	$year_current = strftime("%Y", time());
	$year_start = $year_current;
	$today = date("Y-m-d");
} else {
	$year_current = $year;
	$year_start = $year;
	$today = $year_current;
}

if($action == "add"){
	
	$event->title = $title;
	$event->start = $start;
	$event->end = $end;
	$event->color = $color;
	
	$idaction=$event->add($user);
	
}

if($action == "edit"){
	$event->id = $id;
	if(!empty($delete)){
		$event->delete($user);
	}else{
		
		$event->title = $title;
		$event->color = $color;
		$event->updateTitle($user);
	}
	
}
llxHeader('', $langs->trans('Calendar'), '');

?>


    <div data-role="page" id="index">
    
        <div data-role="header">
            <h1><img src="<?php print dol_buildpath('dolifullcalendar/img/calendar.png', 1);?>"> <?php print $langs->trans("Calendar");?></h1>
			<small>
			<?php 
			$textprevyear = '<a href="' . $_SERVER["PHP_SELF"] . '?year=' . ($year_current - 1) . '">' . img_previous() . '</a>';
			$textnextyear = '&nbsp;<a href="' . $_SERVER["PHP_SELF"] . '?year=' . ($year_current + 1) . '">' . img_next() . '</a>';

			print $textprevyear . " " . $langs->trans("Year") . " " . $year_start . " " . $textnextyear;
			
			$events = $event->fetchAll($year_current);
			?>
			</small>
        </div><!-- /header -->
        
        <div data-role="content">       
            <div id='calendar' style="margin:20px 10px;"></div>
        </div><!-- /content -->
    
    </div><!-- /page -->
	
	<!-- Modal -->
	<div class="modal fade" id="ModalAdd" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		<form action="<?php print $_SERVER["PHP_SELF"]; ?>" method="post">
			<input type="hidden" name="token" value="<?php print $_SESSION['newtoken']; ?>">
			<input type="hidden" name="action" value="add">
			
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><?php print $langs->trans("addEvent"); ?></h4>
		  </div>
		  <div class="modal-body">
				<table class="table" width="100%">
					<tbody>
						<tr>
							<td width="12%" align="right"><?php print $langs->trans("Title"); ?></td>
							<td>
								<input type="text" name="title" class="form-control" id="title" placeholder="Title">
							</td>
						</tr>
						<tr>
							<td  align="right"><?php print $langs->trans("Color"); ?></td>
							<td>
								<select name="color" class="form-control" id="color">
								  <option value=""><?php print $langs->trans("Choose"); ?></option>
								  <option style="color:#0071c5;" value="#0071c5">&#9724; <?php print $langs->trans("Darkblue"); ?></option>
								  <option style="color:#40E0D0;" value="#40E0D0">&#9724; <?php print $langs->trans("Turquoise"); ?></option>
								  <option style="color:#008000;" value="#008000">&#9724; <?php print $langs->trans("Green"); ?></option>						  
								  <option style="color:#FFD700;" value="#FFD700">&#9724; <?php print $langs->trans("Yellow"); ?></option>
								  <option style="color:#FF8C00;" value="#FF8C00">&#9724; <?php print $langs->trans("Orange"); ?></option>
								  <option style="color:#FF0000;" value="#FF0000">&#9724; <?php print $langs->trans("Red"); ?></option>
								  <option style="color:#000;" value="#000">&#9724; <?php print $langs->trans("Black"); ?></option>
								  
								</select>
							</td>
						</tr>
						<tr>
							<td  align="right"><?php print $langs->trans("Startdate"); ?></td>
							<td>
								<input type="text" name="start" class="form-control" id="start" readonly>
							</td>
						</tr>
						<tr>
							<td  align="right"><?php print $langs->trans("Enddate"); ?></td>
							<td >
								<input type="text" name="end" class="form-control" id="end" readonly>
							</td>
						</tr>
					</tbody>
				</table>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php print $langs->trans("Close"); ?></button>
			<button type="submit" class="btn btn-primary"><?php print $langs->trans("Savechanges"); ?></button>
		  </div>
		</form>
		</div>
	  </div>
	</div>
	
	<!-- Modal -->
	<div class="modal fade" id="ModalEdit" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
	  <div class="modal-dialog" role="document">
		<div class="modal-content">
		<form action="<?php print $_SERVER["PHP_SELF"]; ?>" method="post">
			<input type="hidden" name="token" value="<?php print $_SESSION['newtoken']; ?>">
			<input type="hidden" name="action" value="edit">
			
		  <div class="modal-header">
			<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
			<h4 class="modal-title" id="myModalLabel"><?php print $langs->trans("EditEvent"); ?><</h4>
		  </div>
		  <div class="modal-body">
				<table class="table" width="100%">
					<tbody>
						<tr>
							<td width="12%" align="right"><?php print $langs->trans("Title"); ?> </td>
							<td>
								<input type="text" name="title" class="form-control" id="title" placeholder="Title">
							</td>
						</tr>
						<tr>
							<td  align="right"><?php print $langs->trans("Color"); ?></td>
							<td>
								<select name="color" class="form-control" id="color">
								  <option value=""><?php print $langs->trans("Choose"); ?></option>
								  <option style="color:#0071c5;" value="#0071c5">&#9724; <?php print $langs->trans("Darkblue"); ?></option>
								  <option style="color:#40E0D0;" value="#40E0D0">&#9724; <?php print $langs->trans("Turquoise"); ?></option>
								  <option style="color:#008000;" value="#008000">&#9724; <?php print $langs->trans("Green"); ?></option>						  
								  <option style="color:#FFD700;" value="#FFD700">&#9724; <?php print $langs->trans("Yellow"); ?></option>
								  <option style="color:#FF8C00;" value="#FF8C00">&#9724; <?php print $langs->trans("Orange"); ?></option>
								  <option style="color:#FF0000;" value="#FF0000">&#9724; <?php print $langs->trans("Red"); ?></option>
								  <option style="color:#000;" value="#000">&#9724; <?php print $langs->trans("Black"); ?></option>
								  
								</select>
							</td>
						</tr>
						
						<tr> 
							<td><?php print $langs->trans("DeleteEvent"); ?></td>
							<td ><input type="checkbox"  name="delete"></td>
							  
						</tr>
			  
						<input type="hidden" name="id" class="form-control" id="id">
					</tbody>
				</table>
		  </div>
		  <div class="modal-footer">
			<button type="button" class="btn btn-default" data-dismiss="modal"><?php print $langs->trans("Close"); ?></button>
			<button type="submit" class="btn btn-primary"><?php print $langs->trans("Savechanges"); ?></button>
		  </div>
		</form>
		</div>
	  </div>
	</div>
	
<script>
<?php 
$lang = "en";
if($langs->defaultlang == "fr_FR"){
	$lang = "fr";
}
 ?>
	$(document).ready(function() {
		$('#calendar').fullCalendar({
			header: {
				left: 'prev,next today',
				center: 'title',
				right: 'month,agendaWeek,agendaDay'
			},
			defaultDate: '<?php print $today; ?>',
			editable: true,
			eventLimit: true, // allow "more" link when too many events
			selectable: true,
			selectHelper: true,			
			lang: '<?php print $lang; ?>',
			select: function(start, end) {
				
				$('#ModalAdd #start').val(moment(start).format('YYYY-MM-DD HH:mm:ss'));
				$('#ModalAdd #end').val(moment(end).format('YYYY-MM-DD HH:mm:ss'));
				$('#ModalAdd').modal('show');
			},
			eventRender: function(event, element) {
				element.bind('dblclick', function() {
					$('#ModalEdit #id').val(event.id);
					$('#ModalEdit #title').val(event.title);
					$('#ModalEdit #color').val(event.color);
					$('#ModalEdit').modal('show');
				});
			},
			eventDrop: function(event, delta, revertFunc) { // si changement de position

				edit(event);

			},
			eventResize: function(event,dayDelta,minuteDelta,revertFunc) { // si changement de longueur

				edit(event);

			},
			events: [
			<?php foreach($events as $event): 
			
				$start = explode(" ", $event['start']);
				$end = explode(" ", $event['end']);
				if($start[1] == '00:00:00'){
					$start = $start[0];
				}else{
					$start = $event['start'];
				}
				if($end[1] == '00:00:00'){
					$end = $end[0];
				}else{
					$end = $event['end'];
				}
			?>
				{
					id: "<?php print $event['id']; ?>",
					title: "<?php print $event['title']; ?>",
					start: "<?php print $start; ?>",
					end: "<?php print $end; ?>",
					color: "<?php print $event['color']; ?>",
				},
			<?php endforeach; ?>
			]
		});
		
		function edit(event){
			start = event.start.format('YYYY-MM-DD HH:mm:ss');
			end = '';
			if(event.end){
				end = event.end.format('YYYY-MM-DD HH:mm:ss');
			}else{
				end = start;
			}
			
			id =  event.id;
			
			
			$.ajax({
			 url: 'editEventDate.php',
			 type: "POST",
			 data: {id:id,start:start,end:end},
			 success: function(rep) {
					if(rep == 'OK'){
						
						$.jnotify("<?php print $langs->trans("Saved"); ?>",
						"3000",
						false,
						{ remove: function (){} } );
						
						//alert("<?php print $langs->trans("Saved"); ?>");
					}else{
						$.jnotify("<?php print $langs->trans("NotSaved"); ?>",
						"error",
						false,
						{ remove: function (){} } );
						//alert('<?php print $langs->trans(""); ?>'); 
					}
				}
			});
		}
		
	});

</script>

<?php llxFooter();?>