<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: viewMap.php 2014 2009-11-24 00:48:18Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-11-24 00:48:18 +0000 (Tue, 24 Nov 2009) $
  Version:   $Revision: 2014 $

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

@$projectname = $_GET["project"];
@$date = $_GET["date"];
  
$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);

$projectid = get_project_id($projectname);

if($projectid == -1)
  {
  echo "Wrong project name";
  exit();
  }

checkUserPolicy(@$_SESSION['cdash']['loginid'],$projectid);

$xml = '<?xml version="1.0"?><cdash>';
$xml .= "<title>CDash : Sites map for ".$projectname."</title>";
$xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
$xml .= "<version>".$CDASH_VERSION."</version>";
$xml .= "<backurl>index.php?project=".urlencode($projectname)."&#38;date=$date</backurl>";
$xml .= "<menutitle>CDash</menutitle>";
$xml .= "<menusubtitle>Build location</menusubtitle>";

$xml .= "<dashboard>";
$xml .= "<title>CDash</title>";
$xml .= "<date>".$date."</date>";

// Find the correct google map key
foreach($CDASH_GOOGLE_MAP_API_KEY as $key=>$value)
  {
  if(strstr($_SERVER['HTTP_HOST'],$key) !== FALSE)
    {
    $apikey = $value;
    break;
    }
  } 
$xml .=  add_XML_value("googlemapkey",$apikey);
$xml .=  add_XML_value("projectname",$projectname);
$xml .=  add_XML_value("projectname_encoded",urlencode($projectname));
$xml .= "</dashboard>";

$project = pdo_query("SELECT * FROM project WHERE id='$projectid'");
$project_array = pdo_fetch_array($project);

list ($previousdate, $currenttime, $nextdate) = get_dates($date,$project_array["nightlytime"]);
    
$nightlytime = strtotime($project_array["nightlytime"]);
  
$nightlyhour = gmdate("H",$nightlytime);
$nightlyminute = gmdate("i",$nightlytime);
$nightlysecond = gmdate("s",$nightlytime);
  
$end_timestamp = $currenttime-1; // minus 1 second when the nightly start time is midnight exactly
  
$beginning_timestamp = gmmktime($nightlyhour,$nightlyminute,$nightlysecond,gmdate("m",$end_timestamp),gmdate("d",$end_timestamp),gmdate("Y",$end_timestamp));
if($end_timestamp<$beginning_timestamp)
  {
  $beginning_timestamp = gmmktime($nightlyhour,$nightlyminute,$nightlysecond,gmdate("m",$end_timestamp-24*3600),gmdate("d",$end_timestamp-24*3600),gmdate("Y",$end_timestamp-24*3600));
  }
  
$beginning_UTCDate = gmdate(FMT_DATETIME,$beginning_timestamp);
$end_UTCDate = gmdate(FMT_DATETIME,$end_timestamp);            
  
$site = pdo_query("SELECT s.id,s.name,si.processorclockfrequency,
                     si.description,
                     si.numberphysicalcpus,s.ip,s.latitude,s.longitude, 
                     user.firstname,user.lastname,user.id AS userid
                     FROM build AS b, siteinformation AS si, site as s
                     LEFT JOIN site2user ON (site2user.siteid=s.id)
                     LEFT JOIN user ON (site2user.userid=user.id)
                     WHERE s.id=b.siteid 
                     AND b.starttime<'$end_UTCDate' AND b.starttime>'$beginning_UTCDate'
                     AND si.siteid=s.id
                     AND b.projectid='$projectid' GROUP BY b.siteid");

echo pdo_error();

while($site_array = pdo_fetch_array($site))
  {
  $xml .= "<site>";
  $xml .= add_XML_value("name",$site_array["name"]);
  $xml .= add_XML_value("id",$site_array["id"]);
  $xml .= add_XML_value("description",$site_array["description"]);
  $xml .= add_XML_value("processor_speed",getByteValueWithExtension($site_array["processorclockfrequency"]*1024*1024));
  $xml .= add_XML_value("numberphysicalcpus",$site_array["numberphysicalcpus"]);
  $xml .= add_XML_value("latitude",$site_array["latitude"]);
  $xml .= add_XML_value("longitude",$site_array["longitude"]);
  $xml .= add_XML_value("longitude",$site_array["longitude"]);
  $xml .= add_XML_value("maintainer_name",$site_array["firstname"]." ".$site_array["lastname"]);
  $xml .= add_XML_value("maintainer_id",$site_array["userid"]);
  $xml .= "</site>";
  }

$xml .= "</cdash>";

// Now doing the xslt transition
generate_XSLT($xml,"viewMap");
?>
