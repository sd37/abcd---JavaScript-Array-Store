<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: buildOverview.php 1161 2008-09-19 14:56:14Z jjomier $
  Language:  PHP
  Date:      $Date: 2008-02-04 17:50:42 -0500 (Mon, 04 Feb 2008) $
  Version:   $Revision: 435 $

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
require_once("models/project.php");
require_once("models/subproject.php");

@$projectid = $_GET["projectid"];
// Checks
if(!isset($projectid) || !is_numeric($projectid))
  {
  echo "Not a valid projectid!";
  return;
  }

$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);
  
$project = pdo_query("SELECT * FROM project WHERE id='$projectid'");
if(pdo_num_rows($project)==0)
  {
  return;
  }

$project_array = pdo_fetch_array($project);
checkUserPolicy(@$_SESSION['cdash']['loginid'],$project_array["id"]);

$ctestconfig = "## This file should be placed in the root directory of your project.\n";
$ctestconfig .= "## Then modify the CMakeLists.txt file in the root directory of your\n";
$ctestconfig .= "## project to incorporate the testing dashboard.\n";
$ctestconfig .= "## # The following are required to uses Dart and the Cdash dashboard\n";
$ctestconfig .= "##   ENABLE_TESTING()\n";
$ctestconfig .= "##   INCLUDE(CTest)\n";

$ctestconfig .= "set(CTEST_PROJECT_NAME \"".$project_array["name"]."\")\n";
$ctestconfig .= "set(CTEST_NIGHTLY_START_TIME \"".$project_array["nightlytime"]."\")\n\n";

$ctestconfig .= "set(CTEST_DROP_METHOD \"http\")\n";

$ctestconfig .= "set(CTEST_DROP_SITE \"".$_SERVER['SERVER_NAME']."\")\n";

$currentURI = $_SERVER['REQUEST_URI']; 
$currentURI = substr($currentURI,0,strrpos($currentURI,"/"));
   
$ctestconfig .= "set(CTEST_DROP_LOCATION \"".$currentURI."/submit.php?project=".urlencode($project_array["name"])."\")\n";
$ctestconfig .= "set(CTEST_DROP_SITE_CDASH TRUE)\n";

// Add the subproject
$Project = new Project();
$Project->Id = $projectid;
$subprojectids = $Project->GetSubprojects();

function get_graph_depth($a,$value)
  {
  $SubProject = new SubProject();
  $SubProject->Id = $a;
  $parents = $SubProject->GetDependencies();
  foreach($parents as $parentid)
    {
    $subvalue = get_graph_depth($parentid,$value+1);
    if($subvalue>$value)
      {
      $value = $subvalue;
      }
    }
  return $value;
  }

// Compare two subprojects to check the depth
function cmp($a, $b)
  {
  $va = get_graph_depth($a,0);
  $vb = get_graph_depth($b,0);  
  if ($va == $vb) 
    {
    return 0;
    }
  return ($va < $vb) ? -1 : 1;
  }
usort($subprojectids,"cmp");

if(count($subprojectids)>0)
  {
  $ctestconfig .= "\nset(CTEST_PROJECT_SUBPROJECTS\n";
  }
  
foreach($subprojectids as $subprojectid) 
  {
  $SubProject = new SubProject();
  $SubProject->Id = $subprojectid;
  $ctestconfig .= $SubProject->GetName()."\n";
  }
  
if(count($subprojectids)>0)
  {
  $ctestconfig .= ")\n";
  }
 
header('Vary: User-Agent');
if(ob_get_contents())
  echo "Some data has already been output";
if(isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'],'MSIE'))
  header('Content-Type: application/force-download');
else
  header('Content-Type: application/octet-stream');
if(headers_sent())
  echo "Some data has already been output to browser";
   
header("Content-Disposition: attachment; filename=\"CTestConfig.cmake\"");
header("Content-Transfer-Encoding: binary");
header("Content-Length: ".strlen($ctestconfig));
echo $ctestconfig;

?>
