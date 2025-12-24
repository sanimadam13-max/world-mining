<?php

session_start();

require_once "config.php";

if(!isset($_SESSION['user_id'])){

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION['user_id'];

/* USER */

$user = $pdo->prepare("SELECT * FROM users WHERE id=?");

$user->execute([$user_id]);

$user = $user->fetch();

/* WALLET */

$wallet = $pdo->prepare("SELECT * FROM wallets WHERE user_id=?");

$wallet->execute([$user_id]);

$wallet = $wallet->fetch();

$balance = $wallet ? $wallet['balance'] : 0;

/* MINING */

$mining = $pdo->prepare("SELECT * FROM mining WHERE user_id=?");

$mining->execute([$user_id]);

$mining = $mining->fetch();

$hash_rate = $mining ? $mining['hash_rate'] : 0.1;

$mining_active = false;

$remaining = 0;

if($mining){

    $last = strtotime($mining['last_mined']);

    if(time() - $last < 86400){

        $mining_active = true;

        $remaining = 86400 - (time() - $last);

    }

}

/* START MINING */

if(isset($_POST['start_mining']) && !$mining_active){

    if($mining){

        $pdo->prepare("UPDATE mining SET last_mined=NOW() WHERE user_id=?")

            ->execute([$user_id]);

    }else{

        $pdo->prepare("INSERT INTO mining(user_id,hash_rate,mining_start,last_mined)

            VALUES(?,0.1,NOW(),NOW())")->execute([$user_id]);

    }

    $pdo->prepare("UPDATE wallets SET balance = balance + 0.1 WHERE user_id=?")

        ->execute([$user_id]);

    header("Location: dashboard.php");

    exit;

}

/* REFERRALS */

$total_ref = $pdo->prepare("SELECT COUNT(*) FROM referrals WHERE referrer_id=?");

$total_ref->execute([$user_id]);

$total_ref = $total_ref->fetchColumn();

$active_ref = $pdo->prepare("

    SELECT COUNT(*) FROM referrals r

    JOIN mining m ON r.referred_id=m.user_id

    WHERE r.referrer_id=? AND m.last_mined > NOW() - INTERVAL 1 DAY

");

$active_ref->execute([$user_id]);

$active_ref = $active_ref->fetchColumn();

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>WORLD MINING</title>

<meta name="viewport" content="width=device-width, initial-scale=1">

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

    background:#050b12;

    font-family:Arial,sans-serif;

    color:#fff;

}

.header{

    text-align:center;

    padding:15px;

    font-size:22px;

    color:#00ff88;

    font-weight:bold;

}

.card{

    background:#0b1a24;

    margin:15px;

    padding:20px;

    border-radius:15px;

}

.balance{

    font-size:30px;

    color:#00ff88;

    font-weight:bold;

}

.btn{

    width:100%;

    padding:15px;

    border:none;

    border-radius:12px;

    font-size:18px;

    margin-top:15px;

}

.start{

    background:#00ff88;

    color:#000;

}

.active{

    background:#00b36b;

    color:#000;

}

.stats{

    display:flex;

    justify-content:space-between;

    margin-top:15px;

    font-size:14px;

}

.nav{

    position:fixed;

    bottom:0;

    width:100%;

    background:#06131d;

    display:flex;

    justify-content:space-around;

    padding:10px 0;

}

.nav a{

    color:#aaa;

    text-decoration:none;

    font-size:12px;

}

.nav a.active{

    color:#00ff88;

}

#timer{

    margin-top:10px;

    text-align:center;

    color:#00ff88;

    font-weight:bold;

}

</style>

</head>

<body>

<div class="header">WORLD 🌍 MINING</div>

<div class="card">

    <div>Available Balance</div>

    <div class="balance"><?= number_format($balance,4) ?> W COIN</div>

</div>

<div class="card">

    <div>Daily Mining</div>

    <div>Earn 0.1 W / 24h</div>

    <?php if($mining_active): ?>

        <button class="btn active">MINING ACTIVE</button>

        <div id="timer"></div>

    <?php else: ?>

        <form method="post">

            <button name="start_mining" class="btn start">START MINING</button>

        </form>

    <?php endif; ?>

</div>

<div class="card">

    <div class="stats">

        <div>⚡ Hash Rate<br><?= number_format($hash_rate,4) ?></div>

        <div>👥 Referrals<br><?= $total_ref ?></div>

        <div>🟢 Active<br><?= $active_ref ?></div>

    </div>

</div>

<div class="bottom-nav">

    <a href="dashboard.php">

        🏠

        <span>Home</span>

    </a>

    <a href="wallet.php" class="active">

        💼

        <span>Wallet</span>

    </a>

    <a href="referral.php">

        👥

        <span>Referral</span>

    </a>

    <a href="upgrade.php">

        🚀

        <span>Upgrade</span>

    </a>

    <a href="profile.php">

        👤

        <span>Profile</span>

    </a>

</div>







<?php if($mining_active): ?>

<script>

let remaining = <?= $remaining ?>;

function timer(){

    if(remaining<=0){ location.reload(); return; }

    let h=Math.floor(remaining/3600);

    let m=Math.floor((remaining%3600)/60);

    let s=remaining%60;

    document.getElementById("timer").innerHTML =

        "Next mining in: "+

        String(h).padStart(2,'0')+":"+

        String(m).padStart(2,'0')+":"+

        String(s).padStart(2,'0');

    remaining--;

}

setInterval(timer,1000);

timer();

</script>

<?php endif; ?>

</body>

</html>