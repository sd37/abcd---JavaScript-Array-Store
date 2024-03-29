<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: addnote.php 1398 2009-02-03 21:16:20Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-02-03 21:16:20 +0000 (Tue, 03 Feb 2009) $
  Version:   $Revision: 1398 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
require_once("../cdash/config.php");
require_once("../cdash/pdo.php");
include("../cdash/common.php");
$noforcelogin = 1;
include('../login.php');
  
$buildid = $_GET["buildid"];
$userid = $_GET["userid"];

if(!$userid || !$buildid || !isset($_SESSION['cdash']))
  {
  echo "Not valid id";
  return;
  }
// Checks
if(!isset($buildid) || !is_numeric($buildid))
  {
  echo "Not a valid buildid!";
  return;
  }
if(!isset($userid) || !is_numeric($userid))
  {
  echo "Not a valid userid!";
  return;
  }
   
$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);

@$AddNote = $_POST["AddNote"];
if($AddNote)
  {
  $TextNote = $_POST["TextNote"];
  $Status = $_POST["Status"];
  if(strlen($TextNote)>0)
    {
    $now = gmdate(FMT_DATETIME);
    pdo_query("INSERT INTO buildnote (buildid,userid,note,timestamp,status) 
                   VALUES ('$buildid','$userid','$TextNote','$now','$Status')");
    }
  $url = "../buildSummary.php?buildid=".$buildid."&message=noteaddded";
  header("location: ".$url);
  }
?><head>
<style type="text/css">
  .submitLink {
   color: #00f;
   background-color: transparent;
   text-decoration: underline;
   border: none;
   cursor: pointer;
   cursor: hand;
  }
</style>
</head>
 

<form method="post" action="ajax/addnote.php?buildid=<?php echo $buildid?>&userid=<?php echo $userid; ?>">
 <table>
 <tr>
 <td><b>Note:</b></td>
 <td> <textarea name="TextNote" cols="50" rows="5"></textarea></td>
 </tr>
 <tr>
 <td><b>Status:</b></td>
 <td><select name="Status">
  <option value="0">Simple Note</option>
  <option value="1">Fix in progress</option>
  <option value="2">Fixed</option>
 </select></td>
 </tr>
 <tr>
 <td></td>
 <td> <input name="AddNote" type="submit" value="Add Note"></td>
 </tr>
 </table>


 </form>

