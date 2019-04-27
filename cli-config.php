<?php
// bootstrap.php
use App\Helper\Config;
use Doctrine\ORM\Tools\Setup as DoctrineSetup;
use Doctrine\ORM\EntityManager;

$config = new Config();

require_once "vendor/autoload.php";

$configuration = DoctrineSetup::createXMLMetadataConfiguration([__DIR__."/config/xml"], $isDevMode = true, null, null, false);
$entityManager = EntityManager::create($config->getDbConfig(), $configuration);

return \Doctrine\ORM\Tools\Console\ConsoleRunner::createHelperSet($entityManager);
