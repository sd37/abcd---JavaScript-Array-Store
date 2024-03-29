<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: import.php 2046 2009-12-01 15:59:22Z david.cole $
  Language:  PHP
  Date:      $Date: 2009-12-01 15:59:22 +0000 (Tue, 01 Dec 2009) $
  Version:   $Revision: 2046 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
include("cdash/config.php");
require_once("cdash/pdo.php");
include('login.php');
include("cdash/version.php");

if($session_OK) 
  {
  include_once('cdash/common.php');
  include_once("cdash/ctestparser.php");

set_time_limit(0);

$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);

checkUserPolicy(@$_SESSION['cdash']['loginid'],0); // only admin

  
//get date info here
@$dayFrom = $_POST["dayFrom"];
if(!isset($dayFrom))
{
  $dayFrom = date('d', strtotime("yesterday"));
  $monthFrom = date('m', strtotime("yesterday"));
  $yearFrom =  date('Y', strtotime("yesterday"));
  $dayTo = date('d');
  $yearTo = date('Y');
  $monthTo = date('m');
}
else
{
  $monthFrom = $_POST["monthFrom"];
  $yearFrom = $_POST["yearFrom"];
  $dayTo = $_POST["dayTo"];
  $monthTo = $_POST["monthTo"];
  $yearTo = $_POST["yearTo"];
}
  
$xml = "<cdash>";
$xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
$xml .= "<version>".$CDASH_VERSION."</version>";
$xml .= "<backurl>manageBackup.php</backurl>";
$xml .= "<title>CDash - Import</title>";
$xml .= "<menutitle>CDash</menutitle>";
$xml .= "<menusubtitle>Import Dart1</menusubtitle>";

$project = pdo_query("SELECT name,id FROM project ORDER BY id");
$projName = "";
while($project_array = pdo_fetch_array($project))
{
  $projName = $project_array["name"];
  $xml .= "<project>";
  $xml .= "<name>".$projName."</name>";
  $xml .= "<id>".$project_array["id"]."</id>";
  $xml .= "</project>";
}

$xml .= "<dayFrom>".$dayFrom."</dayFrom>";
$xml .= "<monthFrom>".$monthFrom."</monthFrom>";
$xml .= "<yearFrom>".$yearFrom."</yearFrom>";
$xml .= "<dayTo>".$dayTo."</dayTo>";
$xml .= "<monthTo>".$monthTo."</monthTo>";
$xml .= "<yearTo>".$yearTo."</yearTo>";
$xml .= "</cdash>";

@$Submit = $_POST["Submit"];
if($Submit)
{
  $directory = $_POST["directory"];
  $projectid = $_POST["project"];
  
  // Checks
  if(!isset($projectid) || !is_numeric($projectid))
    {
    echo "Not a valid projectid!";
    return;
    }
    // Checks
  if(!isset($directory) || strlen($directory)<3)
    {
    echo "Not a valid directory!";
    return;
    }
    
  if($projectid == 0)
    {
    echo("Use your browsers Back button, and select a valid project.<br>");
    ob_flush();
    exit(0);
    }
  echo("Import for Project: ");
  echo(get_project_name($projectid));
  echo("<br>");
  ob_flush();
  if(strlen($directory)>0 )
    {
    $directory = str_replace('\\\\','/',$directory);  
    if(!file_exists($directory) || strpos($directory, "Sites") === FALSE)
      {
      echo("Error: $directory is not a valid path to Dart XML data on the server.<br>\n");
      generate_XSLT($xml,"import_dart_classic");
      exit(0);
      }
    $startDate = mktime(0,0,0,$monthFrom,$dayFrom,$yearFrom);
    $endDate = mktime(0,0,0,$monthTo,$dayTo,$yearTo);
    $numDays = ($endDate - $startDate) / (24 * 3600) + 1;
    for($i=0;$i<$numDays;$i++)
      {
      $currentDay = date(FMT_DATE, mktime(0,0,0,$monthFrom,$dayFrom+$i,$yearFrom));
      echo("Gathering XML files for $currentDay...  $directory/*/*/$currentDay-*/XML/*.xml <br>\n");
      flush();
      ob_flush();
      $files = glob($directory."/*/*/$currentDay-*/XML/*.xml");
      $numFiles = count($files);
      echo("$numFiles found<br>\n");
      flush();
      ob_flush();
      $numDots = 0;
      foreach($files as $file)
        {
        if(strlen($file)==0)
          {
          continue;
          }
        $handle = fopen($file,"r");
        $contents = fread($handle,filesize($file));
        echo ".";
        flush();
        ob_flush();
        $numDots++;
        if($numDots > 79)
          {
          echo "<br>\n";
          flush();
          ob_flush();
          $numDots = 0;
          }
        $xml_array = parse_XML($contents);
        ctest_parse($xml_array,$projectid);
        fclose($handle);
        }
      echo "<br>Done for the day".$currentDay."<br>\n";
      flush();
      ob_flush();
      }
    } // end strlen(directory)>0
  echo("<a href=index.php?project=".urlencode($projName).">Back to $projName dashboard</a>\n");
  exit(0);
}

// Now doing the xslt transition
generate_XSLT($xml,"import");

} // end session
?>
