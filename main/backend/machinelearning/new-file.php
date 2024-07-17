<?php
$output = shell_exec('python3 linear-regression-patient.py 2>&1');
echo "<pre>$output</pre>";
?>