<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: subscribeProject.php 2101 2010-01-06 05:40:58Z jjomier $
  Language:  PHP
  Date:      $Date: 2010-01-06 05:40:58 +0000 (Wed, 06 Jan 2010) $
  Version:   $Revision: 2101 $

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
include("models/project.php");
include("models/user.php");
include("models/label.php");
include("models/labelemail.php");

if ($session_OK) 
  {
  $userid = $_SESSION['cdash']['loginid'];
   
  @$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
  pdo_select_db("$CDASH_DB_NAME",$db);
  
  $xml = "<cdash>";
  $xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
  $xml .= "<version>".$CDASH_VERSION."</version>";
  $xml .= "<backurl>user.php</backurl>";
  $xml .= "<title>CDash - Subscribe to a project</title>";
  $xml .= "<menutitle>CDash</menutitle>";
  $xml .= "<menusubtitle>Subscription</menusubtitle>";
 
  @$projectid = $_GET["projectid"];
  @$edit = $_GET["edit"];
  
  // Checks
  if(!isset($projectid) || !is_numeric($projectid))
    {
    echo "Not a valid projectid!";
    return;
    }
  if(isset($edit) && $edit!=1)
    {
    echo "Not a valid edit!";
    return;
    }
    
  if($edit)
    {
    $xml .= "<edit>1</edit>";
    }
  else
    {
    $xml .= "<edit>0</edit>";
    }
 
  $project = pdo_query("SELECT id,name,public,emailbrokensubmission FROM project WHERE id='$projectid'");
  $project_array = pdo_fetch_array($project);
  
  $Project = new Project;
  $User = new User;
  $User->Id = $userid;
  $Project->Id = $projectid;
  $role = $Project->GetUserRole($userid);
    
  // Check if the project is public
  if(!$project_array['public'] && ($User->IsAdmin()===FALSE && $role<0))
    {
    echo "You don't have the permissions to access this page";
    return;
    }
  
  // Check if the user is not already in the database
  $user2project = pdo_query("SELECT cvslogin,role,emailtype,emailcategory,emailmissingsites,emailsuccess
                             FROM user2project WHERE userid='$userid' AND projectid='$projectid'");
  if(pdo_num_rows($user2project)>0)
    {
    $user2project_array = pdo_fetch_array($user2project);
    $xml .= add_XML_value("cvslogin",$user2project_array["cvslogin"]);
    $xml .= add_XML_value("role",$user2project_array["role"]);
    $xml .= add_XML_value("emailtype",$user2project_array["emailtype"]);
    $xml .= add_XML_value("emailmissingsites",$user2project_array["emailmissingsites"]);
    $xml .= add_XML_value("emailsuccess",$user2project_array["emailsuccess"]);
    $emailcategory = $user2project_array["emailcategory"];
    $xml .= add_XML_value("emailcategory_update",check_email_category("update",$emailcategory));
    $xml .= add_XML_value("emailcategory_configure",check_email_category("configure",$emailcategory));
    $xml .= add_XML_value("emailcategory_warning",check_email_category("warning",$emailcategory));
    $xml .= add_XML_value("emailcategory_error",check_email_category("error",$emailcategory));
    $xml .= add_XML_value("emailcategory_test",check_email_category("test",$emailcategory));
    }
  else // we set the default categories
    {
    $xml .= add_XML_value("emailcategory_update",1);
    $xml .= add_XML_value("emailcategory_configure",1);
    $xml .= add_XML_value("emailcategory_warning",1);
    $xml .= add_XML_value("emailcategory_error",1);
    $xml .= add_XML_value("emailcategory_test",1);
    }
  
  // If we ask to subscribe
  @$Subscribe = $_POST["subscribe"];
  @$UpdateSubscription = $_POST["updatesubscription"];
  @$Unsubscribe = $_POST["unsubscribe"]; 
  @$Role = $_POST["role"];
  @$CVSLogin = $_POST["cvslogin"];
  @$EmailType = $_POST["emailtype"];
  if(!isset($_POST["emailmissingsites"]))
    {
    $EmailMissingSites = 0;  
    }
  else
    {
    $EmailMissingSites = $_POST["emailmissingsites"];
    }
  if(!isset($_POST["emailsuccess"]))
    {
    $EmailSuccess = 0;  
    }
  else
    {
    $EmailSuccess = $_POST["emailsuccess"];
    }
  
  // Deals with label email
  $LabelEmail = new LabelEmail();
  $Label = new Label();
  $LabelEmail->ProjectId = $projectid;
  $LabelEmail->UserId = $userid;
      
  if($Unsubscribe)
    {
    pdo_query("DELETE FROM user2project WHERE userid='$userid' AND projectid='$projectid'");
    
    // Remove the claim sites for this project if they are only part of this project
    pdo_query("DELETE FROM site2user WHERE userid='$userid' 
               AND siteid NOT IN 
              (SELECT build.siteid FROM build,user2project as up WHERE 
               up.projectid = build.projectid AND up.userid='$userid' AND up.role>0
               GROUP BY build.siteid)");             
    header( 'location: user.php?note=unsubscribedtoproject' );
    }
  else if($UpdateSubscription)
    {
    @$emailcategory_update = $_POST["emailcategory_update"];
    @$emailcategory_configure = $_POST["emailcategory_configure"];
    @$emailcategory_warning = $_POST["emailcategory_warning"];
    @$emailcategory_error = $_POST["emailcategory_error"];
    @$emailcategory_test = $_POST["emailcategory_test"];
    
    $EmailCategory = $emailcategory_update+$emailcategory_configure+$emailcategory_warning+$emailcategory_error+$emailcategory_test;    
    if(pdo_num_rows($user2project)>0)
      {
      pdo_query("UPDATE user2project SET role='$Role',cvslogin='$CVSLogin',emailtype='$EmailType',
                         emailcategory='$EmailCategory',
                         emailmissingsites='$EmailMissingSites',
                         emailsuccess='$EmailSuccess' 
                         WHERE userid='$userid' AND projectid='$projectid'");
      if($Role==0)
        { 
        // Remove the claim sites for this project if they are only part of this project
        pdo_query("DELETE FROM site2user WHERE userid='$userid' 
                 AND siteid NOT IN 
                (SELECT build.siteid FROM build,user2project as up WHERE 
                 up.projectid = build.projectid AND up.userid='$userid' AND up.role>0
                 GROUP BY build.siteid)");
        }
      }
    
    if(isset($_POST['emaillabels']))
      {
      $LabelEmail->UpdateLabels($_POST['emaillabels']);
      }
    else
      {
      $LabelEmail->UpdateLabels(NULL);
      }
    // Redirect
    header( 'location: user.php' );
    }  
  else if($Subscribe)
    {
    @$emailcategory_update = $_POST["emailcategory_update"];
    @$emailcategory_configure = $_POST["emailcategory_configure"];
    @$emailcategory_warning = $_POST["emailcategory_warning"];
    @$emailcategory_error = $_POST["emailcategory_error"];
    @$emailcategory_test = $_POST["emailcategory_test"];
    
    $EmailCategory = $emailcategory_update+$emailcategory_configure+$emailcategory_warning+$emailcategory_error+$emailcategory_test;    
    if(pdo_num_rows($user2project)>0)
      {
      pdo_query("UPDATE user2project SET role='$Role',cvslogin='$CVSLogin',emailtype='$EmailType',
                         emailcategory='$EmailCategory'.
                         emailmissingsites='$EmailMissingSites',
                         emailsuccess='$EmailSuccess'  
                         WHERE userid='$userid' AND projectid='$projectid'");
      if($Role==0)
        { 
        // Remove the claim sites for this project if they are only part of this project
        pdo_query("DELETE FROM site2user WHERE userid='$userid' 
                 AND siteid NOT IN 
                (SELECT build.siteid FROM build,user2project as up WHERE 
                 up.projectid = build.projectid AND up.userid='$userid' AND up.role>0
                 GROUP BY build.siteid)");
        }
      }
    else
      {
      pdo_query("INSERT INTO user2project (role,cvslogin,userid,projectid,emailtype,emailcategory,emailsuccess,
                                           emailmissingsites) 
                 VALUES ('$Role','$CVSLogin','$userid','$projectid','$EmailType','$EmailCategory',
                         '$EmailSuccess','$EmailMissingSites')");
      }
    header( 'location: user.php?note=subscribedtoproject' );
    }


  // XML
  $xml .= "<project>";
  $xml .= add_XML_value("id",$project_array['id']);
  $xml .= add_XML_value("name",$project_array['name']);
  $xml .= add_XML_value("emailbrokensubmission",$project_array['emailbrokensubmission']);
   
  $labelavailableids = $Project->GetLabels(7); // Get the labels for the last 7 days
  $labelids = $LabelEmail->GetLabels();

  $labelavailableids = array_diff($labelavailableids,$labelids);
   
  foreach($labelavailableids as $labelid)
    {
    $xml .= "<label>";
    $xml .= add_XML_value("id",$labelid);
    $Label->Id = $labelid;
    $xml .= add_XML_value("text",$Label->GetText());
    $xml .= "</label>";
    }
  
  foreach($labelids as $labelid)
    {
    $xml .= "<labelemail>";
    $xml .= add_XML_value("id",$labelid);
    $Label->Id = $labelid;
    $xml .= add_XML_value("text",$Label->GetText());
    $xml .= "</labelemail>";
    }
  
  $xml .= "</project>";  
  
  $sql = "SELECT id,name FROM project";
  if($User->IsAdmin() == false)
    {
    $sql .= " WHERE id IN (SELECT projectid AS id FROM user2project WHERE userid='$userid' AND role>0)"; 
    }
  $projects = pdo_query($sql);
  while($project_array = pdo_fetch_array($projects))
     {
     $xml .= "<availableproject>";
     $xml .= add_XML_value("id",$project_array['id']);
     $xml .= add_XML_value("name",$project_array['name']);
     if($project_array['id']==$projectid)
        {
        $xml .= add_XML_value("selected","1");
        }
     $xml .= "</availableproject>";
     }
   
  $xml .= "</cdash>";
  
  // Now doing the xslt transition
  generate_XSLT($xml,"subscribeProject");
  } // end session OK

?>

