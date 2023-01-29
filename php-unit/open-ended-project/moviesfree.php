<?php
$feedback = '<div class="bg-danger text-white mt-4 p-3">pls fix this stuff:<br>';

function ssidVerification():bool{
	$ssid = $_POST["ssid"];
	if (strlen($ssid) > 32){
		return false;
	}
	else {
		return true;
	}
}

function wifiPassVerification():bool{
	$wifipword = $_POST["wifipword"];
	if (strlen($wifipword) < 8){
		return false;
	}
	else {
		return true;
	}
}

function emailVerification():bool{
	$email = $_POST["email"];
	$atat = strpos($email, '@');
	$dotat = strpos($email, '.');
	if ($atat === false){
		return false;
	}
	if ($dotat === false){
		return false;
	}
	if ($atat > $dotat){
		return false;
	}
	return true;
}

function emailPassVerification():bool{
	$emailpword = $_POST["emailpword"];
	if (strlen($emailpword) < 8){
		return false;
	}
	return true;
}

$bformsubmitted = isset($_POST['ssid']);
if ($bformsubmitted === true){
	$bhaserror = false;
	$ssid = $_POST["ssid"];
	$wifipword = $_POST["wifipword"];
	$email = $_POST["email"];
	
	if (ssidVerification() === false){
		$feedback .= '<ul><li>the ssid should be less than 32 characters :)</li></ul>';
		$bhaserror = true;
	}
	if (strlen($ssid) < 1){
		$feedback .= '<ul><li>pls enter an ssid :)</li></ul>';
		$bhaserror = true;
	}
	if (wifiPassVerification() === false){
		$feedback .= '<ul><li>the wifi password is too short :)</li></ul>';
		$bhaserror = true;
	}
	if (strlen($wifipword) > 63){
		$feedback .= '<ul><li>the wifi password cant be more than 63 characters :) (why would you even do this?)</li></ul>';
		$bhaserror = true;
	}
	
	if (strlen($email) < 5){
		$feedback .= '<ul><li>email is too short :)</li></ul>';
		$bhaserror = true;
	}
	else if (emailVerification() === false){
		$feedback .= '<ul><li>' . $_POST['email'] . ' is invalid :)</li></ul>';
		$bhaserror = true;
	}
	if (emailPassVerification() === false){
		$feedback .= '<ul><li>your password is too short :)</li></ul>';
		$bhaserror = true;
	}
	
	if ($bhaserror === false){
		$feedback = '<div class="bg-info text-white mt-4 p-3">you did it! :)';
		header('location: ./humanverify.php');
	}
}

?>
<!doctype html>
<html lang="en">
 	<head>
    	<!-- Required meta tags -->
    	<meta charset="utf-8">
    	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    	<!-- Bootstrap CSS -->
    	<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
 		<!-- jQuery --> 
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js" crossorigin="anonymous"></script> 

    	<title>moviesfree.com</title>
  	</head>
  	<body>
		<div class="col-10 offset-1">
    		<h1 class=mt-4>have you ever wanted movies free?</h1>
			
			<div class="m-2 p-3 border rounded"><h4>please fill out this form for movies free :)</h4></div>
			
			<form method="post" name="form">
				<label for="wifiitems">wifi info:</label>
				<div id="wifiitems" class="rounded col-12 border mb-4">
					<div class="row">
						<div class="col-12 col-md-6 mt-4 mb-4">
							<label for="ssid">pls enter wifi ssid</label>
							<input name="ssid" id="ssid" type="text" class="rounded form-control mt-2" placeholder="ssid goes here :)">
						</div>
						<div class="col-12 col-md-6 mt-4 mb-4">
							<label for="wifipword">pls enter wifi password</label>
							<input id="wifipword" name="wifipword" type="text" class="rounded form-control mt-2" placeholder="password goes here :)">
						</div>
					</div>
				</div>
				
				<label for="emailitems">wacky lil' email stuff:</label>
				<div id="emailitems" class="rounded col-12 border mb-4">
					<div class="row">
						<div class="col-12 col-md-6 mt-4 mb-4">
							<label for="email">enter email pls</label>
							<input id="email" name="email" type="text" class="rounded form-control mt-2" placeholder="email go here :)">
						</div>
						<div class="col-12 col-md-6 mt-4 mb-4">
							<label for="emailpword">enter email pls</label>
							<input id="emailpword" name="emailpword" type="text" class="rounded form-control mt-2" placeholder="GIVE PASSWORD NOW!">
						</div>
					</div>
				</div>
				
				<input type="submit" name="submit" class="btn-primary text-light rounded p-1 mt-3 mb-3">
				
				<div class="col-12" name="errors">
					<?php
					if ($bformsubmitted && $feedback > 0){
						echo $feedback;
						echo '</div>';
					}
					?>
				</div>
			</form>
		</div>

    	<!-- Optional JavaScript -->
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  	</body>
</html>