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
            <h2 id="directory">Home</h2>
        </div>

        <div id="centered">
            <form action="scan.php" method="post">
                <button type="submit" id="scan-files-button">Scan files</button>
            </form>
        </div>
    </div>
</body>
</html>