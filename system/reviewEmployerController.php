<?php
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isemployer()){
    if(isset($_POST['addReview'])){
        $description = htmlspecialchars(trim($_POST['description']));
        $freelancerId = htmlspecialchars(trim($_POST['freelancerId']));
        $rating = 6 - htmlspecialchars(trim($_POST['rating']));

        
        $checkOperationQuery = $pdo->prepare("SELECT * FROM orders WHERE employer_id=:eid AND freelancer_id=:fid AND completed_status=1 AND verification_status=1");
        $checkOperationQuery->execute([':eid'=> $_SESSION['id'],':fid'=>$freelancerId]);
        $checkOperation = $checkOperationQuery->fetch(PDO::FETCH_ASSOC);

        if(!$checkOperation){
            header('Location: ../dashboard/reviews.php');
        }else{
            $addReviewQuery = $pdo->prepare("INSERT INTO reviews(freelancer_id,employer_id,review_rating,review_text) VALUES(?,?,?,?)");
            $addReviewQuery->execute([$freelancerId,$_SESSION['id'],$rating,$description]);
            if($addReviewQuery){
                $getAverageQuery = $pdo->prepare("SELECT AVG(review_rating) as avg_rating FROM reviews WHERE freelancer_id=:fid");
                $getAverageQuery->execute([':fid'=>$freelancerId]);
                $average = $getAverageQuery->fetch(PDO::FETCH_ASSOC);
                $updatedRating = round($average['avg_rating'],2);

                $changeRatingOfFreelancerQuery = $pdo->prepare("UPDATE users SET rating=:rating WHERE id=:fid");
                $changeRatingOfFreelancerQuery->execute([':rating' => $updatedRating, ':fid' => $freelancerId]);
            }
            header('Location: ../dashboard/reviews.php');
        }

    }


    if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));

            $getFreelancerIdQuery = $pdo->prepare("SELECT freelancer_id FROM reviews WHERE review_id=:id AND employer_id=:eid");
            $getFreelancerIdQuery->execute([':id' => $id,':eid' =>$_SESSION['id']]);
            $getFreelancerId = $getFreelancerIdQuery->fetch(PDO::FETCH_ASSOC);

            $deleteReviewQuery = $pdo->prepare("DELETE FROM reviews WHERE review_id=:id AND employer_id=:eid");
            $deleteReviewQuery->execute([':id' => $id,':eid' =>$_SESSION['id']]);
            
            if($deleteReviewQuery){
                $getAverageQuery = $pdo->prepare("SELECT AVG(review_rating) as avg_rating FROM reviews WHERE freelancer_id=:fid");
                $getAverageQuery->execute([':fid'=>$getFreelancerId['freelancer_id']]);
                $average = $getAverageQuery->fetch(PDO::FETCH_ASSOC);
                $updatedRating = round($average['avg_rating'],2);

                $changeRatingOfFreelancerQuery = $pdo->prepare("UPDATE users SET rating=:rating WHERE id=:fid");
                $changeRatingOfFreelancerQuery->execute([':rating' => $updatedRating, ':fid' => $getFreelancerId['freelancer_id']]);
            }
            header('Location: ../dashboard/reviews.php');
        }
    }
    
}

?>