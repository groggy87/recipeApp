<?php  
namespace App\Utils;

use \DOMDocument;
use Symfony\Component\DomCrawler\Crawler;
/**
* This class is used to parse and filter data from a given url - uses curl. see Guzzle for alternative API consumption
*/
class GetDataFromUrl 
{
	// should I set up dependency injections for all functions in here?
	// can't set upcrawler as it requires html param
	// can you use construnt if they're from a diff namespace?
	function __construct()
	{
		# code...
	}

	/* gets the data from a URL */
	function curlGetData($url) {
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}

	/* gets the data from a URL, return array */
	function phpDomGetData($url) {
		$keywords = array();
		$domain = array($url);
		$doc = new DOMDocument;
		$doc->preserveWhiteSpace = FALSE;
		foreach ($domain as $key => $value) {
		    @$doc->loadHTMLFile($value);
		    $anchor_tags = $doc->getElementsByTagName('a');
		    foreach ($anchor_tags as $tag) {
		        $keywords[] = strtolower($tag->nodeValue);
		    }
		}
	
		return $keywords;
	}

	//https://symfony.com/doc/current/components/dom_crawler.html
	//could be handy for parsing results 
	function filterHTML($html) {
		
		$crawler = new Crawler($html);
		//this method will probably work for most blogsites where the recipe will be in a div with id #content 
		//TODO: build intelligence into this to try other common possibilities
		try {
		    return $crawler->filter('#content')->children();
		    //in here try other common container ids...
		}
		catch (\InvalidArgumentException $e) {
		    // Handle the current node list is empty..
		    return false;
		}		

		//need a funtion to further filter this looking for keywords, h tags, ingredients, method etc
	}
}
?>