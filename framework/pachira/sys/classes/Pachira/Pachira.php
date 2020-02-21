<?php // -*- mode: php; -*-

namespace Pachira;

class Pachira {
  /* route map array */
  static $map_get = [];
  static $map_post = [];
  static $map_other_methods = [];

  /* view vars */
  static $view_vars = [];

  /* */
  static $path_info = null;

  /* --------------------------------------------------
   * route methods
   */
  public static function start($path_info){
    $path_info = "/" . ltrim($path_info, "/");
    self::$path_info = $path_info;

    $failed = true;
    $args;

    switch(\Request::method()){
    case "GET": $map = self::$map_get; break;
    case "POST": $map = self::$map_post; break;
    default: $map = $map_other_methods;
    }

    foreach($map as $set){
      $re = $set[0];
      $fn = $set[1];

      if(preg_match("/^".str_replace("/", "\\/", $re)."$/", $path_info, $args)){
        array_shift($args);
        $args = array_map(function($arg){
          return urldecode($arg);
        }, $args);

        try{
          call_user_func_array($fn, $args);
          $failed = false;
          break;
        }catch(NextRoute $e){
        }
      }
    }

    if($failed) not_found();
  }

  public static function get($re, $fn){
    self::$map_get[] = [$re, $fn];
  }

  public static function post($re, $fn){
    self::$map_post[] = [$re, $fn];
  }

  public static function other_methods($re, $fn){
    self::$map_other_methods[] = [$re, $fn];
  }


  /* --------------------------------------------------
   * view methods
   */
  public static function view($name, $vars=[]){
    $path = APPLICATION_DIR . "views/{$name}";
    if(!is_file($path)) $path .= ".php";

    if(is_file($path)){
      extract(array_merge(self::$view_vars, $vars));
      include $path;
      return true;
    }
    return false;
  }

  public static function set_view_var($var, $val){
    self::$view_vars[$var] = $val;
  }
}
