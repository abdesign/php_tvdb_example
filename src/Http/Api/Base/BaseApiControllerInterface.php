<?php
namespace App\Http\Api\Base;

use Doctrine\ORM\EntityManager;

interface BaseApiControllerInterface
{
  public function setParams(Array $params, EntityManager $em);
}
