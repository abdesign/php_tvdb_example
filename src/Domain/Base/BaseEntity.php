<?php

namespace App\Domain\Base;

class BaseEntity implements BaseEntityInterface
{
  private $updated;
  private $created;

  public function getCreated()
  {
    return $this->created;
  }

  public function setCreated($created)
  {
    $this->created = $created;
  }

  public function getUpdated()
  {
    return $this->updated;
  }

  public function setUpdated($updated)
  {
    $this->updated = $updated;
  }

}
