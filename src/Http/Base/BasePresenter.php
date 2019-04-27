<?php

namespace App\Http\Base;

use Doctrine\ORM\EntityManager;
use \Twig_Loader_Filesystem;
use \Twig_Environment;

class BasePresenter implements BasePresenterInterface
{
  protected $loader;
  protected $twig;

  /**
   * Loads the Twig Templating Engine
   */
  public function __construct()
  {
    //Initialize Twig
    $loader = new Twig_Loader_Filesystem(BASEPATH . '/resources/templates');
    $twig = new Twig_Environment($loader, array('cache' => BASEPATH . '/cache'));

    $this->loader = $loader;
    $this->twig = $twig;
  }

  /**
   * Set the parameters passed through the API
   * @param Array         $params [Associative array of parameters]
   * @param EntityManager $em     [Doctrine Entity Manager]
   */
  public function setParams(Array $params,EntityManager $em){
    $this->params = $params;
    $this->em = $em;
  }

  public function execute(){

  }

}
