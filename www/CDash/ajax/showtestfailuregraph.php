<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: showtestfailuregraph.php 1970 2009-10-02 19:08:35Z jjomier $
  Language:  PHP
  Date:      $Date: 2009-10-02 19:08:35 +0000 (Fri, 02 Oct 2009) $
  Version:   $Revision: 1970 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
require_once("../cdash/config.php");
require_once("../cdash/pdo.php");
require_once("../cdash/common.php");

$projectid = $_GET["projectid"];
$testname = $_GET["testname"];
$starttime = $_GET["starttime"];
@$zoomout = $_GET["zoomout"];

if(!isset($projectid) || !is_numeric($projectid))
  {
  echo "Not a valid projectid!";
  return;
  }
if(!isset($testname))
  {
  echo "Not a valid test name!";
  return;
  }
if(!isset($starttime))
  {
  echo "Not a valid starttime!";
  return;
  }
  


  
$db = pdo_connect("$CDASH_DB_HOST", "$CDASH_DB_LOGIN","$CDASH_DB_PASS");
pdo_select_db("$CDASH_DB_NAME",$db);

// We have to loop for the previous days
$failures = array();
for($beginning_timestamp = $starttime; $beginning_timestamp>$starttime-3600*24*7;$beginning_timestamp-=3600*24)
  {
  $end_timestamp = $beginning_timestamp+3600*24;
  
  $beginning_UTCDate = gmdate(FMT_DATETIME,$beginning_timestamp);
  $end_UTCDate = gmdate(FMT_DATETIME,$end_timestamp);  
  
  $query = "SELECT min(starttime) AS starttime,count(*)
            FROM build
            JOIN build2test ON (build.id = build2test.buildid)
            WHERE build.projectid = '$projectid'
            AND build.starttime>='$beginning_UTCDate'
            AND build.starttime<'$end_UTCDate'
            AND build2test.testid IN (SELECT id FROM test WHERE name='$testname')
            AND (build2test.status!='passed' OR build2test.timestatus!=0)
            ";        
  $result = pdo_query($query);
  echo pdo_error();
  $result_array = pdo_fetch_array($result);
  $failures[$beginning_timestamp]=$result_array["count(*)"];
  }
?>
    
<br>





<script language="javascript" type="text/javascript">
$(function () {
  var d1 = [];
  var ty = [];
  //ty.push([-1,"Failed"]);
  //ty.push([1,"Passed"]);
  
  <?php
    $tarray = array();
    foreach($failures as $key=>$value)
      {
      $t['x'] = $key*1000; 
      $t['y'] = $value;
      $tarray[]=$t;
    ?>
    <?php
      }
    
    $tarray = array_reverse($tarray);
    foreach($tarray as $axis)
      {
    ?>
      d1.push([<?php echo $axis['x']; ?>,<?php echo $axis['y']; ?>]);
    <?php 
      $t = $axis['x'];
      } ?>

  var options = {
    bars: { show: true,
      barWidth: 35000000,
      lineWidth: 0.9 
      },
    //points: { show: true },
    yaxis: { min: 0 }, 
    xaxis: { mode: "time" }, 
    grid: {backgroundColor: "#fffaff"},
    selection: { mode: "x" },
    colors: ["#0000FF", "#dba255", "#919733"]
  };
  
  $("#testfailuregrapholder").bind("selected", function (event, area) {
  $.plot($("#testfailuregrapholder"), [{label: "# builds failed",  data: d1}],
         $.extend(true, {}, options, {xaxis: { min: area.x1, max: area.x2 }}));
  });

<?php if(isset($zoomout))
{
?>
  $.plot($("#testfailuregrapholder"), [{label: "# builds failed",  data: d1}],options);
<?php } else { ?>
  $.plot($("#testfailuregrapholder"), [{label: "# builds failed",  data: d1}],
$.extend(true,{},options,{xaxis: { min: <?php echo $t-604800000?>,max: <?php echo $t+100000000 ?>}} )); 
<?php } ?>
});


</script>
