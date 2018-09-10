<?php
header("content-type:image/jpeg");

echo file_get_contents(isset($_GET['url']) ? $_GET['url'] : './images/default.jpg');