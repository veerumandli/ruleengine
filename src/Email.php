<?php

namespace Veerumandli\Ruleengine;

include_once (dirname(__FILE__) . '/Ruleengine.php');

class Email extends Ruleengine
{

	public static $subject;

	public static $from_email;

	public static $to_emails = array();

	public static $body;

	public static $message;

	public static $body_type = 'text/html';

	public static $transport;

	public static $zjbProvider;

	public static $channelId = 1;

	public static $activeChannel;
	
	public static $activeProviderObj;

	public static $any_reference_provider;

	static function init($group=0, $provider='', $any_reference_provider = ''){
		parent::init($group,self::$channelId);
		self::$activeChannel = parent::zdb()->join("providers p","p.pid = c.default_provider","LEFT")->where("c.cid",self::$channelId)->getOne('channels c');
		if(!self::$activeChannel){
			die('Channel is not exist. Please contact your admin');
		}
		if(self::$activeChannel['channel_status']!==1){
			die('Channel has been deactivated. Please contact your admin');
		}
		self::$zjbProvider = self::$activeChannel['vendor_class'];
		if($provider && self::$activeChannel['slug_name']!==$provider){
			$exist = parent::zdb()->where("slug_name",$provider)->where('provider_status',1)->where('channel_id',self::$channelId)->getOne('providers');
			if(!$exist)
				die('Channel provider is not exist. Please contact your admin');
			self::$zjbProvider = $exist['vendor_class'];
			self::$activeChannel['slug_name'] = $exist['slug_name'];
			self::$activeChannel['provider_name'] = $exist['provider_name'];
			self::$activeChannel['vendor_class'] = $exist['vendor_class'];
			self::$activeChannel['pid'] = $exist['pid'];

		}
		$transport_name = self::$activeChannel['slug_name'].'_transport';
		self::$transport_name();
		self::$activeProviderObj = new self::$zjbProvider(self::$transport);
	}


	public static function activeProvider(){
		return self::$activeProviderObj;
	}

	public static function send(){
		$message_name = self::$activeChannel['slug_name'].'_message';
		self::$message_name();
		try{
			if(self::$activeChannel['slug_name']=='sendgrid')
				$result = self::$activeProviderObj->post(self::$message);
			else
				$result = self::$activeProviderObj->send(self::$message);
			return $result;
		}catch(Exception $e){
			throw $e;
		}
	}

	public static function subject($subject = null){
		self::$subject = $subject;
	}

	public static function fromEmail($from_email){
		self::$from_email = $from_email;
	}

	public static function toEmail($to_emails){
		self::$to_emails = $to_emails;
	}

	public static function body($body){
		self::$body = $body;
	}

	public static function mandrill_transport(){
		$provider_meta = parent::zdb()->where("provider_id",self::$activeChannel['pid'])->get('provider_meta',null,'meta_value,meta_key');
		$provider_meta = array_column($provider_meta,'meta_value','meta_key');
		self::$transport = $provider_meta['api_key'];

	}
	public static function mandrill_message(){
		$to_emails = array();
		foreach(self::$to_emails as $key=>$to_email){
			$to_emails[] = array(
			                'email' => $to_email,
			                //'name' => $to_email,
			                'type' => 'to'
			            );
		}
		self::$message = array(
					'html' => self::$body,
			        'text' => self::$body,
			        'subject' => self::$subject,
			        'from_email' => key(self::$from_email),
			        'from_name' => end(self::$from_email),
			        'to' => $to_emails
			);
		self::$activeProviderObj = self::$activeProviderObj->messages;
	}	

	public static function sendgrid_transport(){
		$provider_meta = parent::zdb()->where("provider_id",self::$activeChannel['pid'])->get('provider_meta',null,'meta_value,meta_key');
		$provider_meta = array_column($provider_meta,'meta_value','meta_key');
		self::$transport = $provider_meta['api_key'];

	}

	public static function sendgrid_message(){
		$to_emails = array();
		foreach(self::$to_emails as $key=>$to_email){
			$to_emails[] = array(
			                'email' => $to_email
			                	);
		}
		$request_body = array();
		$request_body['personalizations']= array(array('to'=>$to_emails,'subject'=>self::$subject));
		$request_body['from']= array('email'=>key(self::$from_email));
		$request_body['content']= array(array('type'=>self::$body_type,'value'=>self::$body));

		self::$message = json_decode(json_encode($request_body));

		self::$activeProviderObj = self::$activeProviderObj->client->mail()->send();
	}	

	public static function smtp_transport(){
		if(self::$any_reference_provider){
			$provider_meta = parent::zdb()->where("provider_id",self::$activeChannel['pid'])->where("any_reference_provider",self::$any_reference_provider)->get('provider_meta',null,'meta_value,meta_key');
		}else{
			$provider_meta = parent::zdb()->where("provider_id",self::$activeChannel['pid'])->get('provider_meta',null,'meta_value,meta_key');
		}
		$provider_meta = array_column($provider_meta,'meta_value','meta_key');
		self::$transport =  (new \Swift_SmtpTransport($provider_meta['hostname'], $provider_meta['port']))
					  ->setUsername($provider_meta['username'])
					  ->setPassword($provider_meta['password']);
	}

	public static function smtp_message(){
		self::$message = (new \Swift_Message(self::$subject))
					  	 ->setFrom(self::$from_email)
					 	 ->setTo(self::$to_emails)
					 	 ->setBody(self::$body)
					 	 ->setContentType(self::$body_type);
	}

}
