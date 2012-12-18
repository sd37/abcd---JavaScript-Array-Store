<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: viewCoverageFile.php 2094 2009-12-23 23:50:16Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-12-23 23:50:16 +0000 (Wed, 23 Dec 2009) $
  Version:   $Revision: 2094 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
$noforcelogin = 1;
include("cdash/config.php");
require_once("cdash/pdo.php");
include('login.php');
include_once("cdash/common.php");
include("cdash/version.php");

@$buildid = $_GET["buildid"];
@$fileid = $_GET["fileid"];
@$date = $_GET["date"];

// Checks
if(!isset($buildid) || !is_numeric($buildid))
  {
  echo "Not a valid buildid!";
  return;
  }
  
$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);

$build_array = pdo_fetch_array(pdo_query("SELECT starttime,projectid FROM build WHERE id='$buildid'"));  
$projectid = $build_array["projectid"];

checkUserPolicy(@$_SESSION['cdash']['loginid'],$projectid);
    
$project = pdo_query("SELECT * FROM project WHERE id='$projectid'");
if(pdo_num_rows($project)>0)
  {
  $project_array = pdo_fetch_array($project);
  $projectname = $project_array["name"];  
  }

list ($previousdate, $currenttime, $nextdate) = get_dates($date,$project_array["nightlytime"]);
$logoid = getLogoID($projectid);

$xml = '<?xml version="1.0"?><cdash>';
$xml .= "<title>CDash : ".$projectname."</title>";
$xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
$xml .= "<version>".$CDASH_VERSION."</version>";

$xml .= get_cdash_dashboard_xml_by_name($projectname,$date);
  
  // Build
  $xml .= "<build>";
  $build = pdo_query("SELECT * FROM build WHERE id='$buildid'");
  $build_array = pdo_fetch_array($build);
  $siteid = $build_array["siteid"];
  $site_array = pdo_fetch_array(pdo_query("SELECT name FROM site WHERE id='$siteid'"));
  $xml .= add_XML_value("site",$site_array["name"]);
  $xml .= add_XML_value("buildname",$build_array["name"]);
  $xml .= add_XML_value("buildid",$build_array["id"]);
  $xml .= add_XML_value("buildtime",$build_array["starttime"]); 
  $xml .= "</build>";
  
  // coverage
  $coveragefile_array = pdo_fetch_array(pdo_query("SELECT fullpath,file FROM coveragefile WHERE id='$fileid'"));

  $xml .= "<coverage>";
  $xml .= add_XML_value("fullpath",$coveragefile_array["fullpath"]);
  $file = $coveragefile_array["file"];

  if($CDASH_DB_TYPE == "pgsql")
    {
    $file = stream_get_contents($file);
    }
  
  // Generating the html file
  $file_array = explode("<br>",$file);
  $i = 0;
  
  // Get the codes in an array
  $linecodes = array();
  $coveragefilelog = pdo_query("SELECT line,code FROM coveragefilelog WHERE fileid=".qnum($fileid)." AND buildid=".qnum($buildid));
  if(pdo_num_rows($coveragefilelog)>0)
    {
    while($coveragefilelog_array = pdo_fetch_array($coveragefilelog))
      {
      $linecodes[$coveragefilelog_array["line"]] = $coveragefilelog_array["code"];
      }
    }
  
  foreach($file_array as $line)
    {
    $linenumber = $i+1;  
    $line = htmlentities($line);
    
    $file_array[$i] = '<span class="lineNum">'.str_pad($linenumber,5,' ', STR_PAD_LEFT).'</span>';
    
    if(array_key_exists($i,$linecodes))
      {
      $code = $linecodes[$i];
      if($code==0)
        {
        $file_array[$i] .= '<span class="lineNoCov">';
        } 
      else
        {
        $file_array[$i] .= '<span class="lineCov">';
        }  
      $file_array[$i] .= str_pad($code,5,' ', STR_PAD_LEFT)." | ".$line;  
      $file_array[$i] .= "</span>";
      }
    else
      {
      $file_array[$i] .= str_pad('',5,' ', STR_PAD_LEFT)." | ".$line;
      }
    $i++;
    }
  
  $file = implode("<br>",$file_array);
  
  $xml .= "<file>".utf8_encode(htmlspecialchars($file))."</file>";
  $xml .= "</coverage>";
  $xml .= "</cdash>";

// Now doing the xslt transition
generate_XSLT($xml,"viewCoverageFile");
?>
