<?php
/*
 * m3uh, a m3u music playlist generator
 * Author: Sunny Ripert -- http://sunfox.org
 * Licence: GPL -- http://www.gnu.org/copyleft/gpl.html
 */


/* Options */
// Path to the music folder
define('PATH', '../music/');

// You may add a prefix to add to link to your music files
// may be a hard path or a uri, for example:
# define('PREFIX', PATH);
# define('PREFIX', '/home/sunny/music/');
define('PREFIX', 'http://'.$_SERVER['HTTP_HOST'].'/music/');


/* Configuration */
define('EXTENSIONS_RE', '/\.(mp3|ogg|flac)$/');
define('IS_URI', preg_match('/:\/\//', PREFIX));


/* Functions */
// Returns a list of all files in a directory recursively
function files($dir, &$files = array()) {
  $handle = opendir($dir);
  while ($file = readdir($handle)) {
    if ($file == '.' or $file == '..')
      continue;

    $path = $dir.'/'.$file;
    if (is_dir($path))
      files($path, $files);
    else
      $files[] = $path;
  }
  closedir($handle);
  return $files;
}


/* Go! */
$i = 0;
$files = files(PATH);
natcasesort($files);

header('Content-type: audio/x-mpegurl');
echo "#EXTM3U\n";
foreach ($files as $file) {
  if (!preg_match(EXTENSIONS_RE, $file))
    continue;

  ++$i;

  $name = array_pop(explode('/', $file)); // the name is the last part of the filename
	$name = preg_replace(EXTENSIONS_RE, '', $name);

  $uri = substr($file, strlen(PATH) + 1); // take off the top path
  $uri = IS_URI ? PREFIX . str_replace('%2F', '/', rawurlencode($uri)) : PREFIX . $uri;

  echo "#EXTINF:$i,$name\n$uri\n";
}
 

