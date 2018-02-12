<?php
include 'Connections/ligacaodados.php';
include 'Connections/ligacao.php';
include 'includes/verificar-idioma.php';

$query_montra=mysql_query("SELECT * FROM montra WHERE LANG = '$idioma'");
$text_montra=mysql_fetch_assoc($query_montra);

$query_botoes=mysql_query("SELECT * FROM botoes WHERE LANG = '$idioma'");
$botao=mysql_fetch_assoc($query_botoes);

mysql_select_db($database_ligacaodados) or die(mysql_error());

$query_parametro_botoes=mysql_query("SELECT * FROM parametros WHERE parametro = 'Botoes'");
$parametro_botoes=mysql_fetch_assoc($query_parametro_botoes);
$query_parametro_encomendas=mysql_query("SELECT * FROM parametros WHERE parametro = 'Encomendas'");
$parametro_encomendas=mysql_fetch_assoc($query_parametro_encomendas);
$query_parametro_orcamentos=mysql_query("SELECT * FROM parametros WHERE parametro = 'Orcamentos'");
$parametro_orcamentos=mysql_fetch_assoc($query_parametro_orcamentos);

$currentPage = $_SERVER["PHP_SELF"];

$maxRows_Destaques = 3;
$pageNum_Destaques = 0;
if (isset($_GET['pageNum_Destaques'])) {
  $pageNum_Destaques = $_GET['pageNum_Destaques'];
}
$startRow_Destaques = $pageNum_Destaques * $maxRows_Destaques;

$query_Destaques = "SELECT * FROM artigos WHERE DESTAQUE = '-1'";
$query_limit_Destaques = sprintf("%s LIMIT %d, %d", $query_Destaques, $startRow_Destaques, $maxRows_Destaques);
$Destaques = mysql_query($query_limit_Destaques) or die(mysql_error());
$row_Destaques = mysql_fetch_assoc($Destaques);

if (isset($_GET['totalRows_Destaques'])) {
  $totalRows_Destaques = $_GET['totalRows_Destaques'];
} else {
  $all_Destaques = mysql_query($query_Destaques);
  $totalRows_Destaques = mysql_num_rows($all_Destaques);
}
$totalPages_Destaques = ceil($totalRows_Destaques/$maxRows_Destaques)-1;

$queryString_Destaques = "";
if (!empty($_SERVER['QUERY_STRING'])) {
  $params = explode("&", $_SERVER['QUERY_STRING']);
  $newParams = array();
  foreach ($params as $param) {
    if (stristr($param, "pageNum_Destaques") == false && 
        stristr($param, "totalRows_Destaques") == false) {
      array_push($newParams, $param);
    }
  }
  if (count($newParams) != 0) {
    $queryString_Destaques = "&" . htmlentities(implode("&", $newParams));
  }
}
$queryString_Destaques = sprintf("&totalRows_Destaques=%d%s", $totalRows_Destaques, $queryString_Destaques);

$query_parametros=mysql_query("SELECT * FROM parametros WHERE parametro = 'Preço'");
$parametro_preco=mysql_fetch_assoc($query_parametros);
$row_preco=$parametro_preco['sim/nao'];

?>

<!doctype html>
<html>
<head>
<meta charset="utf-8">
<title>VIEIRAMOVEL Ⓡ</title>
<link type="text/css" rel="stylesheet" href="CSS/estilos.css">
<script type="text/javascript">

function overlay() {
	el = document.getElementById("overlay");
	el.style.visibility = (el.style.visibility == "visible") ? "hidden" : "visible";
}

var num=1;

function imagemSegAnt(incdec){
	clearTimeout(pausa);
	num=num+incdec;
	slideSequencial(num);
}

function slideSequencial(slide){
	if (slide>=6)
		slide=1;
	else if (slide<1)
		slide=5;   
	document.getElementById('MainImage').src="imagens/index/imagem_0"+slide+".png";
	document.getElementById('MainImage2').src="imagens/index/imagem_0"+slide+".png";
	num=slide+1;
	pausa=setTimeout("slideSequencial(num)", 5000);
}

</script>
</head>
<body onLoad="slideSequencial(1);">
<header>
<?php include 'includes/header_index.php';?>
</header>
<div id="main">
<div id="middle_index">
<div id="conteudo_index">
<div id="empresa">
<div id="texto_index_empresa">
<h3><?php $query_tituloempresa=mysql_query("SELECT * FROM conteudo WHERE PAGE = 'index' AND content = 'titulo_empresa' AND LANG = '$idioma'");
$texto_tituloempresa=mysql_fetch_assoc($query_tituloempresa); echo utf8_encode($texto_tituloempresa['TEXT']);?></h3>
<p><?php $query_textoempresa=mysql_query("SELECT * FROM conteudo WHERE PAGE = 'index' AND content = 'texto_empresa' AND LANG = '$idioma'");
$textoempresa=mysql_fetch_assoc($query_textoempresa); echo utf8_encode($textoempresa['TEXT']);?></p>
<a href="empresa.php"><div id="bt_lermais"><?php $query_lermais=mysql_query("SELECT * FROM botoes WHERE LANG = '$idioma'"); $texto_lermais=mysql_fetch_assoc($query_lermais); echo utf8_encode($texto_lermais['lermais']); ?></div></a>
</div>
</div>

<?php if($totalRows_Destaques>0){?>
<div id="destaques">
<h3><?php $query_titulodestaques=mysql_query("SELECT * FROM conteudo WHERE PAGE = 'index' AND content = 'titulo_destaques' AND LANG = '$idioma'"); $text_titulodestaques=mysql_fetch_assoc($query_titulodestaques); echo $text_titulodestaques['TEXT']; ?></h3>
<?php do { ?>
<?php
	mysql_select_db($database_ligacaodados) or die(mysql_error());
	$data=date("y-m-d");
	$artigo=$row_Destaques['CODIGO'];
	$query_promocao=mysql_query("SELECT * FROM excepdes WHERE ARTIGO = '$artigo' AND DTINICIO <= '$data' AND DTFIM >= '$data'");
	$rows_promocao=mysql_num_rows($query_promocao);
	$row_promocao=mysql_fetch_assoc($query_promocao);
	if($rows_promocao>0)
	{
		$percentagem_desconto=$row_promocao['PERC1'];
		$preco_promocao=$row_Destaques['PVPCIVA']-($row_Destaques['PVPCIVA']/$percentagem_desconto);
	}
?>
<div class="produto">
<div class="nome_produto"><?php mysql_select_db($database_ligacaodados) or die(mysql_error()); $cod=$row_Destaques['CODIGO']; $query_nomeproduto=mysql_query("SELECT * FROM artidiom WHERE IDIOMA LIKE '$idioma' AND ARTIGO = '$cod'"); $nomeproduto=mysql_fetch_assoc($query_nomeproduto);?><a href="detalhe.php?cod=<?php echo $row_Destaques['CODIGO'];?>"><?php echo utf8_encode($nomeproduto['DESCR']);?></a></div>
<div class="preco_produto"><?php $query_parametro_preco=mysql_query("SELECT * FROM parametros WHERE parametro = 'Preco'");
$parametro_preco=mysql_fetch_assoc($query_parametro_preco);if($parametro_preco['par']=="Sim"){?><?php echo utf8_encode($text_montra['preco']). " : "; if($rows_promocao>0){ echo number_format($preco_promocao,2,",",".");} else echo number_format($row_Destaques['PVPCIVA'],2,",",".");?> €<?php }?></div>
<?php if($parametro_botoes['par']=="Mon"||$parametro_botoes['par']=="Amb"){?><div class="carrinho_produto"><?php if($parametro_encomendas['par']=="Sim"){?><?php if(isset($_COOKIE['email'])){?><a href="carrinho.php?cod=<?php echo $row_Destaques['CODIGO'];?>&amp;acao=add&amp;qtd=1"><?php }?><?php if(!isset($_COOKIE['email'])){?><a href='#' onclick='overlay()'><?php }?><img src="CSS/imagens/carrinho_small.png" alt="<?php echo utf8_encode($botao['adcarrinho']);?>" title="<?php echo utf8_encode($botao['adcarrinho']);?>"></a><?php }?></div>
<div class="orcamento_produto"><?php if($parametro_orcamentos['par']=="Sim"){?><?php if(isset($_COOKIE['email'])){?><a href="orcamentos.php?cod=<?php echo $row_Destaques['CODIGO'];?>&amp;acao=add&amp;qtd=1"><?php }?><?php if(!isset($_COOKIE['email'])){?><a href='#' onclick='overlay()'><?php }?><img src="CSS/imagens/info_small.png" alt="<?php echo utf8_encode($botao['pedorcamento']);?>" title="<?php echo utf8_encode($botao['pedorcamento']);?>"></a><?php }?></div><?php }?>
<div class="img_produto"><a href="detalhe.php?cod=<?php echo $row_Destaques['CODIGO'];?>"><img src="imagens/FDISQL_ARTG/<?php echo $row_Destaques['CODIGO'];?>.jpg" height="129" width="172" alt="imagem indisponivel"></a></div>
</div>
<?php } while ($row_Destaques = mysql_fetch_assoc($Destaques)); ?>
</div>
<?php }?>
</div>
</div>
<?php include 'modal-window.php';?>
</div>
<footer>
<?php include 'includes/footer.php';?>
</footer>
</body>
</html>