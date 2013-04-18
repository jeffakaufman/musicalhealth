<?php
/**
* @package WordPress
* @subpackage Toolbox
* Template Name: Support
*/
get_header(); ?>
<?php $supportEmail = "SUPPORT@MUSICALHEALTHTECH.COM"; ?>
<div id="main">
	<div id="primary">
		<div id="interior" role="main" class="support">
			<div id="left_column">
<!-- BEGIN FORM CODE -->	
<?php
if($_POST) {
	processForm($supportEmail);
} else {
	showForm($firstname, $lastname, $email, $vemail, $device, $ios, $message, $error);
}

function processForm($supportEmail) {
	$firstname = $_POST['firstname'];
	$lastname = $_POST['lastname'];
	$email = $_POST['email'];
	$vemail = $_POST['vemail'];
	$device = $_POST['device'];
	$ios = $_POST['ios'];	
	$message = $_POST['message'];
	
	$error = "";
	if(!$firstname) $error .= "<li>First Name is required</li>\n";
	if(!$lastname) $error .= "<li>Last Name is required</li>\n";
	if(!$email) $error .= "<li>Email address is required</li>\n";
	if(!$vemail) $error .= "<li>Please verify your email address</li>\n";
	if($email != $vemail) $error .=	"<li>Invalid email address; make sure the email and verify email fields match</li>\n";
	if(!$device) $error .= "<li>Please select your device)</li>\n";
	if(!$ios) $error .= "<li>Please select your iOS version)</li>\n";	
	if(!$message) $error .= "<li>Please describe your problem</li>\n";
	if($error == "") $error = false;
	
	if($error) {
		showForm($firstname, $lastname, $email, $vemail, $device, $ios, $message, $error);
	} else {
		sendEmail($supportEmail, $firstname, $lastname, $email, $vemail, $device, $ios, $message);
	}
}

function sendEmail($supportEmail, $firstname, $lastname, $email, $vemail, $device, $ios, $message) {
	// compose and send an email for confirmation
	$to = $supportEmail;
	$subject = 'iPhone Support';
	$mailmessage = "$message\n\n";
	$mailmessage .= "Name: $firstname $lastname\n";
	$mailmessage .= "Email: $email\n";
	$mailmessage .= "Device: $device\n";
	$mailmessage .= "iOS: $ios\n";	
	
	// In case any of the message is larger than 70 characters, we should wordwrap
	$mailmessage = wordwrap($mailmessage, 70);
	
	$headers = 'From: '.$email.'' . "\r\n" .
	'Reply-To: '.$email.'' . "\r\n";
	$headers .= 'X-Mailer: PHP/' . phpversion();
	// send the mail
	mail($to, $subject, $mailmessage, $headers);
	//echo "$to\n";
	//echo "$subject\n";
	//echo "$mailmessage\n";
	
	echo "<p>Your support request has been received. Thanks!<br><br>
	Our Support Team works Monday-Friday 8 hours a day. We are closed weekends and holidays. You can expect a response from us within 24-48 hours of your support request. It may take a bit longer if your question is submitted between Friday and Sunday.<br><br>Feel free to email further details regarding this Support Request by completing the Support Form again. Please use the same email address when communicating with us so that our system can best keep track of your emails.<br><br>We appreciate your business and will do our best to get back to you in a timely manner.<br><br>While you are waiting for our response, click on NEWS above to see our newsletter promotions.<br><br>Thanks,<br>Sonoma Wire Works Support Team
</p>";
}

// Show the Form
function showForm($firstname, $lastname, $email, $vemail, $device, $ios, $message, $error) {
	if($error) {
		echo '<div class="error nuc"><div class="nuc"><div class="nuc"><div class="nuc">';
		echo "<ul>\n";
		echo $error;
		echo "</ul>";
		echo '</div></div></div></div>';
	}
?>
			
<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST"> 
<div id="page_title">Support</div> 
<h3>Please complete the form so we can help you as quickly as possible!</h3>
<p>Do you want to request a song? <a href="http://www.musicalhealthtech.com/wordpress/?page_id=114">Use our Song Request Form instead.</a></p>
<br />

<div class="inputgroup"> 
<label class="required">First Name<input class="text" name="firstname" id="firstname" value="" /></label> 
<label class="required">Last Name<input class="text" name="lastname" id="lastname" value="" /></label> 
<label class="required">Email Address<input class="text" name="email" id="email" value="" /></label> 
<label class="required">Verify Email Address<input class="text" name="vemail" id="vemail" value="" /></label> 
 
<label class="required">Hardware
<select name="device" id="device"> 
	<option>Choose One</option> 
	<option>iPad WiFi</option> 
	<option>iPad WiFi 3G</option> 
	<option>iPhone 4</option> 
	<option>iPhone 3GS</option> 
    <option>iPhone 3G</option> 
    <option>iPhone</option> 
	<option>iPod touch (4th Generation)</option> 
	<option>iPod touch (3rd Generation)</option> 
	<option>iPod touch (2nd Generation)</option>	
	<option>I don't have any of these yet</option> 
</select> 
</label> 
 
<label class="required">iOS Version
<select name="ios" id="ios"> 
	<option>Choose One</option> 
	<option>iOS 4.3</option> 
	<option>iOS 4.2</option> 
	<option>iOS 4.1</option> 
	<option>iOS 4.0.2</option> 
	<option>iOS 4.0.1</option> 
	<option>iOS 4.0</option> 
	<option>iOS 3.2</option> 
	<option>iOS 3.1.3</option> 
	<option>iOS 3.1</option> 
	<option>iOS 3.0</option> 
	<option>iOS 2.1</option> 
	<option>I don't have any of these yet</option> 
</select> 
</label>
<br />

<label class="textarea required"> 
Please describe the problem
<textarea class="large" name="message" id="message"></textarea> 
</label> 

<input type="submit" value="submit" class="submit" /> 
</div> 

<div class="clear"> </div> 
</form>

<?php } // eof showForm ?>

<!-- END FORM CODE -->

				</div>
				<?php get_sidebar(); ?>
				<div class="clear"></div>	
			</div><!--#content-->
		</div><!--#primary-->
<?php get_footer(); ?>
