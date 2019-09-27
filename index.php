<?php

define('API_KEY', 'A0EC1BC6F1E199CBC422A2659BAD781A'); // fill in your API key
define('STEAM_ID', 76561198016667233); // fill in your STEAM ID
include('vendor/autoload.php');
define('WEEK', 604800);
define('DAY', 86400);
define('HOUR', 3600);
define('MINUTE', 60);
$times = [];

function format_time($minutes) {
	return round($minutes / 60, 1);

	//scratch the rest
	$hrs = floor($minutes / 60);
	$minutes -= $hrs * 60;

	return $hrs.':'.$minutes;
}

function getWord($unit, $val) {
	switch ($unit) {
		case WEEK:
			if ($val > 1) { // weeks plural
				return $val.' týdny';
			} elseif ($val == 1) { // last week singular
				return 'minulý týden';
			}
		break;
		case DAY:
			if ($val > 2) { // before N days
				return $val.' dny';
			} elseif ($val == 2) { // day before yesterday
				return 'předevčírem';
			} else { // yesterday
				return 'včera';
			}
		break;
		case HOURS:
			if ($val > 1) { // N hours ago
				return $val.' hodinami';
			} elseif ($val == 1) {
				return '1 hodinou'; // 1 hour ago
			}
		break;
		case MINUTE:
			if ($val > 1) {
				return $val.' minutami'; // N minutes ago
			} elseif ($val == 1) { 
				return '1 minutou'; // one minute ago
			} else {
				return 'Před méně než minutou'; // less than one minute
			}
		break;
	}
}

function format_logoff_time($seconds) {
	$process = [
		WEEK => 'weeks',
		DAY => 'days',
		HOUR => 'hours',
		MINUTE => 'minutes'
	];
	
	foreach ($process as $divider => $var) {
		$times[$var] = floor($seconds / $divider);
		$seconds = $seconds - ($times[$var] * $divider);
	}

	switch (true) {
		case $times['weeks'] > 0:
			return getWord(WEEK, $times['weeks']);
		break;
		case $times['days'] > 0:
			return getWord(DAY, $times['days']);
		break;
		case $times['hours'] > 0:
			return getWord(HOUR, $times['hours']);
		break;
		default:
			return getWord(MINUTE, $times['minutes']);
		break;
	}
}

$client = new \Zyberspace\SteamWebApi\Client(API_KEY);
$steamUser = new \Zyberspace\SteamWebApi\Interfaces\ISteamUser($client);
$response = $steamUser->GetPlayerSummariesV2(STEAM_ID);

$player = $response->response->players[0];

$status = $player->gameextrainfo ? 'ingame' : ($player->personastate == 1 ? 'online' : 'offline');
$status_readable = $player->gameextrainfo ? $player->gameextrainfo : ($player->personastate == 1 ? 'Online' : 'Offline');

$recently_played_json = json_decode(file_get_contents('http://api.steampowered.com/IPlayerService/GetRecentlyPlayedGames/v0001/?key='.API_KEY.'&steamid='.STEAM_ID.'&format=json'), true);
$recently_played = $recently_played_json['response']['games'];

if ($player->personastate == 0) {
	$last_online_diff = time() - $player->lastlogoff;
	$status_readable = 'Naposledy online před '.format_logoff_time($last_online_diff);
}


?><!doctype html>
<html>
<head>
<link href="css/style.css" rel="stylesheet" />
</head>
<div class="badge">
	<div class="icon"><img src="<?= $player->avatarmedium?>" alt="" /></div>
	<div class="player-status <?= $status?>">
		<h3><?= $player->personaname?></h3>
		<h4><?=$status_readable?></h4>
	</div>
</div>
<div class="recent">
	<h4>Nedávno hrané:</h4>
	<ul>
	<?
	foreach ($recently_played as $game):?>
		<li>
			<span class="icon"><img src="http://media.steampowered.com/steamcommunity/public/images/apps/<?=$game['appid']?>/<?=$game['img_icon_url']?>.jpg" /></span>
			<span class="data"><?= $game['name']?><br />
				<span class="small"><?= format_time($game['playtime_2weeks'])?> hod. za poslední 2 týdny</span>
			</span>
		</li>
	<?endforeach;?>
	</ul>
</div>
</html>