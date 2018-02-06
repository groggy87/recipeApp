<?php  
namespace App\Utils;

use \DOMDocument;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Stream\Stream;

//http://docs.guzzlephp.org/en/latest/quickstart.html
//https://stackoverflow.com/questions/13072097/symfony2-how-to-perform-an-external-request
//https://stackoverflow.com/questions/30549226/guzzlehttp-how-get-the-body-of-a-response-from-guzzle-6
//http://guzzle3.readthedocs.io/http-client/response.html

/**
* This class is testing using Guzzle
*/
class GuzzleTest 
{
	// should I set up dependency injections for all functions in here?
	// can't set upcrawler as it requires html param
	// can you use construnt if they're from a diff namespace?
	function __construct()
	{
		# code...
	}

	/* gets the data from a URL */
	function performGuzzleRequest() {

	$client = new Client(['base_uri' => 'http://httpbin.org/']);

	$response = $client->request('GET', 'http://httpbin.org/xml');
	
	//read number of bytes
	//return $response->getBody()->read(4);
	//raw output of remaining contents (ie. a second call to this method will be empty)
	return $response->getBody()->getContents();
	// all the data from beginning to end 
	//return (string) $response->getBody();
	}

}
?>