<?php
if (isset($_GET['file'])) {
$file_id = $_GET['file'];
$file = 'http://localhost/bit-zentrum/shop/bit-zentrum-hrsg-befehlsliste-microsoft-outlook-2019-stand-september-2019-2-copy-copy/a08546_befehlsliste_microsoft_outlook_2019_256kbps/';
$mime_type = $_GET['mime_type'];
if ($mime_type == 'application-zip') {
 $header =  'Content-Type: application/zip';
}
if (file_exists($file) && is_readable($file)) {
	header($header);
	header("Content-Disposition: attachment; filename=\"$file\"");
	readfile($file);
	}
}
// if (file_exists($filename)) {
//   header('Content-Description: File Transfer');
//   header('Content-Type: application/octet-stream');
//   header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
//   header('Expires: 0');
//   header('Cache-Control: must-revalidate');
//   header('Pragma: public');
//   header('Content-Length: ' . filesize($filename));
//   readfile($filename);
//   exit;
// }
