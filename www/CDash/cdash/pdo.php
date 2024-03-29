<?php
/*=========================================================================

  Program:   CDash - Cross-Platform Dashboard System
  Module:    $Id: pdo.php 2242 2010-02-21 17:52:17Z jjomier $
  Language:  PHP
  Date:      $Date: 2010-02-21 17:52:17 +0000 (Sun, 21 Feb 2010) $
  Version:   $Revision: 2242 $

  Copyright (c) 2002 Kitware, Inc.  All rights reserved.
  See Copyright.txt or http://www.cmake.org/HTML/Copyright.html for details.

     This software is distributed WITHOUT ANY WARRANTY; without even 
     the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR 
     PURPOSE.  See the above copyright notices for more information.

=========================================================================*/
@include_once("cdash/config.php");

/** */
function pdo_connect($server = NULL, $username = NULL, $password = NULL, $new_link = false, $client_flags = 0)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    global $db_server, $db_username, $db_password;
    // using PDO, we cannot connect without a database name,
    // so store the information for later use:
    $db_server = $server;
    $db_username = $username;
    $db_password = $password;
    return true;
    }
  else 
    {
    global $last_link;
    if(!isset($server)) $server = ini_get("mysql.default_host");
    if(!isset($username)) $username = ini_get("mysql.default_user");
    if(!isset($password)) $password = ini_get("mysql.default_password");
    $last_link = mysql_connect($server, $username, $password, $new_link, $client_flags);
    return $last_link;
    }
}

/** */
function get_link_identifier($link_identifier=NULL)
{
  global $CDASH_DB_TYPE;
  global $last_link;

  if(isset($link_identifier))
    return $link_identifier;

  if(isset($last_link))
    return $last_link;

  $last_link = pdo_connect();
  return $last_link;
}

/** */
function pdo_error($link_identifier = NULL)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    $error_info = get_link_identifier($link_identifier)->errorInfo();
    if(isset($error_info[2]))
      {
      return $error_info[2];
      }
    else
      {
      return ""; // no error;
      }   
    }
  else 
    {
    return mysql_error(get_link_identifier($link_identifier));
    }
}

/** Return true if the given index exists for the column */
function pdo_check_index_exists($tablename,$columnname)
{
  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    echo "NOT IMPLEMENTED";
    return false;
    }
  else 
    {
    $query = pdo_query("SHOW INDEX FROM ".$tablename);
    if($query)
      {
      while($index_array = pdo_fetch_array($query))
        {
        if($index_array['Column_name'] == $columnname)
          {
          return true;
          }
        }
      }
    }
  return false;
}


/** */
function pdo_fetch_array($result, $result_type = MYSQL_BOTH)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    if    ($result_type == MYSQL_BOTH)  $result_type = PDO::FETCH_BOTH;
    elseif($result_type == MYSQL_NUM)   $result_type = PDO::FETCH_NUM;
    elseif($result_type == MYSQL_ASSOC) $result_type = PDO::FETCH_ASSOC;
    return $result->fetch($result_type);
    }
  else 
    {
    return mysql_fetch_array($result, $result_type);
    }
}

/** */
function pdo_fetch_row($result)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    return pdo_fetch_array($result, PDO::FETCH_NUM);
    }
  else 
    {
    return mysql_fetch_row($result);
    }
}

/** */
function pdo_field_type($result, $field_offset)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    // TODO...
    }
  else 
    {
    return mysql_field_type($result, $field_offset);
    }
}

/** */
function pdo_field_len($result, $field_offset)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    // TODO...
    }
  else 
    {
    return mysql_field_len($result, $field_offset);
    }
}

/** */
function pdo_free_result($result)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    // TODO...
    }
  else 
    {
    return mysql_free_result($result);
    }
}

/** */
function pdo_insert_id($tablename)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    $seq  = "";
    // pgsql requires the sequence name
    if($CDASH_DB_TYPE == "pgsql")
      {
      $seq = $tablename."_id_seq";
      }
    return get_link_identifier(NULL)->lastInsertId($seq);
    }
  else 
    {
    return mysql_insert_id(get_link_identifier());
    }
}

/** */
function pdo_num_rows($result)
{
  if(!$result) 
    {
    return false;
    }
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    return $result->rowCount();
    }
  else 
    {
    return mysql_num_rows($result);
    }
}

/** */
function pdo_query($query, $link_identifier = NULL)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE)  && $CDASH_DB_TYPE!="mysql") 
    {
    return get_link_identifier($link_identifier)->query($query);
    }
  else 
    {
    return mysql_query($query, get_link_identifier($link_identifier));
    }
}

/** */
function pdo_real_escape_string($unescaped_string, $link_identifier = NULL)
{
  global $CDASH_DB_TYPE;
    
  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    $str = get_link_identifier($link_identifier)->quote($unescaped_string);
    // in contrast to mysql_real_escape_string(),
    // PDO::quote() also adds the enclosing quotes,
    // which need to be removed:
    return substr($str, 1, strlen($str) - 2);
    }
  else 
    { 
    return mysql_real_escape_string($unescaped_string, get_link_identifier($link_identifier));
    }
}

/** */
function pdo_real_escape_numeric($unescaped_string, $link_identifier = NULL)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE=="pgsql" && $unescaped_string=="") {
    // MySQL interprets an empty string as zero when assigned to a numeric field,
    // for PostgreSQL this must be done explicitly:
    $unescaped_string = "0";
  }

  return pdo_real_escape_string($unescaped_string, $link_identifier);
}

/** */
function pdo_select_db($database_name, &$link_identifier)
{
  global $CDASH_DB_TYPE;

  if(isset($CDASH_DB_TYPE) && $CDASH_DB_TYPE!="mysql") 
    {
    global $db_server, $db_username, $db_password, $last_link;
    $dsn = "$CDASH_DB_TYPE:host=$db_server;dbname=$database_name";

    try 
      {
      $last_link = $link_identifier = new PDO($dsn, $db_username, $db_password);
      if($CDASH_DB_TYPE == "mysql") // necessary for looping through rows
        {
        $link_identifier->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        }
      pdo_query("SET client_encoding to 'UTF8'");
      return true;
      }
    catch(PDOException $e) 
      {
      //print_r($e); // Add debug information
      return false;
      }
    }
  else 
    {
    $ln = get_link_identifier($link_identifier);
    $db = mysql_select_db($database_name, $ln);
    if (PHP_VERSION >= 5.3)
      {
      mysql_set_charset('utf8', $ln);
      }
    else
      {
      mysql_query("SET NAMES 'utf8'");  
      }  
    return $db;
    }
}


// pdo_single_row_query returns a single row. Useful for SELECT
// queries that are expected to return 0 or 1 rows.
//
function pdo_single_row_query($qry)
{
  $result = pdo_query($qry);
  if (FALSE === $result)
  {
    add_log('error: pdo_query failed: ' . pdo_error(),
      'pdo_single_row_query');
    return array();
  }

  $num_rows = pdo_num_rows($result);
  if (0 !== $num_rows && 1 !== $num_rows)
  {
    add_log('error: at most 1 row should be returned, not ' . $num_rows,
      'pdo_single_row_query');
    add_log('warning: returning the first row anyway even though result ' .
      'contains ' . $num_rows . ' rows', 'pdo_single_row_query');
  }

  $row = pdo_fetch_array($result);
  pdo_free_result($result);

  return $row;
}


// pdo_all_rows_query returns all rows. Useful for SELECT
// queries that return any number of rows. Only use
// this one on queries expected to return small result
// sets.
//
function pdo_all_rows_query($qry)
{
  $result = pdo_query($qry);
  if (FALSE === $result)
  {
    add_log('error: pdo_query failed: ' . pdo_error(), 'pdo_all_rows_query');
    return array();
  }

  $all_rows = array();
  while ($row = pdo_fetch_array($result))
    {
    $all_rows[] = $row;
    }
  pdo_free_result($result);

  return $all_rows;
}


// pdo_get_field_value executes the given query, expected to return 0 rows
// or 1 row. If it gets a row, it retrieves the value of the named field
// and returns it. Otherwise, it returns the passed in default value.
//
function pdo_get_field_value($qry, $fieldname, $default)
{
  $row = pdo_single_row_query($qry);

  if (!empty($row))
  {
    $f = $row["$fieldname"];
  }
  else
  {
    $f = $default;
  }

  return $f;
}


?>
