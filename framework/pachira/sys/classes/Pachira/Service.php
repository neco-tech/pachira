<?php // -*- mode: php; -*-

namespace Pachira;

class Service {
  public function __construct($name, $post){
    $this->name = $name;
    $this->post = $post;
    $this->before_function = function(){return null;};
    $this->after_function = function(){return null;};
  }

  public function before($callback){
    $this->before_function = (function($method)use($callback){
      return $callback($this->post, $method);
    })->bindTo($this);
  }

  public function receive($method, $callback){
    post("/{$this->name}/{$method}", (function()use($method, $callback){
      $before = $this->before_function;
      $arg = $before($method);

      if(\Hook\exists("before/service/{$this->name}/{$method}")){
        $pipe = \Hook\fire("before/service/{$this->name}/{$method}", ["post" => $this->post, "arg" => $arg]);
        $this->post = $pipe["post"];
        $arg = $pipe["arg"];
      }

      $callback($this->post, $arg);

      if(\Hook\exists("after/service/{$this->name}/{$method}")){
        $pipe = \Hook\fire("after/service/{$this->name}/{$method}", ["post" => $this->post, "arg" => $arg]);
      }

      $after = $this->after_function;
      $after();
    })->bindTo($this));
  }

  public function view($name, $args=[]){
    $this->after_function = function()use($name, $args){
      view($name, $args);
      exit;
    };
  }

  public function redirect($path){
    $this->after_function = function()use($path){
      redirect($path);
      exit;
    };
  }

  public function json($data){
    $this->after_function = function()use($data){
      exit;
    };
  }

  public function error($errors, $view="error"){
    $this->view($view, ["errors" => $errors]);
  }
}
