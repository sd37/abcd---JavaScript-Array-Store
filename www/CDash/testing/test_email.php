<?php
// kwtest library
require_once('kwtest/kw_web_tester.php');
require_once('kwtest/kw_db.php');

class EmailTestCase extends KWWebTestCase
{
  var $url           = null;
  var $db            = null;
  var $projecttestid = null;
  var $logfilename   = null;
  
  function __construct()
    {
    parent::__construct();
    require('config.test.php');
    $this->url = $configure['urlwebsite'];
    $this->db  =& new database($db['type']);
    $this->db->setDb($db['name']);
    $this->db->setHost($db['host']);
    $this->db->setUser($db['login']);
    $this->db->setPassword($db['pwd']);
    $this->logfilename = $cdashpath."/backup/cdash.log";
    }
   
  function testCreateProjectTest()
    {
    $this->login();
    // first project necessary for testing
    $name = 'EmailProjectExample';
    $description = 'Project EmailProjectExample test for cdash testing';

    $this->clickLink('[Create new project]');
    $this->setField('name',$name);
    $this->setField('description',$description);
    $this->setField('public','1');
    $this->setField('emailBrokenSubmission','1');
    $this->setField('emailRedundantFailures','0');  
    $this->clickSubmitByName('Submit');
        
    $content = $this->connect($this->url.'/index.php?project=EmailProjectExample');
    if(!$content)
      {
      return;
      }
    $this->assertText('EmailProjectExample Dashboard');
    if(!$this->checkLog($this->logfilename))
      {
      return;  
      }
    }
  
  function testRegisterUser()
    {
    $url = $this->url.'/register.php';
    $content = $this->connect($url);
    if($content == false)
      {
      return;
      }

    $this->setField('fname','Firstname');
    $this->setField('lname','Lastname');
    $this->setField('email','user1@kw');
    $this->setField('passwd','user1');
    $this->setField('passwd2','user1');
    $this->setField('institution','Kitware Inc');
    $this->clickSubmitByName('sent',array('url' => 'catchbot'));
    $this->assertText('Registration Complete. Please login with your email and password.');
    
    // Login as the user and subscribe to the project
    $this->get($this->url);
    $this->clickLink('Login');
    $this->setField('login','user1@kw');
    $this->setField('passwd','user1');
    $this->clickSubmitByName('sent');
    
    $this->clickLink('[Subscribe to this project]');
    $this->setField('cvslogin','user1kw');
    $this->setField('emailsuccess','1');
    $this->clickSubmitByName('subscribe');
    if(!$this->checkLog($this->logfilename))
      {
      return;  
      }
    }

  function testSubmissionFirstBuild()
    {
    $this->deleteLog($this->logfilename); 
    $rep  = dirname(__FILE__)."/data/EmailProjectExample";
    $file = "$rep/1_configure.xml";
    if(!$this->submission('EmailProjectExample',$file))
      {
      return;
      }
    $file = "$rep/1_build.xml";
    if(!$this->submission('EmailProjectExample',$file))
      {
      return;
      }
    $file = "$rep/1_test.xml";
    if(!$this->submission('EmailProjectExample',$file))
      {
      return;
      }
    if(!$this->checkLog($this->logfilename))
      {
      return; 
      }
    $this->pass("Submission of $file has succeeded");  
    }  
    
  function testSubmissionEmailBuild()
    {
    $this->deleteLog($this->logfilename);
    $rep  = dirname(__FILE__)."/data/EmailProjectExample";
    $file = "$rep/2_build.xml";
    if(!$this->submission('EmailProjectExample',$file))
      { 
      return;
      }

    $file = "$rep/2_update.xml";
    if(!$this->submission('EmailProjectExample',$file))
      { 
      return;
      }  
      
    if(!$this->compareLog($this->logfilename,$rep."/cdash_1.log")) {return;}
    $this->pass("Passed");
    }
  
  function testSubmissionEmailTest()
    {  
    $this->deleteLog($this->logfilename);
    $rep  = dirname(__FILE__)."/data/EmailProjectExample";
    $file = "$rep/2_test.xml";
    if(!$this->submission('EmailProjectExample',$file))
      {
      return;
      }
    if(!$this->compareLog($this->logfilename,$rep."/cdash_2.log")) {return;}
    $this->pass("Passed");
    }  
}

?>
