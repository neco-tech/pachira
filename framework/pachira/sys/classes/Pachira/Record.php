<?php // -*- mode: php; -*-

namespace Pachira;

class Record extends \Model {
  public static function load($id){
    return static::m()->where("id", $id)->find_one();
  }

  public static function load_by($key, $val){
    return static::m()->where($key, $val)->find_one();
  }

  public static function load_by_post($post){
    if(el($post, "id")){
      $record = static::m()->find_one(el($post, "id"));
    }else{
      $record = static::m()->create();
    }

    foreach(static::$scalars as $scalar){
      if(el($post, $scalar, null) !== null) $record->$scalar = el($post, $scalar);
    }

    return $record;
  }

  public static function all(){
    return static::m()->find_many();
  }

  public static function last(){
    return static::m()->order_by_desc("id")->find_one();
  }

  public static function m(){
    return \Model::Factory(get_called_class());
  }

  public function save_key($key, $val){
    $this->$key = $val;
    $this->save();
  }
}
