<?php
// So I don't have to deal with unset $_REQUEST['user'] when refilling the form
$_REQUEST['user']=!empty($_REQUEST['user']) ? $_REQUEST['user'] : '';
$_REQUEST['password']=!empty($_REQUEST['password']) ? $_REQUEST['password'] : '';
?>
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
						<button type="submit" name="navigation" value="battle" class="btn">Battle</button>
					</form>
					<?php if($_SESSION['userGuesses'] >= 10) : ?>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="results" class="btn">Results</button>
					</form>
					<?php endif; ?>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="profile" class="btn disabled">Profile</button>
					</form>
					<form action="index.php" method="post">
						<button type="submit" name="navigation" value="logout" class="btn">Logout</button>
					</form>
      </ul>
		</nav>

		<main>
			<h1>Profile</h1>
			<h2>Tell Us About Yourself</h2></h2>
			<form action="index.php" method="post">
				<fieldset>
				<legend>Update Profile</legend>
				<table>
					<!-- Trick below to re-fill the user form field -->
					<tr><th><label for="user">User</label></th><td><input type="text" name="user" value="<?php echo($_SESSION['user']); ?>" readonly = "readonly"/></td></tr>
					<tr><th><label for="password">Password</label></th><td> <input type="password" name="password" value="<?php echo($_SESSION['password']); ?>" /></td></tr>
					<tr><th><label for="favRes">Favourite Restaurant</label></th><td><input type="text" name="favRes" value="<?php echo($_SESSION['favRes']); ?>" /></td></tr>
					<tr><th><label for="phone">Phone Number</label></th><td><input type="text" name="phone" value="<?php echo($_SESSION['phone']); ?>" maxlength="10"/></td></tr>
					<tr><th><label for="gender">Gender</label></th>
						<td>
						<input type="radio" name="gender" value="male"
						<?php
						if($_SESSION['gender'] == 0) {
							echo("checked");
						}?> >Male<br>
						<input type="radio" name="gender" value="female"
						<?php
						if($_SESSION['gender'] == 1) {
							echo("checked");
						}?> >Female<br>
						<input type="radio" name="gender" value="other"
						<?php
						if($_SESSION['gender'] == 2) {
							echo("checked");
						}?> >Other
						</td>
					</tr>
					<tr><th><label for="transport">Transportation to Restaurant</label></th>
						<td>
							<input type="checkbox" name="bike" value="bike"
							<?php
							if($_SESSION['bike'] == 1) {
								echo("checked");
							}?>>Bike<br>
							<input type="checkbox" name="car" value="car"
							<?php
							if($_SESSION['car'] == 1) {
								echo("checked");
							}?>>Car<br>
							<input type="checkbox" name="bus" value="bus"
							<?php
							if($_SESSION['bus'] == 1) {
								echo("checked");
							}?>>Bus<br>
							<input type="checkbox" name="walk" value="walk"
							<?php
							if($_SESSION['walk'] == 1) {
								echo("checked");
							}?>>Walk<br>
							<input type="checkbox" name="strut" value="strut"
							<?php
							if($_SESSION['strut'] == 1) {
								echo("checked");
							}?>>Strut<br>
						</td>
					</tr>
					<tr><th>&nbsp;</th><td><input type="submit" name="update" value="Update" /></td></tr>
					<tr><th>&nbsp;</th><td><?php echo(view_errors($errors)); ?></td></tr>
				</table>
			</form>
		</main>
		<footer>
		</footer>
	</body>
</html>
