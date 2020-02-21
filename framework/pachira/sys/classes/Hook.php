<?php // -*- mode: php; -*-

namespace Hook;

class Hooks {
  static $hooks = [];
}

function exists($name){
  return isset(Hooks::$hooks[$name]);
}

function fire($name, $args=[]){
  if(isset(Hooks::$hooks[$name])){
    foreach(Hooks::$hooks[$name] as $fn){
      $args = $fn($args);
    }
  }
  return $args;
}

function register($name, $fn){
  if(!isset(Hooks::$hooks[$name])) Hooks::$hooks[$name] = [];
  Hooks::$hooks[$name][] = $fn;
}
