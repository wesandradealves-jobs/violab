<?php
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header("Content-Type: application/json, text/html");
	header("Access-Control-Allow-Origin: *");

	require("./src/PHPMailer.php");
	require("./src/SMTP.php");	

	$mail = new PHPMailer\PHPMailer\PHPMailer();	

	$mailingFiles = json_encode($_GET['mailingFiles']);
	$mailingFilesObj = json_decode(json_decode($mailingFiles, true));

	$senderData = json_encode($_GET['senderData']);
	$senderDataObj = json_decode(json_decode($senderData, true));	

	$nome = $senderDataObj->first_name.' '.$senderDataObj->middle_name;
	$email = $senderDataObj->email;

   	$mensagem = '
    	<div style="background-color: black;color:white;padding:30px;display:block;">
    		<img src="https://violab.hottank.com.br/sistema/wp-content/uploads/2019/08/logo.png" style="display:block;margin:0 auto 15px" />
    		<h1 style="display:block;font-size:2rem;color:white;font-weight:bold;text-align:center;">Parabéns! Você acaba de comprar com a violab. <small style="display:block;font-size:10px;margin:4px auto;">O Código da sua compra é:</small></h1>
			<div style="display:block;text-align:center;margin:15px auto;padding:30px;background-color:white;color:black;font-weight:bold;font-size:3rem;text-align:center;">'.$_GET['cid'].'</div>
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
						foreach ($mailingFilesObj as $value) {
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

    $mail->IsSMTP();                                      
    $mail->Host = 'mail.uppercreative.com.br';                 
    $mail->Port = 465;                                    
    $mail->SMTPAuth = true;                               
    $mail->Username = 'noreply@dev.uppercreative.com.br';                
    $mail->Password = 'mudar123';                  
    $mail->SMTPSecure = 'ssl';                            

    $mail->From = 'noreply@violab.com.br';
    $mail->FromName = 'NoReply - Violab.com.br';
    $mail->AddReplyTo('noreply@violab.com.br', 'NoReply - Violab.com.br');

    $mail->AddAddress($email, $nome); 

    $mail->addBCC('wesandradealves@gmail.com');

    $mail->IsHTML(true);                                  

    $mail->Subject = 'Olá, Sua compra na Violab chegou!';
    $mail->Body    = $mensagem;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->Send();

    if($mail->Send()) {
    	print_r(json_encode(array('status'=>$mail)));
    }