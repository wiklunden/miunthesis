<?php

session_start();

include('includes/head.php');
include('../config/unset_session_variables.php');

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