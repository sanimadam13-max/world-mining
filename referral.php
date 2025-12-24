<?php

session_start();

require_once "config.php";

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION['user_id'];

/* USER */

$user = $pdo->prepare("SELECT username, referral_code FROM users WHERE id=?");

$user->execute([$user_id]);

$user = $user->fetch();

/* TOTAL REFERRALS */

$totalRef = $pdo->prepare("SELECT COUNT(*) FROM referrals WHERE referrer_id=?");

$totalRef->execute([$user_id]);

$total_referrals = $totalRef->fetchColumn();

/* ACTIVE MINERS */

$activeRef = $pdo->prepare("

SELECT COUNT(*) FROM referrals r

JOIN mining m ON r.referred_id = m.user_id

WHERE r.referrer_id=? AND m.hash_rate > 0

");

$activeRef->execute([$user_id]);

$active_miners = $activeRef->fetchColumn();

/* REFERRAL EARNINGS (5%) */

$earn = $pdo->prepare("

SELECT SUM(amount) FROM transactions

WHERE user_id=? AND type='referral'

");

$earn->execute([$user_id]);

$ref_earnings = $earn->fetchColumn() ?: 0;

$ref_link = "https://worldmining.free.nf/signup.php?ref=".$user['referral_code'];

?>

<!DOCTYPE html>

<html>

<head>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Referral - WORLD MINING</title>

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

.green{

    color:#00ff99;

}

.ref-box{

    display:flex;

    justify-content:space-between;

    align-items:center;

    background:#020812;

    padding:10px;

    border-radius:10px;

    margin-top:10px;

}

.copy{

    background:#00aa66;

    border:none;

    padding:8px 15px;

    color:#fff;

    border-radius:8px;

}

.stat{

    display:flex;

    justify-content:space-between;

    padding:10px 0;

    border-bottom:1px solid #0a253a;

}

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

.bottom-nav a.active{ color:#00ff99; }

</style>

</head>

<body>

<div class="header">👥 Referral</div>

<div class="card">

    <b>Earn <span class="green">5%</span> from your team</b>

    <p>You earn 5% of your referrals mining rewards</p>

</div>

<div class="card">

    <b>Your referral code</b>

    <div class="ref-box">

        <span class="green"><?php echo $user['referral_code']; ?></span>

        <button class="copy" onclick="copyText('<?php echo $user['referral_code']; ?>')">COPY</button>

    </div>

    <small><?php echo $ref_link; ?></small>

</div>

<div class="card">

    <div class="stat">

        <span>Total Referrals</span>

        <b class="green"><?php echo $total_referrals; ?></b>

    </div>

    <div class="stat">

        <span>Active Miners</span>

        <b class="green"><?php echo $active_miners; ?></b>

    </div>

    <div class="stat">

        <span>Referral Earnings</span>

        <b class="green"><?php echo number_format($ref_earnings,2); ?> W COIN</b>

    </div>

</div>

<div class="bottom-nav">

    <a href="dashboard.php">🏠<br>Home</a>

    <a href="wallet.php">💼<br>Wallet</a>

    <a href="referral.php" class="active">👥<br>Referral</a>

    <a href="upgrade.php">🚀<br>Upgrade</a>

    <a href="profile.php">👤<br>Profile</a>

</div>

<script>

function copyText(text){

    navigator.clipboard.writeText(text);

    alert("Copied!");

}

</script>

</body>

</html>