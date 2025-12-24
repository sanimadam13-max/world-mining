<?php

$msg = "";

if(isset($_POST['reset'])){

    $msg = "If this email exists, reset instructions will be sent.";

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Forgot Password | W Mining</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{box-sizing:border-box;font-family:Arial}

body{

    margin:0;

    min-height:100vh;

    background:radial-gradient(circle at top,#0b1a22,#02070a);

    color:#fff;

    display:flex;

    justify-content:center;

    align-items:center;

}

.card{

    width:100%;

    max-width:420px;

    background:#0c1820;

    border-radius:22px;

    padding:28px 24px;

    box-shadow:0 0 35px rgba(0,255,153,.15);

}

h2{text-align:center;margin:0}

.sub{text-align:center;color:#aaa;font-size:13px;margin-bottom:20px}

.input{

    width:100%;

    padding:15px;

    border-radius:14px;

    border:none;

    outline:none;

    background:#101f28;

    color:#fff;

    margin-bottom:15px;

}

.btn{

    width:100%;

    padding:16px;

    background:linear-gradient(90deg,#00ff99,#00cc77);

    border:none;

    border-radius:16px;

    font-weight:bold;

    cursor:pointer;

}

.msg{

    background:#0f5132;

    padding:10px;

    border-radius:12px;

    text-align:center;

    margin-bottom:15px;

    color:#00ff99;

}

.back{

    text-align:center;

    margin-top:15px;

}

.back a{color:#00ff99;text-decoration:none}

</style>

</head>

<body>

<div class="card">

    <h2>Reset Password</h2>

    <div class="sub">Enter your email to reset password</div>

    <?php if($msg!=""){ echo "<div class='msg'>$msg</div>"; } ?>

    <form method="post">

        <input class="input" type="email" name="email" placeholder="Enter your email" required>

        <button class="btn" name="reset">SEND RESET LINK</button>

    </form>

    <div class="back">

        <a href="login.php">← Back to Login</a>

    </div>

</div>

</body>

</html>