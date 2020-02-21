<?php // -*- mode: php; -*-

get("^/$", function(){
  view("index", array("title" => "Hello, Pachira!"));
});
