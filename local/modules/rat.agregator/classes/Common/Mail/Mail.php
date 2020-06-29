<?php
namespace Agregator\Common\Mail;

use Bitrix\Main\Mail\EventMessageCompiler;
use Bitrix\Main\Mail\Internal as MailInternal;
use PHPMailer\PHPMailer\PHPMailer;
use Agregator\Logger;
use ReflectionMethod;
use T50Config;
use COption;

class Mail
{
	const DBG_MODE = false;
	protected $logger;

	private function __construct(){
		$this->logger = new Logger("mail");
	}

	static function __callStatic($method, $args){
		$obj = new self;
		if( method_exists($obj, $method) ){
			$rMethod = new ReflectionMethod(self::class, $method);
			if( $rMethod->isProtected() )
				return $obj->$method(...$args);
		}
	}

	protected function prepareByTemplate(string $typeCode, array $fields){
		$message = MailInternal\EventMessageTable::getRow([
			"filter" => ["EVENT_NAME" => htmlspecialchars($typeCode), "ACTIVE" => "Y"]
		]);

		if( empty($message) ){
			$this->logger->log("not found active template with code '{$typeCode}'");
		    return false;
		}

		$message = EventMessageCompiler::createInstance([
			'FIELDS' => $fields,
		    'MESSAGE' => $message,
		    'SITE' => "s1", 'CHARSET' => "UTF-8",
		]);

		try {
		    $message->compile();
		} catch(\Exception $e) {
		    $this->logger->log("EventMessageCompiler compile error for '{$typeCode}': " . $e->getMessage());
		    return false;
		}

		$options = $message->getMailHeaders();

		$structure = new MailStructure;
		$structure->content_type = $message->getMailContentType();
		$structure->from = $options["From"];
		$structure->to = $message->getMailTo();
		$structure->subject = $message->getMailSubject();
		$structure->body = $message->getMailBody();
		$structure->file = $fields['FILE'];
		$structure->cc = $options["CC"];
		$structure->bcc = $options["BCC"];
		$structure->reply_to = $options["Reply-To"];

		$priority = intval(preg_replace("#[^\d]#", "", $options["X-Priority"]));
		if( $priority > 0 )
			$structure->priority = $priority;

		return $structure;
	}

	protected function sendByTemplate(string $typeCode, array $fields){
		return self::send(self::prepareByTemplate($typeCode, $fields));
	}

	protected function send(MailStructure $mailStructure){
		$mail = $this->getPHPMailer($mailStructure->from);
		if( empty($mail) )
			return false;

		$arEmailsTo = $this->prepareEmails($mailStructure->to);
		if( empty($arEmailsTo) ){
			$this->logger->log("empty email to (subject '{$subject}')");
			return false;
		}

		foreach($arEmailsTo as $emailTo => $name)
			$mail->AddAddress($emailTo, $name);

		$mail->Subject = $mailStructure->subject;
		$mail->Body = $mailStructure->body;

		$mail->isHTML($mailStructure->content_type == "html");

		$files = $this->prepareFiles($mailStructure->file);
		foreach($files as $file)
		    $mail->addAttachment($file);

		if( isset($mailStructure->priority) )
			$mail->Priority = $mailStructure->priority;

		foreach($this->prepareEmails($mailStructure->reply_to) as $email => $name)
			$mail->addReplyTo($email, $name);

		foreach($this->prepareEmails($mailStructure->cc) as $email => $name)
			$mail->addCC($email, $name);

		foreach($this->prepareEmails($mailStructure->bcc) as $email => $name)
			$mail->addBCC($email, $name);

		try {
			if( ENV === "PRODUCTION" )
				return $mail->Send();
			return $mail->SmtpConnect();
		} catch ( \Exception $e ){
			$this->logger->log("custom_mail error:\n" . $mail->ErrorInfo);
		}
		return false;
	}

	private function getPHPMailer(string $emailFrom){
		$emailFrom = $this->prepareEmails($emailFrom);
		$addressFrom = key($emailFrom);
		$nameFrom = current($emailFrom);

		if( empty($addressFrom) )
			$addressFrom = COption::GetOptionString("main", "email_from");

		if( empty($addressFrom) ){
			$this->logger->log("empty email from (subject '{$subject}')");
			return false;
		}

		$smtpData = T50Config::get("email_settings.[{$addressFrom}].smtp");
		if( empty($smtpData["host"]) || empty($smtpData["login"]) || empty($smtpData["password"]) ){
			$this->logger->log("not found smtp config");
			return ;
		}

		$mail = new PHPMailer;
		$mail->IsSMTP();
	    $mail->SMTPAuth      = true;
	    $mail->SMTPSecure = "ssl";
	    $mail->Host = "smtp." . $smtpData["host"];
	    $mail->Port = 465;
	    $mail->Username = $smtpData["login"];
	    $mail->Password = $smtpData["password"];
	    $mail->CharSet =  'UTF-8';

	    if( self::DBG_MODE ){
		    $mail->SMTPDebug = 2;
		    $mail->Debugoutput = function($str) {
		    	$this->logger->log("PHPMailer debug:\n{$str}");
		    };
	    }

	    $mail->SetFrom($addressFrom, $nameFrom);

	    return $mail;
	}

	private function prepareFiles($fileOrfiles){
		if( empty($fileOrfiles) )
			return [];

		if( is_array($fileOrfiles) )
			$files = $fileOrfiles;
		else
			$files = [$fileOrfiles];

		$files = array_filter($files, function ($file){
			return is_file($file);
		});

		return $files;
	}

	/**
	* Examples for $emailOrEmails:
	* "test@mail.ru"
	* "Иванов <test@mail.ru>"
	* array("test@mail.ru" => "Иванов", ...)
	* array("test@mail.ru", "test2@mail.ru", ...)
	* array("Иванов <test@mail.ru>", ...)
	*/
	private function prepareEmails($emailOrEmails){
		if( empty($emailOrEmails) )
			return [];

		if( is_string($emailOrEmails) )
			$emailOrEmails = explode(",", $emailOrEmails);

		if( !is_array($emailOrEmails) )
			return [];

		$result = [];
		foreach($emailOrEmails as $code => $value){
			$email = $name = "";
			$code = trim($code);
			$value = trim($value);
		    if( is_numeric($code) ){
		    	if( preg_match("#^([^\s<]+)\s*\<([^>]+)\>$#", $value, $match) ){
		    		$email = $match[2];
		    		$name = $match[1];
		    	} else {
		    		$email = $value;
		    	}
		    } else {
		    	$email = $code;
		    	$name = $value;
		    }

		    if( filter_var($email, FILTER_VALIDATE_EMAIL) )
		    	$result[$email] = $name;
		}



		return $result;
	}
}