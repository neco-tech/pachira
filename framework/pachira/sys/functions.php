<?php // -*- mode: php; -*-

/* Routing Functions */

function add_router($router){
  require_once APPLICATION_DIR . "/routers/{$router}.php";
}

function add_service($service){
  require_once APPLICATION_DIR . "/services/{$service}.php";
}

function pass(){
  throw new Pachira\NextRoute();
}

function get($re, $fn){
  Pachira\Pachira::get($re, $fn);
}

function post($re, $fn){
  Pachira\Pachira::post($re, $fn);
}

function group($prefix, $fn){
  $fn(new Pachira\RoutingGroup($prefix));
}

function request($re, $fn){
  get($re, $fn);
  post($re, $fn);
  Pachira\Pachira::other_methods($re, $fn);
}

function service($name, $callback, $post=null){
  if(!$post) $post = $_POST;
  $callback(new Pachira\Service($name, $post));
};

function not_found($view="404"){
  header("Status: 404 Not Found");
  if(!view($view)) echo "404 not found.";
  exit;
}


/* View Functions */

function view($name, $vars=[]){
  if(Hook\exists("before/view/{$name}")){
    $pipe = Hook\fire("before/view/{$name}", ["name" => $name, "vars" => $vars]);
    $vars = $pipe["vars"];
  }

  if(Hook\exists("after/view/{$name}")){
    $view = capture(function()use($name, $vars){Pachira\Pachira::view($name, $vars);});
    $pipe = Hook\fire("after/view/{$name}", ["vars" => $vars, "view" => $view]);
    $view = $pipe["view"];
    $vars = $pipe["vars"];
    echo $view;

  }else{
    Pachira\Pachira::view($name, $vars);
  }
}

function capture_view($name, $vars=[]){
  return capture(function()use($name, $vars){view($name, $vars);});
}

function view_var($var, $val){
  Pachira\Pachira::set_view_var($var, $val);
}


/* Record */

function setup_db($host, $port, $name, $user, $password, $logging=false){
  ORM::configure("mysql:host={$host};port=${port};dbname={$name}");
  ORM::configure("username", $user);
  ORM::configure("password", $password);
  ORM::configure("driver_options", array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
  ORM::configure("error_mode", PDO::ERRMODE_WARNING);
  ORM::configure("logging", $logging);
  ORM::configure("caching", true);
  ORM::configure("caching_auto_clear", true);
}

function transaction($fn){
  $db = ORM::get_db();
  try{
    $db->beginTransaction();
    $fn();
    $db->commit();
  }catch(Exception $e){
    $db->rollBack();
    throw $e;
  }
}



/* Helpers */

function h($str){
  return htmlspecialchars($str);
}

function el($array, $key, $default=null){
  if(is_array($array)){
    return isset($array[$key]) ? $array[$key] : $default;
  }else if(is_object($array)){
    return isset($array->$key) ? $array->$key : $default;
  }else{
    return false;
  }
}

function pathto($path){
  return rtrim(HOME, "/") . "/" . ltrim($path, "/");
}

function redirect($url, $code=302){
  external_redirect(pathto($url), $code);
}

function external_redirect($url, $code=307){
  switch($code){
  case 301: header("HTTP/1.1 301 Moved Permanently"); break;
  case 302: header("HTTP/1.1 302 Found"); break;
  case 303: header("HTTP/1.1 303 See Other"); break;
  case 307: header("HTTP/1.1 307 Temporary Redirect"); break;
  case "js": echo "<script>location.href='{$url}';</script>"; exit;
  }
  header("Location: {$url}");
  exit;
}

function require_all($path, $recursive=true){
  foreach(scandir($path) as $fname){
    if(!preg_match("/^\\./", $fname)){
      if(is_file($path . $fname)){
        require_once $path . $fname;
      }else if($recursive && is_dir($path . $fname)){
        require_all($path . $fname . "/", true);
      }
    }
  }
}

function capture($fn){
  ob_start();
  $fn();
  $output = ob_get_contents();
  ob_end_clean();
  return $output;
}
