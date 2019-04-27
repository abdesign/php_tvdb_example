<?php

namespace App\Http\Index;

use App\Http\Base\BasePresenter;
use App\Http\Base\BasePresenterInterface;


class IndexPresenter extends BasePresenter 
{

  public function __construct(){
    parent::__construct();
  }
  /**
   * Executes the presentation of the current page requested
   * @return none
   */
  public function execute()
  {
    $template = $this->twig->load('index.html.twig');
    echo $template->render();
  }
}
