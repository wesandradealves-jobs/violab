<?php
	header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept");
	header("Content-Type: application/json, text/html");
	header("Access-Control-Allow-Origin: *");

	require("./src/PHPMailer.php");
	require("./src/SMTP.php");	

	$action = $_GET['action'];

	$mail = new PHPMailer\PHPMailer\PHPMailer();	

    $prod = json_encode($_GET['prod']);
    $prodObj = json_decode(json_decode($prod, true));  

    if(!$_GET['email']) {
        $mensagem = '
                <div style="background-color: black;color:white;padding:30px;display:block;">
                    <img src="https://violab.hottank.com.br/sistema/wp-content/uploads/2019/08/logo.png" style="display:block;margin:0 auto 15px" />
                    <h1 style="display:block;font-size:2rem;color:white;font-weight:bold;text-align:center;">Parabéns! Você acaba de receber "'.$prodObj->title.'" como cortesia da Violab!</h1>
                    <div style="display:block;">';

                        $mensagem .= '<a style="    display: block;
        margin: 15px auto 0;
        padding: 15px;
        width: 200px;
        color: black;
        text-decoration: none;
        text-align: center;
        background: lightgoldenrodyellow;
        font-size: 20px;
        font-weight: bold;" href="'.$prodObj->url.'">Download</a>';

                    $mensagem .= '</div>
                </div>
            ';  
        } else {
            $mensagem = $_GET['msg'];
        }

    $mail->IsSMTP();    
    $mail->CharSet = 'UTF-8';                                  
    $mail->Host = 'mail.uppercreative.com.br';                 
    $mail->Port = 465;                                    
    $mail->SMTPAuth = true;                               
    $mail->Username = 'noreply@dev.uppercreative.com.br';                
    $mail->Password = 'mudar123';                  
    $mail->SMTPSecure = 'ssl';                            

    $mail->From = 'noreply@violab.com.br';
    $mail->FromName = 'NoReply - Violab.com.br';

    if($_GET['email']) {
        $mail->AddReplyTo($_GET['email']);
        $mail->addBCC($_GET['email']);
    } else {
        $mail->AddReplyTo('noreply@violab.com.br', 'NoReply - Violab.com.br');
    }

    $email = ((!$_GET['msg']) ? 'vendas@violab.com.br' : 'contato@violab.com.br');
    $assunto = ((!$_GET['msg']) ? 'Olá, a Violab tem um presente para você!' : 'Contato - Violab');

    $mail->AddAddress( $email ); 

    $mail->addBCC('wesandradealves@gmail.com');

    $mail->IsHTML(true);                                  

    $mail->Subject = $assunto;
    $mail->Body    = $mensagem;
    $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

    $mail->Send();

    if($mail->Send()) {
    	print_r(json_encode(array('status'=>$mail)));
    }