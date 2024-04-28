<?php
    include('includes/head.php');
?>
<body>
    <div id="container">
        <?php include('includes/nav.php'); ?>

        <div id="content">
            <h2 id="directory"><a href="index.php">root</a> / Scan Files</h2>
            <h3>Detects security risks and provides solutions.</h3>

            <form action="../config/upload_file.php" method="post" enctype="multipart/form-data">
                <input type="file" name="uploaded-file" id="file">
                <button type="submit" name="submit">Upload</button>
            </form>
        </div>
    </div>
</body>
</html>