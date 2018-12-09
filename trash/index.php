<?php
    require_once ("DB.php");
?>
<!doctype html>
<html class="h-100">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Home Page</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
</head>

<body class="h-100">

    <div class="container h-100">
        <div>
            <h3 class="text-center">Register</h3>
            <?php
                if(!empty($_POST["username"]) && !empty($_POST["password"])){
                    $username = mysqli_real_escape_string($conn, $_POST["username"]);
                    $password = mysqli_real_escape_string($conn,$_POST["password"]);
                    $role_id = 2;
                    $sql = "insert into users (username,password,full_name,role_id,created_at) values ('".$username."', '".$password."','' ,'".$role_id."', now())";
                    $result = mysqli_query($conn,$sql);
                    if($result){
                        echo "Insert Successfully";
                    }else{
                        echo "Failed";
                    }
                }else{
                    echo "Form is not completely filled";
                }
            ?>
            <form class="w-100" method="post" action="index.php">
                <div class="form-group">
                    <label>Username</label>
                    <input type="text" class="form-control" name="username" placeholder="Username">
                </div>
                <div class="form-group">
                    <label>Password</label>
                    <input type="password" class="form-control" name="password" placeholder="Password">
                </div>
                <div class="text-center">
                    <input type="submit" class="btn btn-primary" value="Register">
                </div>
            </form>

        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js" integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/js/bootstrap.min.js" integrity="sha384-ChfqqxuZUCnJSK3+MXmPNIyE6ZbWh2IMqE241rYiqJxyMiZ6OW/JmZQ5stwEULTy" crossorigin="anonymous"></script>
</body>

</html>