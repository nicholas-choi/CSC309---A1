<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css" />
		<title>RestaurantMash</title>
	</head>
	<body>
		<header><h1>RestaurantMash</h1></header>
		<meta http-equiv="refresh" content="5">
		<nav>
			<ul>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="battle" class="btn">Battle</button>
					</form>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="results" class="btn disabled">Results</button>
					</form>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="profile" class="btn">Profile</button>
					</form>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="logout" class="btn">Logout</button>
					</form>
      </ul>
		</nav>

		<main>
			<table id="eloTable">
				<caption>Elo Ranking<caption>
				<tr>
					<th>Name</th>
					<th>Elo</th>
					<th>Wins</th>
					<th>Losses</th>
					<th>Ties</th>
				</tr>
				<?php
					$rawNameArray = $_SESSION['eloTable'];
					//print_r($rawNameArray);
					foreach($rawNameArray as $value){
						echo "<tr><td>";
						echo $value['name'];
						echo "</td><td>";
						echo $value['elo'];
						echo "</td><td>";
						echo $value['win'];
						echo "</td><td>";
						echo $value['lost'];
						echo "</td><td>";
						echo $value['draw'];
					}
				?>

			</table>
			<table id="eloTable">
				<caption>Daily ELO Shift</caption>
				<tr>
					<th>Name</th>
					<th>Elo Change</th>
				</tr>
				<?php
					$rawNameArray = $_SESSION['dailyGains'];

					foreach($rawNameArray as $value) {
						echo "<tr><td>";
						echo $value['name'];
						echo "</td><td>";
						echo $value['elochange'];
					}
				?>
			</table>
		</main>
		<footer>
		</footer>
	</body>
</html>
