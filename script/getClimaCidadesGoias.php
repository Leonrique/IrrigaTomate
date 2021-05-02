<?php

include 'pathConfig.php';
$arquivoPath = configPath;
include($arquivoPath);
include 'funcoesInmet.php';
include 'getDadosNasa.php';
include 'simple_html_dom.php';
include 'funcoesUfg.php';

include 'base30anos.php';
include 'removeDadosRepetidosEstacoesFisicas.php';
include 'cidadesSemEstacoes.php';
include 'removeDadosRepetidos.php';

//
// iNSERIR DADOS DO inmet, ufg E Simehgo
//

date_default_timezone_set('America/Sao_Paulo');

echo "\nStarted getDadosInmet ".date("m/d/Y h:i:s a", time());
getDadosInmet();
echo "\nDone getDadosInmet ".date("m/d/Y h:i:s a", time());

echo "\nStarted getDadosUfg ".date("m/d/Y h:i:s a", time());
getDadosUfg();
echo "\nDone getDadosUfg ".date("m/d/Y h:i:s a", time());

//
// Verificar dados faltantes de 
// temperatura media e temperatura
// de evapotranspiracao (ETC)

echo "\nStarted base30anos ".date("m/d/Y h:i:s a", time());
base30anos();
echo "\nDone base30anos ".date("m/d/Y h:i:s a", time());

// remover dados duplicados das estacoes UFG, INMET e SIMEHGO

echo "\nStarted removeDadosEstacoesFisicas ".date("m/d/Y h:i:s a", time());
removeDadosEstacoesFisicas();  //Remove todos os dados replicados das estacoes existentes
echo "\nDone removeDadosEstacoesFisicas ".date("m/d/Y h:i:s a", time());

//Inserir dados nas demais cidades
// a partir dos dados do Inmet, UFG e Simehgo
//
echo "\nStarted cidadesSemEstacoes ".date("m/d/Y h:i:s a", time());
cidadesSemEstacoes();
echo "\nDone cidadesSemEstacoes ".date("m/d/Y h:i:s a", time());

// 
// remover dados duplicados das demais estacoes

echo "\nStarted removeDadosDuplicados ".date("m/d/Y h:i:s a", time());
removeDadosDuplicados();  //Remove todos os dados replicados das demais estacoes
echo "\nDone removeDadosDuplicados ".date("m/d/Y h:i:s a", time());
