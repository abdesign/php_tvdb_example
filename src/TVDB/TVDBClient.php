<?php

namespace App\TVDB;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\CurlHandler;
use GuzzleHttp\Exception\ClientException;
use Psr\Http\Message\RequestInterface;
use App\Helper\Config;

class TVDBClient
{

  protected $guzzleClient;

  protected $token;
  protected $username;
  protected $userkey;
  protected $apikey;

  /**
   * Constructor
   * Sets up the Guzzle API client and loads the Configuration
   * http://docs.guzzlephp.org/en/stable/handlers-and-middleware.html
   *
   * Guzzle API was choosen over a straight CURL implementation for time
   */
  public function __construct()
  {

    $config = new Config();

    extract($config->getTvdbConfig());

    $this->username = $username;
    $this->userkey = $userkey;
    $this->apikey = $apikey;

    $stack = new HandlerStack();
    $stack->setHandler(new CurlHandler());
    $stack->push($this->middleware());

    $this->guzzleClient = new Client([
      'handler' => $stack,
			'base_uri' => 'https://api.thetvdb.com',
			'headers'  => [
				'Accept' => 'application/json',
				'Content-Type' => 'application/json',
			]
		]);
  }

  /**
   * Returns the request handler if authorization is required
   */
  protected function middleware()
  {
      return function (callable $handler) {
          return function (RequestInterface $request, array $options) use ($handler) {
              if(!empty($this->token)) {
                $request = $request->withHeader('Authorization', 'Bearer ' . $this->token);
              }
              return $handler($request, $options);
          };
      };
  }


  /**
   * Gets the login token from the TheTVDB API
   * @return TVDBClient [This]
   */
  public function login():TVDBClient
  {

    $response = $this->guzzleClient->post('login', ['json' => [
      'apikey'   => $this->apikey,
      'username' => $this->username,
      'userkey'  => $this->userkey
    ]])->getBody();

    $this->token = json_decode($response->getContents())->token;

    return $this;
  }

  /**
   * API search based on Series Name
   * @param  String $name [Series Name]
   * @return stdClass       [Series results from the API]
   */
  public function search(String $name)
  {
    $this->login();
    return json_decode($this->login()->guzzleClient->get('/search/series',['query' =>'name='.$name])->getBody());
  }

  /**
   * API Search for Series details
   * @param  Int    $seriesId [Series TheTVDB Id]
   * @return stdClass           [Series Detail results from the API]
   */
  public function getSeriesInfo(Int $seriesId)
  {
    return json_decode($this->login()->guzzleClient->get('/series/'.$seriesId)->getBody());
  }

  /**
   * API Query to get the Images for the Series
   * @param  Int   $seriesId [Series TheTVDB Id]
   * @return Array           [Array of image and thumbnail]
   */
  public function getSeriesImage(Int $seriesId):Array
  {
    $this->login();
    $images = json_decode($this->login()->guzzleClient->get('/series/'.$seriesId.'/images/query',['query' =>'keyType=poster'])->getBody());

    $image = (isset($images->data[0]->fileName) ? $images->data[0]->fileName : '');
    $thumbNail = (isset($images->data[0]->thumbnail) ? $images->data[0]->thumbnail : '');

    return ['image' => $image, 'thumbnail' => $thumbNail];
  }

  /**
   * API Query to get the Episodes for the series
   * @param  Int    $seriesId   [Series TheTVDB Id]
   * @return stdClass           [Episode Detail results from the API]
   */
  public function getEpisodes(Int $seriesId)
  {
    return json_decode($this->login()->guzzleClient->get('/series/'.$seriesId.'/episodes')->getBody());
  }

  /**
   * API Query to get the Episode information for the series
   * @param  Int    $episodeId [Episode TheTVDB Id]
   * @return stdClass           [Episode Detail results from the API]
   */
  public function getEpisodeInfo(Int $episodeId)
  {
    return json_decode($this->login()->guzzleClient->get('/episodes/'.$episodeId)->getBody());
  }

}
