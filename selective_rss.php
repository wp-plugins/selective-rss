<?php
/*
Plugin Name: Selective RSS
Plugin URI: http://techno-geeks.org/selective-rss
Description: Simple Plugin that allows you to embed RSS feed items into Pages or Posts. Optionally allows you to choose how many items to display and allows you to limit items to ones that contain certain words in the titles.
Version: 0.1.0b
Author: Jesse R. Adams (DualTech Services, Inc.)
Author URI: http://www.dual-tech.com/about-dualtech-services/
*/

require_once('rss_lib.php');

function filter_feeds($content) {
	$limit = null;
	$filter = array();
	$url = null;

	$inputTag = preg_match('/\[srss (.+)\]/', $content, $matches);
	$rawArgs = explode(',', $matches[1]);

	foreach($rawArgs as $input) {
		preg_match_all('/^(url|filter|limit)=(.+)$/i', $input, $matches);
		$key = $matches[1][0];
		$value = $matches[2][0];

		if ($key == 'filter') {
			$filter = explode(';', $value);
		} else {
			$$key = $value;	
		}
	}
	
	if ($url) {
		$xml = parseRSS($url);
		$items = extractRSSItems($xml, $limit, $filter);

		$htmlToAdd = '';
		foreach ($items as $item) {
			$htmlToAdd .= '<a href="' . $item['link'] . '" target="new">' . $item['title'] . '</a>' . "<br/>\n";
			$htmlToAdd .= $item['description'] . "<br/><br/>\n";
		}

		$content = preg_replace('/\[srss .+\]/', $htmlToAdd, $content);
	}
	return $content;
}

add_filter('the_content', 'filter_feeds');
?>
