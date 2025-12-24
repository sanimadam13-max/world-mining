<?php

ob_start();

session_start();

require "config.php";

$msg = "";

/* ===============================

   REFERRAL FROM FORM OR URL

================================ */

$referred_by = "";

if (!empty($_POST['referral'])) {

    $referred_by = trim($_POST['referral']);

} elseif (!empty($_GET['ref'])) {

    $referred_by = trim($_GET['ref']);

}

if (isset($_POST['signup'])) {

    try {

        $full_name = trim($_POST['full_name']);

        $email     = trim($_POST['email']);

        $username  = trim($_POST['username']);

        $country   = trim($_POST['country']);

        $password  = $_POST['password'];

        $confirm   = $_POST['confirm'];

        if ($password !== $confirm) {

            $msg = "Passwords do not match";

        } else {

            /* CHECK DUPLICATE EMAIL / USERNAME */

            $chk = $pdo->prepare("SELECT id FROM users WHERE email=? OR username=?");

            $chk->execute([$email, $username]);

            if ($chk->rowCount() > 0) {

                $msg = "Email or Username already exists";

            } else {

                $hash   = password_hash($password, PASSWORD_DEFAULT);

                $my_ref = "WM-" . strtoupper(substr(md5(uniqid()), 0, 6));

                $pdo->beginTransaction();

                /* INSERT USER */

                $stmt = $pdo->prepare("

                    INSERT INTO users

                    (full_name, username, email, country, password, referral_code, referred_by, role)

                    VALUES (?,?,?,?,?,?,?, 'user')

                ");

                $stmt->execute([

                    $full_name,

                    $username,

                    $email,

                    $country,

                    $hash,

                    $my_ref,

                    $referred_by

                ]);

                $new_user_id = $pdo->lastInsertId();

                /* CREATE WALLET */

                $pdo->prepare("

                    INSERT INTO wallets (user_id, balance)

                    VALUES (?, 0)

                ")->execute([$new_user_id]);

                /* SAVE REFERRAL RELATION */

                if ($referred_by != "") {

                    $q = $pdo->prepare("SELECT id FROM users WHERE referral_code=?");

                    $q->execute([$referred_by]);

                    if ($ref = $q->fetch()) {

                        $pdo->prepare("

                            INSERT INTO referrals (referrer_id, referred_id)

                            VALUES (?,?)

                        ")->execute([$ref['id'], $new_user_id]);

                    }

                }

                $pdo->commit();

                header("Location: login.php");

                exit;

            }

        }

    } catch (Exception $e) {

        $pdo->rollBack();

        $msg = "Signup failed. Please try again.";

    }

}

?>

<!DOCTYPE html>

<html>

<head>

<meta charset="UTF-8">

<title>Create Account | W Mining</title>

<meta name="viewport" content="width=device-width, initial-scale=1.0">

<style>

*{box-sizing:border-box;font-family:Arial,Helvetica,sans-serif}

html,body{height:100%;margin:0}

body{background:radial-gradient(circle at top,#0b1a22,#02070a);color:#fff}

.screen{min-height:100vh;display:flex;justify-content:center;padding:30px 18px}

.card{width:100%;max-width:420px;background:#0c1820;border-radius:22px;padding:28px 24px;box-shadow:0 0 35px rgba(0,255,153,.15)}

.logo{text-align:center;margin-bottom:20px}

.logo h1{margin:0;color:#00ff99}

h2{text-align:center;margin:0}

.sub{text-align:center;font-size:13px;color:#aaa;margin-bottom:22px}

.input,.select{width:100%;padding:15px;margin-bottom:14px;border-radius:14px;border:none;outline:none;background:#101f28;color:#fff;font-size:15px}

.pass-box{position:relative}

.eye{position:absolute;right:15px;top:15px;cursor:pointer;font-size:18px;color:#00ff99}

.btn{width:100%;padding:16px;margin-top:10px;background:linear-gradient(90deg,#00ff99,#00cc77);border:none;border-radius:16px;font-weight:bold;font-size:16px;cursor:pointer}

.error{background:#ff3b3b;padding:10px;border-radius:12px;margin-bottom:15px;text-align:center}

.footer{text-align:center;margin-top:16px;font-size:14px}

.footer a{color:#00ff99;text-decoration:none}

</style>

</head>

<body>

<div class="screen">

<div class="card">

<div class="logo"><h1>🌍 W MINING</h1></div>

<h2>Create Account</h2>

<div class="sub">Register to start mining</div>

<?php if($msg!=""){ echo "<div class='error'>$msg</div>"; } ?>

<form method="post">

<input class="input" name="full_name" placeholder="Full Name" required>

<input class="input" type="email" name="email" placeholder="Gmail" required>

<input class="input" name="username" placeholder="Username" required>

<select class="select" name="country" required>

<option value="">Select Country</option>

<option>Nigeria</option><option>Ghana</option><option>Kenya</option>

<option>South Africa</option><option>Egypt</option><option>Morocco</option>

<option>USA</option><option>UK</option><option>Canada</option>

<option>Germany</option><option>France</option><option>Italy</option>

<option>Spain</option><option>Turkey</option><option>UAE</option>

<option>Saudi Arabia</option><option>India</option><option>Pakistan</option>

<option>Bangladesh</option><option>Indonesia</option><option>Malaysia</option>

<option>Philippines</option><option>Brazil</option><option>Argentina</option>

<option>Mexico</option><option>Chile</option><option>Colombia</option>

<option>Japan</option><option>China</option><option>South Korea</option>

<option>Thailand</option><option>Vietnam</option><option>Nepal</option>

<option>Sri Lanka</option><option>Australia</option><option>New Zealand</option>

<option>Russia</option><option>Ukraine</option><option>Poland</option>

<option>Netherlands</option><option>Belgium</option><option>Sweden</option>

<option>Norway</option><option>Denmark</option><option>Finland</option>

</select>

<input class="input" name="referral" placeholder="Referral Code (optional)">

<div class="pass-box">

<input class="input" type="password" id="p1" name="password" placeholder="Password" required>

<span class="eye" onclick="toggle('p1')">👁</span>

</div>

<div class="pass-box">

<input class="input" type="password" id="p2" name="confirm" placeholder="Confirm Password" required>

<span class="eye" onclick="toggle('p2')">👁</span>

</div>

<button class="btn" name="signup">CREATE ACCOUNT</button>

</form>

<div class="footer">

Already have an account? <a href="login.php">Login</a>

</div>

</div>

</div>

<script>

function toggle(id){

    let x=document.getElementById(id);

    x.type = (x.type==="password") ? "text" : "password";

}

</script>

</body>

</html>

<?php ob_end_flush(); ?>