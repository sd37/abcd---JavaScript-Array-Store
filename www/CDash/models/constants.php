<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: constants.php 2055 2009-12-03 22:57:21Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-12-03 22:57:21 +0000 (Thu, 03 Dec 2009) $
  Version:   $Revision: 2055 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
/** WARNING: JUST ADD TO THIS TABLE. NEVER MODIFY THE ENUMS */
define("CDASH_JOB_SCHEDULED","0");
define("CDASH_JOB_RUNNING","2");
define("CDASH_JOB_FINISHED","3");
define("CDASH_JOB_ABORTED","4");

define("CDASH_JOB_EXPERIMENTAL","0");
define("CDASH_JOB_NIGHTLY","1");
define("CDASH_JOB_CONTINUOUS","2");

define("CDASH_REPOSITORY_CVS","0");
define("CDASH_REPOSITORY_SVN","1");
?>
