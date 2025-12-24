<?php

session_start();

require_once "config.php";

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION['user_id'];

/* USER DATA */

$u = $pdo->prepare("SELECT full_name, username, country, photo, kyc_status FROM users WHERE id=?");

$u->execute([$user_id]);

$user = $u->fetch();

/* HANDLE PHOTO UPLOAD */

if(isset($_POST['upload_photo'])){

    if(!empty($_FILES['photo']['name'])){

        $name = "profile_".$user_id."_".time().".jpg";

        move_uploaded_file($_FILES['photo']['tmp_name'], "uploads/".$name);

        $pdo->prepare("UPDATE users SET photo=? WHERE id=?")->execute([$name,$user_id]);

        header("Refresh:0");

    }

}

/* HANDLE KYC UPLOAD */

if(isset($_POST['upload_kyc'])){

    if(!empty($_FILES['document']['name'])){

        $doc = "kyc_".$user_id."_".time().".jpg";

        move_uploaded_file($_FILES['document']['tmp_name'], "uploads/".$doc);

        $pdo->prepare("INSERT INTO kyc (user_id, document, status) VALUES (?,?, 'pending')")

            ->execute([$user_id,$doc]);

        $pdo->prepare("UPDATE users SET kyc_status='pending' WHERE id=?")->execute([$user_id]);

        header("Refresh:0");

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Profile - WORLD MINING</title>

<style>

body{

    margin:0;

    background:#020812;

    font-family:Arial;

    color:#fff;

}

.header{

    text-align:center;

    padding:15px;

    font-size:20px;

    font-weight:bold;

    color:#00ff99;

}

.card{

    background:#071421;

    margin:15px;

    padding:15px;

    border-radius:15px;

}

.center{text-align:center;}

.green{color:#00ff99;}

img.avatar{

    width:90px;

    height:90px;

    border-radius:50%;

    border:2px solid #00ff99;

}

input[type=file]{

    width:100%;

    margin-top:10px;

}

button{

    width:100%;

    padding:12px;

    border:none;

    border-radius:10px;

    background:#00aa66;

    color:#fff;

    margin-top:10px;

    font-size:16px;

}

.status{

    padding:8px;

    border-radius:8px;

    text-align:center;

    margin-top:10px;

}

.pending{background:#664400;}

.verified{background:#006644;}

.bottom-nav{

    position:fixed;

    bottom:0;

    width:100%;

    display:flex;

    justify-content:space-around;

    background:#020812;

    border-top:1px solid #00ff99;

    padding:8px 0;

}

.bottom-nav a{

    color:#aaa;

    text-decoration:none;

    font-size:12px;

    text-align:center;

}

.bottom-nav a.active{color:#00ff99;}

</style>

</head>

<body>

<div class="header">👤 Profile</div>

<div class="card center">

    <img class="avatar" src="uploads/<?php echo $user['photo'] ?: 'default.png'; ?>">

    <h3><?php echo $user['full_name']; ?></h3>

    <p>@<?php echo $user['username']; ?></p>

    <p class="green"><?php echo $user['country']; ?></p>

    <form method="post" enctype="multipart/form-data">

        <input type="file" name="photo" required>

        <button name="upload_photo">Upload Photo</button>

    </form>

</div>

<div class="card">

    <h3>KYC Verification</h3>

    <div class="status <?php echo $user['kyc_status']=='verified'?'verified':'pending'; ?>">

        Status: <?php echo strtoupper($user['kyc_status'] ?: 'NOT SUBMITTED'); ?>

    </div>

    <?php if($user['kyc_status']!='verified'){ ?>

    <form method="post" enctype="multipart/form-data">

        <input type="file" name="document" required>

        <button name="upload_kyc">Submit KYC</button>

    </form>

    <?php } ?>

</div>

<div class="bottom-nav">

    <a href="dashboard.php">🏠<br>Home</a>

    <a href="wallet.php">💼<br>Wallet</a>

    <a href="referral.php">👥<br>Referral</a>

    <a href="upgrade.php">🚀<br>Upgrade</a>

    <a href="profile.php" class="active">👤<br>Profile</a>

</div>
<div class="card">

    <a href="logout.php">

        <button style="background:#cc3333;">LOGOUT</button>

    </a>

</div>
</body>

</html>