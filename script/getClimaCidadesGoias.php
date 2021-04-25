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
echo "Started getDadosInmet";
getDadosInmet();
echo "Done getDadosInmet";

echo "Started getDadosUfg";
getDadosUfg();
echo "Done getDadosUfg";

//
// Verificar dados faltantes de 
// temperatura media e temperatura
// de evapotranspiracao (ETC)

echo "Started base30anos";
base30anos();
echo "Done base30anos";

// remover dados duplicados das estacoes UFG, INMET e SIMEHGO

echo "Started removeDadosEstacoesFisicas";
removeDadosEstacoesFisicas();  //Remove todos os dados replicados das estacoes existentes
echo "Done removeDadosEstacoesFisicas";

//Inserir dados nas demais cidades
// a partir dos dados do Inmet, UFG e Simehgo
//
echo "Started cidadesSemEstacoes";
cidadesSemEstacoes();
echo "Done cidadesSemEstacoes";

// 
// remover dados duplicados das demais estacoes

echo "Started removeDadosDuplicados";
removeDadosDuplicados();  //Remove todos os dados replicados das demais estacoes
echo "Done removeDadosDuplicados";
