<?php

namespace App\Domain\Series;

use App\Domain\Base\BaseRepositoryInterface;

interface SeriesRepositoryInterface
{
  public function findIds(Array $tvdbIds):Array;
  public function findNames(Array $tvdbIds):Array;
  public function findSimple(Array $tvdbIds):Array;
}
