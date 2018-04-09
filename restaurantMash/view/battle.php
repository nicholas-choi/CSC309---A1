<!DOCTYPE html>
<html lang="en">
	<head>
		<meta charset="utf-8">
		<link rel="stylesheet" type="text/css" href="style.css" />
		<title>RestaurantMash</title>
	</head>
	<body>
		<header><h1>RestaurantMash</h1></header>
		<nav>
			<ul>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="battle" class="btn disabled">Battle</button>
					</form>
					<?php if($_SESSION['userGuesses'] >= 10) : ?>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="results" class="btn">Results</button>
					</form>
					<?php endif; ?>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="profile" class="btn">Profile</button>
					</form>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="logout" class="btn">Logout</button>
					</form>
      </ul>
		</nav>
		<main>
			<h1>Compete</h1>
			<h2>Which restaurant would you rather go to?</h2>
			<?php if(($_SESSION['vote'][0] == $_SESSION['vote'][2] && $_SESSION['vote'][1] > $_SESSION['vote'][4]) || $_SESSION['vote'][0] > $_SESSION['vote'][2]) : ?>
				<p>
					You have finished ranking all matches for this session please login tomorrow.
				</p>
			<?php else : ?>
				<form action="index.php" method="post">
					<input type="hidden" name="token" value=<?php echo $_SESSION['token']?> />
					<table>
						<tr>
							<th class="choice"><button type="submit" name="btn_submit" value="btn_1" class = "btn_submit_left"><?php echo $_SESSION['restaurantTableNames'][$_SESSION['vote'][0]][$_SESSION['vote'][1]][0]; ?></button>
							<th>or</th>
							<th class="choice"><button type="submit" name="btn_submit" value="btn_2" class = "btn_submit_right"><?php echo $_SESSION['restaurantTableNames'][$_SESSION['vote'][0]][$_SESSION['vote'][1]][1]; ?></button></th>
							<th>or</th>
							<th class="choice"><button type="submit" name="btn_submit" value="btn_3" class = "btn_submit">No Preference</button></th>
						</tr>
					</table>
				</form>
			<?php endif; ?>
			<!-- idea of buffering valuye https://stackoverflow.com/questions/19025677/how-to-disable-a-html-button-after-it-has-been-clicked-not-a-submit-button -->
		</main>
		<footer>
		</footer>
	</body>
</html>
