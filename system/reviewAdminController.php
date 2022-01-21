<?php
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isadmin()){

    if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));

            $getFreelancerIdQuery = $pdo->prepare("SELECT freelancer_id FROM reviews WHERE review_id=:id");
            $getFreelancerIdQuery->execute([':id' => $id]);
            $getFreelancerId = $getFreelancerIdQuery->fetch(PDO::FETCH_ASSOC);

            $deleteReviewQuery = $pdo->prepare("DELETE FROM reviews WHERE review_id=:id");
            $deleteReviewQuery->execute([':id' => $id]);

            if($deleteReviewQuery){
                $getAverageQuery = $pdo->prepare("SELECT AVG(review_rating) as avg_rating FROM reviews WHERE freelancer_id=:fid");
                $getAverageQuery->execute([':fid'=>$getFreelancerId['freelancer_id']]);
                $average = $getAverageQuery->fetch(PDO::FETCH_ASSOC);
                $updatedRating = round($average['avg_rating'],2);

                $changeRatingOfFreelancerQuery = $pdo->prepare("UPDATE users SET rating=:rating WHERE id=:fid");
                $changeRatingOfFreelancerQuery->execute([':rating' => $updatedRating, ':fid' => $getFreelancerId['freelancer_id']]);
            }

            header('Location: ../dashboard/reviews_admin.php');
        }
    }
    
}else{
    header('Location: ../dashboard/index.php');
}

?>