<?php

namespace App\Domain\Series;

use App\Domain\Base\BaseEntity;
use App\Domain\Episode\Episode;
use Doctrine\Common\Collections\ArrayCollection;

class Series extends BaseEntity
{

 private $id;
 private $tvdbId;
 private $seriesName;
 private $overview;
 private $image;
 private $thumbnail;
 private $created;
 private $updated;
 private $episodes;
 private $imdbId;
 private $network;

 public function __construct()
  {
      $this->episodes = new ArrayCollection();
  }

 public function getId()
 {
   return $this->id;
 }

 public function getTvdbId()
 {
   return $this->tvdbId;
 }

 public function getImdbId()
 {
   return $this->imdbId;
 }

 public function setImdbId($imdbId)
 {
   $this->imdbId = $imdbId;
 }

 public function setTvdbId($tvdbId)
 {
   $this->tvdbId = $tvdbId;
 }

 public function getSeriesName()
 {
   return $this->seriesName;
 }

 public function setSeriesName($seriesName)
 {
   $this->seriesName = $seriesName;
 }

 public function getOverview()
 {
   return $this->overview;
 }

 public function setOverview($overview)
 {
   $this->overview = $overview;
 }

 public function getImage()
 {
   return $this->image;
 }

 public function setImage($image)
 {
   $this->image = $image;
 }

 public function getThumbnail()
 {
   return $this->thumbnail;
 }

 public function setThumbnail($thumbnail)
 {
   $this->thumbnail = $thumbnail;
 }

 public function getEpisodes()
 {
   return $this->episodes;
 }

 public function addEpisode(Episode $episode)
 {

   $this->episodes->add($episode);
   $episode->setSeries($this);

   return $this;

 }

 public function getNetwork()
 {
   return $this->network;
 }

 public function setNetwork($network)
 {
   $this->network = $network;
 }

}
