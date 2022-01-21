<?php
date_default_timezone_set('Asia/Baku');
@session_start();
require_once('dbController.php');
require_once('userTypeController.php');

if(isfreelancer() || isemployer() || isadmin()){
    if(isset($_POST['userUpdate'])){
        if(isset($_POST['first_name'],$_POST['last_name'],$_POST['number'],$_POST['email']) && !empty($_POST['number']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']))
        {
        
            $firstName = htmlspecialchars(trim($_POST['first_name']));
            $lastName = htmlspecialchars(trim($_POST['last_name']));
            $email = htmlspecialchars(trim($_POST['email']));
            $location = htmlspecialchars(trim($_POST['location']));
            $avatar = "";

            $avatarQuery = $pdo->prepare("SELECT avatar FROM users WHERE id=:uid");
            $avatarQuery->execute([':uid' => $_SESSION['id']]);
            $avatarInDb = $avatarQuery->fetch(PDO::FETCH_ASSOC);

            if(!is_uploaded_file($_FILES['avatar']['tmp_name'])){
                $avatar = $avatarInDb['avatar'];
            }else{
                $uploadedName = $_FILES["avatar"]["name"];
                $uploadedPath = $_FILES['avatar']['tmp_name'];
                $allowedTypes = [
                    'image/png' => 'png',
                    'image/jpeg' => 'jpg'
                 ];                 
                $fileSize = filesize($uploadedPath);
                $fileinfo = finfo_open(FILEINFO_MIME_TYPE);
                $filetype = finfo_file($fileinfo, $uploadedPath);
                if($fileSize > 0 && $fileSize < 1048576 && in_array($filetype, array_keys($allowedTypes))){
                    $fileName = md5($firstName.$lastName).time();
                    $extension = $allowedTypes[$filetype];
                    $targetDirectory = "../assets/images/profile_pictures";
                    $newFilepath = $targetDirectory . "/" . $fileName . "." . $extension;
                    move_uploaded_file($uploadedPath,$newFilepath);
                    $avatar = $fileName.".".$extension;
                    if($avatarInDb['avatar']!="user-avatar-placeholder.png"){
                        unlink($targetDirectory. "/".$avatarInDb['avatar']);
                    }
                }else{
                    $avatar = $avatarInDb['avatar'];
                }
            }

            //$user_type = trim($_POST['user_type']);
            //if($user_type != '1' && $user_type !='2'){
            //  header("Location:index.php");
            //}
            //else{

            $number = htmlspecialchars(trim($_POST['number']));
            $options = array("cost"=>5);
            $date = date('Y-m-d H:i:s');

            if(filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $sql = 'select * from users where email = :email and id!=:uid';
                $stmt = $pdo->prepare($sql);
                $p = ['email'=>$email,':uid' =>$_SESSION['id']];
                $stmt->execute($p);
                
                if($stmt->rowCount() == 0)
                {
                    $sql = "UPDATE users SET first_name=:fname,last_name=:lname,location=:location,email=:email,number=:number,avatar=:avatar,updated_at=:updated_at WHERE id=:uid";
                    try{
                        $handle = $pdo->prepare($sql);
                        $params = [
                            ':fname'=>$firstName,
                            ':lname'=>$lastName,
                            ':location'=>$location,
                            ':email'=>$email,
                            ':number'=>$number,
                            ':avatar'=>$avatar,
                            ':updated_at'=>$date,
                            ':uid' => $_SESSION['id']
                        ];
                
                        
                        $handle->execute($params);

                        header("Location:../dashboard/settings.php");
                    }
                    catch(PDOException $e){
                        $errors[] = $e->getMessage();
                    }
                }
                else{
                    header("Location:../dashboard/settings.php");
                }
            }else{
                header("Location:../dashboard/settings.php");
            }
        // }
        }else{
            header("Location:../dashboard/settings.php");
        }
    }

    else if(isset($_POST['passwordUpdate'])){
        if(isset($_POST['currentPass'], $_POST['newPass'], $_POST['newPassRepeated']) && !empty($_POST['currentPass']) && !empty($_POST['newPass']) && !empty($_POST['newPassRepeated'])){
            $currentPass = htmlspecialchars(trim($_POST['currentPass']));
            $newPass = htmlspecialchars(trim($_POST['newPass']));
            $newPassRepeated = htmlspecialchars(trim($_POST['newPassRepeated']));
    
            $currentUserQuery = $pdo->prepare("SELECT password FROM users WHERE id=:uid");
            $currentUserQuery->execute([':uid' => $_SESSION['id']]);
            $currentUser = $currentUserQuery->fetch(PDO::FETCH_ASSOC);
            $options = array("cost"=>5);
            if(password_verify($currentPass,$currentUser['password'])){
                unset($currentUser['password']);
                if($newPass == $newPassRepeated){
                    $hashPassword = password_hash($newPass,PASSWORD_BCRYPT,$options);
                    $updatePassQuery = $pdo->prepare("UPDATE users SET password=:pass WHERE id=:uid");
                    $updatePassQuery->execute([':pass' => $hashPassword, ':uid' => $_SESSION['id']]);
                    header("Location:../dashboard/settings.php");
                }else{
                    header("Location:../dashboard/settings.php");
                }
            }
            else{
                header("Location:../dashboard/settings.php");
            }
        }
        else{
            header("Location:../dashboard/settings.php");
        }
    }
}
?>
