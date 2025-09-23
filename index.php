<?php
require 'ClassAutoLoad.php';
//makes use of the Layouts class to render different sections of the webpage
$ObjLayout->header($conf);
$ObjLayout->navbar($conf);
$ObjLayout->banner($conf);
$ObjLayout->content($conf);
$ObjLayout->footer($conf);
?>