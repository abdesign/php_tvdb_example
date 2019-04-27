<?php

namespace App\Http\Base;

use Doctrine\ORM\EntityManager;

interface BasePresenterInterface
{
  public function execute();
  public function setParams(Array $params, EntityManager $em);
}
