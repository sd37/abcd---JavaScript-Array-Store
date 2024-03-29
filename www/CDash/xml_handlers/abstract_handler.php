<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: abstract_handler.php 1398 2009-02-03 21:16:20Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-02-03 21:16:20 +0000 (Tue, 03 Feb 2009) $
  Version:   $Revision: 1398 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
require_once 'cdash/ctestparserutils.php';
require_once 'xml_handlers/sax_handler.php';
require_once 'xml_handlers/stack.php';
require_once('models/build.php');
require_once('models/site.php');

abstract class AbstractHandler implements SaxHandler
{
  protected $stack;
  protected $projectid;
  protected $Build;
  protected $Site;
  protected $SubProjectName;

  public function __construct($projectid)
    {
    $this->projectid = $projectid;
    $this->stack = new Stack();  
    }
  
  protected function getParent()
    {
    return $this->stack->at($this->stack->size()-2);
    }
  
  protected function getElement()
    {
    return $this->stack->top();
    }
  
  public function startElement($parser, $name, $attributes)
    {
    $this->stack->push($name);

    if($name == 'SUBPROJECT')
      {
      $this->SubProjectName = $attributes['NAME'];
      }
    }

  public function endElement($parser, $name)
    {
    $this->stack->pop();
    }

  public function processingInstruction($parser, $target, $data){}
  
  public function externalEntity($parser, $open_entity_name, $base, $system_id, $public_id){}
  
  public function skippedEntity($parser, $open_entity_name, $base, $system_id, $public_id){}
  
  public function startPrefixMapping($parser, $user_data, $prefix, $uri){}
  
  public function endPrefixMapping($parser, $user_data, $prefix){}

  public function getSiteName()
    {
    return $this->Site->Name;
    }

  public function getBuildStamp()
    {
    return $this->Build->GetStamp();
    }

  public function getBuildName()
    {
    return $this->Build->Name;
    }      
}
?>
