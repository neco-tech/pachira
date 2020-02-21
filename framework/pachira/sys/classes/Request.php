<?php // -*- mode: php; -*-

class Request {
  public static function get($name, $default=null){
    return el($_GET, $name, $default);
  }

  public static function post($name, $default=null){
    return el($_POST, $name, $default);
  }

  public static function file($name, $default=null){
    return el($_FILES, $name, $default);
  }

  public static function server($name, $default=null){
    return el($_SERVER, $name, $default);
  }

  public static function method(){
    return strtoupper(Request::post("_method", Request::server("REQUEST_METHOD")));
  }

  public static function method_is($method){
    return Request::method() === strtoupper($method);
  }
}
