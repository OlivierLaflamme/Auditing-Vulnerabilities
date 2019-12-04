<?php
    if (isset($_POST['url'])) 
    { 
        $content = file_get_contents($_POST['url']); 
        $filename = './images/'.rand().'img1.jpg'; 
        file_put_contents($filename, $content); 
        echo $_POST['url'].""; 
        $img = "<img src=\"".$filename."\"/>"; 
    } 
    echo $img; 
?>