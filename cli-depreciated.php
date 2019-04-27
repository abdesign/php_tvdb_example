<?php
//Set the constant for the base dir for the app
define('BASEPATH', dirname(__FILE__));

use App\TVDB\TVDBService;
use App\Helper\Config;
use App\Domain\Series\SeriesService;

$bootstrap = BASEPATH.'/../config/bootstrap.php';

if (file_exists($bootstrap)) {
    require $bootstrap;
} else {
    throw new \Exception(
        'Make sure that bootstrap.php is present.'
    );
}

$options = [
  "showname:",
];

$arguments = getopt(null,$options);

var_dump($arguments);

if(!empty($arguments['showname'])){

  echo "Searching TVDB for ".$arguments['showname'];

  //$tvdbService = new TVDBSERVICE();
  //$response = $tvdbService->search($arguments['showname']);

  $seriesService = new SeriesService($entityManager);
  $seriesTvdbIds = $seriesService->searchSeries($arguments['showname'])->seriesTvdbIds;
  $test2 = $seriesService->getNames($seriesTvdbIds);

  var_dump($seriesTvdbIds);
  var_dump($test2);

}
