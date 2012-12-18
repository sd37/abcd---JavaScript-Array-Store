<xsl:stylesheet
    xmlns:xsl="http://www.w3.org/1999/XSL/Transform" version='1.0'>

   <xsl:include href="footer.xsl"/>
   
   <xsl:output method="xml" indent="yes"  doctype-public="-//W3C//DTD XHTML 1.0 Transitional//EN" 
   doctype-system="http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd"/>
        
    <xsl:template match="/">
       <html>
       <head>
       <title><xsl:value-of select="cdash/title"/></title>
        <meta name="robots" content="noindex,nofollow" />
          <link rel="shortcut icon" href="favicon.ico"/> 
           <link rel="StyleSheet" type="text/css">
         <xsl:attribute name="href"><xsl:value-of select="cdash/cssfile"/></xsl:attribute>
         </link>
        <script src="javascript/jquery.js" type="text/javascript" charset="utf-8"></script>
        <!-- Include the sorting -->
        <script src="javascript/jquery.cookie.js" type="text/javascript" charset="utf-8"></script>
        <script src="javascript/jquery.tablesorter.js" type="text/javascript" charset="utf-8"></script>
        
        <!-- include jqModal --> 
        <script src="javascript/jqModal.js" type="text/javascript" charset="utf-8"></script>  
        <link type="text/css" rel="stylesheet" media="all" href="javascript/jqModal.css" />
    
        <script src="javascript/cdashTableSorter.js" type="text/javascript" charset="utf-8"></script>
        <script src="javascript/cdashIndexTable.js" type="text/javascript" charset="utf-8"></script>
       </head>
       <body>
 
 <table width="100%" class="toptable" cellpadding="1" cellspacing="0">
  <tr>
    <td>
  <table width="100%"  cellpadding="0" cellspacing="0" >
  <tr>
    <td height="30" valign="middle">
    <table width="100%" cellspacing="0" cellpadding="0">
      <tr>
        <td width="66%" class="paddl">
        <a><xsl:attribute name="href">user.php</xsl:attribute>
        <xsl:choose>
          <xsl:when test="cdash/user/id>0">
            My CDash  
          </xsl:when>
          <xsl:otherwise>
             Login</xsl:otherwise>
        </xsl:choose>  
        </a>
        
        <xsl:if test="string-length(cdash/user/id)=0  and string-length(cdash/dashboard/noregister)=0">
         | <a href="register.php">Register</a>
        </xsl:if>  
        
        <xsl:if test="cdash/user/id>0">
          <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>|<xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text><a href="user.php?logout=1">Log Out</a>  
        </xsl:if>
        
        </td>
        <td width="34%" class="topdate">
          <span style="float:right">
         <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text>
         </span>
         <xsl:value-of select="cdash/dashboard/datetime"/>
      </td>
      </tr>
    </table>    
    </td>
  </tr>
  <tr>
    <td height="22" class="topline"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
  </tr>
  <tr>
    <td width="100%" align="left" class="topbg">

    <table width="100%" border="0" cellpadding="0" cellspacing="0" >
    <tr>
    <td width="195" height="121" class="topbgleft">
    <xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text> 
    <a href="http://www.cdash.org">
    <img  border="0" alt="" src="images/cdash.gif"/>
    </a>
    </td>
    <td width="425" valign="top" class="insd">
    <div class="insdd">
      <span class="inn1">CDash</span><br />
      <span class="inn2">Projects</span>
      </div>
    </td>
    <td height="121" class="insd2"><xsl:text disable-output-escaping="yes">&amp;nbsp;</xsl:text></td>
   </tr>
  </table>
  </td>
    </tr>
</table></td>
  </tr>
</table>
<table border="0" width="100%">
<xsl:if test="cdash/banner">
  <tr bgcolor="#DDDDDD">
  <td align="center" width="100%" colspan="2">
  <b><xsl:value-of select="cdash/banner/text"/></b>
  </td>
  </tr>
  </xsl:if>  
</table>

<!-- Main table -->
<br/>

<xsl:if test="string-length(cdash/upgradewarning)>0">
  <p style="color:red"><b>The current database shema doesn't match the version of CDash you are running,
    upgrade your database structure in the <a href="backwardCompatibilityTools.php">Administration/CDash maintenance panel of CDash</a></b></p>
</xsl:if>

<table border="0" cellpadding="4" cellspacing="0" width="100%" id="indexTable" class="tabb">
<thead>
<tr class="table-heading1">
  <td colspan="5" align="left" class="nob"><h3>Available Dashboards</h3></td>
</tr>

  <tr class="table-heading">
     <th align="center" id="sort_0"><b>Project</b></th>
     <td align="center"><b>Description</b></td>
     <th align="center" id="sort_1"><b>Submissions</b></th>
     <th align="center" id="sort_2"><b>First build</b></th>
     <th align="center" id="sort_3" class="nob"><b>Last activity</b></th>
  </tr>
 </thead>
 <tbody>
   <xsl:for-each select="cdash/project">
   <tr>
   <xsl:if test="active=0">
   <xsl:attribute name="class">nonactive</xsl:attribute>
   </xsl:if>
   
   <td align="center" >
     <a>
     <xsl:attribute name="href">index.php?project=<xsl:value-of select="name_encoded"/></xsl:attribute>
     <xsl:value-of select="name"/>
     </a></td>
     <td align="center"><xsl:value-of select="description"/></td>
     <td align="center"><xsl:value-of select="nbuilds"/></td>
     <td align="center"><xsl:value-of select="firstbuild"/></td>
    <td align="center" class="nob">
    <a>
    <xsl:attribute name="href">index.php?project=<xsl:value-of select="name_encoded"/>&amp;date=<xsl:value-of select="lastbuilddate"/></xsl:attribute>
    <xsl:value-of select="lastbuild"/>
    </a>
    </td>
    </tr>
   </xsl:for-each>
</tbody>
</table>
   
<table width="100%" cellspacing="0" cellpadding="0">
<tr>
<td height="1" colspan="14" align="left" bgcolor="#888888"></td>
</tr>
<tr>
<td height="1" colspan="14" align="right">
<div id="showold"><a href="#" onclick="javascript:showoldproject()">[show all projects]</a></div>
<div id="hideold"><a href="#" onclick="javascript:hideoldproject()">[hide old projects]</a></div>
</td>
</tr>
</table>


<br/>
Currently hosting <b><xsl:value-of select="count(cdash/project)"/> projects</b> (<xsl:value-of select="cdash/database/size"/>)

<br/>
<!-- FOOTER -->
<br/>
<xsl:call-template name="footer"/>
        </body>
      </html>
    </xsl:template>
</xsl:stylesheet>
