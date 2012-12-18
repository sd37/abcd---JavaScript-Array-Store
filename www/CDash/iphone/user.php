<?php
// Put the CDash root directory in the path
$splitchar = '/';
if(DIRECTORY_SEPARATOR == '\\')
  {
  $splitchar='\\\\';
  }
$path = join(array_slice(split( $splitchar ,dirname(__FILE__)),0,-1),DIRECTORY_SEPARATOR);
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

$NoXSLGenerate = 1;
include("../user.php");

if(empty($xml))
  {
  $xml = "<cdash>";
  $xml .= add_XML_value("cssfile",$CDASH_CSS_FILE);
  $xml .= add_XML_value("version",$CDASH_VERSION);
  $xml .= add_XML_value("showlogin","1");
  $xml .= "</cdash>";  
  }

// Now doing the xslt transition
generate_XSLT($xml,"user");
?>