#!/usr/bin/env php
<?php
/*
 * fixlabel.php
 * 20170803
 *  scan a directory for *MODS.xml files
 *  find a file that has a non-page# title,
 *  save its title string,
 *  look for its admindb identifier and save it,
 *  loop through all other files looking for that admindb in title,
 *    if found, replace admindb string in title with saved title,
 *    move file to finished dir.
 *  
 *  continue loop
 * 20170803
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
//------------- begin main-----------------

$rdir=$xmlname=$dir=$title=$ident='';
//get parameters from command line
if (isset($argv[1])) $rdir=$argv[1];
else {
  print "\n";
  print "usage: fixlabel directoryname\n";
  print "Error **  missing parameters ** \n";
  exit();
}
// ---------------
if(!isDir($rdir)) {
  print "Error **  target directory does not exist*** \n";
  print "      **  or permissions are wrong       *** \n";
  exit();
}
print "*---------------------------\n";
print "* fixlabel is now scanning files in $rdir\n";
print "*---------------------------\n";
// change to dir and read filenames
$cwd = getcwd();
chdir($rdir);
$titles = array();
$dirfiles = scandir(".");
foreach ($dirfiles as $d1) {
  // eliminate the dot directories
  if (($d1 == '.') || ($d1 == '..')) {
    continue;
  }
  $end = substr($d1, -4);
  if ($end == '.xml') {
   // get basename
   $xbase = basename($d1, $end);
   $xmlname = $xbase . '.xml';
   $meta = simplexml_load_file($xmlname);
   $ns = $meta->getNamespaces(true);
   //var_dump($ns);
   if (isset($ns['mods'])) {
     continue;
   }else{
    echo "======== filename = $xmlname \n";
    echo "------book------ \n";
    $id = (string)$meta->identifier;
    $curtitle = (string)$meta->titleInfo->title;
    echo " $id  $curtitle \n";
    $titles["$id"] = $curtitle;
    } //end else
   }//endif xml
}//end foreach
    //var_dump($titles);
// loop thru pages
foreach ($dirfiles as $d1) {
  // eliminate the dot directories
  if (($d1 == '.') || ($d1 == '..')) {
    continue;
  }
  $end = substr($d1, -4);
  if ($end == '.xml') {
   // get basename
   $xbase = basename($d1, $end);
   $xmlname = $xbase . '.xml';
   $meta = simplexml_load_file($xmlname);
   $ns = $meta->getNamespaces(true);
   //var_dump($ns);
   if (isset($ns['mods'])) {
     echo "======== filename = $xmlname \n";
     print "--page --- \n";
     $mods = $meta->children($ns['mods']);
     $pagetitle = (string)$mods->titleInfo->title;
     echo "old $pagetitle \n";
     foreach ($titles as $i => $t) {
       if (strstr($pagetitle,$i)) {
         $new=str_replace($i,$t,$pagetitle);
         $new=htmlentities($new, ENT_HTML401, 'UTF-8');
         $new=str_replace('&amp;','&',$new);
         $mods->titleInfo->title=$new;
         echo "new $new \n";
       }// end if
     }//end foreach
     //echo $meta->asXML();
     $meta->asXML($xmlname.'.done'); 
    }// end if
   }//endif xml 
}//end foreach

//chdir($cwd);

print "\n*---------------------\n";
print "* fixlabel has finished.\n";
print "*---------------------\n\n";
//unset($dirfiles);
?>
