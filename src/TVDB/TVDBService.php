<?php

namespace App\TVDB;

use App\TVDB\TVDBClient;
use App\Helper\Config;

/**
 * Service Layer for Handling the TVDB Client
 */
class TVDBService
{

  private $imgPath;
  private $bannerUrl;
  private $client;
  private $data;

  /**
   * Creates a new TVDBClient and Reads the config
   */
  public function __construct()
  {
    $config = new Config();

    $this->client = new TVDBClient();
    $this->bannerUrl = $config->getTvdbConfig()['bannerUrl'];
    $this->imgPath = $config->getTvdbImgPath();
  }

  /**
   * Search Service for searching series base on the Name of the Series
   * @param  String      $seriesname [Series Name]
   * @return TVDBService             [This]
   */
  public function search(String $seriesname):TVDBService
  {
      $data = [];

      $response = $this->client->search($seriesname);

      if(count((array)$response))
      {
        foreach($response->data as $series)
        {
          if(!empty($series->id)){

            $this->data[] = array(
              'tvdbId'      => $series->id,
              'seriesName'  => $series->seriesName,
              'overview'    => $series->overview,
              'network'     => $series->network,
              'episodes'    => [],
            );

          }
        }
      }
      return $this;
  }

  /**
   * Removes a series from the data array
   * @param Int $existingId [Series TheTVDB id]
   * @return TVDBService [this]
   */
  public function unsetIds(Int $existingId):TVDBService
  {

    foreach($this->data as $seriesKey => $series)
    {
      if($series['tvdbId'] == $existingId)
      {
        unset($this->data[$seriesKey]);
      }
    }

    return $this;
  }

  /**
   * Get the details about the series in addition to what was retrieved when searched
   * @return TVDBService [This]
   */
  public function getSeriesDetails():TVDBService
  {
    foreach($this->data as $seriesKey => $series)
    {

      $response = $this->client->getSeriesInfo($series['tvdbId']);

      $this->data[$seriesKey]['imdbId'] = $response->data->imdbId;

    }

    return $this;

  }

  /**
   * Get the ideas from the data array so they can be searched
   * @return Array [Array of TheTVDB ids]
   */
  public function getSearchIds():Array
  {
    $ids = array_column($this->data, 'tvdbId');
    return $ids;
  }

  /**
   * Get the images for a Series
   * @return TVDBService [this]
   */
  public function setSeriesImages():TVDBService
  {
    foreach($this->data as $seriesKey => $series)
    {

      $imageUrls = [];
      $images = $this->client->getSeriesImage($series['tvdbId']);

      if((count($images)))
      {
        $imageUrls = array_map(
          function($image)
          {
            if(!empty($image)) return $this->bannerUrl."/".$image;
          },
          $images
        );

      }

      $this->data[$seriesKey]['image'] = $imageUrls['image'];
      $this->data[$seriesKey]['thumbnail'] = $imageUrls['thumbnail'];

    }

    return $this;

  }

  /**
   * Gets the details from three episodes for a series
   * @return [type] [description]
   */
  public function getThreeEpisodes():TVDBService
  {

    $seriesIds = $this->getSearchIds();

    foreach($seriesIds as $seriesId){

      $episodes = $this->client->getEpisodes($seriesId);

      if(count((array)$episodes->data)){

        $slicedEpisodes = array_slice($episodes->data, 0, 3);

        foreach($slicedEpisodes as $episode)
        {

          if(!empty($episode->filename))
          {
          $episode->filename = $this->bannerUrl."/".$episode->filename;
          }

          $episodeData = [
            'tvdbId'              => $episode->id,
            'image'               => $episode->filename,
            'episodeName'         => $episode->episodeName,
            'airedSeason'         => $episode->airedSeason,
            'airedEpisodeNumber'  => $episode->airedEpisodeNumber,
            'overview'            => $episode->overview,
          ];

          foreach($this->data as $seriesKey => $series)
          {
            if($series['tvdbId'] == $seriesId)
            {
              $this->data[$seriesKey]['episodes'][] = $episodeData;
            }
          }
        }
      }
    }

    return $this;

  }

  /**
   * Processes and saves the images for a Series
   * @return TVDBService [this]
   */
  public function saveSeriesImages():TVDBService
  {

    foreach($this->data as $dataKey => $series)
    {
      if(!empty($series['image'])){
        $this->data[$dataKey]['image'] = $this->writeImage($series['image'],'posters');
      }

      if(!empty($series['thumbnail'])){
        $this->data[$dataKey]['thumbnail'] = $this->writeImage($series['thumbnail'], 'posters', true);
      }
    }

    return $this;
  }

  /**
   * Processes and saves the images for a Episode
   * @return TVDBService [description]
   */
  public function saveEpisodeImages():TVDBService
  {

    foreach($this->data as $seriesKey => $series)
    {
      if(count($series['episodes']))
      {
        foreach($series['episodes'] as $episodeKey => $episode)
        {
          if(!empty($episode['image'])){
            $this->data[$seriesKey]['episodes'][$episodeKey]['image'] = $this->writeImage($episode['image'], 'episodes');
          }
        }
      }
    }

    return $this;

  }

  /**
   * Writes an image to the filesystem
   * @param  String $orgImage [Original image and url from the API]
   * @param  String  $type     [Type - either 'posters' or 'episodes']
   * @param  boolean $thumb    [Whether or not the image is a thumbnail]
   * @return String            [New filename on filesystem]
   */
  private function writeImage(String $orgImage,String $type,Bool $thumb = false):String
  {

    $finalPath = $this->imgPath.'/'.$type.'/';

    $fileName = ($thumb === false ? basename($orgImage) : "thumb".basename($orgImage));

    if(!file_exists($finalPath.$fileName))
    {
      $webImage = file_get_contents($orgImage);

      if($webImage != false)
      {
        
        file_put_contents($finalPath.$fileName, $webImage);

      }else{

        $fileName = '';
      }
    }

    return $fileName;
  }

  /**
   * Removes a number of series from the data aray
   * @param  Array      $ids [Array of TheTVDB ids]
   * @return TVDBService      [This]
   */
  function removeSeriesData(Array $ids):TVDBService
  {
     foreach($this->data as $key => $series){
        if($series['tvdbId'] == in_array($ids)){
             unset($this->data[$key]);
        }
     }
     return $this;
  }

  /**
   * Empties the data array
   * @return TVDBService [This]
   */
  public function flushData():TVDBService
  {
    $this->data = [];
    return $this;
  }

  /**
   * Returns the data array for use
   * @return Array [Array containing series and episode data]
   */
  public function getData():Array
  {
    return $this->data;
  }
}
