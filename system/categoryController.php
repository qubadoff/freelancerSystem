<?php
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isadmin()){

    if(isset($_POST['addCategory'])){
        $title = htmlspecialchars(trim($_POST['title']));
        $addCatQuery = $pdo->prepare("INSERT INTO categories(category_name) VALUES(?)");
        $addCatQuery->execute([$title]);
        header('Location: ../dashboard/categories.php');
    }
    
    else if(isset($_POST['editCategory'])){
        $title = htmlspecialchars(trim($_POST['title']));
        $id = htmlspecialchars(trim($_POST['id']));
        $editCatQuery = $pdo->prepare("UPDATE categories SET category_name=:title WHERE category_id=:id");
        $editCatQuery->execute([':title' => $title, ':id' => $id]);
        header('Location: ../dashboard/categories.php');
    }
    
    else if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            //todo : edit table so that when category gets deleted all jobs related to category should be deleted)
            $id = htmlspecialchars(trim($_GET['id']));
            $deleteCatQuery = $pdo->prepare("DELETE FROM categories WHERE category_id=:id");
            $deleteCatQuery->execute([':id' => $id]);
            header('Location: ../dashboard/categories.php');
        }
    }
    
}

?>