<?php echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n"; ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" dir="<?php echo $direction; ?>" lang="<?php echo $language; ?>" xml:lang="<?php echo $language; ?>">
    <head>
        <title>Imprimir Etiqueta dos Correios</title>
        <base href="<?php echo $base; ?>" />
        <link rel="stylesheet" type="text/css" href="../admin/view/stylesheet/estilo_codigo.css" />
        <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
        <script type="text/javascript" src="../admin/view/javascript/codigo_de_barra/codigo_de_barra.js"></script>  
        <script type="text/javascript" src="../admin/view/javascript/codigo_de_barra/funcao.js"></script>  
    </head>
    <body onload="updateBarcode()">
        <?php foreach ($orders as $order) { ?>
        <div id="print">
            <table width="391" class="tabela1" cellpadding="14" cellspacing="0">
                <tr>
                    <td width="51%" valign="top">
                        <table width="100%" border="0">
                            <tbody>
                                <tr>
                                    <td align="botton"><img src="../image/etiqueta/destinatario_peq.gif" border="0"></td>
                                    <td align="right">
									<?php $a = $order['correios'];//Contribuição de Alexandre Lasmar
									switch($a)
									{
									case "correios.41106": 
									echo '<img src="../image/etiqueta/chancela_pac.jpg">';
									break;
									case "correios.40010": 
									echo '<img src="../image/etiqueta/chancela_sedex.jpg">';
									break;
									case "correios.40215": 
									echo '<img src="../image/etiqueta/chancela_sedex10.jpg">';
									break;
									case "pickup.pickup": 
									echo '<img src="../image/etiqueta/chancela_pickup.jpg">';
									break;
									case "flat.flat" || "free.free": 
									echo '<img src="../image/etiqueta/correios.gif">';
									}
									?>
									</td>
                                </tr>
                            </tbody>
                        </table>

                        <table border="0" cellpadding="0" cellspacing="0" height="150">
                            <tbody>
                                <tr>
                                    <td valign="top">
                                        <span class="style1">
                                            <?php echo $order['shipping_address']; ?>
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>

                        <div align="center" class="style4">
                            <input style="display: none;" id="barcodeValue" type="text" name="value" value="<?php echo $order['cep']; ?>" />
                            <img id="barcodeImage" />
                        </div>
                        <hr align="center" width="100%" color="silver" size="1" />
                        <div class="style2">
                            <b>Remetente:</b><br /> 
                            <span class="transforme">
								<?php echo $order['store_name'];?><br />
                                <?php echo $order['telefone-loja'];?><br /> 
								<?php echo $order['endereco-loja']?>
                            </span>
                        </div>		  
                    </td>
                </tr>
            </table>
        </div>
        <?php } ?>
        <div class="right">
            <p align="center">
                <a href="javascript:Imprimir('print');"/><button class="bot">Imprimir</button></a>
            </p>
        </div>
		<div class="centro">Está sendo útil a você?<br>
		<b>Contribua fazendo uma doação</b><br>
		<div class="paypal">
		<a target="_blank" href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=NAVNRBMDYGUD6"> <img src="https://www.paypalobjects.com/pt_BR/BR/i/btn/btn_donateCC_LG.gif" /></a>
		</div>
		<div class="pagseguro">
		<!-- INICIO FORMULARIO BOTAO PAGSEGURO -->
<form action="https://pagseguro.uol.com.br/checkout/v2/donation.html" target="_blank" method="post">
<!-- NÃO EDITE OS COMANDOS DAS LINHAS ABAIXO -->
<input type="hidden" name="currency" value="BRL" />
<input type="hidden" name="receiverEmail" value="cesarhenriquebr@gmail.com" />
<input type="image" src="https://p.simg.uol.com.br/out/pagseguro/i/botoes/doacoes/120x53-doar.gif" name="submit" alt="Pague com PagSeguro - é rápido, grátis e seguro!" />
</form>
<!-- FINAL FORMULARIO BOTAO PAGSEGURO -->
	</div>
		</div>
    </body>
</html>

