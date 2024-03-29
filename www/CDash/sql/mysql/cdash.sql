-- 
-- Host: localhost
-- Server version: 4.1.15
-- PHP Version: 5.2.3-1+b1

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";

-- 
-- Database: `cdash`
-- 

-- --------------------------------------------------------

-- 
-- Table structure for table `build`
-- 

CREATE TABLE `build` (
  `id` int(11) NOT NULL auto_increment,
  `siteid` int(11) NOT NULL default '0',
  `projectid` int(11) NOT NULL default '0',
  `stamp` varchar(255) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `type` varchar(255) NOT NULL default '',
  `generator` varchar(255) NOT NULL default '',
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `submittime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `command` text NOT NULL,
  `log` text NOT NULL,
  `builderrors` smallint(6) DEFAULT '-1',
  `buildwarnings` smallint(6) DEFAULT '-1',
  `testnotrun` smallint(6) DEFAULT '-1',
  `testfailed` smallint(6) DEFAULT '-1',
  `testpassed` smallint(6) DEFAULT '-1',
  `testtimestatusfailed` smallint(6) DEFAULT '-1',
  PRIMARY KEY  (`id`),
  KEY `projectid` (`projectid`),
  KEY `starttime` (`starttime`),
  KEY `submittime` (`submittime`),
  KEY `siteid` (`siteid`),
  KEY `stamp` (`stamp`),
  KEY `type` (`type`),
  KEY `name` (`name`)
);


CREATE TABLE `buildgroup` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `projectid` int(11) NOT NULL default '0',
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `description` text NOT NULL default '',
  `summaryemail` tinyint(4) default '0',
  `includesubprojectotal` tinyint(4) default '1',
  PRIMARY KEY  (`id`),
  KEY `projectid` (`projectid`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`) 
);

-- --------------------------------------------------------

-- 
-- Table structure for table `buildgroupposition`
-- 

CREATE TABLE `buildgroupposition` (
  `buildgroupid` int(11) NOT NULL default '0',
  `position` int(11) NOT NULL default '0',
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  KEY `buildgroupid` (`buildgroupid`),
  KEY `endtime` (`endtime`),
  KEY `starttime` (`starttime`),
  KEY `position` (`position`)
);
        


-- --------------------------------------------------------

-- 
-- Table structure for table `build2group`
-- 

CREATE TABLE `build2group` (
  `groupid` int(11) NOT NULL default '0',
  `buildid` int(11) NOT NULL default '0',
  PRIMARY KEY  (`buildid`),
  KEY `groupid` (`groupid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `build2grouprule`
-- 

CREATE TABLE `build2grouprule` (
  `groupid` int(11) NOT NULL default '0',
  `buildtype` varchar(20) NOT NULL default '',
  `buildname` varchar(255) NOT NULL default '',
  `siteid` int(11) NOT NULL default '0',
  `expected` tinyint(4) NOT NULL default '0',
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  KEY `groupid` (`groupid`),
  KEY `buildtype` (`buildtype`),
  KEY `buildname` (`buildname`),
  KEY `siteid` (`siteid`),
  KEY `expected` (`expected`),
  KEY `starttime` (`starttime`),
  KEY `endtime` (`endtime`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `builderror`
-- 

CREATE TABLE `builderror` (
  `buildid` int(11) NOT NULL default '0',
  `type` tinyint(4) NOT NULL default '0',
  `logline` int(11) NOT NULL default '0',
  `text` text NOT NULL,
  `sourcefile` varchar(255) NOT NULL default '',
  `sourceline` int(11) NOT NULL default '0',
  `precontext` text,
  `postcontext` text,
  `repeatcount` int(11) NOT NULL default '0',
  `crc32` bigint(20) NOT NULL default '0',
  `newstatus` tinyint(4) NOT NULL default '0',
  KEY `buildid` (`buildid`),
  KEY `type` (`type`),
  KEY `crc32` (`crc32`),
  KEY `newstatus` (`newstatus`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `buildupdate`
-- 

CREATE TABLE `buildupdate` (
  `buildid` int(11) NOT NULL default '0',
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `command` text NOT NULL,
  `type` varchar(4) NOT NULL default '',
  `status` text NOT NULL,
  `nfiles` smallint(6) DEFAULT '-1',
  `warnings` smallint(6) DEFAULT '-1',
  `revision` varchar(60) NOT NULL default '0',
  `priorrevision` varchar(60) NOT NULL default '0',
  `path` varchar(255) NOT NULL default '',
  KEY `buildid` (`buildid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `configure`
-- 

CREATE TABLE `configure` (
  `buildid` int(11) NOT NULL default '0',
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `command` text NOT NULL,
  `log` MEDIUMTEXT NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  `warnings` smallint(6) DEFAULT '-1',
  KEY `buildid` (`buildid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `coverage`
-- 

CREATE TABLE `coverage` (
  `buildid` int(11) NOT NULL default '0',
  `fileid` int(11) NOT NULL default '0',
  `covered` tinyint(4) NOT NULL default '0',
  `loctested` int(11) NOT NULL default '0',
  `locuntested` int(11) NOT NULL default '0',
  `branchstested` int(11) NOT NULL default '0',
  `branchsuntested` int(11) NOT NULL default '0',
  `functionstested` int(11) NOT NULL default '0',
  `functionsuntested` int(11) NOT NULL default '0',
  KEY `buildid` (`buildid`),
  KEY `fileid` (`fileid`),
  KEY `covered` (`covered`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `coveragefile`
-- 

CREATE TABLE `coveragefile` (
  `id` int(11) NOT NULL auto_increment,
  `fullpath` varchar(255) NOT NULL default '',
  `file` longblob,
  `crc32` bigint(20) default NULL,
  PRIMARY KEY  (`id`),
  KEY `fullpath` (`fullpath`),
  KEY `crc32` (`crc32`) 
);

-- --------------------------------------------------------

-- 
-- Table structure for table `coveragefilelog`
-- 

CREATE TABLE `coveragefilelog` (
  `buildid` int(11) NOT NULL default '0',
  `fileid` int(11) NOT NULL default '0',
  `line` int(11) NOT NULL default '0',
  `code` varchar(10) NOT NULL default '',
  KEY `fileid` (`fileid`),
  KEY `buildid` (`buildid`),
  KEY `line` (`line`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `coveragesummary`
-- 

CREATE TABLE `coveragesummary` (
  `buildid` int(11) NOT NULL default '0',
  `loctested` int(11) NOT NULL default '0',
  `locuntested` int(11) NOT NULL default '0',
  PRIMARY KEY  (`buildid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `dynamicanalysis`
-- 

CREATE TABLE `dynamicanalysis` (
  `id` int(11) NOT NULL auto_increment,
  `buildid` int(11) NOT NULL default '0',
  `status` varchar(10) NOT NULL default '',
  `checker` varchar(60) NOT NULL default '',
  `name` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `fullcommandline` varchar(255) NOT NULL default '',
  `log` longtext NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `buildid` (`buildid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `dynamicanalysisdefect`
-- 

CREATE TABLE `dynamicanalysisdefect` (
  `dynamicanalysisid` int(11) NOT NULL default '0',
  `type` varchar(50) NOT NULL default '',
  `value` int(11) NOT NULL default '0',
  KEY `buildid` (`dynamicanalysisid`)
);

  

-- --------------------------------------------------------

-- 
-- Table structure for table `image`
-- 

CREATE TABLE `image` (
  `id` int(11) NOT NULL auto_increment,
  `img` longblob NOT NULL,
  `extension` tinytext NOT NULL,
  `checksum` bigint(20) NOT NULL,
  PRIMARY KEY `id` (`id`),
  KEY `checksum` (`checksum`)
);


-- --------------------------------------------------------

-- 
-- Table structure for table `test2image`
-- 
CREATE TABLE `test2image` (
  `imgid` int(11) NOT NULL auto_increment,
  `testid` int(11) NOT NULL default '0',
  `role` tinytext NOT NULL,
  PRIMARY KEY  (`imgid`),
  KEY `testid` (`testid`)
) AUTO_INCREMENT=1 ;

-- --------------------------------------------------------
-- 
-- Table structure for table `note`
-- 
CREATE TABLE `note` (
  `id` bigint(20) NOT NULL auto_increment,
  `text` mediumtext NOT NULL,
  `name` varchar(255) NOT NULL,
  `crc32` bigint(20) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `crc32` (`crc32`)
) ;

-- --------------------------------------------------------

-- 
-- Table structure for table `project`
-- 

CREATE TABLE `project` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `description` text NOT NULL,
  `homeurl` varchar(255) NOT NULL default '',
  `cvsurl` varchar(255) NOT NULL default '',
  `bugtrackerurl` varchar(255) NOT NULL default '',
  `bugtrackerfileurl` varchar(255) NOT NULL DEFAULT '',
  `documentationurl` varchar(255) NOT NULL default '',
  `imageid` int(11) NOT NULL default '0',
  `public` tinyint(4) NOT NULL default '1',
  `coveragethreshold` smallint(6) NOT NULL default '70',
  `nightlytime` varchar(50) NOT NULL default '00:00:00',
  `googletracker` varchar(50) NOT NULL default '',
  `emaillowcoverage` tinyint(4) NOT NULL default '0',
  `emailtesttimingchanged` tinyint(4) NOT NULL default '0',
  `emailbrokensubmission` tinyint(4) NOT NULL default '1',
  `emailredundantfailures` tinyint(4) NOT NULL default '0',
  `emailadministrator` tinyint(4) NOT NULL default '1',
  `showipaddresses` tinyint(4) NOT NULL default '1',
  `cvsviewertype` varchar(10) default NULL,
  `testtimestd` float(3,1) default '4.0',
  `testtimestdthreshold` float(3,1) default '1.0',
  `showtesttime` tinyint(4) default '0',
  `testtimemaxstatus` tinyint(4) default '3',
  `emailmaxitems` tinyint(4) default '5',
  `emailmaxchars` int(11) default '255',
  `displaylabels` tinyint(4) default '1',
  `autoremovetimeframe` int(11) default '0',
  `autoremovemaxbuilds` int(11) default '300',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`),
  KEY `public` (`public`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `site`
-- 

CREATE TABLE `site` (
  `id` int(11) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL default '',
  `ip` varchar(255) NOT NULL default '',
  `latitude` varchar(10) NOT NULL default '',
  `longitude` varchar(10) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `name` (`name`)
) ;

-- 
-- Table structure for table `siteinformation`
-- 

CREATE TABLE `siteinformation` (
  `siteid` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL default '1980-01-01 00:00:00',
  `processoris64bits` tinyint(4) NOT NULL default '-1',
  `processorvendor` varchar(255) NOT NULL default 'NA',
  `processorvendorid` varchar(255) NOT NULL default 'NA',
  `processorfamilyid` int(11) NOT NULL default '-1',
  `processormodelid` int(11) NOT NULL default '-1',
  `processorcachesize` int(11) NOT NULL default '-1',
  `numberlogicalcpus` tinyint(4) NOT NULL default '-1',
  `numberphysicalcpus` tinyint(4) NOT NULL default '-1',
  `totalvirtualmemory` int(11) NOT NULL default '-1',
  `totalphysicalmemory` int(11) NOT NULL default '-1',
  `logicalprocessorsperphysical` int(11) NOT NULL default '-1',
  `processorclockfrequency` int(11) NOT NULL default '-1',
  `description` varchar(255) NOT NULL default 'NA',
  KEY `siteid` (`siteid`,`timestamp`)
);


CREATE TABLE `buildinformation` (
  `buildid` int(11) NOT NULL,
  `osname` varchar(255) NOT NULL,
  `osplatform` varchar(255) NOT NULL,
  `osrelease` varchar(255) NOT NULL,
  `osversion` varchar(255) NOT NULL,
  `compilername` varchar(255) NOT NULL,
  `compilerversion` varchar(20) NOT NULL,
  PRIMARY KEY  (`buildid`)
);


-- --------------------------------------------------------

-- 
-- Table structure for table `site2user`
-- 

CREATE TABLE `site2user` (
  `siteid` int(11) NOT NULL default '0',
  `userid` int(11) NOT NULL default '0',
  KEY `siteid` (`siteid`),
  KEY `userid` (`userid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `test`
-- 
CREATE TABLE `test` (
  `id` int(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `crc32` bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL default '',
  `path` varchar(255) NOT NULL default '',
  `command` text NOT NULL,
  `details` text NOT NULL,
  `output` MEDIUMBLOB NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `projectid` (`projectid`),
  KEY `crc32` (`crc32`),
  KEY `name` (`name`)
);

-- 
-- Table structure for table `build2test`
--
CREATE TABLE `build2test` (
  `buildid` int(11) NOT NULL default '0',
  `testid` int(11) NOT NULL default '0',
  `status` varchar(10) NOT NULL default '',
  `time` float(7,2) NOT NULL default '0.00',
  `timemean` float(7,2) NOT NULL default '0.00',
  `timestd` float(7,2) NOT NULL default '0.00',
  `timestatus` tinyint(4) NOT NULL default '0',
  `newstatus` tinyint(4) NOT NULL default '0',
  KEY `buildid` (`buildid`),
  KEY `testid` (`testid`),
  KEY `status` (`status`),
  KEY `timestatus` (`timestatus`),
  KEY `newstatus` (`newstatus`)
);

--
-- Table structure for table `buildtesttime`
--

CREATE TABLE `buildtesttime` (
  `buildid` int(11) NOT NULL default '0',
  `time` float(7,2) NOT NULL default '0.00',
  PRIMARY KEY `buildid` (`buildid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `updatefile`
-- 

CREATE TABLE `updatefile` (
  `buildid` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `checkindate` timestamp NOT NULL default '1980-01-01 00:00:00',
  `author` varchar(255) NOT NULL default '',
  `email` varchar(255) NOT NULL default '',
  `log` text NOT NULL,
  `revision` varchar(60) NOT NULL default '0',
  `priorrevision` varchar(60) NOT NULL default '0',
  `status` varchar(12) NOT NULL default '',
  KEY `buildid` (`buildid`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `user`
-- 

CREATE TABLE `user` (
  `id` int(11) NOT NULL auto_increment,
  `email` varchar(255) NOT NULL default '',
  `password` varchar(40) NOT NULL default '',
  `firstname` varchar(40) NOT NULL default '',
  `lastname` varchar(40) NOT NULL default '',
  `institution` varchar(255) NOT NULL default '',
  `admin` tinyint(4) NOT NULL default '0',
  `cookiekey` varchar(40) NOT NULL default '',
  PRIMARY KEY  (`id`),
  KEY `email` (`email`)
);

-- --------------------------------------------------------

-- 
-- Table structure for table `user2project`
-- 

CREATE TABLE `user2project` (
  `userid` int(11) NOT NULL default '0',
  `projectid` int(11) NOT NULL default '0',
  `role` int(11) NOT NULL default '0',
  `cvslogin` varchar(50) NOT NULL default '',
  `emailtype` tinyint(4) NOT NULL default '0',
  `emailcategory` tinyint(4) NOT NULL default '62',
  `emailsuccess` tinyint(4) NOT NULL default '0',
  `emailmissingsites` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`userid`,`projectid`),
  KEY `cvslogin` (`cvslogin`),
  KEY `emailtype` (`emailtype`),
  KEY `emailsucess` (`emailsuccess`),
  KEY `emailmissingsites` (`emailmissingsites`)
);

-- 
-- Table structure for table `buildnote`
-- 
CREATE TABLE `buildnote` (
  `buildid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `note` mediumtext NOT NULL,
  `timestamp` datetime NOT NULL,
  `status` tinyint(4) NOT NULL default '0',
  KEY `buildid` (`buildid`)
);

-- --------------------------------------------------------

--
-- Table structure for table `repositories`
--

CREATE TABLE `repositories` (
  `id` int(11) NOT NULL auto_increment,
  `url` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL default '',
  `password` varchar(50) NOT NULL default '',
  PRIMARY KEY  (`id`)
);

-- --------------------------------------------------------

--
-- Table structure for table `project2repositories`
--

CREATE TABLE `project2repositories` (
  `projectid` int(11) NOT NULL,
  `repositoryid` int(11) NOT NULL,
  PRIMARY KEY  (`projectid`,`repositoryid`)
);


-- --------------------------------------------------------
CREATE TABLE `testmeasurement` (
  `testid` bigint(20) NOT NULL,
  `name` varchar(70) NOT NULL,
  `type` varchar(70) NOT NULL,
  `value` text NOT NULL,
  KEY `testid` (`testid`)
);



CREATE TABLE `dailyupdate` (
  `id` bigint(11) NOT NULL auto_increment,
  `projectid` int(11) NOT NULL,
  `date` date NOT NULL,
  `command` text NOT NULL,
  `type` varchar(4) NOT NULL default '',
  `status` tinyint(4) NOT NULL default '0',
  PRIMARY KEY (`id`),
  KEY `date` (`date`),
  KEY `projectid` (`projectid`)
);


CREATE TABLE `dailyupdatefile` (
  `dailyupdateid` int(11) NOT NULL default '0',
  `filename` varchar(255) NOT NULL default '',
  `checkindate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `author` varchar(255) NOT NULL default '',
  `log` text NOT NULL,
  `revision` varchar(60) NOT NULL default '0',
  `priorrevision` varchar(60) NOT NULL default '0',
  KEY `dailyupdateid` (`dailyupdateid`),
  KEY `author` (`author`)
);


CREATE TABLE `builderrordiff` (
  `buildid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `difference_positive` int(11) NOT NULL,
  `difference_negative` int(11) NOT NULL,
  KEY `buildid` (`buildid`),
  KEY `difference_positive` (`difference_positive`),
  KEY `difference_negative` (`difference_negative`)
);

CREATE TABLE `testdiff` (
  `buildid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `difference_positive` int(11) NOT NULL,
  `difference_negative` int(11) NOT NULL,
  KEY `buildid` (`buildid`,`type`),
  KEY `difference_positive` (`difference_positive`),
  KEY `difference_negative` (`difference_negative`)
);

CREATE TABLE `build2note` (
  `buildid` bigint(20) NOT NULL,
  `noteid`  bigint(20) NOT NULL,
  `time` timestamp NOT NULL default '1980-01-01 00:00:00',
  KEY `buildid` (`buildid`),
  KEY `noteid` (`noteid`)
);


CREATE TABLE `userstatistics` (
  `userid` int(11) NOT NULL,
  `projectid` smallint(6) NOT NULL,
  `checkindate` timestamp NOT NULL default CURRENT_TIMESTAMP,
  `totalupdatedfiles` bigint(20) NOT NULL,
  `totalbuilds` bigint(20) NOT NULL,
  `nfixedwarnings` bigint(20) NOT NULL,
  `nfailedwarnings` bigint(20) NOT NULL,
  `nfixederrors` bigint(20) NOT NULL,
  `nfailederrors` bigint(20) NOT NULL,
  `nfixedtests` bigint(20) NOT NULL,
  `nfailedtests` bigint(20) NOT NULL,
  KEY `userid` (`userid`),
  KEY `projectid` (`projectid`),
  KEY `checkindate` (`checkindate`)
);

CREATE TABLE `version` (
  `major` tinyint(4) NOT NULL,
  `minor` tinyint(4) NOT NULL,
  `patch` tinyint(4) NOT NULL
);


CREATE TABLE `summaryemail` (
  `buildid` bigint(20) NOT NULL,
  `date` date NOT NULL,
  `groupid` smallint(6) NOT NULL,
  KEY `date` (`date`),
  KEY `groupid` (`groupid`)
);


CREATE TABLE `configureerror` (
  `buildid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `text` text NOT NULL,
  KEY `buildid` (`buildid`),
  KEY `type` (`type`)
);


CREATE TABLE `configureerrordiff` (
  `buildid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `difference` int(11) NOT NULL,
  KEY `buildid` (`buildid`),
  KEY `type` (`type`)
);


CREATE TABLE `coveragesummarydiff` (
  `buildid` bigint(20) NOT NULL,
  `loctested` int(11) NOT NULL default '0',
  `locuntested` int(11) NOT NULL default '0',
  PRIMARY KEY  (`buildid`)
);


CREATE TABLE `banner` (
  `projectid` int(11) NOT NULL,
  `text` varchar(500) NOT NULL,
  PRIMARY KEY  (`projectid`)
);


CREATE TABLE `coveragefile2user` (
  `fileid` bigint(20) NOT NULL,
  `userid` bigint(20) NOT NULL,
  `position` tinyint(4) NOT NULL,
  KEY `coveragefileid` (`fileid`),
  KEY `userid` (`userid`)
);


CREATE TABLE IF NOT EXISTS `label` (
  `id` bigint(20) NOT NULL auto_increment,
  `text` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `text` (`text`)
);

-- 
-- Table structure for table `label2build`
-- 
CREATE TABLE `label2build` (
  `labelid` bigint(20) NOT NULL,
  `buildid` bigint(20) NOT NULL,
  PRIMARY KEY (`labelid`,`buildid`)
);

-- 
-- Table structure for table `label2buildfailure`
-- 
CREATE TABLE `label2buildfailure` (
  `labelid` bigint(20) NOT NULL,
  `buildfailureid` bigint(20) NOT NULL,
  PRIMARY KEY (`labelid`,`buildfailureid`)
);

-- 
-- Table structure for table `label2coveragefile`
-- 
CREATE TABLE `label2coveragefile` (
  `labelid` bigint(20) NOT NULL,
  `buildid` bigint(20) NOT NULL,
  `coveragefileid` bigint(20) NOT NULL,
  PRIMARY KEY (`labelid`,`buildid`,`coveragefileid`)
);

-- 
-- Table structure for table `label2dynamicanalysis`
-- 
CREATE TABLE `label2dynamicanalysis` (
  `labelid` bigint(20) NOT NULL,
  `dynamicanalysisid` bigint(20) NOT NULL,
  PRIMARY KEY (`labelid`,`dynamicanalysisid`)
);


-- 
-- Table structure for table `label2test`
-- 
CREATE TABLE `label2test` (
  `labelid` bigint(20) NOT NULL,
  `buildid` bigint(20) NOT NULL,
  `testid` bigint(20) NOT NULL,
  PRIMARY KEY (`labelid`,`buildid`,`testid`)
);

-- 
-- Table structure for table `label2update`
-- 
CREATE TABLE `label2update` (
  `labelid` bigint(20) NOT NULL,
  `updateid` bigint(20) NOT NULL,
  PRIMARY KEY (`labelid`,`updateid`)
);


-- 
-- Table structure for table `subproject`
-- 
CREATE TABLE `subproject` (
  `id` bigint(20) NOT NULL auto_increment,
  `name` varchar(255) NOT NULL,
  `projectid` int(11) NOT NULL,
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  PRIMARY KEY  (`id`),
  KEY `projectid` (`projectid`)
);


CREATE TABLE `subproject2subproject` (
  `subprojectid` int(11) NOT NULL,
  `dependsonid` int(11) NOT NULL,
  `starttime` timestamp NOT NULL default '1980-01-01 00:00:00',
  `endtime` timestamp NOT NULL default '1980-01-01 00:00:00',
  KEY `subprojectid` (`subprojectid`),
  KEY `dependsonid` (`dependsonid`)
);


CREATE TABLE `subproject2build` (
  `subprojectid` int(11) NOT NULL,
  `buildid` bigint(20) NOT NULL,
  PRIMARY KEY  (`buildid`),
  KEY `subprojectid` (`subprojectid`)
);



CREATE TABLE `buildfailure` (
  `id` bigint(20) NOT NULL auto_increment,
  `buildid` bigint(20) NOT NULL,
  `type` tinyint(4) NOT NULL,
  `workingdirectory` varchar(255) NOT NULL,
  `stdoutput` text NOT NULL,
  `stderror` text NOT NULL,
  `exitcondition` varchar(255) NOT NULL,
  `language` varchar(64) NOT NULL,
  `targetname` varchar(255) NOT NULL,
  `outputfile` varchar(255) NOT NULL,
  `outputtype` varchar(255) NOT NULL,
  `sourcefile` varchar(512) NOT NULL,
  `crc32` bigint(20) NOT NULL default '0',
  `newstatus` tinyint(4) NOT NULL default '0',
  PRIMARY KEY  (`id`),
  KEY `buildid` (`buildid`),
  KEY `type` (`type`),
  KEY `crc32` (`crc32`),
  KEY `newstatus` (`newstatus`)
);


CREATE TABLE  `buildfailureargument` (
  `id` bigint(20) NOT NULL auto_increment,
  `argument` varchar(255) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `argument` (`argument`)
);

CREATE TABLE  `buildfailure2argument` (
  `buildfailureid` bigint(20) NOT NULL,
  `argumentid` bigint(20) NOT NULL,
  `place` int(11) NOT NULL default '0',
  KEY `argumentid` (`argumentid`),
  KEY `buildfailureid` (`buildfailureid`)
);


CREATE TABLE `labelemail` (
  `projectid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `labelid` bigint(20) NOT NULL,
  KEY `projectid` (`projectid`),
  KEY `userid` (`userid`)
);


CREATE TABLE `buildemail` (
  `userid` int(11) NOT NULL,
  `buildid` bigint(20) NOT NULL,
  `category` tinyint(4) NOT NULL,
  `time` timestamp NOT NULL default '1980-01-01 00:00:00',
  KEY `userid` (`userid`),
  KEY `buildid` (`buildid`),
  KEY `category` (`category`)
);


CREATE TABLE IF NOT EXISTS `coveragefilepriority` (
  `id` bigint(20) NOT NULL auto_increment,
  `priority` tinyint(4) NOT NULL,
  `fullpath` varchar(255) NOT NULL,
  `projectid` int(11) NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `priority` (`priority`),
  KEY `fullpath` (`fullpath`),
  KEY `projectid` (`projectid`)
);


CREATE TABLE IF NOT EXISTS `submission` (
  `id` bigint(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(500) NOT NULL,
  `projectid` int(11) NOT NULL,
  `status` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectid` (`projectid`),
  KEY `status` (`status`)
);


CREATE TABLE IF NOT EXISTS `blockbuild` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `projectid` int(11) NOT NULL,
  `buildname` varchar(255) NOT NULL,
  `sitename` varchar(255) NOT NULL,
  `ipaddress` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `projectid` (`projectid`),
  KEY `buildname` (`buildname`),
  KEY `sitename` (`sitename`),
  KEY `ipaddress` (`ipaddress`)
);


-- --------------------------------------------------------

--
-- Table structure for table 'client_cmake'
--

CREATE TABLE IF NOT EXISTS client_cmake (
  id int(11) NOT NULL AUTO_INCREMENT,
  version varchar(255) NOT NULL,
  PRIMARY KEY (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_compiler'
--

CREATE TABLE IF NOT EXISTS client_compiler (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  version varchar(255) NOT NULL,
  PRIMARY KEY (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_job'
--

CREATE TABLE IF NOT EXISTS client_job (
  id bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  scheduleid bigint(20) NOT NULL,
  osid tinyint(4) NOT NULL,
  siteid int(11) DEFAULT NULL,
  startdate timestamp NOT NULL DEFAULT '1980-01-01 00:00:00',
  enddate timestamp NOT NULL DEFAULT '1980-01-01 00:00:00',
  `status` int(11) DEFAULT NULL,
  output text,
  cmakeid int(11) NOT NULL,
  compilerid int(11) NOT NULL,
  UNIQUE KEY id (id),
  KEY scheduleid (scheduleid),
  KEY startdate (startdate),
  KEY enddate (enddate),
  KEY `status` (`status`)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule'
--

CREATE TABLE IF NOT EXISTS client_jobschedule (
  `id` bigint(20) unsigned NOT NULL auto_increment,
  `userid` int(11) default NULL,
  `projectid` int(11) default NULL,
  `cmakecache` mediumtext NOT NULL,
  `startdate` timestamp NOT NULL default '1980-01-01 00:00:00',
  `enddate` timestamp NOT NULL default '1980-01-01 00:00:00',
  `type` tinyint(4) NOT NULL,
  `starttime` time NOT NULL default '00:00:00',
  `repeattime` decimal(3,2) NOT NULL default '0.00',
  `enable` tinyint(4) NOT NULL,
  `lastrun` timestamp NOT NULL default '1980-01-01 00:00:00',
  `repository` varchar(512) default '',
  `module` varchar(255) default '',
  `buildnamesuffix` varchar(255) default '',
  `tag` varchar(255) default '',
  UNIQUE KEY `id` (`id`),
  KEY `userid` (`userid`),
  KEY `projectid` (`projectid`),
  KEY `enable` (`enable`),
  KEY `starttime` (`starttime`),
  KEY `repeattime` (`repeattime`)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule2cmake'
--

CREATE TABLE IF NOT EXISTS client_jobschedule2cmake (
  scheduleid bigint(20) NOT NULL,
  cmakeid int(11) NOT NULL,
  UNIQUE KEY scheduleid (scheduleid,cmakeid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule2compiler'
--

CREATE TABLE IF NOT EXISTS client_jobschedule2compiler (
  scheduleid bigint(20) NOT NULL,
  compilerid int(11) NOT NULL,
  UNIQUE KEY scheduleid (scheduleid,compilerid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule2library'
--

CREATE TABLE IF NOT EXISTS client_jobschedule2library (
  scheduleid bigint(20) NOT NULL,
  libraryid int(11) NOT NULL,
  UNIQUE KEY scheduleid (scheduleid,libraryid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule2os'
--

CREATE TABLE IF NOT EXISTS client_jobschedule2os (
  scheduleid bigint(20) NOT NULL,
  osid int(11) NOT NULL,
  UNIQUE KEY scheduleid (scheduleid,osid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule2site'
--

CREATE TABLE IF NOT EXISTS client_jobschedule2site (
  scheduleid bigint(20) NOT NULL,
  siteid int(11) NOT NULL,
  UNIQUE KEY scheduleid (scheduleid,siteid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_jobschedule2toolkit'
--

CREATE TABLE IF NOT EXISTS client_jobschedule2toolkit (
  scheduleid bigint(20) NOT NULL,
  toolkitconfigurationid int(11) NOT NULL,
  UNIQUE KEY scheduleid (scheduleid,toolkitconfigurationid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_library'
--

CREATE TABLE IF NOT EXISTS client_library (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  version varchar(255) NOT NULL,
  PRIMARY KEY (id)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_os'
--

CREATE TABLE IF NOT EXISTS client_os (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  version varchar(255) NOT NULL,
  bits tinyint(4) NOT NULL DEFAULT '32',
  PRIMARY KEY (id),
  KEY `name` (`name`),
  KEY version (version),
  KEY bits (bits)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_site'
--

CREATE TABLE IF NOT EXISTS client_site (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) DEFAULT NULL,
  osid int(11) DEFAULT NULL,
  systemname varchar(255) DEFAULT NULL,
  `host` varchar(255) DEFAULT NULL,
  basedirectory varchar(512) NOT NULL,
  PRIMARY KEY (id),
  KEY `name` (`name`),
  KEY system (osid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_site2cmake'
--

CREATE TABLE IF NOT EXISTS client_site2cmake (
  siteid int(11) DEFAULT NULL,
  cmakeid int(11) DEFAULT NULL,
  path varchar(512) DEFAULT NULL,
  KEY siteid (siteid),
  KEY version (cmakeid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_site2compiler'
--

CREATE TABLE IF NOT EXISTS client_site2compiler (
  siteid int(11) DEFAULT NULL,
  compilerid int(11) DEFAULT NULL,
  command varchar(512) DEFAULT NULL,
  generator varchar(255) NOT NULL,
  KEY siteid (siteid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_site2library'
--

CREATE TABLE IF NOT EXISTS client_site2library (
  siteid int(11) DEFAULT NULL,
  libraryid int(11) DEFAULT NULL,
  path varchar(512) DEFAULT NULL,
  include varchar(512) NOT NULL,
  KEY siteid (siteid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_toolkit'
--

CREATE TABLE IF NOT EXISTS client_toolkit (
  id int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  projectid bigint(20) NOT NULL DEFAULT '0',
  PRIMARY KEY (id),
  KEY projectid (projectid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_toolkitconfiguration'
--

CREATE TABLE IF NOT EXISTS client_toolkitconfiguration (
  id int(11) NOT NULL AUTO_INCREMENT,
  toolkitversionid bigint(20) NOT NULL,
  `name` varchar(255) NOT NULL,
  cmakecache text,
  environment text,
  binarypath varchar(512) NOT NULL,
  PRIMARY KEY (id),
  KEY `name` (`name`),
  KEY binarypath (binarypath)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_toolkitconfiguration2os'
--

CREATE TABLE IF NOT EXISTS client_toolkitconfiguration2os (
  toolkitconfigurationid bigint(20) NOT NULL,
  osid int(11) NOT NULL,
  KEY toolkitconfigurationid (toolkitconfigurationid),
  KEY osid (osid)
);

-- --------------------------------------------------------

--
-- Table structure for table 'client_toolkitversion'
--

CREATE TABLE IF NOT EXISTS client_toolkitversion (
  id int(11) NOT NULL AUTO_INCREMENT,
  toolkitid int(11) NOT NULL,
  `name` varchar(10) NOT NULL,
  repositoryurl varchar(255) NOT NULL,
  repositorytype tinyint(4) NOT NULL,
  repositorymodule varchar(100) NOT NULL,
  tag varchar(30) DEFAULT NULL,
  sourcepath varchar(512) NOT NULL,
  ctestprojectname varchar(50) DEFAULT NULL,
  PRIMARY KEY (id),
  KEY toolkitid (toolkitid),
  KEY version (`name`)
);


--
-- Table structure for table `projectrobot`
--

CREATE TABLE IF NOT EXISTS `projectrobot` (
  `projectid` int(11) NOT NULL,
  `robotname` varchar(255) NOT NULL,
  `authorregex` varchar(512) NOT NULL,
  KEY `projectid` (`projectid`),
  KEY `robotname` (`robotname`)
);

--
-- Table structure for table `filesum`
--

CREATE TABLE IF NOT EXISTS `filesum` (
  `id` int(11) NOT NULL auto_increment,
  `md5sum` varchar(32) NOT NULL,
  `contents` longblob,
  PRIMARY KEY `id` (`id`),
  KEY `md5sum` (`md5sum`)
);

--
-- Change the table maximum size to be more than 4GB
-- 
alter table test max_rows = 200000000000 avg_row_length = 3458;
alter table builderror max_rows = 200000000000 avg_row_length = 3458;
alter table coverage max_rows = 200000000000 avg_row_length = 3458;
alter table coveragefilelog max_rows = 200000000000 avg_row_length = 3458;
alter table coveragefile max_rows = 200000000000 avg_row_length = 3458;
alter table image max_rows = 200000000000 avg_row_length = 3458;
alter table note max_rows = 200000000000 avg_row_length = 3458;
alter table buildnote max_rows = 200000000000 avg_row_length = 3458;
