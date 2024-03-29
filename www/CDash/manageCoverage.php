<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: manageCoverage.php 2010 2009-11-23 01:57:22Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-11-23 01:57:22 +0000 (Mon, 23 Nov 2009) $
  Version:   $Revision: 2010 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
include("cdash/config.php");
require_once("cdash/pdo.php");
include_once("cdash/common.php");
include('login.php');
include('cdash/version.php');
include_once("models/project.php");
include_once("models/coverage.php");
include_once("models/build.php");
include_once("models/user.php");
include_once("models/site.php");

if ($session_OK) 
{
@$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);

$userid = $_SESSION['cdash']['loginid'];
// Checks
if(!isset($userid) || !is_numeric($userid))
  {
  echo "Not a valid userid!";
  return;
  }
  
$xml = "<cdash>";
$xml .= "<cssfile>".$CDASH_CSS_FILE."</cssfile>";
$xml .= "<version>".$CDASH_VERSION."</version>";
$xml .= "<backurl>user.php</backurl>";
$xml .= "<title>CDash - Manage Coverage</title>";
$xml .= "<menutitle>CDash</menutitle>";
$xml .= "<menusubtitle>Coverage</menusubtitle>";
  
@$projectid = $_GET["projectid"];
$Project = new Project;

$buildid = 0;
if(isset($_GET['buildid']))
  {
  $buildid = $_GET['buildid'];
  }
    
// If the projectid is not set and there is only one project we go directly to the page
if(isset($edit) && !isset($projectid))
  {
  $projectids = $Project->GetIds();
  if(count($projectids)==1)
    {
    $projectid = $projectids[0];
    }
  }

$User = new User;
$User->Id = $userid;
$Project->Id = $projectid;
  
$role = $Project->GetUserRole($userid);
     
if(!(isset($_SESSION['cdash']['user_can_create_project']) && 
   $_SESSION['cdash']['user_can_create_project'] == 1)
   && ($User->IsAdmin()===FALSE && $role<=1))
  {
  echo "You don't have the permissions to access this page";
  return;
  }
  
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

// Display the current builds who have coverage for the past 7 days
$currentUTCTime =  gmdate(FMT_DATETIME);
$beginUTCTime = gmdate(FMT_DATETIME,time()-3600*7*24); // 7 days

$CoverageFile2User = new CoverageFile2User();
$CoverageFile2User->ProjectId = $projectid;

// Change the priority of selected files
if(isset($_POST["changePrioritySelected"]))
  {
  foreach($_POST['selectionFiles'] as $key => $value)
    {
    $CoverageFile2User->FullPath = $value;
    $CoverageFile2User->SetPriority($_POST['prioritySelectedSelection']);
    }
  }
  
// Remove the selected authors
if(isset($_POST["removeAuthorsSelected"]))
  {
  foreach($_POST['selectionFiles'] as $key => $value)
    {
    $CoverageFile2User->FullPath = $value;
    $CoverageFile2User->RemoveAuthors();
    }
  }
  
// Add the selected authors
if(isset($_POST["addAuthorsSelected"]))
  {
  foreach($_POST['selectionFiles'] as $key => $value)
    {
    $CoverageFile2User->UserId = $_POST["userSelectedSelection"];
    $CoverageFile2User->FullPath = $value;
    $CoverageFile2User->Insert();
    }
  }
  
// Add an author manually
if(isset($_POST["addAuthor"]))
  {
  $CoverageFile2User->UserId = $_POST["userSelection"];
  $CoverageFile2User->FullPath = $_POST["fullpath"];
  $CoverageFile2User->Insert();
  } // end add author

// Remove an author manually
if(isset($_GET["removefileid"]))
  {
  $CoverageFile2User->UserId = $_GET["removeuserid"];
  $CoverageFile2User->FileId = $_GET["removefileid"];
  $CoverageFile2User->Remove();
  } // end remove author

// Assign last author
if(isset($_POST["assignLastAuthor"]))
  {
  $CoverageFile2User->AssignLastAuthor($buildid,$beginUTCTime,$currentUTCTime);
  } // end last author
  
// Assign all authors
if(isset($_POST["assignAllAuthors"]))
  {
  $CoverageFile2User->AssignAllAuthors($buildid,$beginUTCTime,$currentUTCTime);
  } // end Assign all authors

// Upload file
if(isset($_POST["uploadAuthorsFile"]))
  {
  $contents = file_get_contents($_FILES['authorsFile']['tmp_name']);
  if(strlen($contents)>0)
    {  
    $pos = 0;
    $pos2 = strpos($contents,"\n");
    while($pos !== false)
      {
      $line = substr($contents,$pos,$pos2-$pos);
      
      $file = "";
      $authors = array();
      
      // first is the svnuser
      $posfile = strpos($line,":");
      if($posfile !== false)
        {
        $file = trim(substr($line,0,$posfile));
        $begauthor = $posfile+1;
        $endauthor = strpos($line,",",$begauthor);
        while($endauthor !== false)
          {
          $authors[] = trim(substr($line,$begauthor,$endauthor-$begauthor));
          $begauthor = $endauthor+1;
          $endauthor = strpos($line,",",$begauthor);
          }
        
        $authors[] = trim(substr($line,$begauthor));
        
        // Insert the user        
        $CoverageFile = new CoverageFile;
        if($CoverageFile->GetIdFromName($file,$buildid) === false)
          {
          $xml .= add_XML_value("warning","*File not found for: ".$file);
          }
        else
          {      
          foreach($authors as $author)
            {
            $User = new User;
            $CoverageFile2User->UserId = $User->GetIdFromName($author);
            if($CoverageFile2User->UserId === false)
              {
              $xml .= add_XML_value("warning","*User not found for: ".$author);
              }
            else
              {
              $CoverageFile2User->FullPath = $file;
              $CoverageFile2User->Insert();
              }
            }
          }
        }
        
      $pos = $pos2;
      $pos2 = strpos($contents,"\n",$pos2+1);
      } // end looping through lines
    } // end if strlen>0    
  }  // end upload authors file
  
// Send an email
if(isset($_POST["sendEmail"]))
  {
  $coverageThreshold = $Project->GetCoverageThreshold();
    
  $userids = $CoverageFile2User->GetUsersFromProject();
  foreach($userids as $userid)
    {
    $CoverageFile2User->UserId = $userid;
    $fileids = $CoverageFile2User->GetFiles();

    $files = array();
    
    // For each file check the coverage metric
    foreach($fileids as $fileid)
      {
      $coveragefile = new CoverageFile;
      $CoverageFile2User->FileId = $fileid;
      $coveragefile->Id = $CoverageFile2User->GetCoverageFileId($buildid);
      $metric = $coveragefile->GetMetric();
      if($metric < ($coverageThreshold/100.0))
        {
        $file['percent'] = $coveragefile->GetLastPercentCoverage();
        $file['path'] = $coveragefile->GetPath();
        $file['id'] = $fileid;
        $files[] = $file; 
        }
      }
    
    // Send an email if the number of uncovered file is greater than one
    if(count($files)>0)
      {
      // Writing the message
      $messagePlainText = "The following files for the project ".$Project->GetName();
      $messagePlainText .= " have a low coverage and "; 
      $messagePlainText .= "you have been identified as one of the authors of these files.\n";
      
      foreach($files as $file)
        {
        $messagePlainText .= $file['path']." (".round($file['percent'],2)."%)\n";
        }  
        
      $messagePlainText .= "Details on the submission can be found at ";
    
      $messagePlainText .= get_server_URI();
      $messagePlainText .= "\n\n";  
      $serverName = $CDASH_SERVER_NAME;
      if(strlen($serverName) == 0)
        {
        $serverName = $_SERVER['SERVER_NAME'];
        }
      
      $messagePlainText .= "\n-CDash on ".$serverName."\n";
        
      // Send the email
      $title = "CDash [".$Project->GetName()."] - Low Coverage";
      
      $User = new User();
      $User->Id=$userid;
      $email = $User->GetEmail();
            
      mail("$email", $title, $messagePlainText,
          "From: CDash <".$CDASH_EMAIL_FROM.">\nReply-To: ".$CDASH_EMAIL_REPLY."\nX-Mailer: PHP/" . phpversion()."\nMIME-Version: 1.0" );
      
      $xml .= add_XML_value("warning","*The email has been sent successfully.");
      }
    else
      {
      $xml .= add_XML_value("warning","*No email sent because the coverage is green.");
      }  
    }
  
  } // end sendEmail
  
// If we change the priority
if(isset($_POST['prioritySelection']))
  {
  $CoverageFile2User = new CoverageFile2User();
  $CoverageFile2User->ProjectId = $projectid;
  $CoverageFile2User->FullPath = $_POST['fullpath'];
  $CoverageFile2User->SetPriority($_POST['prioritySelection']);
  }  
  
  
/** We start generating the XML here */

// Find the recent builds for this project
if($projectid>0)
  {
  $xml .= "<project>";
  $xml .= add_XML_value("id",$Project->Id);
  $xml .= add_XML_value("name",$Project->GetName());
  $xml .= add_XML_value("name_encoded",urlencode($Project->GetName()));
  
  if($buildid>0)
    {
    $xml.= add_XML_value("buildid",$buildid);
    }
    
  $CoverageSummary = new CoverageSummary();
  
  $buildids = $CoverageSummary->GetBuilds($Project->Id,$beginUTCTime,$currentUTCTime);
  rsort($buildids);
  foreach($buildids as $buildId)
    {
    $Build = new Build();
    $Build->Id = $buildId;
    $Build->FillFromId($Build->Id);
    $xml .= "<build>";
    $xml .= add_XML_value("id",$buildId);
    $Site = new Site();
    $Site->Id = $Build->SiteId;
    $xml .= add_XML_value("name",$Site->GetName()."-".$Build->GetName()." [".gmdate(FMT_DATETIME,strtotime($Build->StartTime))."]");
    if($buildid>0 && $buildId==$buildid)
      {
      $xml .= add_XML_value("selected",1);
      }
    $xml .= "</build>";
    }
  
  // For now take the first one
  if($buildid>0)
    {
    // Find the files associated with the build
    $Coverage = new Coverage();
    $Coverage->BuildId = $buildid;
    $fileIds = $Coverage->GetFiles();
    $row = "0";
    sort($fileIds);
    foreach($fileIds as $fileid)
      {
      $CoverageFile = new CoverageFile();
      $CoverageFile->Id = $fileid;
      $xml .= "<file>";
      $CoverageFile2User->FullPath = $CoverageFile->GetPath();
     
      $xml .= add_XML_value("fullpath",$CoverageFile->GetPath());
      $xml .= add_XML_value("id",$CoverageFile2User->GetId());
      $xml .= add_XML_value("fileid",$fileid);
      
      if($row == 0)
        {
        $row = 1;
        }
      else
        {
        $row = 0;
        }   
      $xml .= add_XML_value("row",$row);
      
      // Get the authors
      $CoverageFile2User->FullPath = $CoverageFile->GetPath();
      $authorids = $CoverageFile2User->GetAuthors();
      foreach($authorids as $authorid)
        {
        $xml .= "<author>";
        $User = new User();
        $User->Id = $authorid;
        $xml .= add_XML_value("name",$User->GetName());
        $xml .= add_XML_value("id",$authorid);
        $xml .= "</author>";
        }
      
      $priority = $CoverageFile2User->GetPriority(); 
      if($priority>0)
        {
        $xml .= add_XML_value("priority",$priority);
        }  
        
      $xml .= "</file>";
      }
    } // end count(buildids)
 
  // List all the users of the project
  $UserProject = new UserProject();
  $UserProject->ProjectId = $Project->Id;
  $userIds = $UserProject->GetUsers();
  foreach($userIds as $userid)
    {
    $User = new User;
    $User->Id = $userid;
    $xml .= "<user>";
    $xml .= add_XML_value("id",$userid);
    $xml .= add_XML_value("name",$User->GetName());
    $xml .= "</user>";
    }
 
  $xml .= "</project>";
  }
$xml .= "</cdash>";

// Now doing the xslt transition
generate_XSLT($xml,"manageCoverage");

} // end session OK
?>

