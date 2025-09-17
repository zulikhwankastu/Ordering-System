<?php

// Set the page title and include the HTML header.
$page_title = 'Login';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    require_once('mysqli.php'); // Connect to the db.
    global $dbc;

    $errors = array();

    // Check for email and agent_id.
    if (empty($_POST['email'])) {
        $errors[] = 'You forgot to enter your email address.';
    } else {
        $e = ($_POST['email']);
    }

    if (empty($_POST['agent_id'])) {
        $errors[] = 'You forgot to enter your agent ID.';
    } else {
        $agent_id = ($_POST['agent_id']);
    }

    if (empty($errors)) {
        // Use prepared statements to prevent SQL injection
        $query = "SELECT agent_id, first_name, password FROM agent WHERE email=? AND agent_id=?";
        $stmt = $dbc->prepare($query);

        // Bind parameters
        $stmt->bind_param("ss", $e, $agent_id);

        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        if ($row) {
            // Agent exists, set the session data & redirect.
            session_start();
            $_SESSION['agent_id'] = $row['agent_id'];
            $_SESSION['first_name'] = $row['first_name'];

            // Redirect the user to the agent_homepage.php page.
            header("Location: agent_homepage.php");
            exit(); // Quit the script.
        } else {
            // Invalid email or agent_id
            $errors[] = 'The email address and agent ID entered do not match those on file.';
        }

        $stmt->close();
    }
}

$page_title = 'Login';

if (!empty($errors)) { // Print any error messages.
    echo '<h1 id="mainhead">Error!</h1>
    <p class="error">The following error(s) occurred:<br />';
    foreach ($errors as $msg) { // Print each error.
        echo " - $msg<br />\n";
    }
    echo '</p><p>Please try again.</p>';
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }

        #wrapper {
            margin: 0 auto;
            width: 80%;
            max-width: 400px;
            background: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
            margin-top: 50px;
        }

        #content {
            margin: 0 20px;
            padding: 20px;
        }

        #content p {
            margin: 0 0 16px 0;
            text-align: justify;
        }

        form {
            margin-top: 20px;
        }

        input[type="text"],
        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            width: 100%;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .error {
            color: #FF0033;
        }
        a.button {
            display: inline-block;
            padding: 15px 20px;
            text-decoration: none;
            background-color: #3498db;
            color: #fff;
            border: 1px solid #3498db;
            border-radius: 5px;
            margin-top: 10px;
            margin-left : 90px;
        }

        a.button:hover {
            background-color: #2573af;
            border-color: #2573af;
        }
    </style>
</head>

<body>

    <div id="wrapper">
        <div id="content">
            <h1>Login</h1>
            <?php
            if (!empty($errors)) {
                echo '<h2>Error!</h2>
                    <p class="error">The following error(s) occurred:<br />';
                foreach ($errors as $msg) {
                    echo " - $msg<br />\n";
                }
                echo '</p><p>Please try again.</p>';
            }
            ?>
            <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
                <label for="email">Email:</label>
                <input type="text" name="email" id="email" required>

                <label for="agent_id">Password:</label>
                <input type="password" name="agent_id" id="agent_id" required>

                <input type="submit" value="Login">
            </form>
            <br><br>
            <p>Click the button below if you are a supplier</p>
            <a href="login.php" class="button" title="Login as Supplier">I'm a supplier</a>
        </div>
        
    </div>

</body>

</html>



