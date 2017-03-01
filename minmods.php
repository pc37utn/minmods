#!/usr/bin/php
<?php
/*
 * minmods.php
 * 20170301
 * use a list of directory names to make minimum mods for ingestion
 * should have a directoryname.mods which has two fields
 * - an identifer with filename in it and a title with filename in it
 * - this will enable a bookprep run to prepare for ingestion
 * 20170301
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

$rdir=$xmlname=$dir=$booktitle=$ident='';
//get parameters from command line
if (isset($argv[1])) $rdir=$argv[1];
else {
  print "\n";
  print "usage: minmods directoryname\n";
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
print "* minmods is now making mods\n";
print "*---------------------------\n";
// change to dir and read filenames
$cwd = getcwd();
chdir($rdir);
$dirfiles = scandir(".");
foreach ($dirfiles as $d1) {
  // eliminate the dot directories
  if (($d1=='.')||($d1=='..')) continue;
  if (is_dir($d1)) {
    // trim the dir name
    $d1=trim($d1);
    $xmlname=$d1.'.xml';
    if (!file_exists($xmlname)) { // create mods file
      // get booktitle and identifier specific to this book
      $booktitle=$d1;
      $ident=$xmlname;      
      // encode entities
      $booktitle=htmlentities($booktitle,ENT_QUOTES,'UTF-8');
      $ident=htmlentities($ident,ENT_QUOTES,'UTF-8');
      // make mods.xml
      $pagexml=<<<EOL
<?xml version="1.0" encoding="UTF-8"?>
<mods xmlns="http://www.loc.gov/mods/v3" xmlns:xlink="http://www.w3.org/1999/xlink" xmlns:xs="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.loc.gov/mods/v3 http://www.loc.gov/standards/mods/v3/mods-3-5.xsd" version="3.5">
  <identifier type="local">$ident</identifier>
  <mods:titleInfo>
    <mods:title>$booktitle</mods:title>
  </mods:titleInfo>
</mods:mods>
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
  } //endif is dir
} //end foreach
chdir($cwd);

print "*---------------------\n";
print "* minmods has finished.\n";
print "*---------------------\n";
unset($dirfiles);
?>
