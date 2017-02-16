<?php
header("Content-type: application/pdf");
header("Content-Disposition: inline; filename=filename.pdf");
@readfile('CV_SARNO_Thomas_english.pdf');
?>