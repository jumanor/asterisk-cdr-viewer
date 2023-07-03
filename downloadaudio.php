<?php 
    
    header('Cache-Control: no-store, no-cache, must-revalidate, post-check=0, pre-check=0');
    header('Pragma: no-cache');

    $ruta=$_REQUEST['audio'];
    $mime_type=mime_content_type($ruta);
    $handle = fopen($ruta, "rb");

    header('Content-Description: File Transfer');
    header("Content-Type: ".$mime_type);
	header('Content-Transfer-Encoding: binary');
	header('Content-Length: '.filesize($ruta));
	header("Content-Disposition: attachment; filename=mio.wav");
	
    while (!feof($handle)) {
        echo fread($handle, 4096);
        flush();
    }
    fclose($handle);
?>