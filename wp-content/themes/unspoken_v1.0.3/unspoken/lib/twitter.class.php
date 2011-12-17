<?php
/**
 * myTimeline PHP Script
 * This script gets a user's twitter timeline and returns it as a multidimension array
 * each array containing 'tweet, date and link' respectively.
 *
 * LICENSE: This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or any later version.
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * http://www.gnu.org/licenses/
 *
 * @author Opeyemi Obembe <ray@devedgelabs.com>
 * @copyright Copyright (c) 2010, devEdgeLabs.
 */

class myTimeline
{
	var $count;
	var $feedUrl;
	var $username;
    var $timeformat;

	//@params: twitter username, number of needed updates (20 max)
	function myTimeline($username, $count = 5, $timeformat)
	{
		$this->feedUrl = 'http://api.twitter.com/1/statuses/user_timeline/'.$username.'.rss';
		$this->count = ( $count < 1 || $count > 20 ) ? 5 : $count;
		$this->username = $username;
        $this->timeformat = isset($timeformat) ? $timeformat : 'D, d M, Y h:ia';
	}

	function since($date, $timeformat)
	{
		$timestamp = strtotime($date);
		$seconds = time() - $timestamp;

		$units = array(
			'second' => 1,
			'minute' => 60,
			'hour' 	 => 3600,
			'day'	 => 86400,
			'month'  => 2629743,
			'year'   => 31556926
		);

		foreach($units as $k => $v)
		{
			if($seconds >= $v)
			{
				$results = floor($seconds/$v);
				if($k == 'day' | $k == 'month' | $k == 'year')
					$timeago = date($timeformat, $timestamp);
				else
					$timeago = ($results >= 2) ? 'about '.$results.' '.$k.'s ago' : 'about '.$results.' '.$k.' ago';
			}
		}

		return $timeago;
	}

	// Returns a multidimentional array, each containg 'tweet, date and link' respectively
	function get()
	{
		// Append the count
		$url = $this->feedUrl;
		$url .= $this->count == 20 ? '' : '?count='.$this->count;

		// The http CURL thingy
		$curl_handle = curl_init();
		curl_setopt($curl_handle, CURLOPT_URL, $url);
		curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, TRUE);
		curl_setopt($curl_handle, CURLOPT_TIMEOUT, 10);//10 secs max
		$data = curl_exec($curl_handle);
		curl_close($curl_handle);

		// Some error? Return an empty array
		// You may want to extend this to know the exact error
		// echo curl_error($curl_handle);
		// or know the http status
		// echo curl_getinfo($curl_handle, CURLINFO_HTTP_CODE);
		if(!$data)	return array();

		// Some reformatting
		$pattern = array(
			'/[^(:\/\/)](www\.[^ \n\r]+)/',
			'/(https?:\/\/[^ \n\r]+)/',
			'/@(\w+)/',
			'/^'.$this->username.':\s*/i'
		);
		$replace = array(
			'<a href="http://$1" rel="external nofollow">$1</a>',
			'<a href="$1" rel="external nofollow">$1</a>',
			'<a href="http://twitter.com/$1" rel="external nofollow">@$1</a>'.
			''
		);

		$tweets = array();
		$xml = new SimpleXMLElement($data);
		foreach($xml->channel->item as $item)
		{
			$tweet = preg_replace($pattern, $replace, $item->description);
			$date = $this->since($item->pubDate, $this->timeformat);
			$permalink = $item->link;
			$tweets[] = array($tweet, $date, $permalink);
		}

		return $tweets;
	}
}
?>
