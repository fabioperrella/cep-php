<?php
error_reporting(E_ERROR);
header('content-type: text/plain; charset: utf-8');

$db_host = 'localhost';
$db_user = 'root';
$db_pass = '';
$db_name = 'cep';
if(!$db = mysql_connect($db_host,$db_user,$db_pass)){
  finish('erro ao conectar no DB');
}

if(!mysql_select_db($db_name)){
  finish('erro ao selecionar db');
}

$cep = addslashes($_REQUEST['cep']);
if(preg_match('/^(\d{5})(\d{3})$/',$cep,$matches)){
  $cep = $matches[1].'-'.$matches[2];
}
elseif(!preg_match('/^\d{5}-\d{3}$/',$cep)){
  finish('cep invalido');
}

$cep_parts = explode('-',$cep);
$sql = "select uf from cep_log_index where cep5='{$cep_parts[0]}'";
$res = mysql_query($sql,$db);
if($row = mysql_fetch_array($res)){
  $uf = $row[0];
  //print "uf: $uf";

  $sql = "select * from $uf where cep='$cep'";
  $res = mysql_query($sql,$db);
  if($row = mysql_fetch_assoc($res)){
    unset($row['id']);
    finish(json_encode($row));
  }
  else{
    finish('cep nao encontrado');
  }
}
else{
  finish('cep nao encontrado');
}

function finish($msg){
  mysql_close($db);
  if($msg){
    die($msg);
  }
}
?>
