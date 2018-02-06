<?php
// src/Controller/TwitterApiController.php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use App\Utils\TwitterApi;
use App\Utils\GuzzleTest;

class TwitterApiController extends Controller
{
    /**
     * @Route("/twitterTimeline", name="twitter_timeline")
     */
    function getTimeline(TwitterApi $TwitterApi) {

        //html 
        $html = $TwitterApi->getUserTimeline();

         return $this->render('twitter/tweet-results.html.twig', array(
            'data' => $html,
        ));   
        
    }
    /**
     * @Route("/recipeTweets", name="recipe_tweets")
     */
    function getRecipeTweets(TwitterApi $TwitterApi) {

        //html 
        $html = $TwitterApi->getTrendingRecipes();

         return $this->render('twitter/hashtag-results.html.twig', array(
            'data' => $html,
        ));   
        
    }
    /**
    * @Route("/testGuzzle", name="test_guzzle")
    */
    function testGuzzle(GuzzleTest $GuzzleTest) {

        //html 
        $html = $GuzzleTest->performGuzzleRequest();

         return $this->render('twitter/dump-results.html.twig', array(
            'data' => $html,
        ));   
        
    }
}