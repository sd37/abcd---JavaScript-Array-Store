<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: buildtestdiff.php 1396 2009-02-03 19:09:26Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-02-03 19:09:26 +0000 (Tue, 03 Feb 2009) $
  Version:   $Revision: 1396 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
/** Build Test Diff */
class BuildTestDiff
{
  var $Type;
  var $Difference;
  var $BuildId;
  
  function SetValue($tag,$value)  
    {
    switch($tag)
      {
      case "TESTDIFF": $this->Difference = $value;break;
      }
    }
    
  // Insert in the database
  function Insert()
    {
    if(!$this->BuildId)
      {
      echo "BuildTestDiff::Insert(): BuildId is not set<br>";
      return false;
      }
      
    $query = "INSERT INTO testdiff (buildid,type,difference) VALUES ('$this->BuildId','$this->Type','$this->Difference')";                     
    if(!pdo_query($query))
      {
      add_last_sql_error("BuildTestDiff Insert");
      return false;
      }  
    return true;
    }      
}

?>
