<?php
namespace feiron\felaraframe\lib\traits;

    trait error{
        function sendError($validator){
            $msg='';
            foreach($validator->getMessageBag()->toArray() as $key=>$message){
                foreach($message as $info){
                    $msg.=($key.': '.$info."\n");
                }
            }
            response()->json(['error' => ['message' => $msg]],400)->send(); 
            exit;
        }
    }
?>