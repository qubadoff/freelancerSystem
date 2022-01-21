<?php
date_default_timezone_set('Asia/Baku');
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isadmin()){

    if(isset($_POST['editFreelancer'])){
        $id = htmlspecialchars(trim($_POST['id']));

        $initialBalanceQuery = $pdo->prepare("SELECT balance from users WHERE id=:id");
        $initialBalanceQuery->execute([':id'=>$id]);
        $initialBalanceFetched = $initialBalanceQuery->fetch(PDO::FETCH_ASSOC);
        $initialBalance = $initialBalanceFetched['balance'];

        $first_name = htmlspecialchars(trim($_POST['first_name']));
        $last_name = htmlspecialchars(trim($_POST['last_name']));
        $number = htmlspecialchars(trim($_POST['number']));
        $email = htmlspecialchars(trim($_POST['email']));
        $location = htmlspecialchars(trim($_POST['location']));
        $modifiedBalance = htmlspecialchars(trim($_POST['balance']));
        $editFreelancerQuery = $pdo->prepare("UPDATE users SET first_name=:first_name,last_name=:last_name,number=:number,email=:email,location=:location,balance=:balance WHERE id=:id");
        $editFreelancerQuery->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':number' => $number,
            ':email' => $email,
            ':location' => $location,
            ':id' => $id,
            ':balance'=>$modifiedBalance
        ]);

        $date = date('Y-m-d H:i:s');
        $addTransactionQuery = $pdo->prepare("INSERT INTO transactions(order_id,job_price,transaction_type,amount,balance_before,balance_after,transaction_time,user_id) VALUES(?,?,?,?,?,?,?,?)");
        $addTransactionQuery->execute([0,0,3,abs($modifiedBalance - $initialBalance),$initialBalance,$modifiedBalance,$date,$id]);

        header('Location: ../dashboard/freelancers_admin.php');
    }
    
    else if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));
            $deleteFreelancerQuery = $pdo->prepare("DELETE FROM users WHERE id=:id");
            $deleteFreelancerQuery->execute([':id' => $id]);
            header('Location: ../dashboard/freelancers_admin.php');
        }
    }
    
}

?>