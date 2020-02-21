<?php // -*- mode: php; -*-

class Session {
  public static function get($key){
    return isset($_SESSION[$key]) ? $_SESSION[$key] : false;
  }

  public static function set($key, $val){
    if(is_null($val)){
      unset($_SESSION[$key]);
    }else{
      $_SESSION[$key] = $val;
    }
  }

  public static function message($val=null){
    $message = self::get("_message");
    if(is_null($val) && $message){
      Session::set("_message", null);
      return $message;
    }else{
      Session::set("_message", $val);
    }
  }

  public static function has_message(){
    return array_key_exists("_message", $_SESSION);
  }
}
