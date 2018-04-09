<?php
	ini_set('display_errors', 'On');
	require_once "lib/lib.php";
	require_once "model/Compete.php";
	require_once "model/Division.php";
	require_once "model/Functions.php";

	session_save_path("sess");
	session_start();

	$dbconn = db_connect();

	$errors = array();
	$view = "";
	if(!isset($_SESSION['restaurantTableNames'])){
  	$_SESSION['restaurantTableNames'] = array();
	}
	if(!isset($_SESSION['vote'])){
		$_SESSION['vote'] = array();
	}

	if(!isset($_SESSION['voteCount'])){
		$_SESSION['voteCount'] = 0;
	}

	if(!isset($_SESSION['token'])){
		$_SESSION['token'] = rand(0, 1000);
	}

	if(!isset($_SESSION['userGuesses'])){
		$_SESSION['userGuesses'] = 0;
	}

	if(!isset($_SESSION['eloTable'])){
		$_SESSION['eloTable'] = array();
		loadResultsTables($dbconn);
	}

	if(!isset($_SESSION['dailyGains'])){
		$_SESSION['dailyGains'] = array();
		loadResultsTables($dbconn);
	}


	/* Controller code */
	if(!isset($_SESSION['state'])){
		$_SESSION['state'] = 'login';
	}


	switch($_SESSION['state']){

		case "battle":

			// The Default View Post-Login.
			$view = "battle.php";
			$token = $_SESSION['token'];

			if (isset($_POST["btn_submit"]) && $_POST['token'] == $token){
				$WDL = "default";
				$nameOne = $_SESSION['restaurantTableNames'][$_SESSION['vote'][0]][$_SESSION['vote'][1]][0];
				$nameTwo = $_SESSION['restaurantTableNames'][$_SESSION['vote'][0]][$_SESSION['vote'][1]][1];

				if($_POST['btn_submit']=="btn_1")
					{
						$WDL = "WIN";
					}
					else if($_POST['btn_submit']=="btn_2")
					{
						$WDL = "LOST";
					}
					else if($_POST['btn_submit']=="btn_3")
					{
						$WDL = "DRAW";
					}
					if ($tryToUpdate = update($dbconn, $nameOne, $nameTwo, $WDL)){
						$errors[] = "Failed To Record Decision";
						$_SESSION['voteCount']--;
					} else{
						postClick();
					}
					$_SESSION['token'] = rand(0, 1000);
			} else {

			}

			// Remove invalid index issues.
			if(isset($_POST['navigation']) == 'logout') {
				// Change to Login.
				if($_POST['navigation'] == 'logout') {
					session_destroy();
					$_SESSION['state'] = 'login';
					$view = 'login.php';
				}
			}

			// Remove invalid index issues.
			if(isset($_POST['navigation']) == 'results') {
				// Change to Results.
				if($_POST['navigation'] == 'results') {
					$_SESSION['state'] = 'results';
					$view = 'results.php';
				}
			}

			if(isset($_POST['navigation']) == 'profile') {
				// Change to Profile.
				if($_POST['navigation'] == 'profile') {
					$_SESSION['state'] = 'profile';
					$view = 'profile.php';
				}
			}

			if(isset($_POST['navigation']) == 'battle') {
				// Change to Battle.
				if($_POST['navigation'] == 'battle') {
					$_SESSION['state'] = 'battle';
					$view = 'battle.php';
				}
			}

			// Set Session Variables on Login.
			setSession($dbconn);

			break;

		case "login":

			// The Default View: Login.
			$view = "login.php";

			// Remove Invalid Index Issues.
			if(isset($_REQUEST['operation']) == 'register') {
				// Change to Register View.
				if($_REQUEST['operation'] == 'register') {
					$_SESSION['state'] = 'register';
					$view = 'register.php';
				}
			}

			// Check Submit or Not.
			if(empty($_REQUEST['submit']) || $_REQUEST['submit'] != "login"){
				break;
			}

			// Error Checking: Username Field Empty.
			if(empty($_REQUEST['user'])){
				$errors[] = 'A Username is required';
			}

			// Error Checking: Password Field Empty.
			if(empty($_REQUEST['password'])){
				$errors[] = 'A Password is required';
			}

			if(!empty($errors))break;

			// Attempting View Switch from Login to Battle.
			if(!$dbconn) return;

			// To change to Battle view.
			$query = "SELECT * FROM appuser WHERE id=$1 and password=$2;";
     		$result = pg_prepare($dbconn, "", $query);
    		$result = pg_execute($dbconn, "", array($_REQUEST['user'], $_REQUEST['password']));

    		// Case 1: We found the User/Password Combination in SQL DB.
      		if($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)){

				// ?? Comment this Ralph.
				loadRestaurantTableNames($dbconn);
				$_SESSION['Compete'] = new Compete($_SESSION['restaurantTableNames']);

				// Save User/Password in Session.
				$_SESSION['user'] = $_REQUEST['user'];
				$_SESSION['password'] = $_REQUEST['password'];
				getUserGuesses($dbconn);
				// Swaps to Battle View.
				$_SESSION['state'] = 'battle';
				$view = "battle.php";

			// Case 2: User/Password Combination Not Found.
			} else {
				$errors[] = "Invalid login";
			}

			break;


		case "register":

			// The Register View.
			$view = "register.php";

			// Remove Invalid Index Issues.
			if(isset($_REQUEST['operation']) == 'login') {
				// Change to Login View.
				if($_REQUEST['operation'] == 'login') {
					$_SESSION['state'] = 'login';
					$view = 'login.php';
				}
			}

			// Check Submit or Not.
			if(empty($_REQUEST['submit']) || $_REQUEST['submit'] != "register"){
				break;
			}

			// Error Checking.
			if(empty($_REQUEST['user'])){
				$errors[] = 'A Username is required';
				$_SESSION['message'] = "";
			}

			if(empty($_REQUEST['password'])){
				$errors[] = 'A Password is required';
				$_SESSION['message'] = "";
			}

			if(!empty($errors))break;

			// Attempting Register User/Password Combination.
			if(!$dbconn) return;

			// Registers User.
			$user = $_REQUEST['user'];
			$pass = $_REQUEST['password'];
			$val = checkUser($dbconn, $user, $pass);

			// Case 1: User Exists, Display Error.
			if ($val == 0){
				$errors[] = 'User already exists';
				$_SESSION['message'] = "";
				break;
			}
			// Case 2: User/Password Combination Doesn't Exist, Register to SQL DB.
			else {
				registerUser($dbconn, $user, $pass);
				$_SESSION['message'] = "Successfully Registered";
			}

			break;


		case "results":

			// The Results View.
			$token = $_SESSION['token'];

			tokenCheck($token);
			loadResultsTables($dbconn);

			$view = "results.php";

			// Remove Invalid Index Issues.
			if(isset($_POST['navigation']) == 'logout') {
				// Change To Login.
				if($_POST['navigation'] == 'logout') {
					session_destroy();
					$_SESSION['state'] = 'login';
					$view = 'login.php';
				}
			}

			// Remove invalid index issues.
			if(isset($_POST['navigation']) == 'battle') {
				// Change to Battle.
				if($_POST['navigation'] == 'battle') {
					$_SESSION['state'] = 'battle';
					$view = 'battle.php';
				}
			}

			// Remove invalid index issues.
			if(isset($_POST['navigation']) == 'profile') {
				// Change to Profile.
				if($_POST['navigation'] == 'profile') {
					$_SESSION['state'] = 'profile';
					$view = 'profile.php';
				}
			}

			// Remove invalid index issues.
			if(isset($_POST['navigation']) == 'results') {
				// Change to Results.
				if($_POST['navigation'] == 'results') {
					$_SESSION['state'] = 'results';
					$view = 'results.php';
				}
			}

			break;

		case "profile":

				// The Profile View.
				$view = "profile.php";
				$token = $_SESSION['token'];
				tokenCheck($token);

				// Remove Invalid Index Issues.
				if(isset($_POST['navigation']) == 'battle') {
					// Change to Battle.
					if($_POST['navigation'] == 'battle') {
						$_SESSION['state'] = 'battle';
						$view = 'battle.php';
					}
				}
				// Remove invalid index issues.
				if(isset($_POST['navigation']) == 'results') {
					// Change to Results.
					if($_POST['navigation'] == 'results') {
						$_SESSION['state'] = 'results';
						$view = 'results.php';
					}
				}

				// Remove invalid index issues.
				if(isset($_POST['navigation']) == 'logout') {
					// Change to Login.
					if($_POST['navigation'] == 'logout') {
						session_destroy();
						$_SESSION['state'] = 'login';
						$view = 'login.php';
					}
				}

				// Modify Values in SQL DB If Changed.
				if(isset($_POST['update'])) {

					// Error Checking: Password Is Size 0.
					if($_POST['password'] == '') {
						$errors[] = 'Invalid Password: Size 0';
						break;
					}

					// Error Checking: Phone Contains Non-Numbers.
					if((!ctype_digit($_POST['phone'])) && (strlen($_POST['phone']) >= 1)) {
						$errors[] = 'Invalid Phone: Non-Numbers';
						break;
					}

					// Error Checking: Phone Needs 0 or 10 Digits.
					if((strlen($_POST['phone']) != 0) && (strlen($_POST['phone']) != 10)) {
						$errors[] = 'Invalid Phone: Need 10 or 0 Digits';
						break;
					}

					// Update Profile in SQL DB.
					updateProfile($dbconn);
				}

				break;
	}
	require_once "view/$view";

	function tokenCheck($token){
		if($token != $_SESSION['token']){
			header('Refresh: 1; url=index.php');
			echo "mismatch";
		}
	  $_SESSION['token'] = rand(0, 1000);
		//echo "match";
	}

?>
