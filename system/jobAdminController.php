<?php
@session_start();
require_once('dbController.php');
require_once "userTypeController.php";

if(isadmin()){

    if(isset($_POST['editJobAdmin'])){
        $job_title = htmlspecialchars(trim($_POST['title']));
        $job_description = htmlspecialchars(trim($_POST['description']));
        $job_price = htmlspecialchars(trim($_POST['price']));
        $category = htmlspecialchars(trim($_POST['category']));
        $id = htmlspecialchars(trim($_POST['id']));
        $editJobQuery = $pdo->prepare("UPDATE jobs SET job_title=:job_title,job_description=:job_description,job_price=:job_price,category_id=:cat WHERE job_id=:id");
        $editJobQuery->execute([
            ':job_title' => $job_title,
            ':job_description' => $job_description,
            ':job_price' => $job_price,
            ':cat' => $category,
            ':id' => $id
        ]);

        $currentAmountOfPicturesQuery = $pdo->prepare("SELECT COUNT(*) as current_amount FROM jobs_pictures WHERE job_id=:jid");
        $currentAmountOfPicturesQuery->execute([':jid' =>$id]);
        $currentAmountOfPictures = $currentAmountOfPicturesQuery->fetch(PDO::FETCH_ASSOC);

        if(count($_FILES["pictures"]["tmp_name"])  <= 4 - $currentAmountOfPictures['current_amount']){    
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
                    $fileName = md5(basename($uploadedName)).time().rand(); 
                    $extension = $allowedTypes[$filetype];
                    $targetDirectory = "../assets/images/job_pictures";
                    $newFilepath = $targetDirectory . "/" . $fileName . "." . $extension;
                    move_uploaded_file($uploadedPath,$newFilepath);
                    $toDbUrl = $fileName.".".$extension;

                    $addImageToDbQuery = $pdo->prepare("INSERT INTO jobs_pictures(job_id,picture_url) VALUES(?,?)");
                    $addImageToDbQuery->execute([$id,$toDbUrl]);
                }else{
                    header("Location: ../dashboard/edit_job_admin.php?id={$id}");
                }
            }
        }
        header("Location: ../dashboard/edit_job_admin.php?id={$id}");
    }
    
    else if(isset($_GET['operation']) && isset($_GET['id'])){
        if($_GET['operation'] == "delete"){
            $id = htmlspecialchars(trim($_GET['id']));

            $picturesQuery = $pdo->prepare("SELECT picture_url FROM jobs_pictures WHERE job_id=:jid");
            $picturesQuery->execute([':jid' => $id]);
            while($picture = $picturesQuery->fetch(PDO::FETCH_ASSOC)){
                unlink('../assets/images/job_pictures/'.$picture['picture_url']);
            }
            $deleteJobQuery = $pdo->prepare("DELETE FROM jobs WHERE job_id=:id");
            $deleteJobQuery->execute([':id' => $id]);


            header('Location: ../dashboard/jobs_admin.php');
        }

        else if($_GET['operation'] == "toggle"){
            $id = htmlspecialchars(trim($_GET['id']));
            $selectJobQuery = $pdo->prepare("SELECT * FROM jobs WHERE job_id=:id");
            $selectJobQuery->execute([':id' => $id]);
            $job = $selectJobQuery->fetch(PDO::FETCH_ASSOC);

            $changeTo = $job['verification_status'] ? 0 : 1;
            $toggleJobQuery = $pdo->prepare("UPDATE jobs SET verification_status=:ver_status WHERE job_id=:id");
            $toggleJobQuery->execute([':ver_status'=> $changeTo, ':id' => $id]);
            if(isset($_GET['returnTo'])){
                if($_GET['returnTo'] == 'view'){
                    header("Location: ../dashboard/view_job_admin.php?id={$id}");
                }
            }
            else{
                header('Location: ../dashboard/jobs_admin.php');
            }
        }


        else if($_GET['operation'] == "deletePic" && isset($_GET['jobId'])){
            $id = htmlspecialchars(trim($_GET['id']));
            $jobId = htmlspecialchars(trim($_GET['jobId']));

            $verifyJobPictureQuery = $pdo->prepare("SELECT COUNT(*) as count_job FROM jobs WHERE job_id=:jid");
            $verifyJobPictureQuery->execute([':jid' =>$jobId]);
            $verifyJobPicture = $verifyJobPictureQuery->fetch(PDO::FETCH_ASSOC);

            if($verifyJobPicture['count_job'] > 0){
                $picturesQuery = $pdo->prepare("SELECT COUNT(*) as picture_count, picture_url FROM jobs_pictures WHERE jobs_pictures_id=:jpid AND job_id=:jid");
                $picturesQuery->execute([':jpid' => $id,':jid' => $jobId]);
                $picture = $picturesQuery->fetch(PDO::FETCH_ASSOC);
                if($picture['picture_count'] > 0){
                    unlink('../assets/images/job_pictures/'.$picture['picture_url']);
                    $deletePictureQuery = $pdo->prepare("DELETE FROM jobs_pictures WHERE jobs_pictures_id=:jpid AND job_id=:jid");
                    $deletePictureQuery->execute([':jpid' => $id,':jid' => $jobId]);
                    header("Location: ../dashboard/edit_job_admin.php?id={$jobId}");
                }
                else{
                    header("Location: ../dashboard/edit_job_admin.php?id={$id}");
                }
            }
            else{
                header("Location: ../dashboard/edit_job_admin.php?id={$id}");
            }

        }

    }
    
}

?>