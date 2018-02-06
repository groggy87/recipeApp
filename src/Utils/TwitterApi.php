<?php
namespace App\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Subscriber\Oauth\Oauth1;
// use GuzzleHttp\Psr7\Request;
// use GuzzleHttp\Psr7\Response;
// use GuzzleHttp\Stream\Stream;

//https://github.com/guzzle/oauth-subscriber

class TwitterApi {

    private $consumerKey;
    private $consumerSecret;
    private $token;
    private $tokenSecret;

    public function __construct($consumerKey, $consumerSecret, $token, $tokenSecret)
    {
        $this->consumerKey = $consumerKey;
        $this->consumerSecret = $consumerSecret;
        $this->token = $token;
        $this->tokenSecret = $tokenSecret;

        date_default_timezone_set('Europe/Dublin'); //Set to a timezone
    }

    public function getUserTimeline() 
    {
        $stack = HandlerStack::create();


        /*
         * Setting up the oauth subscriber parameters.  Parameter values have to be generated at the Twitter site
         */

        $middleware  = new Oauth1([
            //this is for laravel, check symfony enc access method + set up in .env
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'token'           => $this->token,
            'token_secret'    => $this->tokenSecret
        ]);

        /*
         * Attaching the oauth and the log subscriber to the client
         */
        $stack->push($middleware);

        /*
         * Creating the Guzzle client, we are setting oauth as the default authentication method
         */
        $client = new Client([
            'base_uri' => 'https://api.twitter.com/1.1/',
            'handler' => $stack
        ]);

        /*
         * Executing a GET request on the timeline service, pass the result to the json parser
         */
        $res = $client->get('statuses/home_timeline.json', ['auth' => 'oauth']);

        return json_decode($res->getBody()->getContents());
    }

    public function getTrendingRecipes() 
    {
        $stack = HandlerStack::create();

        $middleware  = new Oauth1([
            //this is for laravel, check symfony enc access method + set up in .env
            'consumer_key'    => $this->consumerKey,
            'consumer_secret' => $this->consumerSecret,
            'token'           => $this->token,
            'token_secret'    => $this->tokenSecret
        ]);

        $stack->push($middleware);

        $client = new Client([
            'base_uri' => 'https://api.twitter.com/1.1/',
            'handler' => $stack
        ]);

        $res = $client->get('search/tweets.json?q=%23recipes', ['auth' => 'oauth']);
        
        return json_decode($res->getBody()->getContents());
    }
}