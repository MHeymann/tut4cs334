var email = null;

$(document).ready(function() {
	email = localStorage.getItem("email");

	$("#submit-form").submit(function(e) {
		e.preventDefault();
		console.log("hello! " + email);
	});

	$.ajax({
		url: "php/get_qs.php",
		type: 'post',
		data: {
			user: localStorage.getItem("email")
		},
		dataType: 'html',
		success: handle_get_qs_response,
		error: function(xhr, textStatus, errorMessage) {
			console.log(errorMessage);
		}
	});
});

function handle_get_qs_response(response) {
	console.log(response);
	//console.log(JSON.stringify(response));
}
