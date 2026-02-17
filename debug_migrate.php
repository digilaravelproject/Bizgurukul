<?php
$output = shell_exec('php artisan migrate --force 2>&1');
echo "<pre>$output</pre>";
file_put_contents('migrate_result_new.txt', $output);
