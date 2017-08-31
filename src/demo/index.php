<?php
require_once '../Email.php';
use Ruleengine\Email;

/*
------------------------
1 ---> Amazon SES  --> amazon
2 --> Sendgrid  --> sendgrid
3 --> Mandrill  --> mandrill
4 --> SMTP -->  smtp
API
--------------------------

*/

Email::init(1,'sendgrid');
Email::subject("sample subject");
Email::fromEmail(array('habits@zoojoo.be'=>'Zoojoo.be'));
Email::toEmail(array('veeru@zoojoo.be'));
Email::body('Test body');
Email::send();

/*
------------------------

SMTP
--------------------------

*/


Email::init(1,'smtp');
Email::subject("sample subject");
Email::fromEmail(array('habits@zoojoo.be'=>'Zoojoo.be'));
Email::toEmail(array('veeru@zoojoo.be'));
Email::body('Test body');
Email::send();

/*
------------------------

SMTP with vendor details
--------------------------

*/

Email::init(1,'smtp',3);
Email::subject("sample subject");
Email::fromEmail(array('habits@zoojoo.be'=>'Zoojoo.be'));
Email::toEmail(array('veeru@zoojoo.be'));
Email::body('Test body');
Email::send();