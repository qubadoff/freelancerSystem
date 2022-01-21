<?php
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isfreelancer()){

    if(isset($_POST['addJobFreelancer'])){
        $job_title = htmlspecialchars(trim($_POST['title']));
        $job_description = htmlspecialchars(trim($_POST['description']));
        $job_price = htmlspecialchars(trim($_POST['price']));
        $category = htmlspecialchars(trim($_POST['category']));
        $regions = $_POST['location'];
        $id = $_SESSION['id'];
        $hasOneVerifiedImage = false;
        if(count($_FILES["pictures"]["tmp_name"])  <= 4 && $_FILES["pictures"]["size"][0] != 0){
            $addJobQuery = $pdo->prepare("INSERT INTO jobs(job_title, job_description, job_price, category_id, user_id) VALUES(?,?,?,?,?)");
            $addJobQuery->execute([
                $job_title,
                $job_description,
                $job_price,
                $category,
                $id
            ]);

            $jobId = $pdo->lastInsertId();

            foreach($_FILES["pictures"]["tmp_name"] as $key=>$tmp_name){
                $uploadedName = $_FILES["pictures"]["name"][$key];
                $uploadedPath = $_FILES["pictures"]["tmp_name"][$key];
                $allowedTypes = [
                    'image/png' => 'png',
                    'image/jpeg' => 'jpg'
                 ];
                $fileSize = filesize($uploadedPath);
                $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
                $filetype = finfo_file($fileinfo, $uploadedPath);
                if($fileSize > 0 && $fileSize < 1048576 && in_array($filetype, array_keys($allowedTypes))){
                    $hasOneVerifiedImage = true;
                    $fileName = md5(basename($uploadedName)).time().rand();
                    $extension = $allowedTypes[$filetype];
                    $targetDirectory = "../assets/images/job_pictures";
                    $newFilepath = $targetDirectory . "/" . $fileName . "." . $extension;
                    move_uploaded_file($uploadedPath,$newFilepath);
                    $toDbUrl = $fileName.".".$extension;

                    $addImageToDbQuery = $pdo->prepare("INSERT INTO jobs_pictures(job_id,picture_url) VALUES(?,?)");
                    $addImageToDbQuery->execute([$jobId,$toDbUrl]);
                }
            }
            if($hasOneVerifiedImage){
                $addLocationQuery = $pdo->prepare("INSERT INTO jobs_regions(job_id, region_id) VALUES(?,?)");
                foreach($regions as $region){
                    $addLocationQuery->execute([$jobId, $region]);
                }
                header('Location: ../dashboard/jobs_freelancer.php');
            }
            else{
                $deleteLastJobQuery = $pdo->prepare("DELETE FROM jobs WHERE job_id=:jid");
                $deleteLastJobQuery->execute([':jid'=>$jobId]);
                header('Location: ../dashboard/add_job.php');
            }


        }else{
            header('Location: ../dashboard/add_job.php');
        }
    }

    else if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));
            $freelancerId = $_SESSION['id'];

            $verifyJobPictureQuery = $pdo->prepare("SELECT COUNT(*) as count_job FROM jobs WHERE job_id=:jid AND user_id=:uid");
            $verifyJobPictureQuery->execute([':jid' =>$id,':uid'=>$freelancerId]);
            $verifyJobPicture = $verifyJobPictureQuery->fetch(PDO::FETCH_ASSOC);

            if($verifyJobPicture['count_job'] > 0){
                $picturesQuery = $pdo->prepare("SELECT picture_url FROM jobs_pictures WHERE job_id=:jid");
                $picturesQuery->execute([':jid' => $id]);
                while($picture = $picturesQuery->fetch(PDO::FETCH_ASSOC)){
                    unlink('../assets/images/job_pictures/'.$picture['picture_url']);
                }

                $deleteJobQuery = $pdo->prepare("DELETE FROM jobs WHERE job_id=:id AND user_id=:uid");
                $deleteJobQuery->execute([':id' => $id,':uid'=>$freelancerId]);
                header('Location: ../dashboard/jobs_freelancer.php');
            }
            else{
                header('Location: ../dashboard/jobs_freelancer.php');
            }
        }
        else if($_GET['operation'] == "deletePic" && isset($_GET['jobId'])){
            $id = htmlspecialchars(trim($_GET['id']));
            $jobId = htmlspecialchars(trim($_GET['jobId']));
            $freelancerId = $_SESSION['id'];

            $verifyJobPictureQuery = $pdo->prepare("SELECT COUNT(*) as count_job FROM jobs WHERE job_id=:jid AND user_id=:uid");
            $verifyJobPictureQuery->execute([':jid' =>$jobId,':uid'=>$freelancerId]);
            $verifyJobPicture = $verifyJobPictureQuery->fetch(PDO::FETCH_ASSOC);

            if($verifyJobPicture['count_job'] > 0){
                $picturesQuery = $pdo->prepare("SELECT COUNT(*) as picture_count, picture_url FROM jobs_pictures WHERE jobs_pictures_id=:jpid AND job_id=:jid");
                $picturesQuery->execute([':jpid' => $id,':jid' => $jobId]);
                $picture = $picturesQuery->fetch(PDO::FETCH_ASSOC);
                if($picture['picture_count'] > 0){
                    unlink('../assets/images/job_pictures/'.$picture['picture_url']);
                    $deletePictureQuery = $pdo->prepare("DELETE FROM jobs_pictures WHERE jobs_pictures_id=:jpid AND job_id=:jid");
                    $deletePictureQuery->execute([':jpid' => $id,':jid' => $jobId]);
                    header("Location: ../dashboard/edit_job_freelancer.php?id={$jobId}");
                }
                else{
                    header('Location: ../dashboard/jobs_freelancer.php');
                }
            }
            else{
                header('Location: ../dashboard/jobs_freelancer.php');
            }

        }
        else{
            header('Location: ../dashboard/jobs_freelancer.php');
        }
    }

    else if(isset($_POST['editJobFreelancer'])){
        $job_title = htmlspecialchars(trim($_POST['title']));
        $job_description = htmlspecialchars(trim($_POST['description']));
        $job_price = htmlspecialchars(trim($_POST['price']));
        $category = htmlspecialchars(trim($_POST['category']));
        $id = htmlspecialchars(trim($_POST['id']));
        $freelancerId = $_SESSION['id'];
        $regions = $_POST['location'];
        $editJobQuery = $pdo->prepare("UPDATE jobs SET job_title=:job_title,job_description=:job_description,job_price=:job_price,category_id=:cat, verification_status=0 WHERE job_id=:id AND user_id=:uid");
        $editJobQuery->execute([
            ':job_title' => $job_title,
            ':job_description' => $job_description,
            ':job_price' => $job_price,
            ':cat' => $category,
            ':id' => $id,
            ':uid' => $freelancerId
        ]);

        $verifyJobToUser = $pdo->prepare("SELECT job_id FROM jobs WHERE job_id=:id AND user_id=:uid");
        $verifyJobToUser->execute([':id'=>$id, ':uid' => $freelancerId]);
        $verify = $verifyJobToUser->fetch(PDO::FETCH_ASSOC);


        if($verify){
            $currentAmountOfPicturesQuery = $pdo->prepare("SELECT COUNT(*) as current_amount FROM jobs_pictures WHERE job_id=:jid");
            $currentAmountOfPicturesQuery->execute([':jid' =>$id]);
            $currentAmountOfPictures = $currentAmountOfPicturesQuery->fetch(PDO::FETCH_ASSOC);
            if(count($_FILES["pictures"]["tmp_name"])  <= 4 - $currentAmountOfPictures['current_amount']){
                foreach($_FILES["pictures"]["tmp_name"] as $key=>$tmp_name){
                    $uploadedName = $_FILES["pictures"]["name"][$key];
                    $uploadedPath = $_FILES["pictures"]["tmp_name"][$key];
                    $allowedTypes = [
                        'image/png' => 'png',
                        'image/jpeg' => 'jpg',
                        'image/jpeg' => 'jpg'
                     ];
                    $fileSize = filesize($uploadedPath);
                    $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
                    $filetype = finfo_file($fileinfo, $uploadedPath);
                    if($fileSize > 0 && $fileSize < 1048576 && in_array($filetype, array_keys($allowedTypes))){
                        $fileName = md5(basename($uploadedName)).time().rand();
                        $extension = $allowedTypes[$filetype];
                        $targetDirectory = "../assets/images/job_pictures";
                        $newFilepath = $targetDirectory . "/" . $fileName . "." . $extension;
                        move_uploaded_file($uploadedPath,$newFilepath);
                        $toDbUrl = $fileName.".".$extension;

                        $addImageToDbQuery = $pdo->prepare("INSERT INTO jobs_pictures(job_id,picture_url) VALUES(?,?)");
                        $addImageToDbQuery->execute([$id,$toDbUrl]);
                    }else{
                        header("Location: ../dashboard/edit_job_freelancer.php?id={$id}");
                    }
                }
            }

        }


        header("Location: ../dashboard/edit_job_freelancer.php?id={$id}");
    }
    else{
        header('Location: ../dashboard/jobs_freelancer.php');
    }
}else{
    header('Location: ../dashboard/index.php');
}

?>
