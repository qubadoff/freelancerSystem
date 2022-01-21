<?php
date_default_timezone_set('Asia/Baku');
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isadmin()){

    if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));
            $deleteOrderQuery = $pdo->prepare("DELETE FROM orders WHERE order_id=:id");
            $deleteOrderQuery->execute([':id' => $id]);
            header('Location: ../dashboard/operations_admin.php');
        }
        else if($_GET['operation'] == "toggle"){
            $id = htmlspecialchars(trim($_GET['id']));
            $selectOrderQuery = $pdo->prepare("SELECT * FROM orders WHERE order_id=:id");
            $selectOrderQuery->execute([':id' => $id]);
            $order = $selectOrderQuery->fetch(PDO::FETCH_ASSOC);
            if(!$order['completed_status']){
                $changeTo = $order['verification_status'] ? 0 : 1;
                $toggleOrderQuery = $pdo->prepare("UPDATE orders SET verification_status=:ver_status WHERE order_id=:id");
                $toggleOrderQuery->execute([':ver_status'=> $changeTo, ':id' => $id]);
                if(isset($_GET['returnTo'])){
                    if($_GET['returnTo'] == 'view'){
                        header("Location: ../dashboard/view_operation_admin.php?id={$id}");
                    }
                }
                else{
                    header('Location: ../dashboard/operations_admin.php');
                }
            }
            else{
                header('Location: ../dashboard/operations_admin.php');
            }

        }
        else if($_GET['operation'] == "toggleCompleted"){
            $id = htmlspecialchars(trim($_GET['id']));
            $selectOrderQuery = $pdo->prepare("SELECT * FROM orders WHERE order_id=:id");
            $selectOrderQuery->execute([':id' => $id]);
            $order = $selectOrderQuery->fetch(PDO::FETCH_ASSOC);

            if($order['verification_status']){
                if(!$order['completed_status']){
                    $changeTo = 1;
                    $toggleOrderQuery = $pdo->prepare("UPDATE orders SET completed_status=:com_status WHERE order_id=:id");
                    $toggleOrderQuery->execute([':com_status'=> $changeTo, ':id' => $id]);

                    //check if transaction exists for this order, if not calculate commission, delete from balance and add to transactions
                    $transactionCountQuery = $pdo->prepare("SELECT COUNT(*) as transaction_count FROM transactions WHERE order_id=:oid AND transaction_type=1");
                    $transactionCountQuery->execute([':oid' => $order['order_id']]);
                    $transactionCount =  $transactionCountQuery->fetch(PDO::FETCH_ASSOC);
                    if($transactionCount['transaction_count'] == 0){
                        $getCommissionRateQuery = $pdo->prepare("SELECT commission_rate FROM admin_settings WHERE setting_id=1");
                        $getCommissionRateQuery->execute();
                        $getCommissionRate = $getCommissionRateQuery->fetch(PDO::FETCH_ASSOC);

                        $getJobPriceQuery = $pdo->prepare("SELECT job_price FROM jobs WHERE job_id=:jid");
                        $getJobPriceQuery->execute([':jid'=>$order['job_id']]);
                        $getJobPrice = $getJobPriceQuery->fetch(PDO::FETCH_ASSOC);

                        $getBalanceQuery = $pdo->prepare("SELECT balance FROM users WHERE id=:fid");
                        $getBalanceQuery->execute([':fid' => $order['freelancer_id']]);
                        $getBalance = $getBalanceQuery->fetch(PDO::FETCH_ASSOC);

                        $commissionRate = $getCommissionRate['commission_rate'];
                        $jobPrice = $getJobPrice['job_price'];
                        $initialBalance = $getBalance['balance'];

                        $commission = round($jobPrice * ($commissionRate / 100),2);
                        $modifiedBalance = $initialBalance - $commission;

                        $date = date('Y-m-d H:i:s');
                        $addTransactionQuery = $pdo->prepare("INSERT INTO transactions(order_id,job_price,transaction_type,amount,balance_before,balance_after,transaction_time,user_id) VALUES(?,?,?,?,?,?,?,?)");
                        $addTransactionQuery->execute([$order['order_id'], $jobPrice,1,$commission, $initialBalance,$modifiedBalance,$date,$order['freelancer_id']]);

                        $modifyBalanceQuery = $pdo->prepare("UPDATE users SET balance=:modifiedBalance WHERE id=:fid");
                        $modifyBalanceQuery->execute([':modifiedBalance' => $modifiedBalance, ':fid' => $order['freelancer_id']]);

                    }
                }
            }   
            header("Location: ../dashboard/view_operation_admin.php?id={$id}");
        }
    }
    
}

?>