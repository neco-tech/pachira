<?php // -*- mode: php; -*-

namespace Pachira;

class RoutingGroup {
  public function __construct($prefix){
    $this->prefix = $prefix;
  }

  public function group($prefix, $fn){
    $fn(new RoutingGroup("{$this->prefix}{$prefix}"));
  }

  public function get($path, $callback){
    get("{$this->prefix}{$path}", $callback);
  }

  public function post($path, $callback){
    post("{$this->prefix}{$path}", $callback);
  }

  public function request($path, $callback){
    request("{$this->prefix}{$path}", $callback);
  }
}
