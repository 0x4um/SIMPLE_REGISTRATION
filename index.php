<?
require_once "connection_main.php";
$name = $username = $email = $password = $confirm_password = "";
$name_err = $username_err = $email_err = $password_err = $confirm_password_err = "";
if($_SERVER["REQUEST_METHOD"] == "POST"){
    if(empty(trim($_POST["name"]))){
        $name_err = "please enter a name.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["name"]))){
        $name_err = "Names can only contain letters, numbers, and underscores.";
    }
    if(empty(trim($_POST["email"]))){
        $email_err = "please enter an email";
    } elseif(!preg_match('/@.+\./', trim($_POST["email"]))){
        $email_err = "Invalid email.";
    } else{
        $sql = "SELECT id FROM users WHERE email = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_email);
            $param_username = trim($_POST["email"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_num_rows($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $email_err = "Email is already in use.";
                } else{
                    $email = trim($_POST["email"]);
                }
            } else{
                die("ERROR");
            }
        }
    }
    if(empty(trim($_POST["username"]))){
        $username_err = "Please enter a username.";
    } elseif(!preg_match('/^[a-zA-Z0-9_]+$/', trim($_POST["username"]))){
        $username_err = "Username can only contain letters, numbers, and underscores.";
    } else{
        $sql = "SELECT id FROM users WHERE username = ?";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "s", $param_username);
            $param_username = trim($_POST["username"]);
            if(mysqli_stmt_execute($stmt)){
                mysqli_stmt_store_result($stmt);
                if(mysqli_stmt_num_rows($stmt) == 1){
                    $username_err = "This username is already taken.";
                } else{
                    $username = trim($_POST["username"]);
                }
            } else{
                echo "Oops! Something went wrong. Please try again later.";
            }
            mysqli_stmt_close($stmt);
        }
    }
    if(empty(trim($_POST["password"]))){
        $password_err = "Please enter a password.";     
    } elseif(strlen(trim($_POST["password"])) < 6){
        $password_err = "Password must have atleast 6 characters.";
    } else{
        $password = trim($_POST["password"]);
    }
    
    if(empty(trim($_POST["confirm_password"]))){
        $confirm_password_err = "Please confirm password.";     
    } else{
        $confirm_password = trim($_POST["confirm_password"]);
        if(empty($password_err) && ($password != $confirm_password)){
            $confirm_password_err = "Password did not match.";
        }
    }
    //name, email, username, password
    if(empty($name_err) && empty($email_err) && empty($username_err) && empty($password_err) && empty($confirm_password_err)){
        $date = date('Y/m/d');
        $userid = uniqid(rand(10,20), true);
        $sql = "INSERT INTO users(userid,account_user,username,email,password,date) 
        VALUES(?, ?, ?, ?, ?, ?)";
        if($stmt = mysqli_prepare($conn, $sql)){
            mysqli_stmt_bind_param($stmt, "ssssss", $param_userid, $param_name, $param_username, $param_email, $param_password, $param_date);
            
            $param_userid = $userid;
            $param_name = $_POST["name"];
            $param_username = $_POST["username"];
            $param_email = $email;
            $param_password = password_hash($password, PASSWORD_DEFAULT);
            $param_date = $date;
            
            if(mysqli_stmt_execute($stmt)){
                header('location: login');
            } else{
                die("ERROR");
            }
            mysqli_stmt_close($stmt);
        }
    }
    mysqli_close($conn);
}
?>
<!DOCTYPE html>
<html>
<body>
    <div>
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <div>
                <lable>Name</lable>
                <input type='text' name='name' value="<?php echo $name; ?>">
                <span><?php echo $name_err; ?></span>
            </div>
            <div>
                <label>Username</label>
                <input type="text" name="username" value="<?php echo $username; ?>">
                <span><?php echo $username_err; ?></span>
            </div>  
            <div>
                <label>Email</label>
                <input type="text" name="email" value="<?php echo $email; ?>">
                <span><?php echo $email_err; ?></span>
            </div>
            <div>
                <label>Password</label>
                <input type="password" name="password" value="<?php echo $password; ?>">
                <span><?php echo $password_err; ?></span>
            </div>
            <div>
                <label>Confirm Password</label>
                <input type="password" name="confirm_password" value="<?php echo $confirm_password; ?>">
                <span><?php echo $confirm_password_err; ?></span>
            </div>
            <div>
                <input type="submit"  value="Submit">
                <input type="reset"  value="Reset">
            </div>
            <p>Already have an account? <a href="login.php">Login here</a>.</p>
        </form>
    </div>    
</body>
</html>