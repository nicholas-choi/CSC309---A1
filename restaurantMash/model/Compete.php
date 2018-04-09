<?php
require_once 'Rating.php';

class Compete {
	//$restaurantTable = [];
	public $results = array();
	public $competeRound = 0;
	public $restaurantTableNames = array();

	public function __construct($restaurantTableNames) {
		$this->competeRound = 0;
		$this->restaurantTableNames = $restaurantTableNames;
	}

	public function UpdateElo($restaurantOneElo, $restaurantTwoElo, $WinOrLose) {
		if ($WinOrLose==="WIN"){
			$rating = new Rating($restaurantOneElo, $restaurantTwoElo, Rating::WIN, Rating::LOST);
		} else if ($WinOrLose==="DRAW"){
			$rating = new Rating($restaurantOneElo, $restaurantTwoElo, Rating::DRAW, Rating::DRAW);
		} else {
			$rating = new Rating($restaurantOneElo, $restaurantTwoElo, Rating::LOST, Rating::WIN);
		}

		$this->results = $rating->getNewRatings();
	}

	public function getResults($whichResult){
		if ($whichResult == 'a'){
			return $this->results['a'];
		} else if ($whichResult == 'b'){
			return $this->results['b'];
		}

	}

	public function nextRound(){
		$this->competeRound++;
	}

	public function alert($msg) {
	    echo "<script type='text/javascript'>alert('$msg');</script>";
	}
}
?>
