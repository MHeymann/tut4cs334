var questions = null;
var email = null;
var active = true;

$(document).ready(function() {
	email = localStorage.getItem("email");

	$("#submit-form").submit(handle_submit);

	$.ajax({
		url: "php/get_qs.php",
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
});

function handle_submit(e) {
	e.preventDefault();
	if (!active) {
		return;
	} else {
		active = !active;
	}
	var $form = $("#submit-form");
	var form_data = getFormData($form);
	var send_url = $form.attr("action");

	multi_data = {};
	written_data = {};
	for (key in form_data) {
		if (key.startsWith("multi")) {
			var stripkey = key.substr("multi".length, key.length -
					"multi".length);
			multi_data[stripkey] = $.trim(form_data[key]);
		} else if (key.startsWith("written")) {
			var stripkey = key.substr("written".length, key.length -
					"written".length);
			written_data[stripkey] = $.trim(form_data[key]);
		} else {
			console.log("eeeeh");
		}
	}
	var submit_data = {};
	submit_data["user"] = email;
	submit_data["multi"] = multi_data;
	submit_data["written"] = written_data;

	$.ajax({
		url: send_url,
		type: 'post',
		data: submit_data,
		dataType: 'json',
		success: handle_submit_response,
		error: function(xhr, textStatus, errorMessage) {
			console.log(errorMessage);
		}
	});

}

function handle_submit_response(response) {
	if (response.error) {
		console.log(response.error_message);
	} else  {
		for (q in response.answers.multi) {
			if (!response.answers.multi[q].user) {
				console.log("multi question " + q + ": false");
				$(".multi" + q).parent().css("background-color", "red");
				$(".multi" + q).append("<p>Correct answer: " +
						response.answers.multi[q].answer + "</p>");
			} else {
				$(".multi" + q).parent().css("background-color", "lime");
				$(".multi" + q).append("<p>Correct!</p>");
			}
		}
		for (q in response.answers.written) {
			if (!response.answers.written[q].user) {
				console.log("written question " + q + ": false");
				$(".written" + q).parent().css("background-color", "red");
				$(".written" + q).append("<p>Correct answer: " +
						response.answers.written[q].answer + "</p>");
			} else {
				$(".written" + q).parent().css("background-color", "lime");
				$(".written" + q).append("<p>Correct!</p>");
			}
		}
	}

}

function handle_get_qs_response(response) {
//	console.log(response);
	if (response.error) {
		console.log(response.error_message);
	} else  {
		questions = response.questions;
		set_questions_to_html();
	}

}

function set_questions_to_html() {
	var $multi_div = $("#multi-div");
	var $written_div = $("#written-div");
	var overall_count = 0;
	if (questions == null) {
		console.log("No questoins set!");
		return;
	}

	$multi_div.empty();
	for (var i = 0; i < questions.multi.length; i++) {
		overall_count++;
		var $panel_inner = $("<div/>", {
			"class": "panel-body multi" + questions.multi[i].qID
		});
		var $panel_outer = $("<div/>", {
			"class": "panel panel-default",
			html: $panel_inner
		});
		$panel_inner.html($("<label/>", {
			text: "Question " + overall_count + ":"
		}));
		$panel_inner.append("<br>");
		$panel_inner.append($("<label/>", {
			text: questions.multi[i].question
		}));

		for (var j = 0; j < questions.multi[i].opts.length; j++) {
			var $radio = $("<input/>", {
				type: "radio",
				required: true,
				name: "multi" + questions.multi[i].qID,
				value: questions.multi[i]['opts'][j]
			});
			var $label = $("<label/>", {
				text: questions.multi[i]['opts'][j]
			});
			var $div = $("<div/>", {
				"class": "radio"
			});

			$label.prepend($radio);
			$div.html($label);
			
			$panel_inner.append($div);
		}

		if (questions.multi[i].stats == null) {
			$panel_inner.append("<p>Server ommited the stats...</p>");
		} else {
			if ((questions.multi[i].stats != null) && 
				(questions.multi[i].stats.system != null)) {
			$panel_inner.append("<p>The average user gets this right " +
					questions.multi[i].stats.system + "% of the time</p>");


				if ((questions.multi[i].stats != null) && 
					(questions.multi[i].stats.user != null)) {
					$panel_inner.append("<p>This user gets this right " +
						questions.multi[i].stats.user +
						"% of the time</p>");

				} else {
					$panel_inner.append("<p>This user has never" +
							" had this question</p>");
				}

			} else {
			$panel_inner.append("<p>No user has ever had this" +
					" question before</p>");
			}

		}

		//$panel_outer.append($panel_inner);
		$multi_div.append($panel_outer);
	}

	$written_div.empty();
	for (var i = 0; i < questions.written.length; i++) {
		overall_count++;
		var $panel_inner = $("<div/>", {
			"class": "panel-body written" + questions.written[i].qID
		});
		var $panel_outer = $("<div/>", {
			"class": "panel panel-default",
			html: $panel_inner
		});
		$panel_inner.html($("<label/>", {
			text: "Question " + overall_count + ":"
		}));
		$panel_inner.append("<br>");
		$panel_inner.append($("<label/>", {
			"for": questions.written[i].qID,
			text: questions.written[i].question
		}));
		$panel_inner.append($("<input/>", {
			"id": questions.written[i].qID,
			name: "written" + questions.written[i].qID,
			title: "Type answer here",
			type: "text",
			"class": "form-control text-field",
			required: true
		}));

		if (questions.written[i].stats == null) {
			$panel_inner.append("<p>Server ommited the stats...</p>");
		} else {
			if ((questions.written[i].stats != null) && 
				(questions.written[i].stats.system != null)) {
					$panel_inner.append("<p>The average user gets this" +
							" right " + questions.written[i].stats.system +
							"% of the time</p>");


				if ((questions.written[i].stats != null) && 
					(questions.written[i].stats.user != null)) {
					$panel_inner.append("<p>This user gets this right " +
						questions.written[i].stats.user +
						"% of the time</p>");

				} else {
					$panel_inner.append("<p>This user has never had" +
							" this question</p>");
				}

			} else {
				$panel_inner.append("<p>No user has ever had this " +
						"question before</p>");
			}

		}

		$written_div.append($panel_outer);
	}

}

/*** Utility Functions ***************************************************/

function getFormData($form) {
	var unindexed_array = $form.serializeArray();
	var indexed_array = {};

	$.map(unindexed_array, function(n, i) {
		indexed_array[n['name']] = n['value'];
	});

	return indexed_array;
}

