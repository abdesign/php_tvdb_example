<?php

namespace App\Domain\Episode;

use App\Domain\Base\BaseEntity;
use App\Domain\Series\Series;

class Episode extends BaseEntity
{

 private $id;
 private $tvdbId;
 private $seriesId;
 private $episodeName;
 private $overview;
 private $image;
 private $airedSeason;
 private $airedEpisodeNumber;
 private $created;
 private $updated;
 private $series;


 public function getId()
 {
   return $this->id;
 }

 public function getTvdbId()
 {
   return $this->tvdbId;
 }

 public function setTvdbId($tvdbId)
 {
   $this->tvdbId = $tvdbId;
 }

 public function getSeriesId()
 {
   return $this->seriesId;
 }

 public function setSeriesId($seriesId)
 {
   $this->seriesId = $seriesId;
 }

 public function getAiredSeason()
 {
   return $this->airedSeason;
 }

 public function setAiredSeason($airedSeason)
 {
   $this->airedSeason = $airedSeason;
 }

 public function getAiredEpisodeNumber()
 {
   return $this->airedEpisodeNumber;
 }

 public function setAiredEpisodeNumber($airedEpisodeNumber)
 {
    $this->airedEpisodeNumber = $airedEpisodeNumber;
 }

 public function getEpisodeName()
 {
   return $this->seriesName;
 }

 public function setEpisodeName($episodeName)
 {
   $this->episodeName = $episodeName;
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

 public function getSeries()
 {
   return $this->series;
 }

 public function setSeries(Series $series)
 {
   $this->series = $series;
 }

}
