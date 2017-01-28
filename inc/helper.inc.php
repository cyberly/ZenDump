<?php
namespace ZenDump;

class Helper{

    public function __constructor(){
        //stuff lol
        $stuff = "lol";
    }

    public static function dbSafe($dbObj, $column, $data){
        if($data != NULL){
            self::db = new $dbObj;

            echo "hit lol", PHP_EOL;
        }
    }
}
