
/*** The starting routine ***********************************************/

window.onload = function() {
	$("#subButton").click(handle_submit);
	$("#regButton").click(handle_register);
	console.log("ehem");
}

/*** Custom Helper Functions ********************************************/

function handle_submit(e) {
	var $form = $('#login_form');
	var send_url = $form.attr("action");
	var login_data = getFormData($form);

	console.log(JSON.stringify(login_data));
	console.log(send_url);


	$.ajax({
		url: send_url,
		type: 'post',
		data: login_data,
		dataType: 'html',
		success: handle_submit_response,
		error: function(xhr, textStatus, errorMessage) {
			console.log(errorMessage);
		}
	});
}

function handle_submit_response(response) {
	console.log(response);
	if (response.error) {
		console.log(responese.error_message);
	} else {
		console.log("success!");
		/* TODO: redirect to quiz page */
	}
}

function handle_register(e) {
}

/*** Utiility Functions *************************************************/

/**
 * Take a form and process it's data into a json object for submission.
 *
 * @param $form The form to processed.
 * @return The json object containing the form data.
*/
function getFormData($form) {
	var unindexed_array = $form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function(n, i) {
		indexed_array[n['name']] = n['value'];
	});

	return indexed_array;
}

