<?php

session_start();

require "config.php";

$error = "";

if(isset($_POST['login'])){

    $email = trim($_POST['email']);

    $pass  = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");

    $stmt->execute([$email]);

    $user = $stmt->fetch();

    if($user && password_verify($pass, $user['password'])){

        $_SESSION['user_id'] = $user['id'];

        header("Location: dashboard.php");

        exit;

    }else{

        $error = "Invalid email or password";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Login | W Mining</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{

    box-sizing:border-box;

    font-family:Arial, Helvetica, sans-serif;

}

html,body{

    width:100%;

    height:100%;

    margin:0;

}

body{

    background:radial-gradient(circle at top,#0b1a22,#02070a);

    display:flex;

    align-items:center;

    justify-content:center;

    color:#fff;

}

/* FULL PAGE WRAPPER */

.page{

    width:100%;

    height:100%;

    display:flex;

    align-items:center;

    justify-content:center;

    padding:20px;

}

/* LOGIN CARD */

.card{

    width:100%;

    max-width:420px;

    background:#0c1820;

    border-radius:24px;

    padding:30px 25px;

    box-shadow:0 0 40px rgba(0,255,153,.18);

}

/* LOGO */

.logo{

    text-align:center;

    margin-bottom:20px;

}

.logo h1{

    margin:0;

    color:#00ff99;

    letter-spacing:1px;

}

/* TEXT */

h2{

    text-align:center;

    margin:10px 0 5px;

}

.sub{

    text-align:center;

    color:#aaa;

    font-size:14px;

    margin-bottom:25px;

}

/* INPUTS */

.input{

    width:100%;

    padding:16px;

    margin-bottom:15px;

    border:none;

    outline:none;

    border-radius:16px;

    background:#101f28;

    color:#fff;

    font-size:15px;

}

/* PASSWORD */

.pass-box{

    position:relative;

}

.eye{

    position:absolute;

    right:16px;

    top:17px;

    cursor:pointer;

    font-size:18px;

    color:#00ff99;

}

/* FORGOT */

.forgot{

    text-align:right;

    font-size:13px;

    margin:5px 0 20px;

}

.forgot a{

    color:#00ff99;

    text-decoration:none;

}

/* BUTTON */

.btn{

    width:100%;

    padding:16px;

    border:none;

    border-radius:18px;

    font-size:16px;

    font-weight:bold;

    cursor:pointer;

    background:linear-gradient(90deg,#00ff99,#00cc77);

}

/* FOOTER */

.footer{

    text-align:center;

    margin-top:18px;

    font-size:14px;

}

.footer a{

    color:#00ff99;

    text-decoration:none;

}

/* ERROR */

.error{

    background:#ff3b3b;

    padding:12px;

    border-radius:14px;

    text-align:center;

    margin-bottom:15px;

}

</style>

</head>

<body>

<div class="page">

    <div class="card">

        <div class="logo">

            <h1>🌍 W MINING</h1>

        </div>

        <h2>Welcome back</h2>

        <div class="sub">Login to continue mining</div>

        <?php if($error!=""){ echo "<div class='error'>$error</div>"; } ?>

        <form method="post">

            <input class="input" type="email" name="email" placeholder="Email" required>

            <div class="pass-box">

                <input class="input" type="password" id="pass" name="password" placeholder="Password" required>

                <span class="eye" onclick="toggle()">👁</span>

            </div>

            <div class="forgot">

                <a href="forgot.php">Forgot Password?</a>

            </div>

            <button class="btn" name="login">LOGIN</button>

        </form>

        <div class="footer">

            Don’t have an account? <a href="signup.php">Sign Up</a>

        </div>

    </div>

</div>

<script>

function toggle(){

    let p = document.getElementById("pass");

    p.type = (p.type === "password") ? "text" : "password";

}

</script>

</body>

</html>