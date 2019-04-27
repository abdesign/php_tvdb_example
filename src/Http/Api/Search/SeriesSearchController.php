<?php

namespace App\Http\Api\Search;

use App\Domain\Series\SeriesService;
use App\Http\Api\Base\BaseApiController;
use App\Http\Api\Base\BaseApiControllerInterface;

class SeriesSearchController extends BaseApiController implements BaseApiControllerInterface
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
    
    if(count($this->params) && !empty($this->params['name']))
    {
      $seriesService = new SeriesService($this->em);
      $seriesTvdbIds = $seriesService->searchSeries($this->params['name'])->seriesTvdbIds;
      $series = $seriesService->getSeriesByTvdbId($seriesTvdbIds);
      echo json_encode($series);
    }

  }

}
