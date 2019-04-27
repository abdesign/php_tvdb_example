<?php

namespace App\Http\Api\Base;

use App\Domain\Series\SeriesService;
use Doctrine\ORM\EntityManager;

class BaseApiController
{
  protected $params;
  protected $em;

  public function __construct(){
  }
  
  /**
   * Set the parameters passed through the API
   * @param Array         $params [Associative array of parameters]
   * @param EntityManager $em     [Doctrine Entity Manager]
   */
  public function setParams(Array $params, EntityManager $em){
    $this->params = $params;
    $this->em = $em;
  }
}
