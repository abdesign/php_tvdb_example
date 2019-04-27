<?php

namespace App\Domain\Series;

use App\TVDB\TVDBService;
use Doctrine\ORM\EntityManager;
use pmill\Doctrine\Hydrator\ArrayHydrator;

/**
 * Service Layer for Handling the TV Series Entities
 */
class SeriesService
{

  private $apiData;
  private $em;
  private $tvdb;
  private $seriesRepository;
  public $seriesTvdbIds;

  /**
   * Constructor
   * @param EntityManager $em [Doctrine Entity Manageer]
   */
  public function __construct(EntityManager $em){
    $this->tvdb = new TVDBService;
    $this->em = $em;
  }

  /**
   * Basic Search Method that Searches using a Provided String (note: as this is an example, we didn't include pagination or anything)
   * @param  String        $seriesName [String of the Series name to search]
   * @return SeriesService             [This]
   */
  public function searchSeries(String $seriesName):SeriesService
  {

      //$seriesName = urldecode($seriesName);

      // Search the TVDB series API and MYSQL for Comparision (Whe only make one API Call Here)
      $ids = $this->tvdb->search($seriesName)->getSearchIds();

      // Get the existing TheTVDB Ids from the database
      $existingIds = $this->getSeriesIds($ids)->seriesTvdbIds;


     /* For this excercise, we are only going to compare based upon return count.
      However, it would be best to dig in deeper and compare by the series details, and episodes (espeically when Episodes are added to TheTVDB)
      to insure a proper data set. */

      /* Also, since this is an example, we are skipping over additional work that would handle large datasets */

      if(!count($existingIds))
      {
        // Record count does not exist, we need to hydrate the database
        $this->hydrateService();

      }else if(count($existingIds) != count($this->tvdb->getData())){

        // Record count does not match, we need to compare the ids and only insert needed records
        $this->compare($ids)->hydrateService();

      }

      $this->seriesTvdbIds = array_column($this->em->getRepository('App\Domain\Series\Series')->findIds($ids), 'tvdbId');

      return $this;
  }

  /**
   * Basic function to compare records. For now we only compare retrieved TheTVDB Ids vs Existing TheTVDB Ids
   * @param  Array $seriesCollection [Array of existing series]
   * @param  Array $ids              [Array of retrieved TheTVDB Ids]
   * @return SeriesService           [This]
   */
  private function compare(Array $ids):SeriesService
  {
    if(count($ids))
    {
      foreach($this->seriesTvdbIds as $existingId)
      {
        if(in_array($existingId, $ids))
        {
          $this->tvdb->unsetIds($existingId);
        }
      }
    }else{
      return [];
    }

    return $this;

  }

  /**
   * Grabs the existing Ids from the Series Repository
   * @param  Array $ids              [Array of retrieved TheTVDB Ids]
   * @return SeriesService [This]
   */
  public function getSeriesIds(Array $ids):SeriesService
  {
    $this->seriesTvdbIds = array_column($this->em->getRepository('App\Domain\Series\Series')->findIds($ids), 'tvdbId');
    return $this;
  }

  /**
   * Grabs a list of Series names
   * @param  Array $seriesTvdbIds [An array of TheTVDB ids to search on]
   * @return Array                [An array of the series names with the TheTVDB ids]
   */
  public function getNames(Array $seriesTvdbIds):Array
  {
    if(!count($this->seriesTvdbIds))
    {
      return [];
    }

    return $this->em->getRepository('App\Domain\Series\Series')->findNames($seriesTvdbIds);
  }

  /**
   * Gets Basic/Limited information about the series
   * @param  Array $seriesTvdbIds [An array of TheTVDB ids to search on]
   * @return Array                [An array containing the information on the series]
   */
  public function getSimple(Array $seriesTvdbIds):Array
  {
    if(!count($this->seriesTvdbIds))
    {
      return [];
    }

    return $this->em->getRepository('App\Domain\Series\Series')->findSimple($seriesTvdbIds);
  }

  /**
   * Gets an Entity Collection for the series and returns it as an array
   * @param  Array  $seriesTvdbIds [An array of TheTVDB ids to search on]
   * @return Array                [An array containing all of the information on the series derived from the Entities]
   */
  public function getSeriesByTvdbId(Array $seriesTvdbIds):Array
  {

    $returnCollectionArr = [];

    if(!count($seriesTvdbIds))
    {
      return [];
    }

    $seriesCollection = $this->em->getRepository('App\Domain\Series\Series')->findBy(['tvdbId' => $seriesTvdbIds]);

    foreach($seriesCollection as $series)
    {

      $returnCollectionArr[] = [
        'tvdbId'      => $series->getTvdbId(),
        'seriesName'  => $series->getSeriesName(),
        'overview'    => $series->getOverview(),
        'thumbnail'   => $series->getThumbnail(),
        'image'       => $series->getImage(),
        'imdbId'      => $series->getImdbId(),
        'id'          => $series->getId(),
        'network'     => $series->getNetwork(),
      ];


    }

    return $returnCollectionArr;

  }



  /**
   * Hydrates the database. In a more realistic environment, the database would be hydrated prior to launch,
   * then this would be used less frequently.
   * @return SeriesService [This]
   */
  public function hydrateService():SeriesService
  {

     $date = date("Y-m-d H:i:s");

     //Supachain - since we need to hydrate the database we retrieve episodes and images for a the series
     $tvdbData = $this->tvdb->getSeriesDetails()->setSeriesImages()->saveSeriesImages()->getThreeEpisodes()->saveEpisodeImages()->getData();

     //Doctrine does not natively have a library to populate enitities by array, we use pmill\Doctrine\Hydrator
     $hydrator = new ArrayHydrator($this->em);



     //Create the entities
     foreach($tvdbData as $series)
     {

       $episodes = $series['episodes'];

       //Drop the series from array so it doesn't attempt to add to entity.
       unset($series['episodes']);

       $series = $hydrator->hydrate('App\Domain\Series\Series', $series);

       $series->setCreated($date);
       $series->setUpdated($date);

       $this->em->persist($series);

       foreach($episodes as $episode)
       {

         $episode = $hydrator->hydrate('App\Domain\Episode\Episode', $episode);

         $episode->setCreated($date);
         $episode->setUpdated($date);
         $series->addEpisode($episode);

       }

       $this->em->persist($series);

     }

     //Save the entitites and then flush the data from the TVDB Service since no longer needed.
     $this->em->flush();
     $this->tvdb->flushData();

     return $this;

  }

}
