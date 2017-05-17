$(document).ready(function() {
	console.log("Whaaahahaha");
	$.ajax({
		url: "php/get_qs.php",
		async: false,
		type: 'post',
		data: {
			user: localStorage.getItem("email")
		},
		dataType: 'json',
		success: handle_get_qs_response,
		error: function(xhr, textStatus, errorMessage) {
			console.log(errorMessage);
		}
	});
	console.log("Whaaaheeehehehehe");

});

function handle_get_qs_response(response) {
	//console.log(response);
	console.log(JSON.stringify(response));
}
