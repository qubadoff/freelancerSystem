<?php
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isadmin()){

    if(isset($_POST['editEmployer'])){
        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $number = htmlspecialchars(trim($_POST['number']));
        $email = htmlspecialchars(trim($_POST['email']));
        $location = htmlspecialchars(trim($_POST['location']));
        $id = htmlspecialchars(trim($_POST['id']));
        $editEmployerQuery = $pdo->prepare("UPDATE users SET first_name=:first_name,last_name=:last_name,number=:number,email=:email,location=:location WHERE id=:id");
        $editEmployerQuery->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':number' => $number,
            ':email' => $email,
            ':location' => $location,
            ':id' => $id
        ]);
        header('Location: ../dashboard/employers_admin.php');
    }
    
    else if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));
            $deleteEmployerQuery = $pdo->prepare("DELETE FROM users WHERE id=:id");
            $deleteEmployerQuery->execute([':id' => $id]);
            header('Location: ../dashboard/employers_admin.php');
        }
    }
    
}

?>