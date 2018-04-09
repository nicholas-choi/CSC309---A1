<?php

require_once "lib/lib.php";
require_once "model/Compete.php";
require_once "model/Division.php";
require_once "model/Functions.php";

function checkUser($dbconn, $user, $pass){

  // Find If User Exists.
  $query = "SELECT * FROM appuser WHERE id=$1;";
  $result = pg_prepare($dbconn, "", $query);
  $result = pg_execute($dbconn, "", array($user));

  // Case 1: User Exists: Return 0 For Fail.
  if ($row = pg_fetch_array($result, NULL, PGSQL_ASSOC)){
    return 0;
  }

  // Case 2: User Does Not Exist: Return 1 For Success.
  else {
    return 1;
  }
}

function registerUser($dbconn, $user, $pass){

  // Register User.
  $query = "INSERT INTO appuser(id, password) VALUES($1, $2);";
  $result = pg_prepare($dbconn, "", $query);
  $result = pg_execute($dbconn, "", array($user, $pass));
}

function updateProfile($dbconn){

  // Storing Values In Session.
  $_SESSION['password'] = $_POST['password'];
  $_SESSION['favRes'] = $_POST['favRes'];
  $_SESSION['phone'] = $_POST['phone'];

  if($_POST['gender'] == "male") {
    $_SESSION['gender'] = 0;
  } else if ($_POST['gender'] == "female") {
    $_SESSION['gender'] = 1;
  } else {
    $_SESSION['gender'] = 2;
  }

  $_SESSION['bike'] = 0;
  $_SESSION['car'] = 0;
  $_SESSION['bus'] = 0;
  $_SESSION['walk'] = 0;
  $_SESSION['strut'] = 0;

  if(isset($_POST['bike'])) {
    $_SESSION['bike'] = 1;
  }
  if(isset($_POST['car'])) {
    $_SESSION['car'] = 1;
  }
  if(isset($_POST['bus'])) {
    $_SESSION['bus'] = 1;
  }
  if(isset($_POST['walk'])) {
    $_SESSION['walk'] = 1;
  }
  if(isset($_POST['strut'])) {
    $_SESSION['strut'] = 1;
  }

  // To Query the SQL DB (Preventing SQL Injection w/ Prepare).
  $query = "UPDATE appuser SET password=$1, fav_restaurant=$2, phone=$3, gender=$4, bike=$5, car=$6, bus=$7, walk=$8, strut=$9 WHERE id=$10;";
  $result = pg_prepare($dbconn, "", $query);
  $result = pg_execute($dbconn, "", array($_SESSION['password'], $_SESSION['favRes'], $_SESSION['phone'], $_SESSION['gender'], $_SESSION['bike'], $_SESSION['car'], $_SESSION['bus'], $_SESSION['walk'], $_SESSION['strut'], $_SESSION['user']));
}

function setSession($dbconn){
  // To Fetch Values from SQL DB to Fill Profile.
  $query = "SELECT * FROM appuser WHERE id=$1;";
  $result = pg_prepare($dbconn, "", $query);
  $result = pg_execute($dbconn, "", array($_SESSION['user']));

  // Store Values in Session.
  if($row = pg_fetch_array($result)) {
    $_SESSION['favRes'] = $row['fav_restaurant'];
    $_SESSION['phone'] = $row['phone'];
    $_SESSION['gender'] = $row['gender'];
    $_SESSION['bike'] = $row['bike'];
    $_SESSION['car'] = $row['car'];
    $_SESSION['bus'] = $row['bus'];
    $_SESSION['walk'] = $row['walk'];
    $_SESSION['strut'] = $row['strut'];
  }
}

function loadRestaurantTableNames($dbconn){

  $query = "SELECT name FROM restaurants ORDER BY elo";
  $result = pg_query($dbconn, $query);
  $nameTableArray = array();
  if ($rawNameArray = pg_fetch_all($result)){
    $errors[] = 'Unable To Fetch Name Table';
  }

  foreach($rawNameArray as $value){
    array_push($nameTableArray, $value['name']);
  }

  $classA = new Division();
  $classA->constructDivision($nameTableArray);
  $_SESSION['restaurantTableNames'] = $classA->getDivisions();

  $matchesPerDivision = count($_SESSION['restaurantTableNames'][0]) - 1;
  $numberOfDivisions = count($_SESSION['restaurantTableNames']) - 1;
  $matchInFinalDivision = count($_SESSION['restaurantTableNames'][$numberOfDivisions]) - 1;
  $_SESSION['vote'] = array(0, 0, $numberOfDivisions, $matchesPerDivision, $matchInFinalDivision);
  //$_SESSION['restaurantTableNames'] = $classA->getDivisions();
}

function loadResultsTables($dbconn){

  $query = "SELECT * from restaurants ORDER BY elo DESC";
  $result = pg_query($dbconn, $query);
  if ($eloTableArray = pg_fetch_all($result)){
    $errors[] = 'Unable To Fetch Name Table';
  }
  $_SESSION['eloTable'] = $eloTableArray;

  date_default_timezone_set('America/Toronto');
  $time2 = date("Y/m/d");

  $query = "SELECT * from dailyelo WHERE today=$$$time2$$ ORDER BY elochange DESC";
  $result = pg_query($dbconn, $query);
  if ($dailyGainsTableArray = pg_fetch_all($result)){
    $errors[] = 'Unable To Fetch Name Table';
  }
  $_SESSION['dailyGains'] = $dailyGainsTableArray;


  //$_SESSION['restaurantTableNames'] = $classA->getDivisions();
}

function alert($msg) {
  echo "<script type='text/javascript'>alert('$msg');</script>";
}

function postClick(){
  if(($_SESSION['vote'][1] + 1) > $_SESSION['vote'][3]){
    $_SESSION['vote'][1] = 0;
    $_SESSION['vote'][0]++;
  } else {
    $_SESSION['vote'][1]++;
  }
  $_SESSION['voteCount']++;
}

function update($dbconn, $nameOfChoiceOne, $nameOfChoiceTwo, $WDL) {
  $userGuesses = $_SESSION['userGuesses'];
  try {
    date_default_timezone_set('America/Toronto');

    $query = "SELECT name FROM restaurants ORDER BY elo";
    $result = pg_query($dbconn, "BEGIN");

    $query =  "SELECT elo FROM restaurants WHERE name=$$$nameOfChoiceOne$$";
    $resultChoiceOne = pg_query($dbconn, $query);
    $query = "SELECT elo FROM restaurants WHERE name=$$$nameOfChoiceTwo$$";
    $resultChoiceTwo = pg_query($dbconn, $query);
    //$eloOneArray = pg_fetch_array($resultChoiceOne, NULL, PGSQL_ASSOC);
    //$eloTwoArray = pg_fetch_array($resultChoiceTwo, NULL, PGSQL_ASSOC);
    //$eloOne = (int) $eloOneArray['elo'];
    $eloOne = pg_fetch_result($resultChoiceOne, 0);
    //$eloTwo = (int) $eloTwoArray['elo'];
    $eloTwo = pg_fetch_result($resultChoiceTwo, 0);
    $_SESSION['Compete']->UpdateElo($eloOne, $eloTwo, $WDL);
    $newEloOfChoiceOne = (int) $_SESSION['Compete']->getResults('a');
    $newEloOfChoiceTwo =  (int) $_SESSION['Compete']->getResults('b');

    $eloChangeOne = (int) $newEloOfChoiceOne - (int) $eloOne;
    $eloChangeTwo = (int) $newEloOfChoiceTwo - (int) $eloTwo;


    $updateDailyEloOne = "SELECT * FROM dailyelo WHERE name=$$$nameOfChoiceOne$$";
    $updateDailyEloTwo = "SELECT * FROM dailyelo WHERE name=$$$nameOfChoiceTwo$$";

    $resultOfDailyEloOne = pg_query($dbconn, $updateDailyEloOne);
    $resultOfDailyEloTwo = pg_query($dbconn, $updateDailyEloTwo);


    $dailyChangeOneArray = pg_fetch_array($resultOfDailyEloOne, NULL, PGSQL_ASSOC);
    $dailyChangeTwoArray = pg_fetch_array($resultOfDailyEloTwo, NULL, PGSQL_ASSOC);

    $dailyChangeOneDate = $dailyChangeOneArray['today'];
    $dailyChangeTwoDate = $dailyChangeTwoArray['today'];

    $todaysDate = date("Y-m-d");
    $time = strtotime($dailyChangeOneDate);
    $realtime = date('Y-m-d',$time);
    $time2 = strtotime($dailyChangeTwoDate);
    $realtime2 = date('Y-m-d',$time2);

    if($todaysDate > $realtime){
      $query = "UPDATE dailyelo SET today='$todaysDate', elochange=0 WHERE name=$$$nameOfChoiceOne$$";
      pg_query($dbconn, $query);
    }
    if($todaysDate > $realtime2){
      $query ="UPDATE dailyelo SET today='$todaysDate', elochange=0 WHERE name=$$$nameOfChoiceTwo$$";
      pg_query($dbconn, $query);
    }
    $query ="UPDATE dailyelo SET elochange=elochange + '$eloChangeOne'  WHERE name=$$$nameOfChoiceOne$$";
    pg_query($dbconn, $query);

    $query = "UPDATE dailyelo SET elochange=elochange + '$eloChangeTwo' WHERE name=$$$nameOfChoiceTwo$$";
    pg_query($dbconn, $query);
    if ($WDL === 'WIN'){
      $query ="UPDATE restaurants SET elo='$newEloOfChoiceOne', win=win+1 WHERE name=$$$nameOfChoiceOne$$";
      pg_query($dbconn, $query);
      $query = "UPDATE restaurants SET elo='$newEloOfChoiceTwo', lost=lost+1 WHERE name=$$$nameOfChoiceTwo$$";
      pg_query($dbconn, $query);
      //pg_query($dbconn, "UPDATE dailyelo SET elochange=elochange - 1 WHERE name='$nameOfChoiceTwo'");
    } else if ($WDL === 'DRAW'){
      $query = "UPDATE restaurants SET elo='$newEloOfChoiceOne', draw=draw+1 WHERE name=$$$nameOfChoiceOne$$";
      pg_query($dbconn, $query);
      $query = "UPDATE restaurants SET elo='$newEloOfChoiceTwo', draw=draw+1 WHERE name=$$$nameOfChoiceTwo$$";
      pg_query($dbconn, $query);
    } else {
      $query = "UPDATE restaurants SET elo='$newEloOfChoiceOne', lost=lost+1 WHERE name=$$$nameOfChoiceOne$$";
      pg_query($dbconn, $query);
      $query = "UPDATE restaurants SET elo='$newEloOfChoiceTwo', win=win+1 WHERE name=$$$nameOfChoiceTwo$$";
      pg_query($dbconn, $query);
      //pg_query($dbconn, "UPDATE dailyelo SET elochange=elochange - 1  WHERE name='$nameOfChoiceOne'");
    }
    $user = $_SESSION['user'];
    $query = "SELECT votes FROM appuser WHERE id = $$$user$$";
    $result = pg_query($dbconn, $query);
    $val = pg_fetch_result($result, 0);
    $_SESSION['userGuesses'] = $val + 1;
    $query = "UPDATE appuser SET votes=votes+1 WHERE id=$$$user$$";
    pg_query($dbconn, $query);
    pg_query($dbconn, "COMMIT");

    //print_r($eloChangeOne);
  } catch (Exception $e) {
    $_SESSION['userGuesses'] = $userGuesses;
    echo 'Unable To Update Table: ' + $e->getMessage();
    $result = pg_query($dbconn, "ROLLBACK");
  }
}

function getUserGuesses($dbconn){
  $user = $_SESSION['user'];
  $query = "SELECT votes FROM appuser WHERE id = $$$user$$";
  $result = pg_query($dbconn, $query);
  $val = pg_fetch_result($result, 0);
  $_SESSION['userGuesses'] = $val;
}
?>
