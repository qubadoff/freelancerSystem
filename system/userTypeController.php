<?php
@session_start();
function isadmin() {

    if(isset($_SESSION['id'])){

        return $_SESSION['user_type'] == '3';
    }
    return false;
}

function isemployer() {
    if(isset($_SESSION['id'])){
        return $_SESSION['user_type'] == '2';
    }
    return false;
}

function isfreelancer() {
    if(isset($_SESSION['id'])){
        return $_SESSION['user_type'] == '1';
    }
    return false;

}

?>