<?php
date_default_timezone_set('Asia/Baku');
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isemployer()){
    
    if(isset($_GET['operation']) && (isset($_GET['id']) || isset($_GET['job_id']))){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));
            $deleteOrderQuery = $pdo->prepare("DELETE FROM orders WHERE order_id=:id AND completed_status=0 AND employer_id=:eid");
            $deleteOrderQuery->execute([':id' => $id, ':eid' => $_SESSION['id']]);
            header('Location: ../dashboard/operations_employer.php');
        }
        else if($_GET['operation'] == "new"){
            $job_id = htmlspecialchars(trim($_GET['job_id']));
            $jobQuery = $pdo->prepare("SELECT * FROM jobs WHERE job_id=:jid AND verification_status=1");
            $jobQuery->execute([':jid'=>$job_id]);
            $job = $jobQuery->fetch(PDO::FETCH_ASSOC);
            if(!$job){
                header('Location: ../dashboard/operations_employer.php');
            }else{
                $date = date('Y-m-d H:i:s');
                $createOrderQuery = $pdo->prepare("INSERT INTO orders(job_id,employer_id,freelancer_id,created_at) VALUES(?,?,?,?)");
                $createOrderQuery->execute([$job_id, $_SESSION['id'], $job['user_id'],$date]);
                header('Location: ../dashboard/operations_employer.php');
            }
        }
    }    
}

?>