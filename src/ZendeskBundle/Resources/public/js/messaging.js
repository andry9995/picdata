$(document).ready(function() {

	// messaging_list(0);

	messaging_list($('#msg-client').val(),0);

	$(document).on('click','#btn-refresh', function(event) {
		$('.icon-refresh').addClass('fa-spin');
		messaging_list($('#msg-client').val(),0);
	})

	function messaging_list(client,status) {
		var url = Routing.generate('messaging_list', {
			client : client,
			status : status
		});
		$.ajax({
			url : url,
			type : 'GET',
			datatype : 'json',
			async : true,
			success: function(html) {
				$('#messaging-list').html('');
				$('#messaging-list').append(html);
				$('.icon-refresh').removeClass('fa-spin');
				switch(status) {
					case 0:
						$('#btn-span').html('Tous les tickets');
						break;
					case 1:
						$('#btn-span').html('Nouveaux tickets');
						break;
					case 2:
						$('#btn-span').html('Tickets ouverts');
						break;
					case 3:
						$('#btn-span').html('Tickets résolus');
						break;
				}

			}
		});
	}

	$(document).on('click','#all-tickets',function() {
		messaging_list($('#msg-client').val(),0);
	})

	$(document).on('click','#new-tickets',function() {
		messaging_list($('#msg-client').val(),1);
	})

	$(document).on('click','#open-tickets',function() {
		messaging_list($('#msg-client').val(),2);
	})

	$(document).on('click','#solved-tickets',function() {
		messaging_list($('#msg-client').val(),3);
	})

	$(document).on('click','.btn-solved',function() {
		var id = $(this).data("id");
		var data = {
			id        : id,
			new_status: 'solved'
		};
		change_status(data).then(function(response) {
			show_info('SUCCES', 'TICKET #' + response + ' RESOLU');
			messaging_list($('#msg-client').val(),0);

		})

	})

	$(document).on('click','.btn-open',function() {
		var id = $(this).data("id");
		var data = {
			id        : id,
			new_status: 'open'
		};
		change_status(data).then(function(response) {
			show_info('SUCCES', 'TICKET #' + response + ' REOUVERT');
			messaging_list($('#msg-client').val(),0);
		})

	})

	$('#form-search').submit(function(event) {
		search();
		event.preventDefault();
		return;
	})

	$(document).on('click','#btn-search', function(event) {
		search();
		event.preventDefault();
		return;
	})

	$(document).on('change', '#msg-client', function (event) {
		event.preventDefault();
		var client = $(this).val();
		messaging_list(client,0);

	});

	function search() {
		var url = Routing.generate('messaging_search');
		var data = {
			word : $('#search').val(),
			client : $('#msg-client').val()
		}
		$.ajax({
			url : url,
			type : 'POST',
			async: true,
			data : data,
			success : function(response) {
				$('#messaging-list').html('');
				$('#messaging-list').append(response);
			}
		});
	}

	function change_status(data) {
		return new Promise(function(resolve,reject) {
			var url = Routing.generate('messaging_change_status');
			$.ajax({
				url : url,
				type : 'POST',
				async : true,
				data : data,
				success : function(response) {
					resolve(response);
				}
			});	
		});
	}

	$(document).on('click','.show-conversation', function(event) {

		var id = $(this).data("id");
		var url = Routing.generate('messaging_conversation', {
			id : id
		});

		$.ajax({
			url: url,
			type: 'GET',
			datatype: 'json',
			async : true,
			success : function(data) {
				var modal_id = '#conversation-' + id;
				var modal_content_id = '#modal-conversation-' + id;
				$(modal_content_id).html('');
				$(modal_content_id).append(data);
				$(modal_id).modal('show');
			}
		});
	})


	$(document).on('click','.with-status-list', function(event) {

		var status = $(this).data('value');

		$('#comment-status').val(status);

		var label = "";

		switch(status) {
			case 'open':
				label = "Ouvert";
				break;
			case 'solved':
				label = "Résolu"
				break;
			case 'new':
				label = "Nouveau";
				break;
		}

		$('.with-status').html(label);
	})

	$(document).on('click','.send-comment', function(event) {
		event.preventDefault();
		var id = $(this).data('id');
		var comment = $('#comment').val();
		var status = $('#comment-status').val();

		var url = Routing.generate('messaging_comment');

		var data = {
			id : id,
			comment : comment,
			status : status
		}

		$.ajax({
			url : url,
			type : 'POST',
			async : true,
			data : data,
			success : function(response) {
				show_info('SUCCES', 'REPONSE DU TICKET #' + response + ' ENVOYE');
				$('#conversation-' + response).modal('hide');
			}
		});	

	});


	$(document).on('click','.show-dossier-box', function(event) {
		if ($(this).html() == '<i class="fa fa-chevron-up"></i>') {
			$('.dossier-box').attr('style','display:block;');
			$(this).html('<i class="fa fa-chevron-down"></i>');
		} else {
			$('.dossier-box').attr('style','display:none;');
			$(this).html('<i class="fa fa-chevron-up"></i>');
		}
	})




});
