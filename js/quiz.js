var questions = null;

$(document).ready(function() {
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

});

function handle_get_qs_response(response) {
	//console.log(response);
	if (response.error) {
		console.log(response.error_message);
	} else  {
		console.log(JSON.stringify(response));
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
			"class": "panel-body"
		});
		var $panel_outer = $("<div/>", {
			"class": "panel panel-default",
			html: $panel_inner
		});
		$panel_inner.html($("<label/>", {
			text: "question " + overall_count + ":"
		}));
		$panel_inner.append("<br>");
		$panel_inner.append($("<label/>", {
			text: questions.multi[i].question
		}));

		for (var j = 0; j < questions.multi[i].opts.length; j++) {
			var $radio = $("<input/>", {
				type: "radio",
				name: questions.multi[i].qID,
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

		//$panel_outer.append($panel_inner);
		$multi_div.append($panel_outer);
	}
}
