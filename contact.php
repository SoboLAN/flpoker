<?php

require_once 'autoload.php';

use FileListPoker\Main\Site;
use FileListPoker\Main\Config;
	
$site = new Site();
	
$htmlout = $site->getHeader('contact.php');
	
$htmlout .= '<div id="title">' . $site->getWord('menu_contact') . '</div>';
	
$htmlout .= '<div id="content-narrower">';

function getCaptcha ()
{
	$out = '<script type="text/javascript">
		var RecaptchaOptions = {
		theme : \'blackglass\'
		};
		</script>';
	
	require_once ('recaptchalib.php');

	$out .= recaptcha_get_html (Config::getValue('recaptcha_public_key'));
		
	return $out;
}
	
function getForm ()
{
	$output = '
	<form name="form" method="post" action="contact.php">
	<table style="width:850px;">
		<tr><th><div style="text-align:right;">
			<input type="checkbox" name="confirm" onclick="javascript:if(document.form.confirm.checked==true) document.form.submit.disabled=false; else document.form.submit.disabled=true;" /></div></th><td>I confirm that this message is not spam.</td></tr>
		<tr><th><div style="text-align:right;">Subject:</div></th><td><input style="margin-top: 10px" type="text" name="subject"'; if (isset ($_POST['subject'])) { $output .= "value=\"{$_POST['subject']}\""; } $output .= ' size="40" maxlength="40" /></td></tr>
		<tr><th><div style="text-align:right;">Your email address:</div></th><td><input style="margin-top: 10px; margin-bottom: 10px" type="text" name="email"'; if (isset ($_POST['email'])) { $output .= "value=\"{$_POST['email']}\""; } $output .= ' size="40" maxlength="40" /> (optional, if you want me to have a way to contact you)</td></tr>
		<tr><th style="vertical-align:middle;"><div style="text-align:right;">Message:</div></th><td><textarea name="message" cols="70" rows="20"></textarea></td></tr>
	</table>
	<input type="hidden" name="is_submitted" value="TRUE" />
	<br /><br />';
		
	$output .= getCaptcha ();
	
	$output .= '<br /><br />
			<div style="text-align:center;"><input type="submit" name="submit" value="Send Message" disabled /></div>
			<br />
			</form>';
				
	return $output;
}
	
if (! isset ($_POST['is_submitted']))
{
	$htmlout .= getForm ();
		
	$htmlout .= '</div>
			' . $site->getFooter () . '
			</body>
			</html>';
		
	echo $htmlout;
		
	exit (0);
}
	
require_once ('recaptchalib.php');
$resp = recaptcha_check_answer (Config::getValue('recaptcha_private_key'),
								$_SERVER["REMOTE_ADDR"],
								$_POST["recaptcha_challenge_field"],
								$_POST["recaptcha_response_field"]);
	
if (!$resp->is_valid)
{
	$htmlout .= '<span style="font-size:130%;color:#d00;text-decoration:bold">The CAPTCHA code is incorrect.</span>';
		
	$htmlout .= getForm ();
		
	$htmlout .= '</div>
			' . $site->getFooter () . '
			</body>
			</html>';
		
	echo $htmlout;
		
	exit (0);
}
	
function spamcheck($field)
{
	//filter_var() sanitizes the e-mail
	//address using FILTER_SANITIZE_EMAIL
	$field=filter_var($field, FILTER_SANITIZE_EMAIL);

	//filter_var() validates the e-mail
	//address using FILTER_VALIDATE_EMAIL
	if(filter_var($field, FILTER_VALIDATE_EMAIL))
	{
		return TRUE;
	}
	else
	{
		return FALSE;
	}
}
	
$from = 'nobody@example.com';
	
if (strlen ($_POST['email']) > 0)
{
	$from_check = spamcheck ($_POST['email']);
	if ($from_check == FALSE)
	{
		$htmlout .= '<span style="font-size:130%;color:#d00;text-decoration:bold">Nice try.</span>';
			
		$htmlout .= getForm ();
			
		$htmlout .= '</div>
				' . $site->getFooter () . '
				</body>
				</html>';
			
		echo $htmlout;
		
		exit (0);
	}
		
	$from = $_POST['email'];
}
	
$subject = $_POST['subject'];
$message = $_POST['message'];
$to = Config::getValue('contact_email');
	
if (strlen ($subject) < 4 OR strlen ($message) < 10)
{
	$htmlout .= '<span style="font-size:130%;color:#d00;text-decoration:bold">Totuși scrie ceva.</span>';
		
	$htmlout .= getForm ();
		
	$htmlout .= '</div>
			' . $site->getFooter () . '
			</body>
			</html>';
		
	echo $htmlout;
	
	exit (0);
}
	
$success = mail ($to, $subject, $message, "From:" . $from);
	
if ($success == TRUE)
{
	$htmlout .= '<strong>The message was sent successfully.</strong></div>
				' . $site->getFooter () . '
				</body>
				</html>';
}
else
{
	$htmlout .= '<strong>The message was not sent.</strong></div>
				' . $site->getFooter () . '
				</body>
				</html>';
}

echo $htmlout;