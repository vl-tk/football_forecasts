
<div class="navbar navbar-inverse navbar-fixed-top">
	<div class="navbar-header pull-left">
		<p>
			<a class="brand" href="/">Forecast Tournament CL-2016/17</a> / <a class="brand" style="color:#666; font-size: 10pt" href="/?cup=ec2016">Евро-2016</a> / <a class="brand" style="color:#666; font-size: 10pt" href="/?cup=wc2014">ЧМ-2014</a>
		</p>
	</div>

	<div class="navbar-header pull-right">
		<?
		if ($user->isAuthorized()):
		?>
			<p class="navbar-text pull-right">
				Вы вошли как <a class="login-highlighted" style="font-weight: bold" href="/profile.php"><? echo $user->getLogin()?></a>. <a href="?out=Y" class="white">Logout</a>
			</p>
		<?
		else:
		?>
			<p class="navbar-text pull-right">
				<a href="/reg.php" class="white">Register</a>
				<a href="/auth.php" class="white">Log in</a>
			</p>
		<?
		endif;
		?>

		<ul class="nav">
			<?
			/*if ($user->isAdmin()):
			<li <?=Html::isActive(array('index','main'))?>><a href="/">Main</a></li>
			?>
			<li <?=Html::isActive('projects')?>><a href="/index.php">Games</a></li>
			<li <?=Html::isActive('users')?>><a href="/users.php">Users</a></li>
			<li <?=Html::isActive('reports')?>><a href="/reports.php">Reports</a></li>
			<?
			endif;*/
			?>
		</ul>
	</div>
</div>

<br/>
<br/>
<br/>
