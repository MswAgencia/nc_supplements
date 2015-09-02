<?php
$cep = preg_replace("/[^0-9]/", "", $_GET['cep']);
if(strlen($cep) == '8'){
$resultado = @file_get_contents('http://apps.widenet.com.br/busca-cep/api/cep.json?code='.$cep); 
echo $resultado;
}
?>