<?php

session_start();

include('includes/head.php');

if (!isset($_SESSION['file-name'])) {
	header('Location: scan.php');
	exit;
}

?>

<body>
    <div id="container">
        <?php include('includes/nav.php'); ?>

        <div id="content">
            <h2 id="directory"><a href="index.php">root</a> / <a href="scan.php">Scan Files</a> / Results</h2>

            <div id="centered">
                <h3>Results from scanning <span style="color: var(--highlight-light);"><?= $_SESSION['file-name']; ?></span>:</h3>
                <?php
					if (isset($_SESSION['stmt-results']) && !empty($_SESSION['stmt-results'])) {
						$stmtResults = $_SESSION['stmt-results'];
					
						echo '<div id="results">';
						echo '<span class="scan-section">';
						echo '<h4>Prepared statements:</h4>';
						foreach ($stmtResults as $result) {
							echo $result;
						}
						echo '<span>';
						echo '</div>';
					} else {
						header('Location: scan.php');
						exit;
					}
				?>
            </div> <!-- /centered -->
        </div> <!-- /content -->
    </div> <!-- /container -->
</body>
<!-- <script src="assets/js/fileScan.js"></script> -->
<!-- <script src="assets/js/fileScan2.js"></script> -->
<!-- Initiates highlight.js -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>
<script>document.addEventListener('DOMContentLoaded', (event) => { hljs.highlightAll(); });</script>
</html>