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

		.form-signin input[type="text"], .form-signin input[type="password"]{
			font-size:16px;
			height:auto;
			padding:7px 9px;
		}
	</style>
	<div class="container">
		<form class="form-signin" action="/reg.php" method="POST" />
			<h4 class="form-signin-heading">Регистрация</h4>

			<input type="text" name="login" placeholder="Логин" value="<?=$_REQUEST['login']?>" />
			<p style="margin-bottom: 15px; color: #999; font-size: 10px">3-16 символов a-z 0-9 _ </p>

			<input type="password" name="password" placeholder="Пароль" value="<?=$_REQUEST['password']?>" />
			<p style="margin-bottom: 15px; color: #999; font-size: 10px">минимум 5 символов</p>

			<input type="password" name="password2" placeholder="Повторить пароль" value="<?=$_REQUEST['password2']?>" />
			<p style="margin-bottom: 15px; color: #999; font-size: 10px"></p>

			<input type="hidden" name="name" placeholder="Отображаемое имя" value="<?=$_REQUEST['name']?>" />
			<!-- <p style="margin-bottom: 15px; color: #999; font-size: 10px">Не обязательно, по умолчанию равно логину</p> -->

			<input type="hidden" name="register" value="Y" />
			<input type="submit" class="btn btn-success btn-medium" name="submit_button" value="Зарегистрироваться" />
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
