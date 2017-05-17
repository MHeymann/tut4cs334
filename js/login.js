var submit_action = "";

/*** The starting routine ***********************************************/

window.onload = function() {
	$("#login_form").submit(handle_submit);
	$("#login_clear").click(clear_login_fields);
}

/*** Custom Helper Functions ********************************************/

function handle_submit(e) {
	$("#server-message").empty();
	e.preventDefault();
	var eID = $(document.activeElement).attr("id");
	if (eID == "logButton") {
		handle_login(e);
	} else if (eID == "regButton") {
		handle_register(e);
	} else if ((eID == "sendButton") || (submit_action != "")) {
		if (submit_action == "register" && ($("#password").val() != $("#password2").val())) {
			alert("The two passwords provided don't match.");
		} else {
			send_form(e);
		}
	} else {
		console.log("This is weird.  ");
	}
}


function handle_login(e) {
	$("#email").prop("readonly", true);
	$("#logButton").hide();
	$("#regButton").hide();
	$("#sendButton").show();
	$("#sendButton").prop('value', 'Log In');
	$("#extend-form").empty();
	$("#extend-form").append($("<label/>", {
		"for": "password",
		text: "Password",
	}));
	$("#extend-form").append($('<input/>', {
		type: "password", 
		id: "password",
		name: "password",
		required: true,
		"class": "form-control"
	}));
	$("#extend-form").append($('<br/>'));

	submit_action = "login";
}


function handle_register(e) {
	$("#logButton").hide();
	$("#regButton").hide();
	$("#sendButton").show();
	$("#sendButton").prop('value', 'Register');
	$("#extend-form").empty();
	$("#extend-form").append($("<label/>", {
		"for": "password",
		text: "Password",
	}));
	$("#extend-form").append($('<input/>', {
		type: "password", 
		pattern: "^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{5,15}$",
		title: "Passwords must be between 5 and 15 characters, with uppercase, lowercase and numeric symbols",
		id: "password",
		name: "password",
		required: true,
		"class": "form-control"
	}));
	$("#extend-form").append($('<br/>'));

	$("#extend-form").append($("<label/>", {
		"for": "password2",
		text: "Repeat Password",
	}));
	$("#extend-form").append($('<input/>', {
		type: "password", 
		pattern: "^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9]).{5,15}$",
		title: "Passwords must be between 5 and 15 characters, with uppercase, lowercase and numeric symbols",
		id: "password2",
		name: "password2",
		required: true,
		"class": "form-control"
	}));
	$("#extend-form").append($('<br/>'));

	submit_action = "register";

}

function send_form(e) {
	var $form = $('#login_form');
	var login_data = getFormData($form);
	var send_url = $form.attr("action");
	login_data.submit_action = submit_action;

	console.log(JSON.stringify(login_data));
	console.log(send_url);

	$.ajax({
		url: send_url,
		type: 'post',
		data: login_data,
		dataType: 'json',
		success: handle_send_response,
		error: function(xhr, textStatus, errorMessage) {
			console.log(errorMessage);
		}
	});
}

function handle_send_response(response) {
	//console.log(response);
	if (response.error) {
		console.log(response.error_message);
		$("#server-message").empty();
		$("#server-message").append("<br>");
		$("#server-message").append($("<p/>", {
			text: response.error_message
		}));
	} else {
		console.log("success!");
		if (typeof(Storage) !== "undefined") {
			// Store
			localStorage.setItem("email", response.user);
			window.location = "questions.html";
		} else {
			alert("Sorry, your browser does not support Web Storage...");
		}
		//console.log(JSON.stringify(response));
	}
}

function clear_login_fields(e) {
	$("#email").prop("readonly", false);
	$("#email").val("");
	$("#logButton").show();
	$("#regButton").show();
	$("#sendButton").prop('value', '');
	$("#sendButton").hide();
	$("#extend-form").empty();
	$("#server-message").empty();
	
	submit_action = "";
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

