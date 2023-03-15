$(document).ready(function() {

	window.justClosedControl = false;

	$('.selectpicker').selectpicker();

	$('tr.game_info').on('click', function(e) {

		window.pleaseStop = false;

		$(this).children().children('div.bootstrap-select').each(function(){
			if (!$(this).hasClass('hiddenx'))
				window.pleaseStop = true;
		});

		if (window.pleaseStop)
			return;

		if (window.justClosedControl)
		{
			window.justClosedControl = false;
			return;
		}

		var id = this.id.replace('tr_', '');

		if ($('#' + id + '_info').hasClass('unfolding'))
		{
			$('#' + id + '_info').toggleClass('hiddenx');

			if ($('#' + id + '_info').hasClass('bottom'))
				$(this).toggleClass("bottom");
		}
	});

	$('.clickable').on('click', function(e) {

		var id = this.id.split('_')[1],
			team_number = this.id.split('_')[2];

		$(this).addClass('hiddenx');
		$(this).siblings('div.bootstrap-select').toggleClass('hiddenx');

		e.stopPropagation();
	});

	// $('.selectable').on('click', function(e) {

	// });

	$('.selectable').on('change', function() {

		var id = this.id.split('_')[1],
			team_number = this.id.split('_')[2];

		$(this).addClass('hiddenx');
		$(this).siblings('div.bootstrap-select').toggleClass('hiddenx');
		$(this).siblings('div.clickable').toggleClass('hiddenx');
		window.justClosedControl = true;

		$('#click_' + id + '_' + team_number).text($(this).val());
		$('#click_' + id + '_' + team_number).addClass("big");

		var game_id = $(this).attr('data-game-id'),
			value = $(this).val();

		$.ajax({
			url: '/ajax.php',
			type: "POST",
			data: {
				'game_id': game_id,
				'team_number': team_number,
				'value': value
			},
			success: function(json) {
				if (json == 'success')
					console.log('success: game ' + game_id + ', team_number ' + team_number + ', value ' + value);
				else
					alert('error: game ' + game_id + ', team_number ' + team_number + ', value ' + value);
			},
		});
	});


	$('.finish_button').on('click', function() {

		var answer = confirm('Точно?')
		if (answer)
		{
			var game_id = $(this).attr('data-game-id');

			$.ajax({
				url: '/ajax.php',
				type: "POST",
				data: {
					'game_id': game_id,
					'finished': 'Y'
				},
				success: function(json) {
					if (json == 'success')
					{
						console.log('success');
						document.location = '/';
					}
					else
						alert('error');
				},
			});
		}
	});

	$('.continue_button').on('click', function() {

		var game_id = $(this).attr('data-game-id');

		$.ajax({
			url: '/ajax.php',
			type: "POST",
			data: {
				'game_id': game_id,
				'finished': 'N'
			},
			success: function(json) {
				if (json == 'success')
				{
					console.log('success');
					document.location = '/';
				}
				else
					alert('error');
			},
		});
	});

	$('.random_button').on('click', function() {

		var game_id = $(this).attr('data-game-id'),
			clever = $(this).attr('data-clever'),
			team1_rank = $(this).attr('data-team1-rank'),
			team2_rank = $(this).attr('data-team2-rank'),
			team1_fifa_rank = $(this).attr('data-team1-fifa-rank'),
			team2_fifa_rank = $(this).attr('data-team2-fifa-rank');

		$.ajax({
			url: '/ajax.php',
			type: "POST",
			data: {
				'game_id': game_id,
				'team1_rank': team1_rank,
				'team2_rank': team2_rank,
				'team1_fifa_rank': team1_fifa_rank,
				'team2_fifa_rank': team2_fifa_rank,
				'random': 'Y',
				'clever': clever
			},
			success: function(json) {
				if (json == 'success')
				{
					console.log('success');
					document.location = '/';
				}
				else
					alert('error');
			},
		});
	});

	$('.result_button').on('click', function() {

		var game_id = $(this).attr('data-game-id'),
			current_result = $(this).attr('data-result');

		if (current_result.length == 0)
			current_result = '0:0';

		var result = prompt('Счет', current_result);
		if (result != null)
		{
			$.ajax({
				url: '/ajax.php',
				type: "POST",
				data: {
					'game_id': game_id,
					'result': result
				},
				success: function(json) {
					if (json == 'success')
					{
						console.log('success');
						document.location = '/';
					}
					else
						alert('error');
				},
			});
		}
	});

	$('.more-controls').on('click', function() {
		var id = $(this).attr('data-id');
		$('#' + id).find('.admin-controls').toggleClass('hiddenx');
	});

	$('.group_create_btn').on('click', function(e) {

		e.preventDefault();

		var result = prompt('Название группы', '');
		if (result != null && result != '')
		{
			$.ajax({
				url: '/ajax.php',
				type: "POST",
				data: {
					'group_create': 'Y',
					'group_name': result
				},
				success: function(info) {

					if (info == 'success')
					{
						console.log('success');
						document.location = '/profile.php';
					}
					else {
						alert(info);
					}
				},
			});
		}
		else {
			alert('Введите название группы из латинских букв, цифр и символа _');
		}
	});

	$('.group_delete_link').on('click', function(e) {

		e.preventDefault();

		var id = $(this).attr('data-group-id');

		var result = confirm('Удалить?');

		if (result == true)
		{
			$.ajax({
				url: '/ajax.php',
				type: "POST",
				data: {
					'group_delete': 'Y',
					'group_id': id
				},
				success: function(json) {
					if (json == 'success')
					{
						console.log('success');
						document.location = '/profile.php';
					}
					else {
						console.log(json);
						alert('Ошибка удаления группы');
					}
				},
			});
		}
	});

	$('a[data-toggle="tab"]').on('click', function(e){
		if (e.target != undefined) {
			var $tab = $(e.target),
				id = $tab.attr('id');

			$.cookie("tab", id);
		}
		// console.log(e.target); // активная вкладка
		// e.relatedTarget // предыдущая вкладка
	});

});
