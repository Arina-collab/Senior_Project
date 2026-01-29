<?php session_start(); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>AUBG Career Management Portal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; margin: 0; color: #333; line-height: 1.6; }
        header { background: #003366; color: white; padding: 60px 20px; text-align: center; }
        .container { max-width: 1100px; margin: auto; padding: 20px; }
        .features { display: flex; justify-content: space-around; padding: 50px 0; background: #f4f7f6; }
        .feature-card { flex: 1; margin: 0 15px; padding: 20px; background: white; border-radius: 8px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); text-align: center;}
        .feature-card ul { text-align: left; display: inline-block; padding-left: 20px;}
        .auth-section { display: flex; gap: 40px; padding: 50px 0; }
        .auth-box { flex: 1; padding: 30px; border: 1px solid #ddd; border-radius: 10px; }
        input, select, button { width: 100%; padding: 12px; margin: 10px 0; border-radius: 5px; box-sizing: border-box; }
        button { background: #e03131; color: white; border: none; cursor: pointer; font-weight: bold; }
        .msg { color: #e03131; font-weight: bold; text-align: center; }
    </style>
</head>
<body>

<header>
    <h1>Global Paths, AUBG Roots</h1>
    <p>Helping students navigate careers, events, and campus life.</p>
</header>

<div class="container">
    <section class="features">
        <div class="feature-card">
            <h3>For Students</h3>
            <ul><li>Filter opportunities</li><li>Apply for internships</li><li>Register for events</li></ul>
        </div>
        <div class="feature-card">
            <h3>For Career Services</h3>
            <ul><li>Post opportunities</li><li>Manage applications</li></ul>
        </div>
        <div class="feature-card">
            <h3>For the Dean</h3>
            <ul><li>Approve posters</li><li>Post events</li></ul>
        </div>
    </section>

    <?php if (isset($_SESSION['user_id'])): ?>
        <div class="msg" style="color:green;">Welcome, <?php echo $_SESSION['email']; ?> (<?php echo $_SESSION['role']; ?>)</div>
        <form action="Register.php" method="POST">
            <button type="submit" name="logout_btn">Log Out</button>
        </form>
    <?php else: ?>
        <section class="auth-section">
            <div class="auth-box">
                <h2>Join the Platform</h2>
                <?php if(isset($_SESSION['msg'])) { echo "<p class='msg'>".$_SESSION['msg']."</p>"; unset($_SESSION['msg']); } ?>
                <form action="Register.php" method="POST">
                    <input type="email" name="email" placeholder="AUBG Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <input type="password" name="retype_password" placeholder="Retype password" required>
                    <select name="role">
                        <option value="Student">Student</option>
                        <option value="CareerOffice">Career Office</option>
                        <option value="DeanOffice">Dean Office</option>
                    </select>
                    <button type="submit" name="signup_btn">Get Started</button>
                </form>
            </div>
            <div class="auth-box">
                <h2>Log In</h2>
                <form action="Register.php" method="POST">
                    <input type="email" name="email" placeholder="Email" required>
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="login_btn" style="background:#003366;">Log In</button>
                </form>
            </div>
        </section>
    <?php endif; ?>
</div>
</body>
</html>