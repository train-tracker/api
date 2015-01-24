<?php namespace Facades;

class DBFacade extends \SlimFacades\Facade {
  protected static function getFacadeAccessor() {return 'Database';}
}