<?php

namespace App\Domain\Episode;

use Doctrine\ORM\EntityManager;

/**
 * Service Layer for Handling the TV Episode Entities
 */

class EpisodeService
{

  private $em;

  /**
   * Constructor
   * @param EntityManager $em [Doctrine Entity Manageer]
   */
  public function __construct(EntityManager $em){
    $this->em = $em;
  }

  /**
   * Returns a collection array of Episode entities
   * @param  Int   $seriesId [Series id to search for episodes]
   * @return Array           [Array of episode data]
   */
  public function getSeriesEpisodes(Int $seriesId):Array
  {
    if(empty($seriesId))
    {
      return [];
    }

    $episodeCollection = $this->em->getRepository('App\Domain\Episode\Episode')->findBy(['seriesId' => $seriesId]);

    foreach($episodeCollection as $episode)
    {

      $returnCollectionArr[] = [
        'id'                  => $episode->getId(),
        'seriesId'            => $episode->getSeriesId(),
        'tvdbId'              => $episode->getTvdbId(),
        'episodeName'         => $episode->getEpisodeName(),
        'overview'            => $episode->getOverview(),
        'image'               => $episode->getImage(),
        'airedSeason'         => $episode->getAiredSeason(),
        'airedEpisodeNumber'  => $episode->getAiredEpisodeNumber(),
      ];


    }

    return $returnCollectionArr;

  }
}
