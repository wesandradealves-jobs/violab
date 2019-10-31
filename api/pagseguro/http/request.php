<?php 
	include_once '../config/PagSeguroConfig.php';

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

		$info['email'] .= $form->token;
		$info['token'] .= $form->email;

		$info['receiverEmail'] .= $form->token;

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
		$info['shippingAddressCity'] .= $address->estado;
		$info['shippingAddressState'] .= $address->cidade;

		$info['creditCardToken'] .= $tokenCartao;
		// $info['token'] .= $tokenCartao;
		$info['installmentValue'] .= ($formattedTotal>=1) ? $formattedTotal : null;
		$info['value'] .= ($formattedTotal>=1) ? $formattedTotal : null;
		$info['creditCardHolderName'] .= $card->cartao_nome; 
		$info['creditCardHolderCPF'] .= limpaCPF_CNPJ($card->cartao_cpf_cnpj);
		$info['creditCardHolderAreaCode'] .= preg_replace('/\s/', '', preg_replace('/\(|\)/','', substr($card->cartao_telefone, 0, 3))); 
		$info['creditCardHolderPhone'] .= str_replace('-', '', preg_replace('/\s/', '', substr($card->cartao_telefone, 5)));

		$info['billingAddressStreet'] .=  $billingAddress->cartao_endereco;
		$info['billingAddressNumber'] .=  $billingAddress->cartao_numero;
		$info['billingAddressComplement'] .= $billingAddress->cartao_complemento;
		$info['billingAddressDistrict'] .= $billingAddress->cartao_bairro;
		$info['billingAddressPostalCode'] .=  $billingAddress->cartao_cep;
		$info['billingAddressCity'] .=  $billingAddress->cartao_estado;
		$info['billingAddressState'] .= $billingAddress->cartao_cidade;		

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
 ?>