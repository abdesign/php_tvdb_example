<?php

namespace App\Http\Api\Series;

use App\Domain\Episode\EpisodeService;
use App\Http\Api\Base\BaseApiController;
use App\Http\Api\Base\BaseApiControllerInterface;

class EpisodesController extends BaseApiController implements BaseApiControllerInterface
{
  public function __construct(){
    parent::__construct();
  }

  /**
   * Executes the API Call
   * @return none
   */
  public function execute()
  {

    if(count($this->params) && !empty($this->params['id']))
    {
      $episodeService = new EpisodeService($this->em);
      $episode = $episodeService->getSeriesEpisodes($this->params['id']);
      echo json_encode($episode);
    }

  }

}
