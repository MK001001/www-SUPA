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
    		<h1 class="mt-4">verify that you are human >:(</h1>
			
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
								<select class="rounded text-center form-control mt-2" id="month">
									<option value="0" disabled selected>Select Month</option>
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
		</div>

    	<!-- Optional JavaScript -->
		<script src="https://cdn.jsdelivr.net/npm/popper.js@1.12.9/dist/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
		<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.0.0/dist/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
  	</body>
</html>