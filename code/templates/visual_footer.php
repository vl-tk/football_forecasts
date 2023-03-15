<footer>
	<p class="pull-right">Турнир прогнозов &copy; vlad</p>
	<?
	if ($user->isAdmin()):
		echo round(microtime(true) - $start, 2);
		?> / <a href="/dump.php">Дамп</a>
		<?
	endif;
	?>
</footer>
