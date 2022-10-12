#!/usr/bin/env php
<?php
/*
 * minimgmods.php
 * 20170301
 * use a directory of tif/jp2 file names to make minimum mods for ingestion
 * result should have a (tif/jp2 imagefile name).xml which has two fields
 * - an identifer with filename in it and a title with filename in it
 * - this will prepare for a islandora_batch drush ingest
 * 20201012 - corrected textfor "tif/jp2"
 *
*/

//------functions-------------------
/*
* isDir  checks if a directory exists
* and changes into it and changes back to original
*/
function isDir($dir) {
  $cwd = getcwd();
  $returnValue = false;
  if (@chdir($dir)) {
    chdir($cwd);
    $returnValue = true;
  }
  return $returnValue;
}
/*
 * chkMaindir  checks if the main container directory exists
 * and adds an error if it does not
 *
*/
function chkMaindir($rdir) {
  global $errorlist;
  $returnValue = false;
  if (isDir($rdir)) {
    $returnValue = true;
  }
  return $returnValue;
}
//------------- begin main-----------------

$rdir=$xmlname=$dir=$title=$ident='';
//get parameters from command line
if (isset($argv[1])) $rdir=$argv[1];
else {
  print "\n";
  print "usage: minimgmods directoryname\n";
  print "Error **  missing parameters*** \n";
  exit();
}
// ---------------
if(!chkMaindir($rdir)) {
  print "Error **  target directory does not exist*** \n";
  print "      **  or permissions are wrong       *** \n";
  exit();
}
print "*---------------------------\n";
print "* minimgmods is now making mods\n";
print "*---------------------------\n";
// change to dir and read filenames
$cwd = getcwd();
chdir($rdir);
$dirfiles = scandir(".");
foreach ($dirfiles as $d1) {
  // eliminate the dot directories
  if (($d1 == '.') || ($d1 == '..')) {
    continue;
  }
  if (!is_dir($d1)) {
    $end = substr($d1, -4);
    if (($end == '.tif') || ($end == '.jp2')) {
      // get basename
      $xbase = basename($d1, $end);
      $xmlname = $xbase . '.xml';
      if (!file_exists($xmlname)) { // create mods file
        // get title and identifier specific to this image
        /*
        * could also pull in a template file here to set known values
        */
        $title = $xbase;
        $ident = $xmlname;
        // encode entities
        $title = htmlentities($title, ENT_QUOTES, 'UTF-8');
        $ident = htmlentities($ident, ENT_QUOTES, 'UTF-8');
        // make mods.xml
        $pagexml = <<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<mods xmlns="http://www.loc.gov/mods/v3" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-5.xsd" version="3.5">
  <identifier type="local">$ident</identifier>
  <titleInfo>
    <title>$title</title>
  </titleInfo>
</mods>
EOL;
        // switch contexts to fix syntax highlighting
        ?>
        <?php
        print "Writing MODS:  $xmlname\n";
        file_put_contents($xmlname, $pagexml);
      } //endif file exists
      else {
        print "file: $xmlname  already exists,\n";
        print "will not overwrite, continuing with next file.";
      }
    }//endif .tif|.jp2
  }//endif is dir
}//end foreach
chdir($cwd);

print "*---------------------\n";
print "* minimgmods has finished.\n";
print "*---------------------\n";
unset($dirfiles);
?>
