<?php

session_start();

include('includes/head.php');
require_once('../src/Common/Database.php');
require_once('../src/Common/DatabaseFunctions.php');

if (isset($_SESSION['file-name'])) {
    unset($_SESSION['file-name']);
}

?>

<body>
    <div id="container">
        <?php include('includes/nav.php'); ?>

        <div id="content">
            <h2 id="directory"><a href="index.php">root</a> / Scan Files</h2>

            <div id="centered">
                <h3>Detects security risks and provides solutions.</h3>
                <form id="upload-form" action="../config/upload_file.php" method="post" enctype="multipart/form-data">
                    <input type="file" name="uploaded-file" id="file">
                    <button type="submit" name="submit">Upload</button>
                </form>

                <?php
                    $db = new Database();

                    $sortMethod = isset($_SESSION['sort']) ? $_SESSION['sort'] : 'name';
                    $sortType = isset($_SESSION['sortType']) ? $_SESSION['sortType'] : 'ASC';
                    $files = getUploadedFiles($db, $sortMethod, $sortType);

                    $sortSymbol = $_SESSION['sortType'] === 'ASC' ? '↑' : '↓';

                    echo "<ul id='uploaded-files'>";
                    echo "<h3>Uploaded files</h3>";
                    echo "
                        <form id='sort-method' action='../config/set_sorting_method.php' method='post'>
                            <button type='submit' name='sort-button' value='name'>Name</button>
                            <button type='submit' name='sort-button' value='file_type'>File type</button>
                            <button type='submit' name='sort-button' value='file_size'>Size</button>
                        </form>
                    ";
                    foreach ($files as $file) {
                        $size = ceil($file['file_size'] / 1024);
                        
                        echo "<form class='file' action='../config/remove_file.php' method='post'>";
                        echo "<li><span class='file-name'>{$file['name']}</span><span class='file-type'>{$file['file_type']}</span><span class='file-size'>{$size} kB</span><button class='remove-file-button' type='submit' name='delete-file' value='{$file['id']}'>Remove</button></li>";
                        echo "</form>";
                    }
                    echo "</ul>";
                ?>
            </div> <!-- /centered -->

            <div id="selected-file">
                <h3></h3>
                <div id="file-content"></div>
                <form action="../config/scan_file.php" method="post">
                    <button type="submit" name="scan-file" id="scan-button">Scan File</button>
                </form>
            </div> <!-- /selected-file -->
            
        </div> <!-- /content -->
    </div> <!-- /container -->
</body>
<script src="assets/js/displayFileContents.js"></script>
<!-- Initiates highlight.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', (event) => { hljs.highlightAll(); });</script>
</html>