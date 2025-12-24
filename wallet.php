<?php

session_start();

require "config.php";

if (!isset($_SESSION['user_id'])) {

    header("Location: login.php");

    exit;

}

$user_id = $_SESSION['user_id'];

/* WALLET */

$w = $pdo->prepare("SELECT balance, internal_address FROM wallets WHERE user_id=?");

$w->execute([$user_id]);

$wallet = $w->fetch();

$balance = $wallet ? $wallet['balance'] : 0;

$address = $wallet ? $wallet['internal_address'] : "WM-$user_id";

/* SEND */

$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $to = trim($_POST['to']);

    $amount = floatval($_POST['amount']);

    if ($amount > 0 && $amount <= $balance) {

        $pdo->beginTransaction();

        // sender -

        $pdo->prepare("UPDATE wallets SET balance = balance - ? WHERE user_id=?")

            ->execute([$amount, $user_id]);

        // receiver +

        $pdo->prepare("UPDATE wallets SET balance = balance + ? WHERE internal_address=?")

            ->execute([$amount, $to]);

        // transactions

        $pdo->prepare("INSERT INTO transactions (user_id,type,amount,note) VALUES (?,?,?,?)")

            ->execute([$user_id,'send',$amount,"Send to $to"]);

        $pdo->prepare("INSERT INTO transactions (user_id,type,amount,note) 

            SELECT user_id,'receive',?,? FROM wallets WHERE internal_address=?")

            ->execute([$amount,"Receive from $address",$to]);

        $pdo->commit();

        $msg = "✅ Transaction Successful";

    } else {

        $msg = "❌ Invalid Amount";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Wallet - WORLD MINING</title>

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

    background:#070c12;

    color:#fff;

}

.header{

    text-align:center;

    padding:15px;

    font-size:20px;

    font-weight:bold;

    color:#00ff88;

}

.card{

    background:#0f1823;

    margin:15px;

    padding:20px;

    border-radius:15px;

}

.balance{

    font-size:32px;

    color:#00ff88;

    font-weight:bold;

}

.label{opacity:.7;margin-top:10px;}

input{

    width:100%;

    padding:12px;

    margin-top:10px;

    border:none;

    border-radius:10px;

    background:#08121b;

    color:#fff;

}

.btn{

    width:100%;

    padding:15px;

    margin-top:15px;

    border:none;

    border-radius:12px;

    background:linear-gradient(90deg,#00ff88,#00c46a);

    font-size:16px;

    font-weight:bold;

}

.copy{

    background:#0a1f14;

    color:#00ff88;

    padding:10px;

    border-radius:10px;

    margin-top:10px;

    text-align:center;

}

.msg{text-align:center;margin:10px;}

.nav{

    position:fixed;

    bottom:0;

    left:0;

    right:0;

    background:#050a10;

    display:flex;

}

.nav a{

    flex:1;

    text-align:center;

    padding:10px;

    color:#aaa;

    text-decoration:none;

    font-size:12px;

}

.nav .active{color:#00ff88;}

</style>

<script>

function copyAddr(){

    navigator.clipboard.writeText("<?php echo $address;?>");

    alert("Wallet address copied");

}

</script>

</head>

<body>

<div class="header">WORLD 🌍 MINING</div>

<div class="card">

    <div class="label">Available Balance</div>

    <div class="balance"><?php echo number_format($balance,4); ?> W COIN</div>

</div>

<div class="card">

    <div class="label">Your Wallet Address</div>

    <div class="copy" onclick="copyAddr()"><?php echo $address; ?> (Tap to copy)</div>

</div>

<div class="card">

    <div class="label">Send W COIN</div>

    <form method="post">

        <input type="text" name="to" placeholder="Receiver Address" required>

        <input type="number" step="0.0001" name="amount" placeholder="Amount" required>

        <button class="btn">SEND</button>

    </form>

</div>

<?php if($msg): ?><div class="msg"><?php echo $msg;?></div><?php endif;?>

<div class="bottom-nav">

    <a href="dashboard.php" class="nav-item">

        🏠<span>Home</span>

    </a>

    <a href="wallet.php" class="nav-item">

        💼<span>Wallet</span>

    </a>

    <a href="referral.php" class="nav-item active">

        👥<span>Referral</span>

    </a>

    <a href="upgrade.php" class="nav-item">

        🚀<span>Upgrade</span>

    </a>

    <a href="profile.php" class="nav-item">

        👤<span>Profile</span>

    </a>

</div>







</body>

</html>