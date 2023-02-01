<?php
$feedback = '<div class="bg-danger text-white mt-4 p-3">pls fix this stuff:<br>';
$header = 'verify that you are human >:(';
$isHidden = true;

function yearVerification(){
	$yearBorn = intval($_POST["year"]);
	if ($yearBorn === 0){
		return "0";
	}
	if ($yearBorn < 1900){
		return "1";
	}
	else if ($yearBorn > intval(date("Y"))){
		return "2";
	}
	else {
		return "3";
	}
}

function monthVerification(){
	$monthBorn = intval($_POST["month"]);
	$yearBorn = intval($_POST["year"]);
	if ($monthBorn < 1){
		return "0";
	}
	else if ($yearBorn === intval(date("Y"))){
		if ($monthBorn > intval(date("m"))){
			return "1";
		}
		else {
			return "2";
		}
	}
	else {
		return "2";
	}
}

function dayVerification(){
	$dayBorn = intval($_POST["day"]);
	$monthBorn = intval($_POST["month"]);
	$yearBorn = intval($_POST["year"]);
	
	if ($dayBorn < 1){
		return "0";
	}
	else {
		if ($monthBorn === 1 || $monthBorn === 3 || $monthBorn === 5 || $monthBorn === 7 || $monthBorn === 8 || $monthBorn === 10 || $monthBorn === 12){
			if ($dayBorn > 31){
				return "1";
			}
		}
		else if ($monthBorn === 4 || $monthBorn === 6 || $monthBorn === 9 || $monthBorn === 11){
			if ($dayBorn > 30){
				return "1";
			}
		}
		else if ($monthBorn === 2 && $dayBorn > 29){
			return "1";
		}
		
		if ($yearBorn === intval(date("Y")) && $monthBorn === intval(date("m"))){
			if ($dayBorn > intval(date("d"))){
				return "2";
			}
			else {
				return "3";
			}
		}
	}
}

$bformsubmitted = isset($_POST["year"]);
function submitForm(){
	global $bformsubmitted;
	global $feedback;
	global $isHidden;
	
	if ($bformsubmitted === true){
		$isHidden = true;
		$bhaserror = false;
		
		if (yearVerification() === "0"){
			$feedback .= '<ul><li>please enter a year >:(</li></ul>';
			$bhaserror = true;
		}
		if (yearVerification() === "1"){
			$feedback .= '<ul><li>no living human is that old >:(</li></ul>';
			$bhaserror = true;
		}
		if (yearVerification() === "2"){
			$feedback .= "<ul><li>no human was born on that year yet >:(</li></ul>";
			$bhasserror = true;
		}
		
		if (yearVerification() === "3"){
			if (monthVerification() === "0"){
				$feedback .= "<ul><li>please select a month >:(</li></ul>";
				$bhaserror = true;
			}
			if (monthVerification() === "1"){
				$feedback .= "<ul><li>we haven't even made it to that month yet this year >:(</li></ul>";
				$bhaserror = true;
			}
			
			if (monthVerification() === "2"){
				if (dayVerification() === "0"){
					$feedback .= "<ul><li>please enter a day >:(</li></ul>";
					$bhaserror = true;
				}
				if (dayVerification() === "1"){
					$feedback .= "<ul><li>that day isn't even possible in that month >:(</li></ul>";
					$bhaserror = true;
				}
				if (dayVerification() === "2"){
					$feedback .= "<ul><li>we haven't even made it to that specific day yet >:(</li></ul>";
					$bhaserror = true;
				}
			}
		}
		
		if ($bhaserror === true){
			return "0";
		}
		if ($bhaserror === false){
			$feedback = '<div class="bg-info text-white mt-4 p-3">you really are human! you may now proceed :)';
			$isHidden = false;
			return "1";
			
		}
	}
}

if (submitForm() === "1"){
	$dayBorn = intval($_POST["day"]);
	$monthBorn = intval($_POST["month"]);
	$yearBorn = intval($_POST["year"]);
	$currentDay = intval(date("d"));
	$currentMonth = intval(date("m"));
	$currentYear = intval(date("Y"));
	
	$secondsAlive = 0;
	$daysAlive = 0;
	$daysSince = $currentDay - $dayBorn;
	$monthsSince = $currentMonth - $monthBorn;
	
	$daysAlive += ($currentYear - $yearBorn)*365;
	$daysAlive += $daysSince;
	$daysAlive += $monthsSince * 30;
	
	$secondsAlive += $daysAlive * 86400;
	
	$secondsAliveDiv = "<div class='rounded mt-4 p-3 col-10 offset-1 border'><h5>by the way, you've been alive for about $secondsAlive seconds :)</h5></div>";
	
	echo $secondsAliveDiv;
}

$bcontinuesubmit = isset($_POST["continue"]);
if ($bcontinuesubmit === true){
	if ($_POST["continue"] === "0"){
		header('location: ./moviesfreereal.php');
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

    	<title>verification.com</title>
  	</head>
  	<body>
		<div class="col-10 offset-1">
    		<h1 class="mt-4 mb-3">
				<?php
				echo $header;
				?>
			</h1>
			
			<form method="post" name="form">
				<label for="birthdayitems">date of birth info:</label>
				<div id="birthdayitems" class="rounded col-12 border mb-4">
					<div class="row">
						<div class="col-12 col-md-4 mt-4 mb-4">
							<label for="year">pls enter birth year</label>
							<input name="year" id="year" type="text" class="rounded form-control mt-2" placeholder="birth year goes here :)">
						</div>
						<div class="col-12 col-md-4 mt-4 mb-4">
							<label for="bmonth">pls select birth month</label>
							<div id="bmonth" class="input-group">
								<select class="rounded text-center form-control mt-2" id="month" name="month">
									<option value="0" selected>Select Month</option>
									<option value="1">January</option>
									<option value="2">February</option>
									<option value="3">March</option>
									<option value="4">April</option>
									<option value="5">May</option>
									<option value="6">June</option>
									<option value="7">July</option>
									<option value="8">August</option>
									<option value="9">September</option>
									<option value="10">October</option>
									<option value="11">November</option>
									<option value="12">December</option>
								</select>
							</div>
						</div>
						<div class="col-12 col-md-4 mt-4 mb-4">
							<label for="day">pls enter birthday</label>
							<input id="day" name="day" type="text" class="rounded form-control mt-2" placeholder="birthday goes here :)">
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
			<form method="post">
				<div class="col-1 offset-11 mt-4" id="continueDiv" <?php if ($isHidden === true){echo "hidden";}?>>
					<label for="continueSelect">continue?</label>
					<div id="continueSelect" class="input-group">
						<select class="rounded text-center form-control" id="continue" name="continue">
							<option value="0">Yes</option>
						</select>
					</div>
					<input type="submit" name="submit" class="btn-primary text-light rounded p-1 mt-3">
				</div>
				
			</form>
		</div>

    	<!-- Optional JavaScript -->
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  	</body>
</html>
