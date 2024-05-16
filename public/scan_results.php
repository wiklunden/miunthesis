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
            <h2 id="directory"><a href="index.php">Home</a> / <a href="scan.php">Scan Files</a> / Results</h2>

            <div id="centered">
                <h3>Results from scanning <span style="color: var(--code-purple);"><?= $_SESSION['file-name']; ?></span>:</h3>
                <?php
					echo '<div id="results">';

					// Prints results from SQL injection scan
					if (isset($_SESSION['sqli-results']) && !empty($_SESSION['sqli-results'])) {
						$sqliResults = $_SESSION['sqli-results'];

						echo '<span class="scan-section">';
						echo '<h4>SQL injection:</h4>';
						foreach ($sqliResults as $result) {
							echo $result;
						}
						echo '</span>';
					}
					
					// Prints results from prepared statements scan
					if (isset($_SESSION['stmt-results']) && !empty($_SESSION['stmt-results'])) {
						$stmtResults = $_SESSION['stmt-results'];
					
						echo '<span class="scan-section">';
						echo '<h4>Prepared statements:</h4>';
						foreach ($stmtResults as $result) {
							echo $result;
						}
						echo '</span>';
					}

					// Prints code complexity
					echo empty($_SESSION['complexity']);
					if (isset($_SESSION['complexity']) && !empty($_SESSION['complexity'])) {
						$complexityResults = $_SESSION['complexity'];
						
						echo '<span class="scan-section">';
						echo '<h4>Cyclomatic complexity:</h4>';
						foreach ($complexityResults as $result) {
							echo $result;
						}
						echo '</span>';
					}

					echo '</div>';
				?>
            </div> <!-- /centered -->
        </div> <!-- /content -->
    </div> <!-- /container -->
</body>
</html>