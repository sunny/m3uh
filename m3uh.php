<?php
/*
M3uh, an m3u music playlist generator in PHP
Author: Sunny Ripert -- http://sunfox.org
Licence: GPL -- http://www.gnu.org/copyleft/gpl.html
*/
 
// options
$dir = '../music/.';
$dirloc = 'http://'.$_SERVER['HTTP_HOST'].'/music/';
 
// ugly recursive global array filler
$allfiles = array();
function recur_dir($dir) {
    global $allfiles;
    $dirlist = opendir($dir);
    while ($file = readdir ($dirlist)) {
        if ($file != '.' && $file != '..') {
            $newpath = $dir.'/'.$file;
            if (is_dir($newpath))
                recur_dir($newpath); // recurse!
            else 
                $allfiles[] = rawurlencode($newpath);
        }
 
    }
    closedir($dirlist);
}
 
 
// pile it up
recur_dir($dir);
natcasesort($allfiles);
 
// go!
header('Content-type: audio/x-mpegurl');
echo "#EXTM3U\n"; // start of m3u
for ($i = 0; $i < count($allfiles); $i++) {
	$file = eregi_replace('%2F', '/', $allfiles[$i]); // put back the slashes
	$file = substr($file, strlen($dir) + 1); // takes off awkward first dir and trailing slash
	$name = eregi_replace('.mp3', '', rawurldecode(array_pop(explode('/', $file)))); // the name is last part of url, without .mp3
 
	echo "#EXTINF:$i,$name\n$dirloc$file\n";
}
 
?>
 
