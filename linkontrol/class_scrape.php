<?php

require 'lib/readability.php';

class scrape {
	function getUrl($request_url) {
		$request_url_hash = md5($request_url);
		$request_url_cache_file = sprintf(DIR_CACHE."/%s.url", $request_url_hash);
		if (file_exists($request_url_cache_file) && (time() - filemtime($request_url_cache_file) < CACHE_TIME)) {
			$source = file_get_contents($request_url_cache_file);
		}
		else {
			$handle = curl_init();
			curl_setopt_array($handle, array(
						CURLOPT_USERAGENT => USER_AGENT,
						CURLOPT_FOLLOWLOCATION => true,
						CURLOPT_HEADER  => false,
						CURLOPT_HTTPGET => true,
						CURLOPT_RETURNTRANSFER => true,
						CURLOPT_TIMEOUT => 30,
						CURLOPT_URL => $request_url
						));

			$source = curl_exec($handle);
			curl_close($handle);

			// Write request data into cache file.
			if (file_put_contents($request_url_cache_file, $source) === FALSE) {
				#echo("unable to write file" . $request_url_cache_file);
			}
		}

		preg_match("/charset=([\w|\-]+);?/", $source, $match);
		$charset = isset($match[1]) ? $match[1] : 'utf-8';

		$Readability = new Readability($source, $charset);
		$content = $Readability->getContent();

		$content['url'] = $request_url;
		return $content;
	}

	function getUrlJson($url) {
		return json_encode($this->getUrl($url));
	}
}
?>
