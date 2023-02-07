<?php
$feedback = '<div class="bg-danger text-white mt-4 p-3">Please fix the following:<br>';
$recaptchaData = recaptcha();

function recaptcha():array{
	$n1 = mt_rand(1,20);
	$n2 = mt_rand(1,20);
	$arr = array(
		'n1' => $n1,
		'n2' => $n2,
		'sum' => $n1 + $n2);
	return $arr;
}

function validEmail(){
	$email = trim($_POST["email"]);
	if (strlen($email) < 5)
		return "0";
	$atat = strpos($email, '@');
	$dotat = strpos($email, '.');
	if (false === $dotat)
		return "1";
	if (false === $atat)
		return "1";
	if ($dotat < $atat)
		return "2";
	return "3";
}

function validPass(){
	$password = $_POST["password"];
	$hasAst = strpos($password, '*');
	$hasDol = strpos($password, '$');
	if (strlen($password) < 5){
		return "0";
	}
	if ($hasAst || $hasDol > -1){
		return "1";
	}
	return "2";
}

function validCaptcha(){
	$input = $_POST["captcha"];
	$answer = $_POST["captchaAns"];
	$hashInput = hash("gost", $input);
	echo "User Input Hash: $hashInput <br>";
	if ($hashInput !== $answer){
		return "0";
	}
	else {
		return "1";
	}	
}

$bformsubmit = isset($_POST["email"]);
if ($bformsubmit === true){
	$bhaserror = false;
	
	if (validEmail() !== "3"){
		$feedback .= '<p>Your email is invalid</p>';
		$bhaserror = true;
		
		if (validEmail() === "0"){
			$feedback .= '<ul><li>Email must have at least 5 characters</li></ul>';
		}
		if (validEmail() === "1"){
			$feedback .= '<ul><li>Email must have an "." and an "@"</li></ul>';
		}
		if (validEmail() === "2"){
			$feedback .= '<ul><li>"@" must come before "." in email</li></ul>';
		}
	}
	
	if (validPass() !== "2"){
		$feedback .= '<p>Your password is invalid</p>';
		$bhaserror = true;
		
		if (validPass() === "0"){
			$feedback .= '<ul><li>Password must have at least 5 characters</li></ul>';
		}
		if (validPass() === "1"){
			$feedback .= '<ul><li>Password cannot contain an "*" or a "$"</li></ul>';
		}
	}
	
	if (validCaptcha() !== "1"){
		$feedback .= '<p>Your captcha answer was incorrect</p>';
		$bhaserror = true;
	}
	
	if ($bhaserror === false){
		$feedback = "<div class='bg-info text-white mt-4 p-3'>Successfully logged in!";
		header('location: ./dashboard.php');
	}
	
}

var_dump($_POST);

?>
<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">

    <title>Login!</title>
  </head>
  <body>
	  	<div class="col-9 offset-1">
			<div class="border">
				<p class="p-3">Upgrade your life by accessing these premium educational content including a calculator that will tell you how many years you've been alive!!</p>
			</div>
			<form method="post" name="form">
				<div class="input-group">
					<select class="form-control col-6" id="emailselect">
						<option value="">Email Testing</option>
						<option value="emailshort">Too Short</option>
						<option value="noatsign">No at sign</option>
						<option value="noperiod">No period</option>
						<option value="wrongorder">Period before at sign</option>
						<option value="emailallgood">Email is Good</option>
					</select>
					<select class="form-control col-6" id="pwordselect">
						<option value="">Password Testing</option>
						<option value="pworddollarsign">Dollar Sign in Pword</option>
						<option value="pwordasterisk">Asterisks Sign in Pword</option>
						<option value="pwordtooshort">Too Short</option>
						<option value="pwordallgood">Password is Good</option>
					</select>
				</div>
				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<span class="input-group-text">Email</span>
					</div>
					<input type="text" name="email" id="userEmail" class="form-control" placeholder="Enter your Email">
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<span class="input-group-text">Password</span>
					</div>
					<input type="text" id="userPword" class="form-control" name="password" placeholder="Create a Password">
				</div>

				<div class="input-group mb-3">
					<div class="input-group-prepend">
						<span class="input-group-text" id="random-question">What is <?php echo $recaptchaData['n1'];?> + <?php echo $recaptchaData['n2'];?>?</span>
					</div>
					<input type="text" name="captcha" class="form-control" id="botAnswer" placeholder="Answer Question">
					<input type="hidden" name="captchaAns" value="<?php echo hash("gost", $recaptchaData['sum']);?>"/>
				</div>

				<button class="bg-primary text-white border-0 rounded" id="loginbttn" type="submit" name="submit">Login</button><br>
				
				<div class="col-12" name="error">
				<?php
					if ($bformsubmit && $feedback > 0){
							echo $feedback;
							echo '</div>';
						}
				?>
				</div>
				<div id="errorDivOne"></div>
				<div id="errorDivTwo"></div>
				<div id="errorDivThree"></div>
				<div id="successDiv"></div>
			</form>
		</div>
		
    <!-- Optional JavaScript -->
    <!-- jQuery first, then Popper.js, then Bootstrap JS -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  </body>
	<script>
		let docG = (id) => {return document.getElementById(id);}
		let generateAnswer = -1
		
		let generateBotQ = () => {
			let num1 = Math.floor(Math.random() * 19) + 1;
			let num2 = Math.floor(Math.random() * 19) + 1;
			document.getElementById("random-question").innerHTML = "What is " + num1 + " + " + num2;
			generateAnswer = num1 + num2
		}
		
		let isValidPword = (str) => {
			if (str.length < 5) {
				return false;
			}
			if (str.indexOf("$") > -1 || str.indexOf("*") > -1){
				return false;
				console.log("invalid, wrong signs")
			}
			return true;
		}
		
		let isValidEmail = (str) => {
			if (str.length < 5) {
				return false;
			}
			if (str.indexOf(".") < 0) {
				console.log("Period before @")
				return false;
			}
			if (str.indexOf("@") < 0) {
				return false;
			}
			if (str.indexOf("@") > str.indexOf(".")) {
				return false;
			}
			return true;
		}
		
		let emailSelect = document.getElementById("emailselect");
		
		emailSelect.addEventListener("change", function(evt){
			let selected = docG("emailselect").value;
			console.log(selected);
			
			const CASE_EMAIL_SHORT = "emailshort";
			const CASE_NO_AT = "noatsign";
			const CASE_NO_PERIOD = "noperiod";
			const CASE_WRONG_ORDER = "wrongorder";
			const CASE_EMAIL_GOOD = "emailallgood";
			
			let emailElem = docG("userEmail")
			
			if (selected === CASE_EMAIL_SHORT){
				emailElem.value = "ab@."
			}
			if (selected === CASE_NO_AT){
				emailElem.value = "abcdefgmail.com"
			}
			if (selected === CASE_NO_PERIOD){
				emailElem.value = "abcdef@gmailcom"
			}
			if (selected === CASE_WRONG_ORDER){
				emailElem.value = "abcdef.gmail@com"
			}
			if (selected === CASE_EMAIL_GOOD){
				emailElem.value = "abcdef@gmail.com"
			}
			
			
		})
		
		let pWordSelect = document.getElementById("pwordselect");
		
		pWordSelect.addEventListener("change", function(evt){
			let selected = docG("pwordselect").value;
			console.log(selected);
			
			const CASE_PWORD_TOO_SHORT = "pwordtooshort";
			const CASE_PWORD_HAS_DOLLSIGN = "pworddollarsign"
			const CASE_PWORD_HAS_ASTERISK = "pwordasterisk"
			const CASE_PWORD_GOOD = "pwordallgood"
			
			let pWordElem = docG("userPword")
			
			if (selected === CASE_PWORD_TOO_SHORT){
				pWordElem.value = "abc"
			}
			if (selected === CASE_PWORD_HAS_DOLLSIGN){
				pWordElem.value = "abcde$"
			}
			if (selected === CASE_PWORD_HAS_ASTERISK){
				pWordElem.value = "abcde*"
			}
			if (selected === CASE_PWORD_GOOD){
				pWordElem.value = "abcdef"
			}
			
		});
	</script>
</html>
