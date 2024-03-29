<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: autoRemoveBuilds.php 1894 2009-05-13 15:53:45Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-05-13 15:53:45 +0000 (Wed, 13 May 2009) $
  Version:   $Revision: 1894 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
/** Adding some PHP include path */
$path = dirname(__FILE__);
set_include_path(get_include_path() . PATH_SEPARATOR . $path);

include("cdash/autoremove.php");
// Open the database connection
include("cdash/config.php");
require_once("cdash/pdo.php");

if($argc != 2)
{
  print "Usage: php $argv[0] project_name\n";
  exit(-1);
}

$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);
set_time_limit(0);

$projectname=$argv[1];
print "removing builds for $projectname \n";
$sql = " WHERE name='".$projectname."'";
if($projectname == "all")
  {
  $sql="";
  }

$project = pdo_query("SELECT id,autoremovetimeframe,autoremovemaxbuilds FROM project".$sql);
if(!$project)
  {
  add_last_sql_error("autoRemoveBuilds");
  return false;
  }  
while($project_array = pdo_fetch_array($project))
  {
  removeFirstBuilds($project_array["id"],$project_array["autoremovetimeframe"],$project_array["autoremovemaxbuilds"], true); // force the autoremove
  }

exit(0);
?>
