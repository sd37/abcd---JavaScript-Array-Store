<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: buildconfigureerrordiff.php 1396 2009-02-03 19:09:26Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-02-03 19:09:26 +0000 (Tue, 03 Feb 2009) $
  Version:   $Revision: 1396 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
/** BuildConfigureErrorDiff class */
class BuildConfigureErrorDiff
{
  var $Type;
  var $Difference;
  var $BuildId;
  
  function SetValue($tag,$value)  
    {
    switch($tag)
      {
      case "BUILDERRORDIFF": $this->Difference = $value;break;
      case "TYPE": $this->Type = $value;break;
      }
    }
    /** Return if exists */
  function Exists()
    {
    $query = pdo_query("SELECT count(*) FROM configureerrordiff WHERE buildid=".qnum($this->BuildId));  
    $query_array = pdo_fetch_array($query);
    if($query_array['count(*)']>0)
      {
      return true;
      }
    return false;
    }      
      
  /** Save in the database */
  function Save()
    {
    if(!$this->BuildId)
      {
      echo "BuildConfigureErrorDiff::Save(): BuildId not set";
      return false;    
      }
      
    if($this->Exists())
      {
      // Update
      $query = "UPDATE configureerrordiff SET";
      $query .= " type=".qnum($this->Type);
      $query .= ",difference=".qnum($this->Difference);
      $query .= " WHERE buildid=".qnum($this->BuildId);
      if(!pdo_query($query))
        {
        add_last_sql_error("BuildConfigureErrorDiff Update");
        return false;
        }
      }
    else // insert  
      {
      $query = "INSERT INTO configureerrordiff (buildid,type,difference)
                 VALUES (".qnum($this->BuildId).",".qnum($this->Type).",".qnum($this->Difference).")";                     
      if(!pdo_query($query))
        {
        add_last_sql_error("BuildConfigureErrorDiff Create");
        return false;
        }  
      }
    return true;
    }        
}
?>
