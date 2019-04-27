<?php

namespace App\Domain\Base;

interface BaseEntityInterface
{
  public function getCreated();
  public function setCreated($created);
  public function getUpdated();
  public function setUpdated($updated);
}
