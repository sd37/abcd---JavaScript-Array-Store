<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: manageUsers.php 1628 2009-03-29 01:49:39Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-03-29 01:49:39 +0000 (Sun, 29 Mar 2009) $
  Version:   $Revision: 1628 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
include("cdash/config.php");
require_once("cdash/pdo.php");
include('login.php');
include_once('cdash/common.php');
include('cdash/version.php');
include('models/user.php');

if ($session_OK) 
  {
  $userid = $_SESSION['cdash']['loginid'];
  // Checks
  if(!isset($userid) || !is_numeric($userid))
    {
    echo "Not a valid usersessionid!";
    return;
    }
   
  $user_array = pdo_fetch_array(pdo_query("SELECT admin FROM ".qid("user")." WHERE id='$userid'"));

  if($user_array["admin"]!=1)
    {
    echo "You don't have the permissions to access this page!";
    return;
    }
  
  $xml = "<cdash>";
  $xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
  $xml .= "<version>".$CDASH_VERSION."</version>";
  $xml .= "<backurl>user.php</backurl>";
  $xml .= "<title>CDash - Manage Users</title>";
  $xml .= "<menutitle>CDash</menutitle>";
  $xml .= "<menusubtitle>Manage Users</menusubtitle>";
  
  if(isset($_POST["adduser"])) // arrive from register form 
    {
    $email = $_POST["email"];
    $passwd = $_POST["passwd"];
    $passwd2 = $_POST["passwd2"];
    if(!($passwd == $passwd2))
      {
      $xml .= add_XML_value("error","Passwords do not match!");
      }
    else
      {
      $fname = $_POST["fname"];
      $lname = $_POST["lname"];
      $institution = $_POST["institution"];
      if ($email && $passwd && $passwd2 && $fname && $lname && $institution)
        {
        $User = new User();
        if($User->GetIdFromEmail($email))
          {
          $xml .= add_XML_value("error","Email already registered!");
          }
        else
          {
          $passwdencryted = md5($passwd);
          $User->Email = $email;
          $User->Password = $passwdencryted;
          $User->FirstName = $fname;
          $User->LastName = $lname;
          $User->Institution = $institution;        
          if($User->Save())
            {
            $xml .= add_XML_value("warning","User ".$email." added successfully with password:".$passwd);
            }
          else
            {
            $xml .= add_XML_value("error","Cannot add user");
            }
          }
        }
      else
        {
        $xml .= add_XML_value("error","Please fill in all of the required fields");
        }
      }
    }
  else if(isset($_POST["makenormaluser"]))
    {
    if($_POST["userid"] > 1)
      {
      $update_array = pdo_fetch_array(pdo_query("SELECT firstname,lastname FROM ".qid("user")." WHERE id='".$_POST["userid"]."'"));
      pdo_query("UPDATE ".qid("user")." SET admin=0 WHERE id='".$_POST["userid"]."'");
      $xml .= "<warning>".$update_array['firstname']." ".$update_array['lastname']." is not administrator anymore.</warning>";
      }
    else
      {
      $xml .= "<error>Administrator should remain admin.</error>";
      }
    }
  else if(isset($_POST["makeadmin"]))
    {
    $update_array = pdo_fetch_array(pdo_query("SELECT firstname,lastname FROM ".qid("user")." WHERE id='".$_POST["userid"]."'"));
    pdo_query("UPDATE ".qid("user")." SET admin=1 WHERE id='".$_POST["userid"]."'");
    $xml .= "<warning>".$update_array['firstname']." ".$update_array['lastname']." is now an administrator.</warning>";
    }
  else if(isset($_POST["removeuser"]))
    {
    $update_array = pdo_fetch_array(pdo_query("SELECT firstname,lastname FROM ".qid("user")." WHERE id='".$_POST["userid"]."'"));
    pdo_query("DELETE FROM ".qid("user")." WHERE id='".$_POST["userid"]."'");
    $xml .= "<warning>".$update_array['firstname']." ".$update_array['lastname']." has been removed.</warning>";
    }
    
  if(isset($_POST["search"]))
    {
    $xml .= "<search>".$_POST["search"]."</search>";
    }

if(isset($CDASH_FULL_EMAIL_WHEN_ADDING_USER) && $CDASH_FULL_EMAIL_WHEN_ADDING_USER==1)
  {
  $xml .= add_XML_value("fullemail","1");
  }
  
  
$xml .= "</cdash>";

// Now doing the xslt transition
generate_XSLT($xml,"manageUsers");
  } // end session
?>

