<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Examples</title>
</head>
<body>
<ul>
<?php
    foreach(array_diff(scandir('examples'),array('.','..')) as $file) {
        echo("<li><a href=\"examples/$file\">$file</a></li>");
    }
?>
</ul>
</body>
</html>