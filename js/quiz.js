var total = 0;
var score = 0;
var first_submit = true;
var qs = []
var qs_multi = []
var qs_word = []
for (var i = 0; i < 10; i++) {
	qs[i] = [];
	qs[i][0] = "some question " + (i + 1);
	for (var j = 0; j < 4; j++) {
		qs[i][j + 1] = "some answer " + (j + 1);
	}
}

function set_questions(questions) {
	var i = 0;
	var k;
	for (i = 0; i < 10; i++) {
		document.getElementById("q" + (i+1)).innerHTML = "Question " + 
			(i + 1) + ": " + questions[i][0];
		if (i < 5) {
			var labels = document.getElementsByClassName("rl" + (i+1));
			var radios = document.quizform["rq" + (i+1)];
			for (k = 0; k < 4; k++) {
				labels[k].innerHTML = questions[i][k + 1];
				radios[k].value = questions[i][k + 1];
			}
		}
	}

}

function eliminate_whitespace_from(line) {
	var ret_line = "";
	for (var i = 0; i < line.length; i++) {
		if (/[ \f\n\r\t\v\u00A0\u2028\u2029]/.test(line.charAt(i))) {
			continue;
		} else {
			ret_line = ret_line + line.charAt(i);
		}
	}
	return ret_line;
	
}

function calculate_score() {
	console.log(eliminate_whitespace_from("A          Whole  Lot	of     spaces"));
	for (var i = 0; i < 5; i++) {
		total++;
		var radios = document.quizform["rq" + (i+1)];
		for (var j = 0; j < 4; j++) {
			if (radios[j].checked) {
				break;
			}
		}
		if (j != radios.length &&  eliminate_whitespace_from(radios[j].value) == 
				eliminate_whitespace_from(qs[i][5])) {
			score++;
		} else {
			var panel = document.getElementById("pq" + (i+1));
			panel.style.backgroundColor = "red";
			if (j != radios.length) {
				alert("question " + (i + 1) + ") given " + eliminate_whitespace_from(radios[j].value) 
						+ " answer "  + eliminate_whitespace_from(qs[i][5]));
			} else {
				alert("question " + (i + 1) + ") " +  
						" answer should be"  + eliminate_whitespace_from(qs[i][5]));
			}

		}
	}
	var fieldValues = document.getElementsByClassName("qa");
	for (var i = 0; i < 5; i++) {
		total++;
		if (eliminate_whitespace_from(fieldValues[i].value) == eliminate_whitespace_from(qs[i+5][1])) {
			score++;
		} else {
			alert("false answer in question " + (i + 6));
			alert("given " + fieldValues[i].value + " answer "  +
					eliminate_whitespace_from(qs[i+5][1]));
		}
	}
	alert("You got " + score + " out of " + total);
	var form = document.quizform;
	var url = $(form).attr("action");
	console.log("url: ", url);
	var fdata = {};
	fdata.name = form.name.value;
	fdata.score = "" + score;
	$.post("/tut3" + url, fdata).done(function (data) {
		$("#contactResponse").html(data);
		$(form.subbutton).text("thanks");
		$(form.subbutton).attr("disabled", true);
	});

}

function handle_onsubmit(e) {
	e.preventDefault();
	if (first_submit) {
		first_submit = false;
		var all_ticked = true;
		for (var i = 0; i < 5; i++) {
			var radios = document.quizform["rq" + (i+1)];
			for (var j = 0; j < 4; j++) {
				if (radios[j].checked) {
					break;
				}
			}
			if (j == radios.length) {
				e.preventDefault();
				all_ticked = false;
				var panel = document.getElementById("pq" + (i+1));
				panel.style.backgroundColor = "orange";
				var node = document.createElement("b"); 
				var textnode = document.createTextNode("please choose an anwer"); 
				node.appendChild(textnode);         
				document.getElementById("pbq" + (i + 1)).appendChild(node); 
			}
		}
		var fieldValues = document.getElementsByClassName("qa");
		for (var i = 0; i < 5; i++) {
			if (fieldValues[i].value == null || fieldValues[i].value == "") {
				e.preventDefault();
				all_ticked = false;
				panel = document.getElementById("pq" + (i+6));
				panel.style.backgroundColor = "orange";
				//			alert("please type an anwer in question " + (i + 6));
				var node = document.createElement("b"); 
				var textnode = document.createTextNode("please type an anwer"); 
				node.appendChild(textnode);         
				document.getElementById("pbq" + (i + 6)).appendChild(node); 
			}
		}
		if (all_ticked) {
			calculate_score();
		}
	} else {
		calculate_score()
	}
}

function handle_multi_data(data) {
	var lines = data.split("\n");
	for (var i = 0; i < lines.length - 1; i++) {
		qs_multi[i] = lines[i].split(",");
	}
}

function handle_word_data(data) {
	var lines = data.split("\n");
	for (var i = 0; i < lines.length - 1; i++) {
		qs_word[i] = lines[i].split(",");
	}
}

window.onload = function() {
	document.getElementById("quizform").onsubmit = handle_onsubmit;

	$.ajax({ url: "questions_multi.txt",
		async: false,
		type: "GET",
		success: handle_multi_data 
	});
	$.ajax({ url: "questions_word.txt",
		async: false,
		type: "GET",
		success: handle_word_data 
	});

	for (var i = 0; i < 5; i++) {
		qs[i] = qs_multi[i]
		qs[i+5] = qs_word[i]
	}
	set_questions(qs);
}
