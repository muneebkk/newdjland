<?php
include_once("headers/session_header.php");
require_once("headers/security_header.php");
require_once("headers/menu_header.php");
?>
<html ng-app='djland.editPlaysheet'>

<head>
	<link rel='stylesheet' href='https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/css/bootstrap.min.css'>

	<link rel="stylesheet" href="css/style.css?v=20231107" type="text/css">

	<script type="module">
		import SoundManager2 from 'https://cdn.skypack.dev/soundmanager2';
	</script>
	<script type='text/javascript' src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
	<script type='text/javascript' src="https://code.jquery.com/ui/1.11.3/jquery-ui.min.js"></script>

	<script type='text/javascript' src='js/constants.js'></script>
	<script type='text/javascript' src="https://ajax.googleapis.com/ajax/libs/angularjs/1.3.15/angular.min.js"></script>


	<script type='text/javascript' src="js/angular/sortable.js"></script>
	<script type='text/javascript' src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.1/js/bootstrap.min.js"></script>
	<script type='text/javascript' src='js/bootstrap/ui-bootstrap-tpls-0.12.0-withseconds.js'></script>

	<script type='text/javascript' src='js/playsheet/constants.js?v=20241107'></script>
	<script type='text/javascript' src='js/playsheet/playsheet.js?v=20241107'></script>
	<script type='text/javascript' src='js/api.js'></script>
	<script type='text/javascript' src='js/utils.js'></script>
</head>

<body class='wallpaper' ng-controller="PlaysheetController as playsheet">
	<script type='text/javascript'>
		var playsheet_id = "<?php if (isset($_GET['id'])) {
													echo $_GET['id'];
												} else {
													echo '-1';
												} ?>";
		var member_id = "<?php echo $_SESSION['sv_id']; ?>";
		var username = "<?php echo $_SESSION['sv_username']; ?>";
		var isAdmin = "<?php echo permission_level() >= $djland_permission_levels['administrator']['level'] ? 'true' : 'false'; ?>";
	</script>

	<?php print_menu(); ?>
	<div class='text-center' ng-show='playsheet.loading'><img class='rounded' width='300' height='20' src='images/loading.gif' /></div>
	<div id='socan' class='hidden'>
		<?php if (isset($_GET['socan'])) {
			echo $_GET['socan'];
		} elseif (isset($_POST['socan'])) {
			echo $_POST['socan'];
		} ?>
	</div>
	<div id='playsheet_id' class='hidden'>
		<?php if (isset($_POST['ps_id'])) {
			echo $_POST['ps_id'];
		} ?>
	</div>
	<div ng-hide="playsheet.member_shows || playsheet.loading == true" class='text-center'>You have no shows assigned to this account. Please ask a staff member to assign you to your show</div>
	<div ng-show="playsheet.member_shows" id='wrapper' ng-class="{'socan': playsheet.info.socan }">
		<div class='col1 side-padded'>
			<div class='col4 padded'>

				<div class='col1'>
					Show: <select id='show_select' ng-model="playsheet.show_value" ng-change="playsheet.updateShowValues(this)" ng-options="show.id as show.show.name for show in playsheet.member_shows | orderBy:'name'"
					style="max-width:97%;">
					</select>
				</div>
				<div class='col1'>
					Show Host(s): <input ng-model="playsheet.info.host"></input>
				</div>
				<div class='col1 double-padded-top'>
					Type:
					<select ng-model="playsheet.info.type" ng-change="playsheet.loadIfRebroadcast()">
						<option value="Syndicated">Syndicated</option>
						<option value="Live">Live</option>
						<option value="Rebroadcast">Rebroadcast</option>
						<option value="Simulcast">Simulcast</option>
					</select>
					<span class='col1' ng-show="playsheet.info.type == 'Rebroadcast'">
						<select ng-model="playsheet.existing_playsheet" ng-options="ps.id as ps.start_time + ' ' + ps.title | limitTo: 60 for ps in playsheet.existing_playsheets"></select>
						<button ng-click="playsheet.loadRebroadcast()">Load Rebroadcast</button>
					</span>

				</div>
				<div class='col1'>
					Language: <input ng-model="playsheet.info.lang"></input>
				</div>
				<div class='col1'>
					CRTC Category:
					<button class="crtc" ng-model="playsheet.info.crtc" ng-click="playsheet.changeCRTC()">{{playsheet.info.crtc}}</button>
				</div>
				
			</div>

			<div class='col4 padded' ng-if="playsheet.info.id > 0">

				<div ng-if="playsheet.podcast.url" >
					<h4 class='text-left'>Podcast Audio 🎶✅</h4>
					<p><a ng-href="{{playsheet.podcast.url}}" target="blank">{{playsheet.podcast.url}}</a></p>
				</div>
				<h4 ng-if="!playsheet.podcast.url" class='text-left'>Podcast Audio</h4>
				<div ng-if="playsheet.replacingAudio">
					<input type="file" name='audio_file' id='audio_file' style="max-width:270px"/>
					<button ng-if="playsheet.canUploadAudio() && !playsheet.podcast.url" type="button" ng-click='playsheet.uploadAudio()' >Upload</button>
					<button ng-if="playsheet.canUploadAudio() && playsheet.podcast.url" type="button" ng-click='playsheet.uploadAudio()' >Upload (Replace)</button>
					<button ng-hide="playsheet.uploadingAudio" ng-click='playsheet.cancelReplaceAudio()'>Cancel</button>
				</div>
				<div ng-if="playsheet.uploadingAudio">
					<img src='images/loading.gif' />
				</div>
				<div ng-if="playsheet.podcast.url && !playsheet.replacingAudio" 
					class='text-left'>
					<button ng-click='playsheet.beginReplaceAudio()'>Replace Audio</button>
					<button ng-click='playsheet.deleteAudio()'>Delete Audio</button>
				</div>
				<div ng-if="!playsheet.podcast.url && !playsheet.replacingAudio" class='text-left'>
					<button ng-click='playsheet.beginReplaceAudio()'>Upload Audio</button>
				</div>

			</div>
			<div class='col4 padded' ng-if="playsheet.info.id <= 0">
				<h4 class='text-left'>Podcast Audio </h4>
				<p>To upload, first Save as a Draft</p>
				<button ng-click='playsheet.saveDraft()'>Save Draft</button>
			</div>
			<div class='col2 padded'>
				<div class='col1'>
					
					<div class='col2 side-padded'>
						<div class="col1">
							Start: {{playsheet.info.start_time | date:'yyyy/MM/dd HH:mm:ss'}}
						</div>
						<div class='col1' ng-controller="datepicker as date">
							<button ng-click="date.open($event)">Change Day</button>
							<input class="date_picker" type="text" datepicker-popup="yyyy/MM/dd HH:mm:ss" ng-model="playsheet.info.start_time" is-open="date.opened" ng-required="true" close-text="Close" ng-hide="true" />
						</div>
						<div class="col1">
							End: {{playsheet.info.end_time | date:'yyyy/MM/dd HH:mm:ss'}}
						</div><br /><br />
						<div class='col1' ng-controller="datepicker as date">
							<button ng-click="date.open($event)" style="font-size:smaller">Change End Day</button>
							<input class="date_picker" type="text" datepicker-popup="yyyy/MM/dd HH:mm:ss" ng-model="playsheet.info.end_time" is-open="date.opened" ng-required="true" close-text="Close" ng-hide="true" />
						</div>
						
					</div>
					<div class='col2'>
						<div>
							Start Time:
							<select ng-options="n for n in [] | range:00:24" ng-change='playsheet.updateStart()' ng-model="playsheet.start_hour"></select> :
							<select ng-model="playsheet.start_minute" ng-change='playsheet.updateStart()' ng-update='playsheet.updateStart()' ng-options="n for n in [] | range:0:60 "></select> :
							<select ng-model="playsheet.start_second" ng-change='playsheet.updateStart()' ng-options="n for n in [] | range:0:60"></select>
						</div>
						<div class='padded'>
							End Time:
							<select ng-model="playsheet.end_hour" ng-options="n for n in [] | range:0:24 " ng-change="playsheet.updateEnd()"></select> :
							<select ng-model="playsheet.end_minute" ng-options="n for n in [] | range:0:60" ng-change="playsheet.updateEnd()"></select> :
							<select ng-model="playsheet.end_second" ng-options="n for n in [] | range:0:60" ng-change="playsheet.updateEnd()"></select>

							
							<div class='col1 double-padded-top'>
							<button aria-label="preview audio of the start" ng-click="playsheet.preview_start()">🔊 start</button>
							<button aria-label="preview audio of the end" ng-click="playsheet.preview_end()">🔊 end</button>
							<button aria-label="stop sound" ng-click="playsheet.stop_sound()">stop 🔊</button><br/>
							<span id='elapsed' ng-show='playsheet.playing'></span>
						</div>
                    </div>
					</div>
					<div class='col1 double-padded-top'>
						<!-- removed, make room for moving podcast tools here
						<div>
							Podcast Timing
							<button ng-click="playsheet.startShow()">
								Start Show Now
							</button>
							<button ng-click="playsheet.endShow()">
								End Show Now
							</button>
						</div>-->

					</div>
				</div>
			</div>
		</div>
		<div id='container'>
	<div ng-if="debug" style="height: 400px; width: 100%; overflow: scroll;">
		<hr/>
		<pre>

			[admin? {{playsheet.isAdmin}}]		
			[time_changed? {{playsheet.time_changed}}]				
		</pre>
		<pre ng-if="debug" style="font-size:0.8em; color:blue;">
			playsheet.podcast:
			{{playsheet.podcast | json}}
	
		</pre>
		<pre ng-if="debug" style="font-size:0.8em; color:green;">
			playsheet.info:
			{{playsheet.info | json}}
	
		</pre>
		<pre>
			row template:
			{{playsheet.row_template | json}}
		</pre>
	</div>
			<h3 class='double-padded-top'>Music</h3>
			<table>
				<tr class='music_row_heading border' ng-class="{socan: playsheet.info.socan }">
					<th class='side-padded'>#</th>
					<th><input value="Artist" readonly tooltip="{{playsheet.help.artist}}" ng-class="{socan: playsheet.info.socan }"></input></th>
					<th><input value="Song" ng-class="{socan: playsheet.info.socan}" readonly tooltip="{{playsheet.help.song}}" ng-class="{socan: playsheet.info.socan }"></input></th>
					<th><input value="Album" readonly tooltip-side:'bottom' tooltip="{{playsheet.help.album}}" ng-class="{socan: playsheet.info.socan }"></input></th>
					<th ng-show="playsheet.info.socan"><input ng-class="{socan: playsheet.info.socan}" value="Composer" readonly tooltip="{{playsheet.help['comp']}}" ng-class="{socan: playsheet.info.socan }"></input></th>
					<th ng-show="playsheet.info.socan"><input value="Duration(M:S)" tooltip="{{playsheet.help['timeHelp2']}}" class='socantiming'></input></th>
					<th><button tooltip="{{playsheet.help['playlist']}}" class="box playlist filled pad-top"></button></th>
					<th><button tooltip="{{playsheet.help['cancon']}}" class="box cancon filled pad-top"></button>
					<th class="fairplay"><button tooltip="{{playsheet.help['accessCon']}}" class="box accesscon fairplay filled pad-top"></button></th>
					<th class="fairplay"><button tooltip="{{playsheet.help['afroCon']}}" class="box afrocon fairplay filled pad-top"></button></th>
					<th><button tooltip="{{playsheet.help['femcon']}}" class="box femcon filled pad-top"></button></th>
					<th class="fairplay"><button tooltip="{{playsheet.help['indigiCon']}}" class="box indigicon fairplay filled pad-top"></button></th>
					<th class="fairplay"><button tooltip="{{playsheet.help['pocCon']}}" class="box poccon fairplay filled pad-top"></button></th>
					<th class="fairplay"><button tooltip="{{playsheet.help['queerCon']}}" class="box queercon fairplay filled pad-top"></button></th>
					<th><button tooltip="{{playsheet.help['is_local']}}" class="box is_local filled pad-top"></button></th>
					<th><button tooltip="{{playsheet.help['instrumental']}}" class="box instrumental filled pad-top"></button></th>
					<th><button tooltip="{{playsheet.help['partial']}}" class="box partial filled pad-top"></button></th>
					<th><button tooltip="{{playsheet.help['hit']}}" class="box hit filled pad-top"></button></th>
					<th ng-show="playsheet.info.socan"><button tooltip="{{playsheet.help['background']}}" class="box background filled pad-top"></button></th>
					<th ng-show="playsheet.info.socan"><button tooltip="{{playsheet.help['theme']}}" class="box theme filled pad-top"></button></th>
					<th><a href='https://www.crtc.gc.ca/eng/archive/2010/2010-819.HTM' target='_blank'><input style="width:30px; font-size:0.75em;" readonly tooltip='{{playsheet.help.crtc}}' value="Cat."></a></th>
					<th><input class="lang" tooltip='{{playsheet.help.lang}}' readonly value="Language" /></th>
					<th></th>
					<th></th>
				</tr>
				<tbody ui-sortable id='playitems' ng-change='playsheet.checkIfComplete()' ng-update='playsheet.checkIfComplete()' ng-model='playsheet.playitems'>
					<tr class='playitem border' ng-class="{socan: playsheet.info.socan }" playitem ng-repeat="playitem in playsheet.playitems track by $index"></tr>
				</tbody>
			</table>
			<button id="addRows" class='right' ng-click='playsheet.addFiveRows()'>Add Five More Rows</button>
			<br />
		</div>
		<div class='col1 double-padded-top'>
			<div class='span5col5 side-padded double-padded-top'>
				<h4> Ads, PSAs, Station IDs </h4>
				<table class='table table-responsive border'>
					<th style="width:10px;">#</th>
					<th style="width:185px;">Type</th>
					<th>Name</th>
					<tr promotion class='promotions' ng-model='playsheet.promotions' ng-repeat="promotion in playsheet.promotions"></tr>
				</table>
				<button ng-click='playsheet.addPromotion()' style="float:right; width:3em; height:2em;"> + </button>
			</div>
		</div>
		<div class='col1 side-padded double-padded-top'>
			<h4 class="text-left">Spoken Word Duration</h4>
			<div class='col1'>
				Hours<select class='required' ng-change='playsheet.checkIfComplete(); playsheet.updateSpokenword()' ng-model="playsheet.spokenword_hours" ng-options="n for n in [] | rangeNoPad:0:24"></select>
				&nbsp;&nbsp; Minutes<select class='required' ng-change='playsheet.checkIfComplete(); playsheet.updateSpokenword()' ng-model="playsheet.spokenword_minutes" ng-options="n for n in [] | rangeNoPad:0:60"></select>
			</div><br/>

			<h4 class="text-left padded">Episode Title</h4>
			<input class='wideinput required' ng-change='playsheet.checkIfComplete()' ng-model='playsheet.info.title' />
			<h4 class="text-left padded">Episode Description</h4>
			<textarea class='fill required' ng-change='playsheet.checkIfComplete()' ng-model='playsheet.info.summary'></textarea>
			<!-- commented out for now - need to implement feature for only some shows to upload their own audio
					<h4>Upload Episode Audio</h4>
					<input type="file" name='audio_file' id='audio_file'/> -->
		</div>

		<hr class="side-padded">

		<div class='col1 text-center'>
			<div class="blocker" ng-hide="playsheet.complete">
				{{ playsheet.missing }}
			</div>
			<button style="margin-right:2em;" class="large-button" ng-click="playsheet.saveDraft()" ng-hide="playsheet.info.status == 2">Save Draft</button>
			<div style="display:inline-block;" >
				<div style="display:inline-block; text-align:right;" ng-hide="!playsheet.complete">
					<label for="generate-new-podcast-audio" >Skip creating podcast audio</label> <input ng-disabled="playsheet.podcast.url" id="generate-new-podcast-audio" type="checkbox" ng-model='playsheet.skipPodcast'><br />
				</div>
				<button style="margin-left:0.2em;" class="large-button" ng-click="playsheet.submit()" ng-hide="!playsheet.complete || submitting">Submit</button>
		
			</div>
		
			<br />
			<div id="message" ng-show="message.text != '' && message.age < 6 ">{{message.text}}</div>
		</div>

		<!-- Popup Overlay during submission -->
		<div class="tracklist_overlay" ng-show="playsheet.tracklist_overlay">
			<button ng-click='playsheet.tracklist_overlay = !playsheet.tracklist_overlay'> X </button>
			<h3>{{playsheet.tracklist_overlay_header}}</h3>
			<h3 ng-show='!playsheet.error'>If you're done, please <a class='logout' href="index.php?action=logout" target="_self">click here to log out now</a> </h3>
			<div ng-show='playsheet.info.status == 2' class='text-center'> {{playsheet.podcast_status}}</div>
			<div class='text-center' id='playsheet_error'> </div>
			<hr />
			<h4 ng-show='!playsheet.error'>Tracklist:</h4>
			<ul>
				<li ng-repeat="playitem in playsheet.playitems track by $index">{{playitem.artist}} "{{playitem.song}}" - {{playitem.album}}</li>
			</ul>

		</div>
		<!-- Darkens Background during submission popup -->
		<div class="dark" ng-show="playsheet.tracklist_overlay"></div>

	</div>
	<div class="crtc_totals">
		<table class='col1 table-condensed'>
			<tr>
				<td> Category 2: </td>
				<td><span id='can_2_total'></span>/ <span id='can_2_required'>{{playsheet.show.cc_20_req}}</span>%</td>
				<td> Category 3: </td>
				<td><span id='can_3_total'></span>/ <span id='can_3_required'>{{playsheet.show.cc_30_req}}</span>%</td>
				<td> Fairplay Total: </td>
				<td><span id='fairplay_total'></span>/<span id='fem_required'>{{playsheet.show.fem_req}}</span>%</td>
				<td> New: </td>
				<td><span id='playlist_total'></span>/<span id='playlist_required'>{{playsheet.show.pl_req}}</span>%</td>
				<td> Hit: </td>
				<td><span id='hit_total'></span>/<span id='hit_max'>10</span>% Max</td>
			</tr>
		</table>
	</div>
</body>

</html>