<?php
$debug = true;
$recaptchaData = recaptcha();
$feedback = '<div class="bg-danger text-white mt-4 p-3">Please fix the following:<br>';

function recaptcha():array{
	$n1 = mt_rand(1,20);
	$n2 = mt_rand(1,20);
	$arr = array(
		'n1' => $n1,
		'n2' => $n2,
		'sum' => $n1 + $n2);
	return $arr;
}

function validEmail(string $email):bool{
	$email = trim($email);
	if (strlen($email) < 5)
		return "untrue";
	$atat = strpos($email, '@');
	$dotat = strpos($email, '.');
	if (false === $dotat)
		return false;
	if (false === $atat)
		return false;
	if ($dotat < $atat)
		return false;
	return true;
}

function validAge():bool{
	$age = $_POST['age'];
	if ($age < 18){
		return false;
	}
	return true;
}

function validPassword():bool{
	$password = $_POST['password'];
	if (strlen($password) < 5){
		return 5;
	}
	if (strpos($password, '$') || strpos($password, '*') > -1){
		return false;
	}
	return true;
}

function validRecaptcha():bool{
	$answer = $_POST['captcha'];
	if ($_POST['captchaAns'] != $answer){
		return false;
	}
	else{
		return true;
	}
}

function validUsername():bool{
	$name = $_POST['username'];
	if (strlen($name) < 5){
		return false;
	}
	else {
		return true;
	}
}

$bFormSubmitted = isset($_POST['email1']);
if ($bFormSubmitted === true){
	$bHasError = false;
	$email1 = trim($_POST['email1']);
	$email2 = trim($_POST['email2']);
	$password = $_POST['password'];
	
	$bValidEmail = validEmail($email1);
	if (validUsername() === false){
		$feedback .= "<ul><li>Your username is too short</li></ul>";
		
	}
	if ($email1 != $email2){
		$feedback .= "<ul><li>Your emails do not match</li></ul>";
		$bHasError = true;
	}
	if (false === $bValidEmail){
		$feedback .= "<ul><li>" . $_POST['email1'] . " is not valid</li></ul>";
		$bHasError = true;
	}
	if (strlen($email1) < 5){
		$feedback .= "<ul><li>Your email is too short</li></ul>";
		$bHasError = true;
	}
	if (false === validAge()){
		$feedback .= "<ul><li>You must be at least 18 years old</li></ul>";
		$bHasError = true;
	}
	if (strlen($password) < 5){
		$feedback .= "<ul><li>Your password is too short</li></ul>";
		$bHasError = true;
	}
	if (validPassword() === false){
		$feedback .= "<ul><li>Cannot contain '$' or '*'</li></ul>";
		$bHasError = true;
	}
	if (validRecaptcha() === false){
		$feedback .= "<ul><li>Wrong Recaptcha answer</li></ul>";
		$bHasError = true;
	}
	if ($bHasError === false){
		$feedback = "<div class='bg-info text-white mt-4 p-3'>Thanks for registering!";
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

		<title>Register</title>
	</head>
	<body>
		<h1 class="m-5">Register for Mr M.'s Premium Content</h1>
		<h1>Delete this after you redirect to other page once logged in</h1>
		<div class="col-10 col-md-11 m-5 pl-3 pt-4 pb-5 border">
			Upgrade your <u>life</u> by accessing this premium educational content including a calculator that will tell you how many years you have been alive!!
		</div>
			<form method="post" name="form">
				<div class="row col-12 m-2">
					<div class="col-12 col-md-6 border p-2">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text rounded-left">Username</span>
							</div>
							<input id="username" name="username" type="text" class="form-control rounded-right" placeholder="Enter a Username">
						</div>				
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text rounded-left">Age</span>
							</div>
							<input id="age" name="age" type="number" class="form-control rounded-right" placeholder="Enter your Age">
						</div>				
					</div>
					<div class="col-12 col-md-6 border p-2">
						<div class="form-check">
							<label class="form-check label">
								<input type="radio" name="gender" class="form-check-input" name="gender" value="male" id="m">Male
							</label>
						</div>
						<div class="form-check">
							<label class="form-check label">
								<input type="radio" name="gender" class="form-check-input" name="gender" value="female" id="f">Female
							</label>
						</div>
						<div class="form-check">
							<label class="form-check label">
								<input type="radio" name="gender" class="form-check-input" name="gender" value="other" id="other">Other
							</label>
						</div>
					</div>
					<div class="col-12 border p-2">
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text rounded-left">Password</span>
							</div>
							<input id="pword" name="password" type="text" class="form-control rounded-right" placeholder="Enter your Password">
						</div>				
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text rounded-left">Email</span>
							</div>
							<input id="email1" name="email1" type="text" class="form-control rounded-right" placeholder="Enter your Email">
						</div>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text rounded-left">Confirm Email</span>
							</div>
							<input id="email2" name="email2" type="text" class="form-control rounded-right" placeholder="Confirm your Email">
						</div>
						<div class="input-group mb-3">
							<div class="input-group-prepend">
								<span class="input-group-text" id="random-question">What is <?php echo $recaptchaData['n1'];?> + <?php echo $recaptchaData['n2'];?>?</span>
							</div>
							<input type="text" name="captcha" class="form-control" id="botAnswer" placeholder="Answer Question">
							<input type="hidden" name="captchaAns" value="<?php echo $recaptchaData['sum'];?>"/>
						</div>
					</div>
					<input type="submit" name="submit" class="offset-1 btn-primary text-light rounded p-1 mt-3 mb-3" id="registerbttn">
					
					<div class="col-12" name="errors"><?php
						if ($bFormSubmitted && $feedback > 0){
							echo $feedback;
							echo '</div>';
						}
						?></div>
					
					<div style="opacity: 0;" id="error" class="bg-danger text-white col-12 p-2">Please fix the following:</div>
					<div style="opacity: 0;" id="noError" class="bg-info text-white col-12 p-2">Thanks for registering!</div>
					
				</div>
			</form>
		<!-- Optional JavaScript -->
		<!-- jQuery first, then Popper.js, then Bootstrap JS -->
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<script>
			
			let docG = (id) => {return document.getElementById(id);}
			
			docG("username").focus();
			
			let userCheck = () => {
				if (docG("username").value.length < 5){
					return false;
				}
				return true;
			}
			
			let passCheck = () => {
				if (docG("pword").value.length < 5){
					return false;
				}
				if (docG("pword").value.indexOf("$") > -1 || docG("pword").value.indexOf("*") > -1){
					return false;
				}
				return true;
			}
			
			let emailCheck = () => {
				if (docG("email1").value.length < 5){
					return false;
				}
				if (docG("email1").value.indexOf("@") < 0 || docG("email1").value.indexOf(".") < 0){
					return false;
				}
				if (docG("email1").value.indexOf("@") > docG("email1").value.indexOf(".")){
					return false;
				}
				return true;
			}
			
			let emailConfirm = () => {
				if (docG("email1").value !== docG("email2").value){
					return false;
				}
				return true;
			}
			
			let ageCheck = () => {
				if (docG("age").value < 18){
					return false;
				}
				return true;
			}
			
			let genderCheck = () => {
				if (docG("m").checked === false && docG("f").checked === false && docG("other").checked === false){
					return false;
				}
				return true;
			}
			
			let loginValidation = () => {
				let bHasError = false;
				let htm = "<div>Please fix the following:</div>";
				
				if (userCheck() === false){
					bHasError = true;
					htm += "<ul><li>Username must have 5 characters</li></ul>"
				}
				
				if (passCheck() === false){
					bHasError = true;
					htm += "<ul><li>Password must:<ul><li>Have 5 characters</li><li>Not contain '$' or '*'</li></ul></li></ul>"
				}
				
				if (emailCheck() === false){
					bHasError = true;
					htm += "<ul><li>Email must:<ul><li>Have 5 characters</li><li>Contain '@' and '.'</li><li>Have '@' come before '.'</li></ul></li></ul>"
					
				}
				
				if (emailConfirm() === false){
					bHasError = true;
					htm += "<ul><li>Emails must match</li></ul>"
				}
				
				if (ageCheck() === false){
					bHasError = true;
					htm += "<ul><li>You must be over 18</li></ul>"
				}
				
				if (genderCheck() === false){
					bHasError = true;
					htm += "<ul><li>Please enter a gender</li></ul>"
				}
				
				if (bHasError === true){
					$("#error").fadeTo("slow", 0)
					docG("error").innerHTML = htm
					$("#error").removeClass("bg-info")
					$("#error").addClass("bg-danger")
					$("#error").fadeTo("slow", 1)
				}
				
				else {
					$("#error").fadeTo("slow", 0)
					docG("error").innerHTML = "Thank you for registering!"
					$("#error").removeClass("bg-danger")
					$("#error").addClass("bg-info")
					$("#error").fadeTo("slow", 1)
				}
			}
			
			docG("registerbttn").addEventListener("click", function(e){
				e.preventDefault;
				loginValidation();
			});
		</script>
	</body>
</html>