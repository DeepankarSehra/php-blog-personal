<?php 

    function getRoot(){
        $tt = realpath(__DIR__);
        return $tt;
    }
?>