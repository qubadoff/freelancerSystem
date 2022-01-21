<?php
date_default_timezone_set('Asia/Baku');
@session_start();
require_once('dbController.php');

if(isset($_POST['submit']))
{
    if(isset($_POST['first_name'],$_POST['last_name'],$_POST['number'],$_POST['email'],$_POST['password'],$_POST['user_type']) && !empty($_POST['number']) && !empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['password']) && !empty($_POST['user_type']))
    {
       
        $firstName = htmlspecialchars(trim($_POST['first_name']));
        $lastName = htmlspecialchars(trim($_POST['last_name']));
        $email = htmlspecialchars(trim($_POST['email']));
        $password = htmlspecialchars(trim($_POST['password']));
        $user_type = htmlspecialchars(trim($_POST['user_type']));
        if($user_type != '1' && $user_type !='2'){
            header("Location:index.php");
        }
        else{
            $number = htmlspecialchars(trim($_POST['number']));
            $options = array("cost"=>5);
            $hashPassword = password_hash($password,PASSWORD_BCRYPT,$options);
            $avatar = 'user-avatar-placeholder.png';
            $date = date('Y-m-d H:i:s');

            if(filter_var($email, FILTER_VALIDATE_EMAIL))
            {
                $sql = 'select * from users where email = :email';
                $stmt = $pdo->prepare($sql);
                $p = ['email'=>$email];
                $stmt->execute($p);
                
                if($stmt->rowCount() == 0)
                {
                    $sql = "insert into users (first_name, last_name, email, number, `password`, avatar, user_type, created_at,updated_at) values(:fname,:lname,:email,:number,:pass,:avatar,:user_type,:created_at,:updated_at)";
                    try{
                        $handle = $pdo->prepare($sql);
                        $params = [
                            ':fname'=>$firstName,
                            ':lname'=>$lastName,
                            ':email'=>$email,
                            ':number'=>$number,
                            ':pass'=>$hashPassword,
                            ':avatar'=>$avatar,
                            ':user_type'=>$user_type,
                            ':created_at'=>$date,
                            ':updated_at'=>$date
                        ];
                
                        
                        $handle->execute($params);
                        
                        $success = 'Hesabınız yaradıldı, xahiş edirik login edəsiniz.';
                        
                    }
                    catch(PDOException $e){
                        $errors[] = $e->getMessage();
                    }
                }
                else
                {
                    $valFirstName = $firstName;
                    $valLastName = $lastName;
                    $valEmail = '';
                    $valPassword = $password;
                    $valuser_type = $user_type;

                    $errors[] = 'Email artıq qeydiyyatdan keçib.';
                }
            }
            else
            {
                $errors[] = "Email addres doğru deyil.";
            }
        }
    }
    else
    {
        if(!isset($_POST['first_name']) || empty($_POST['first_name']))
        {
            $errors[] = 'Ad vacibdir';
        }
        else
        {
            $valFirstName = $_POST['first_name'];
        }
        if(!isset($_POST['last_name']) || empty($_POST['last_name']))
        {
            $errors[] = 'Soyad vacibdir';
        }
        else
        {
            $valLastName = $_POST['last_name'];
        }

        if(!isset($_POST['email']) || empty($_POST['email']))
        {
            $errors[] = 'Email vacibdir';
        }
        else
        {
            $valEmail = $_POST['email'];
        }

        if(!isset($_POST['password']) || empty($_POST['password']))
        {
            $errors[] = 'Parol vacibdir';
        }
        else
        {
            $valPassword = $_POST['password'];
        }
        if(!isset($_POST['user_type']) || empty($_POST['user_type']))
        {
            $errors[] = 'İstifadəçi tipi vacibdir';
        }
        else
        {
            $valuser_type = $_POST['user_type'];
        }
        
    }

}
?>
