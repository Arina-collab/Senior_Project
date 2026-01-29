<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student Dashboard | AUBG Portal</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; display: flex; margin: 0; background: #f4f7f6; }
        .sidebar { width: 250px; background: #003366; color: white; height: 100vh; padding: 20px; position: fixed; }
        .sidebar a { color: white; text-decoration: none; display: block; padding: 10px 0; }
        .main-content { margin-left: 290px; padding: 40px; width: calc(100% - 330px); }
        .welcome-header { background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;}
        .card { background: white; padding: 20px; margin-bottom: 15px; border-radius: 8px; border-left: 5px solid #e03131; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .scroll-box { height: 150px; overflow-y: auto; border: 1px solid #ccc; padding: 10px; border-radius: 4px; background: #fff; margin-bottom: 15px;}
        .item { display: block; padding: 5px 0; font-size: 14px; }
        input[type="text"], input[type="number"], textarea { width: 100%; padding: 10px; margin-bottom: 10px; border: 1px solid #ccc; border-radius: 4px; }
        button[name="complete_profile_btn"] { background: #003366; color: white; border: none; padding: 12px 20px; border-radius: 4px; cursor: pointer; width: 100%; }
        .msg { color: #e03131; font-weight: bold; background: #ffeded; padding: 10px; border-radius: 4px; }
        /* Chrome, Safari, Edge, Opera */
        input::-webkit-outer-spin-button,
        input::-webkit-inner-spin-button {-webkit-appearance: none;margin: 0;}
        /* Firefox */
        input[type=number] {-moz-appearance: textfield;}
    </style>
</head>
<body>

<div class="sidebar">
    <h2>AUBG Portal</h2>
    <p>Welcome, <strong><?php echo htmlspecialchars($student_profile['first_name'] ?? $_SESSION['email']); ?></strong></p>
    <nav>
        <a href="#">🏠 Account</a>
        <a href="#">🔔 Notifications</a>
        <a href="#">📢 Opportunities</a>
        <a href="#">📅 Events</a>
        <a href="#">💼 Applications</a>
        <a href="#">📄 Registrations</a>
        <a href="#">✅ Poster Approval</a>
        <hr>
        <form action="Register.php" method="POST">
            <button type="submit" name="logout_btn" style="background:none; color:white; border:none; cursor:pointer; font-size:16px;">Log Out</button>
        </form>
    </nav>
</div>

<div class="main-content">
    <?php if (!$student_profile): ?>
        <div class="welcome-header">
            <h1>Complete Your Profile</h1>
            <?php if(isset($_SESSION['msg'])): ?>
                <p class="msg"><?php echo $_SESSION['msg']; unset($_SESSION['msg']); ?></p>
            <?php endif; ?>
        </div>

        <form method="POST" style="max-width: 600px; background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1);">
            <label>Full Name</label>
            <div style="display:flex; gap:10px;">
                <input type="text" name="first_name" placeholder="First Name" value="<?php echo htmlspecialchars($first); ?>" required>
                <input type="text" name="second_name" placeholder="Last Name" value="<?php echo htmlspecialchars($last); ?>" required>
            </div>

            <label>Graduation Year & Phone</label>
            <input type="text" inputmode="numeric" name="graduation_year" placeholder="Graduation Year" value="<?php echo htmlspecialchars($grad); ?>" required>
            <input type="text" name="phone" placeholder="Phone Number" value="<?php echo htmlspecialchars($phone); ?>" required>

            <label>Select Majors (Max 3)</label>
            <input type="text" id="majorInput" onkeyup="filterList('majorInput', 'majorList')" placeholder="Search majors...">
            <div id="majorList" class="scroll-box">
                <?php foreach($major_list as $m): ?>
                    <label class="item">
                        <input type="checkbox" name="majors[]" value="<?php echo $m; ?>" <?php echo in_array($m, $current_majors) ? "checked" : ""; ?>> 
                        <?php echo htmlspecialchars($m); ?>
                    </label>
                <?php endforeach; ?>
            </div>

            <label>Select Clubs</label>
            <input type="text" id="clubInput" onkeyup="filterList('clubInput', 'clubList')" placeholder="Search clubs...">
            <div id="clubList" class="scroll-box">
                <?php foreach($club_list as $c): ?>
                    <label class="item">
                        <input type="checkbox" name="clubs[]" value="<?php echo $c; ?>" <?php echo in_array($c, $current_clubs) ? "checked" : ""; ?>> 
                        <?php echo htmlspecialchars($c); ?>
                    </label>
                <?php endforeach; ?>
            </div>
            <input type="text" name="custom_club" placeholder="Other Club (Optional)" value="<?php echo htmlspecialchars($custom_club); ?>">

            <label>Bio</label>
            <textarea name="bio" id="bio" rows="4" maxlength="1500"><?php echo htmlspecialchars($bio); ?></textarea>
            <p id="charNum" style="font-size: 12px; color: #666;">0/1500 characters</p>

            <button type="submit" name="complete_profile_btn">Save Profile & Continue</button>
        </form>

    <?php else: ?>
        <div class="welcome-header">
            <h1>Global Paths, AUBG Roots</h1>
            <p>Welcome back, <strong><?php echo htmlspecialchars($student_profile['first_name']); ?></strong>!</p>
            <p>Major(s): <?php echo htmlspecialchars($student_profile['major']); ?></p>
        </div>

        <div class="opportunity-list">
            <h2>Recommended for You</h2>
            <?php if(empty($postings)): ?>
                <p>No opportunities available right now.</p>
            <?php else: ?>
                <?php foreach ($postings as $post): ?>
                    <div class="card">
                        <h3><?php echo htmlspecialchars($post['title']); ?></h3>
                        <span class="badge"><?php echo $post['type']; ?></span>
                        <p><?php echo substr(htmlspecialchars($post['description']), 0, 150); ?>...</p>
                        <button style="background:#003366; color:white; border:none; padding:8px 15px; border-radius:4px; cursor:pointer;">View Details</button>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    <?php endif; ?>
</div>

<script>
function countChars(obj){
    document.getElementById("charNum").innerHTML = obj.value.length + "/1500 characters";
}

function filterList(inputId, listId) {
    var input = document.getElementById(inputId);
    var filter = input.value.toUpperCase();
    var container = document.getElementById(listId);
    var labels = container.getElementsByClassName("item");

    for (var i = 0; i < labels.length; i++) {
        var textValue = labels[i].textContent || labels[i].innerText;
        labels[i].style.display = (textValue.toUpperCase().indexOf(filter) > -1) ? "" : "none";
    }
}
</script>
</body>
</html>