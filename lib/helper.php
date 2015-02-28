<?php

require_once('program.php');

function link_index()
{
	return '';
}

function link_room($room)
{
	return rawurlencode($room).'/';
}

function link_player($room, $format = 'hd', $translated = false)
{
	$defaultformat = room_has_hd($room) ? 'hd' : 'sd';

	return rawurlencode($room).'/'.($defaultformat == $format ? '' : rawurlencode($format).'/').($translated ? 'translated/' : '');
}

function link_stream($protocol, $room, $format, $translated = false)
{
	$language = $translated ? 'translated' : 'native';

	switch ($protocol) {
		case 'rtmp':
			return 'rtmp://cdn.c3voc.de/stream/'.rawurlencode(streamname($room)).'_'.rawurlencode($language).'_'.rawurlencode($format);

		case 'hls':
			return 'http://cdn.c3voc.de/hls/'.rawurlencode(streamname($room)).'_'.rawurlencode($language).($format == 'auto' ? '' : '_'.rawurlencode($format)).'.m3u8';

		case 'webm':
			return 'http://cdn.c3voc.de/'.rawurlencode(streamname($room)).'_'.rawurlencode($language).'_'.rawurlencode($format).'.webm';

		case 'audio':
			if(in_array($room, array('lounge', 'ambient')))
				return 'http://cdn.c3voc.de/'.rawurlencode(streamname($room)).'.'.rawurlencode($format);
			else
				return 'http://cdn.c3voc.de/'.rawurlencode(streamname($room)).'_'.rawurlencode($language).'.'.rawurlencode($format);

		case 'slide':
			return 'http://cdn.c3voc.de/slides/'.rawurlencode(streamname($room)).'/current.png';
	}

	return '#';
}

function link_vod($id)
{
	return 'relive/'.rawurlencode($id).'/';
}

function streamname($room)
{
	switch($room)
	{
		case 'saal1': return 's1';
		case 'saal2': return 's2';
		case 'saalg': return 's3';
		case 'saal6': return 's4';
		case 'sendezentrum': return 's5';
		default: return $room;
	}
}

function irc_channel($room)
{
	return '31C3-hall-'.strtoupper(substr($room, 4, 1));
}

function twitter_hashtag($room)
{
	return '#hall'.strtoupper(substr($room, 4, 1));
}

function format_text($format)
{
	return @$GLOBALS['CONFIG']['FORMAT_TEXT'][$format] ?: '';
}

function baseurl()
{
	if(isset($GLOBALS['CONFIG']['baseurl']))
		return $GLOBALS['CONFIG']['baseurl'];

	$base  = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS']) ? 'https://' : 'http://';
	$base .= $_SERVER['HTTP_HOST'];
	$base .=  rtrim(dirname($_SERVER['SCRIPT_NAME']), '/').'/';

	return $base;
}

function strtoduration($str)
{
	$parts = explode(':', $str);
	return ((int)$parts[0] * 60 + (int)$parts[1]) * 60;
}

function has($keychain)
{
	return _has($GLOBALS['CONFIG'], $keychain);
}
function _has($array, $keychain)
{
	if(!is_array($keychain))
		$keychain = explode('.', $keychain);

	$key = $keychain[0];
	if(!isset($array[$key]))
		return false;

	if(count($keychain) == 1)
		return true;

	return _has($array[$key], array_slice($keychain, 1));
}

function get($keychain, $default = null)
{
	return _get($GLOBALS['CONFIG'], $keychain, $default);
}
function _get($array, $keychain, $default)
{
	if(!is_array($keychain))
		$keychain = explode('.', $keychain);

	$key = $keychain[0];
	if(!isset($array[$key]))
		return $default;

	if(count($keychain) == 1)
		return $array[$key];

	return _get($array[$key], array_slice($keychain, 1), $default);
}



function room_has_hd($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('rtmp-hd', 'hls-hd', 'webm-hd'), $formats)) > 0;
}

function room_has_sd($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('rtmp-sd', 'hls-sd', 'webm-sd'), $formats)) > 0;
}

function room_has_video($room)
{
	return room_has_hd($room) || room_has_sd($room);
}

function room_has_audio($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('audio-mp3', 'audio-opus'), $formats)) > 0;
}

function room_has_slides($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('slides'), $formats)) > 0;
}

function room_has_rtmp($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('rtmp-hd', 'rtmp-sd'), $formats)) > 0;
}

function room_has_webm($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('webm-hd', 'webm-sd'), $formats)) > 0;
}

function room_has_hls($room)
{
	$formats = get("ROOMS.$room.FORMATS");
	return count(array_intersect(array('hls-hd', 'hls-sd'), $formats)) > 0;
}

function startswith($needle, $haystack)
{
	return substr($haystack, 0, strlen($needle)) == $needle;
}
