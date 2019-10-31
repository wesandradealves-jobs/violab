<?php 
	include_once '../config/PagSeguroConfig.php';
	require("../../phpmailer/src/PHPMailer.php");
	require("../../phpmailer/src/SMTP.php");	

	$mail = new PHPMailer\PHPMailer\PHPMailer();	

	// $action = (isset($_POST['action'])) ? $_POST['action'] : '';

	function limpaCPF_CNPJ($valor){
		$valor = trim($valor);
		$valor = str_replace(".", "", $valor);
		$valor = str_replace(",", "", $valor);
		$valor = str_replace("-", "", $valor);
		$valor = str_replace("/", "", $valor);
		return $valor;
	}	

	$data = json_decode($_GET['json']);
	$action = 'finalizar-compra';
	$forma_pagamento = 'creditCard';

	$info = [];

	$info['paymentMode'] .= 'default';
	$info['paymentMethod'] .= $forma_pagamento;
	$info['currency'] .= 'BRL';
	$info['extraAmount'] .= '0.00';
	$info['reference'] .= 'REF_VIOSITE';	
	$info['notificationURL'] .= 'http://violab.hottank.com.br';
	$info['billingAddressCountry'] .= $info['shippingAddressCountry'] .= 'BRA';
	$info['installmentQuantity'] .= 1;
	// $info['noInterestInstallmentQuantity'] .= 1;
	$info['quantity'] .= 1;
	$info['shippingType'] .= 1;
	$info['shippingCost'] .= '0.00';

	foreach ($data as $key => $value) {
		$form = $value;
		$formData = $form->formData;
		// 
		$personalData = $formData->personalData;
		$address = $formData->address;
		$billingAddress = $formData->billingAddress;
		$card = $formData->card;

		$buyer = $form->buyer;
		$seller = $form->seller;
		$paymentData = $buyer->paymentData;

		$tokenCartao = $paymentData->eToken;
		$senderHash = $buyer->senderHash;	

		$formattedTotal = money_format('%i', $form->cart_total);

		$info['email'] .= $form->email;
		$info['token'] .= $form->token;

		$info['receiverEmail'] .= $form->email;

		$info['senderName'] .= $formData->personalData->nome;
		$info['senderCPF'] .= limpaCPF_CNPJ($personalData->cpfcnpj);
		$info['senderAreaCode'] .= preg_replace('/\s/', '', preg_replace('/\(|\)/','', substr($personalData->telefone, 0, 3)));
		$info['senderPhone'] .= str_replace('-', '', preg_replace('/\s/', '', substr($personalData->telefone, 5)));
		
		$info['senderEmail'] .= $personalData->email;

		$info['senderHash'] .= $buyer->senderHash;

		$info['hash'] .= $buyer->senderHash;

		$info['shippingAddressStreet'] .= $address->endereco;
		$info['shippingAddressNumber'] .= $address->numero;
		$info['shippingAddressComplement'] .= $address->complemento;
		$info['shippingAddressDistrict'] .= $address->bairro;
		$info['shippingAddressPostalCode'] .= $address->cep;
		$info['shippingAddressCity'] .= $address->cidade;
		$info['shippingAddressState'] .= $address->estado;

		// creditCardHolderBirthDate
		// BirthDate

		$info['creditCardToken'] .= $tokenCartao;
		// $info['token'] .= $tokenCartao;
		$info['installmentValue'] .= ($formattedTotal>=1) ? $formattedTotal : null;
		$info['value'] .= ($formattedTotal>=1) ? $formattedTotal : null;
		$info['creditCardHolderName'] .= $card->cartao_nome; 
		$info['creditCardHolderBirthDate'] .= $card->nascimento_cartao; 
		$info['creditCardHolderCPF'] .= limpaCPF_CNPJ($card->cartao_cpf_cnpj);
		$info['creditCardHolderAreaCode'] .= preg_replace('/\s/', '', preg_replace('/\(|\)/','', substr($card->cartao_telefone, 0, 3))); 
		$info['creditCardHolderPhone'] .= str_replace('-', '', preg_replace('/\s/', '', substr($card->cartao_telefone, 5)));

		$info['billingAddressStreet'] .=  $billingAddress->cartao_endereco;
		$info['billingAddressNumber'] .=  $billingAddress->cartao_numero;
		$info['billingAddressComplement'] .= $billingAddress->cartao_complemento;
		$info['billingAddressDistrict'] .= $billingAddress->cartao_bairro;
		$info['billingAddressPostalCode'] .=  $billingAddress->cartao_cep;
		$info['billingAddressCity'] .=  $billingAddress->cartao_cidade;
		$info['billingAddressState'] .= $billingAddress->cartao_estado;		

		for ($i = 0; $i < sizeof($form->items); $i++) {
			foreach ($form->items[$i] as $key => $value) {
				$info[$key.($i + 1)] .= $value;
			}		
		}
	}

	$info = http_build_query($info);

	$curl = curl_init( $urls['transaction'] );
	curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
	curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($curl, CURLOPT_POST, true);
	curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
	// curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/xml'));
	curl_setopt($curl, CURLOPT_POSTFIELDS, $info);
	$responseXML = curl_exec($curl);
	$response = simplexml_load_string($responseXML);

	print_r(json_encode($response));

	function xml2array ( $xmlObject, $out = array () )
	{
	    foreach ( (array) $xmlObject as $index => $node )
	        $out[$index] = ( is_object ( $node ) ) ? xml2array ( $node ) : $node;

	    return $out;
	}	

	$arr = xml2array($response);

	// print_r($arr);

	if($arr['code']) {
        $mail->IsSMTP();                                      
        $mail->Host = 'mail.uppercreative.com.br';                 
        $mail->Port = 465;                                    
        $mail->SMTPAuth = true;                               
        $mail->Username = 'noreply@dev.uppercreative.com.br';                
        $mail->Password = 'mudar123';                  
        $mail->SMTPSecure = 'ssl';                            

        $mail->From = 'noreply@dev.uppercreative.com.br';
        $mail->FromName = 'NoReply - Violab.com.br';
        $mail->AddReplyTo('noreply@dev.uppercreative.com.br', 'NoReply - Violab.com.br');

		// print $arr['sender']['email'];

        $mail->AddAddress($arr['sender']['email'], $arr['sender']['nome']); 

  	$mensagem = '
    	<div style="background-color: black;color:white;padding:30px;display:block;">
    		<img src="https://violab.hottank.com.br/sistema/wp-content/uploads/2019/08/logo.png" style="display:block;margin:0 auto 15px" />
    		<h1 style="display:block;font-size:2rem;color:white;font-weight:bold;text-align:center;">Parabéns! Você acaba de comprar com a violab. <small style="display:block;font-size:10px;margin:4px auto;">O Código da sua compra é:</small></h1>
			<div style="display:block;text-align:center;margin:15px auto;padding:30px;background-color:white;color:black;font-weight:bold;font-size:3rem;text-align:center;">'.$arr['code'].'</div>
			<div style="display:block;">';

				$mensagem .= '<p style="display: block;margin: 15px auto; "><small style="font-size:10px;">Baixe seus arquivos nos links abaixo:</small></p>
				<div style="display:block;display: block;
    margin: 0 -32px -32px;
    background: whitesmoke;
    padding: 30px;">
					<table>
						<tr style="font-weight: bold">
							<td style="width: 140px; padding: 3px">Nome</td>
							<td style="padding: 3px">Arquivo</td>
						</tr>
						';
						foreach ($data->cart->mailingFiles as $key => $value) {
							$mensagem .= '
							<tr>
								<td style="width: 140px; padding: 3px">'.$value->itemDescription.'</td>
								<td style="padding: 3px"><a style="color:black; font-weight: bold" href="'.$value->itemUrl.'">'.$value->itemUrl.'</a></td>
							</tr>';		
						}
						$mensagem .= '
					</table>
				</div>';

			$mensagem .= '</div>
    	</div>
    ';        

        $mail->addBCC('wesandradealves@gmail.com');

        $mail->IsHTML(true);                                  

        $mail->Subject = 'Olá, Sua compra na Violab chegou!';
        $mail->Body    = $mensagem;
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        $mail->Send();      			
	}
 ?>