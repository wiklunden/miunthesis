<?php

session_start();

include('includes/head.php');

if (isset($_SESSION['file-name'])) {
    unset($_SESSION['file-name']);
    unset($_SESSION['stmt-results']);
    unset($_SESSION['sqli-results']);
}

?>
<body>
    <div id="container">
        <?php include('includes/nav.php'); ?>

        <div id="content">
            <h2 id="directory"><a href="index.php">Home</a> / About</h2>
        </div>
    </div>
</body>
</html>