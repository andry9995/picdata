$(document).ready(function() {
	// var user_list = $('#user-list');
	// var client_user = $('#zendesk-client-user');

	// getClientUsers(user_list,client_user);

	// $(document).on('change', '#zendesk-client-user', function (event) {
	//     event.preventDefault();
	//     getClientUsers(user_list, client_user);
	// });

	get_all_recipient()

	function get_all_recipient() {
		var url = Routing.generate('zendesk_all_recipient');
		$.ajax({
			url    : url,
			type   : 'GET',
			async  : true,
			success: function(data) {
				$.each(data,function(index,item) {
					var option = '<option value="'+ item.email +'">'+ item.email +'</option>';
					$('#zcm-mail').append(option)
				})
			}
		});
	}

	$('.zcm-save').on('click',function() {

		if ($('#zcm-mail').val() == 0) {
			show_info('ERREUR', 'VEUILLEZ CHOISIR UN MAIL DE SUPPORT','error');
			return;
		}

		var url = Routing.generate('zendesk_save_config_mail');
		var data = {
			client: $('#zcm-client').val(),
			mail  : $('#zcm-mail').val(),
			exist : $('#exist').val()
		};
		$.ajax({
			url    : url,
			type   : 'POST',
			async  : true,
			data   : data,
			success: function(response) {
				switch(response) {
					case 1:
						show_info('SUCCES', 'EMAIL DE SUPPORT BIEN ENREGISTREE');
						$('.mail-row').addClass('hidden');
						$('.mail-input-row').removeClass('hidden');
						$('#zcm-mail-input').val($('#zcm-mail').val());
						$('#zcm-mail-input').attr('disabled','disabled');
						$('#exist').val(1);
						$('.zcm-save').addClass('hidden');
						break;
					case 0:
						show_info('ERREUR', 'MAIL DEJA UTILISE','error');
						$('#zcm-mail').val(0).trigger('chosen:updated');
						break;
				}
			}
		});
	})

	$('.btn-edit').on('click', function() {
		$('.mail-input-row').addClass('hidden');
		$('.mail-row').removeClass('hidden');
		$('.zcm-save').removeClass('hidden');

	})

	$(document).on('change', '#zcm-client', function (event) {
		event.preventDefault();
		var client_id = $(this).val();
		var url = Routing.generate('zendesk_get_mail',{
			client_id : client_id
		});
		$.ajax({
			url : url,
			type : 'GET',
			async : true,
			success : function(data) {
				if (data == 0) {
					$('#zcm-mail').val(data).trigger('chosen:updated');
					$('#exist').val(0);
					$('.mail-input-row').addClass('hidden');
					$('.mail-row').removeClass('hidden');
					$('.zcm-save').removeClass('hidden');
				} else{
					$("#zcm-mail").val(data[0].mail_support).trigger('chosen:updated');
					$('.mail-row').addClass('hidden');
					$('.mail-input-row').removeClass('hidden');
					$('#zcm-mail-input').val(data[0].mail_support);
					$('#zcm-mail-input').attr('disabled','disabled');
					$('#exist').val(1);
					$('.zcm-save').addClass('hidden');
				}
			}
		});
	})

});
