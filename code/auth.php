<?php
include_once("./templates/system_header.php");

if ($user->isAuthorized())
{
	header('Location: /');
	exit();
}
else
	include_once("./templates/visual_header.php");

?>
	<style type="text/css">
		body {
			padding-top:40px;
			padding-bottom:40px;
			background-color:#f5f5f5;
		}

		.form-signin {
			max-width:300px;
			padding:19px 29px 29px;
			margin:0 auto 20px;
			background-color:#fff;
			border:1px solid #e5e5e5;
			-webkit-border-radius:5px;
			-moz-border-radius:5px;
			border-radius:5px;
			-webkit-box-shadow:0 1px 2px rgba(0,0,0,.05);
			-moz-box-shadow:0 1px 2px rgba(0,0,0,.05);
			box-shadow:0 1px 2px rgba(0,0,0,.05);
		}

		.form-signin .form-signin-heading,.form-signin .checkbox{
			margin-bottom:10px;
		}

		.form-signin input[type="text"],.form-signin input[type="password"]{
			font-size:16px;
			height:auto;
			margin-bottom:15px;
			padding:7px 9px;
		}
	</style>
	<div class="container">
		<form class="form-signin" action="/auth.php" method="POST" />
			<h4 class="form-signin-heading">Вход</h4>
			<input type="text" name="login" placeholder="Логин" />
			<input type="password" name="password" placeholder="Пароль"/>
			<label class="checkbox">
				<input type="checkbox" name="remember" value="yes" checked>Запомнить меня
			</label>
			<input type="submit" class="btn btn-success btn-medium" name="submit_button" value="Войти" />
			<br/>
			<?
			if (is_array($error)):
				foreach ($error as $key => $errorMessage):
				?>
					<h6 class="red-message"><?=$errorMessage?></h6>
				<?
				endforeach;
			endif;
			?>
		</form>
	</div>
</body>
</html>
