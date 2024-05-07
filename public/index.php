<?php

session_start();

include('includes/head.php');
include('../config/unset_session_variables.php');

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

        <img id="fissc-big" src="assets/images/fissc-big.png" alt="FISSC image">
    </div>
</body>
</html>