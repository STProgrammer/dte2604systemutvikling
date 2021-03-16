
// Denne koden er tatt (og litt modifisert) fra https://codewithawa.com/posts/check-if-user-already-exists-without-submitting-form
$('document').ready(function(){
	var username_state = false;
	var email_state = false;

	$('#username').on('blur', function(){
		var username = $('#username').val();
		if (username == '') {
			username_state = false;
			return;
		}
		$.ajax({
			url: 'register_user_check.php',
			type: 'post',
			data: {
				'username_check' : 1,
				'username' : username,
			},
			success: function(response){
				if (response == 'taken' ) {
					username_state = false;
					$('#username').parent().removeClass();
					$('#username').parent().addClass("form_error");
					$('#username').siblings("span").text('Beklager... Brukernavn er allerede tatt');
				}else if (response == 'not_taken') {
					username_state = true;
					$('#username').parent().removeClass();
					$('#username').parent().addClass("form_success");
					$('#username').siblings("span").text('Brukernavn er tilgjengelig');
				}
			}
		});
	});
	$('#emailAddress').on('blur', function(){
		var email = $('#emailAddress').val();
		if (email == '') {
			email_state = false;
			return;
		}
		$.ajax({
			url: 'register_user_check.php',
			type: 'post',
			data: {
				'email_check' : 1,
				'emailAddress' : email,
			},
			success: function(response){
				if (response == 'taken' ) {
					email_state = false;
					$('#emailAddress').parent().removeClass();
					$('#emailAddress').parent().addClass("form_error");
					$('#emailAddress').siblings("span").text('Beklager... Epost addressen er allerede tatt');
				}else if (response == 'not_taken') {
					email_state = true;
					$('#emailAddress').parent().removeClass();
					$('#emailAddress').parent().addClass("form_success");
					$('#emailAddress').siblings("span").text('Epost addressen er tilgjengelig');
				}
			}
		});
	});
	$("#user_register_form").submit(function(e){
		if (!email_state || !username_state){
			alert("Vennligst tast inn riktig informasjon \n brukernavn eller email allerede er tatt!")
			e.preventDefault();
		}
	});
});