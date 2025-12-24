<?php

session_start();

require "config.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION['user_id'];

$msg = "";

if ($_SERVER['REQUEST_METHOD']=="POST") {

    $amount = 5; // USDT

    $bonus = 5;  // 5x speed

    $img = $_FILES['screenshot'];

    $name = time()."_".$img['name'];

    move_uploaded_file($img['tmp_name'], "uploads/".$name);

    $stmt = $pdo->prepare("

        INSERT INTO upgrades 

        (user_id,amount_usdt,screenshot,percent_bonus,status,created_at)

        VALUES (?,?,?,?, 'pending', NOW())

    ");

    $stmt->execute([$user_id,$amount,$name,$bonus]);

    $msg = "Upgrade request sent. Waiting for admin approval.";

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Upgrade Plan</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>
    /* ===== FIX BOTTOM NAV ===== */

.bottom-nav{

    position: fixed;

    bottom: 0;

    left: 0;

    width: 100%;

    height: 70px;

    background: #0c1820;

    display: flex;

    justify-content: space-around;

    align-items: center;

    box-shadow: 0 -4px 20px rgba(0,0,0,.8);

    z-index: 99999;

}

/* BODY SPACE DON NAV */

body{

    padding-bottom: 90px;

    overflow-x: hidden;

}

/* NAV ITEMS */

.bottom-nav a{

    flex:1;

    text-align:center;

    color:#aaa;

    text-decoration:none;

    font-size:12px;

}

.bottom-nav a span{

    display:block;

    font-size:11px;

    margin-top:4px;

}

.bottom-nav a.active{

    color:#00ff99;

}

body{

    margin:0;

    font-family:Arial;

    background:linear-gradient(180deg,#050b14,#02050a);

    color:#fff;

}

.header{

    padding:15px;

    text-align:center;

    font-size:20px;

    font-weight:bold;

}

.card{

    background:#0b1625;

    margin:15px;

    padding:20px;

    border-radius:15px;

    box-shadow:0 0 15px rgba(0,255,150,0.1);

    text-align:center;

}

.price{

    color:#00ff99;

    font-size:28px;

    font-weight:bold;

}

.list{

    text-align:left;

    margin-top:15px;

    line-height:1.8;

}

button{

    margin-top:20px;

    width:100%;

    padding:14px;

    background:linear-gradient(90deg,#00ff99,#00cc77);

    border:none;

    border-radius:12px;

    font-size:16px;

    font-weight:bold;

}

input[type=file]{

    width:100%;

    margin-top:10px;

    padding:10px;

    background:#02050a;

    border:1px solid #00ff99;

    border-radius:10px;

    color:#fff;

}

.msg{

    text-align:center;

    color:#00ff99;

    margin-top:10px;

}

</style>

</head>

<body>

<div class="header">Upgrade Plan</div>

<div class="card">

    <h2>Basic Plan</h2>

    <div class="price">$5  </div>

    <div class="list">

        • Earn <b>0.5 W COIN / 24h</b><br>

        • Speed: <b>5x</b><br>

        • Validity: <b>30 Days</b>

    </div>

    <form method="post" enctype="multipart/form-data">

        <input type="file" name="screenshot" required>

        <button type="submit">UPGRADE</button>

    </form>

    <?php if($msg): ?>

        <div class="msg"><?= $msg ?></div>

    <?php endif; ?>

</div>
<style>

.bottom-nav{

    position:fixed;

    bottom:0;

    left:0;

    width:100%;

    background:#020812;

    display:flex;

    justify-content:space-around;

    padding:8px 0;

    border-top:1px solid #00ff99;

}

.bottom-nav a{

    text-decoration:none;

    color:#aaa;

    font-size:12px;

    text-align:center;

}

.bottom-nav a.active{

    color:#00ff99;

}

</style>

<div class="bottom-nav">

    <a href="dashboard.php">

        <span class="icon">🏠</span>

        Home

    </a>

    <a href="wallet.php">

        <span class="icon">💼</span>

        Wallet

    </a>

    <a href="referral.php" class="active">

        <span class="icon">👥</span>

        Referral

    </a>

    <a href="upgrade.php">

        <span class="icon">🚀</span>

        Upgrade

    </a>

    <a href="profile.php">

        <span class="icon">👤</span>

        Profile

    </a>

</div>





































</body>

</html>