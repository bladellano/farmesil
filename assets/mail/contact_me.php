<?php

$data = filter_input_array(INPUT_POST, FILTER_DEFAULT);

if (!empty($data['g-recaptcha-response'])) {

   $url = "https://www.google.com/recaptcha/api/siteverify";
   $respon = $_POST['g-recaptcha-response'];
   $data = array('secret' => "6Le1mbcZAAAAAKKVSHBdMg0jx5Kupb8INK5F07hV", 'response' => $respon);

   $options = array(
      'http' => array(
         'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
         'method'  => 'POST',
         'content' => http_build_query($data)
      )
   );

   $context  = stream_context_create($options);
   $result = file_get_contents($url, false, $context);
   $json = json_decode($result);

   if ($json->success) {

      $name = strip_tags(htmlspecialchars($_POST['name']));
      $email_address = strip_tags(htmlspecialchars($_POST['email']));
      $phone = strip_tags(htmlspecialchars($_POST['phone']));
      $message = strip_tags(htmlspecialchars($_POST['message']));

      //Senha: &rII#Z9GAEmh

      $to = 'contato@fermasil.com.br';
      $email_subject = "Website Formulário de Contato:  $name";
      $email_body = "<h3>Você recebeu uma nova mensagem do formulário de contato do seu site.</h3>" . "<h3>Aqui estão os detalhes:</h3><b>Nome:</b> $name\n\n\r<br><b>E-mail:</b> $email_address\n\n\r<br><b>Telefone:</b> $phone\n\n\r<br><b>Mensagem:</b>\n$message";

      $headers = 'From: Website Fermasil <no-reply@fermasil.com.br>' . "\r\n" .
         'Reply-To: ' . $email_address . "\r\n" .
         'X-Mailer: PHP/' . phpversion() .
         'MIME-Version: 1.0' . "\n" .
         'Content-type: text/html; charset=UTF-8' . "\r\n";

      mail($to, $email_subject, $email_body, $headers);

      die(json_encode(['success' => true, 'msg' => 'Sua mensagem foi enviada.']));

   } else {

      die(json_encode(['success' => false, 'msg' => 'parece que meu servidor de e-mail não está respondendo. Por favor, tente novamente mais tarde!']));
   }

} else {

   die(json_encode(['success' => false, 'msg' => ' mas você não marcou o captcha.']));
}
