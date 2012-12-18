<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: buildOverview.php 1161 2008-09-19 14:56:14Z jjomier $
  Language:  PHP
  Date:      $Date: 2007-10-16 11:23:29 -0400 (Tue, 16 Oct 2007) $
  Version:   $Revision: 12 $

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

if ($session_OK) 
  {
  include_once("cdash/common.php");
  
  $xml = '<?xml version="1.0"?><cdash>';
  $xml .= "<title>CDash - My Profile</title>";
  $xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
  $xml .= "<version>".$CDASH_VERSION."</version>";
  $xml .= "<backurl>user.php</backurl>";
  $xml .= "<title>CDash - My Profile</title>";
  $xml .= "<menutitle>CDash</menutitle>";
  $xml .= "<menusubtitle>My Profile</menusubtitle>";
  
  $db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
  pdo_select_db("$CDASH_DB_NAME",$db);

  $userid = $_SESSION['cdash']['loginid'];

  @$updateprofile = $_POST["updateprofile"]; 
  if($updateprofile) 
    {
    $institution = pdo_real_escape_string($_POST["institution"]); 
    $email = pdo_real_escape_string($_POST["email"]); 
   
    if(strlen($email)<3 || strpos($email,"@")===FALSE)
      {
      $xml .= "<error>Email should be a valid address.</error>";  
      }
    else
      { 
      $lname = pdo_real_escape_string($_POST["lname"]); 
      $fname = pdo_real_escape_string($_POST["fname"]);  
     
      if(pdo_query("UPDATE ".qid("user")." SET email='$email',
                   institution='$institution',
                   firstname='$fname',
                   lastname='$lname' WHERE id='$userid'"))
        {
        $xml .= "<error>Your profile has been updated.</error>";
        }
      else
        {
        $xml .= "<error>Cannot update profile.</error>";
        }
      add_last_sql_error("editUser.php");
      }
    }
  
 @$updatepassword = $_POST["updatepassword"];
 if($updatepassword) 
    {
   $passwd = $_POST["passwd"]; 
   $passwd2 = $_POST["passwd2"]; 
  
  if(strlen($passwd)<5)
    {
   $xml .= "<error>Password should be at least 5 characters.</error>";
    }
   else if($passwd != $passwd2)
    {
   $xml .= "<error>Passwords don't match.</error>";
    }
    else
    {
   $md5pass = md5($passwd); 
   $md5pass = pdo_real_escape_string($md5pass); 
   if(pdo_query("UPDATE ".qid("user")." SET password='$md5pass' WHERE id='$userid'"))
    {
    $xml .= "<error>Your password has been updated.</error>";
    }
   else
    {
    $xml .= "<error>Cannot update password.</error>";
    }
   
    add_last_sql_error("editUser.php");
    }
    }
  
  $xml .= "<user>";
  $user = pdo_query("SELECT * FROM ".qid("user")." WHERE id='$userid'");
  $user_array = pdo_fetch_array($user);
  $xml .= add_XML_value("firstname",$user_array["firstname"]);
  $xml .= add_XML_value("lastname",$user_array["lastname"]);
  $xml .= add_XML_value("email",$user_array["email"]);
  $xml .= add_XML_value("institution",$user_array["institution"]);
   
  $xml .= "</user>";
  $xml .= "</cdash>";
  
  generate_XSLT($xml,"editUser");

} // end session OK
?>
