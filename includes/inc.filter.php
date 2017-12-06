<?php
if (isset($user) && $user->runscript()) {
	?>
<script type="text/javascript">
function filteroutput() {
	if(document.getElementById('filter').value == -1) {
		document.getElementById('filter').classList.add('input_text_invalid');
		return;
	}
	if(document.getElementById('zahl') != null && document.getElementById('filtervalue').value.length == 0) {
		document.getElementById('filtervalue').classList.add('input_text_invalid');
		return;
	}
	if (window.XMLHttpRequest) {
    	xmlhttp=new XMLHttpRequest();
  	} else {
    	xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
  	}
  	xmlhttp.onreadystatechange=function() {
  		if (this.readyState==4 && this.status==200) {
    		document.getElementById("result").innerHTML=this.responseText;
	   	}
	}
	if(document.getElementById('value2') != null) {
		xmlhttp.open("GET","includes/ajax_filter.php?filter="+document.getElementById("filter").value+"&compare="+document.getElementById('compare').value+"&value="+document.getElementById('filtervalue').value+"&compare2="+document.getElementById('compare2').value+"&value2="+document.getElementById('value2').value);
	}else if(document.getElementById('zahl') != null) {
		xmlhttp.open("GET","includes/ajax_filter.php?filter="+document.getElementById("filter").value+"&compare="+document.getElementById('compare').value+"&value="+document.getElementById('filtervalue').value);
	}else if(document.getElementById('datum') != null) {
		xmlhttp.open("GET","includes/ajax_filter.php?filter="+document.getElementById("filter").value+"&compare="+document.getElementById('compare').value+"&value="+document.getElementById('datepicker').value);
	}else{
		xmlhttp.open("GET","includes/ajax_filter.php?filter="+document.getElementById("filter").value+"&value="+document.getElementById('filtervalue').value);
	}
	xmlhttp.send();	
}

function showsecond(selected) {
	console.log(selected);
	document.getElementById("secondinput").innerHTML = '';
	if(selected == "fach" || selected == "tagfach") {
		console.log("test");
		document.getElementById("secondinput").innerHTML = '<div id="zahl" style="display: flex; width: 100%;">\
			<select id="compare" name="compare">\
			<option value="eq">=</option>\
			<option value="nq">außer</option>\
			</select>\
			<select id="filtervalue" name="filtervalue" style="float: right; margin-left: 2%;">\
			<?php	$faecher = get_faecher_all();	for ($i = 0; $i < count($faecher); $i++) {	echo "<option value=" . $faecher[$i]['id'] . ">" . $faecher[$i]['name'] . "</option>";	}?>\
		</select></div>';
	}
	if(selected == "zahlschueler" || selected == "klassenstufe" || selected == "durchschnitt" || selected == "geldausgabe" || selected == "geldeingabe"|| selected == "geldeinausgabe") {
		document.getElementById("secondinput").innerHTML = 	'<div id="zahl" style="display: flex; width: 100%;">\
		<select id="compare" name="compare">\
		<option value="eq">=</option>\
		<option value="nq">außer</option>\
		<option value="lt">&lt;</option>\
		<option value="gt">&gt;</option>\
		</select>\
		<input id="filtervalue" name="filtervalue" type="text" style="float: right; margin-left: 2%;" class="input_text">\
	</div>';
	}
	if(selected == "hinzugefuegt") {
		document.getElementById("secondinput").innerHTML = 	'<div id="datum" style="display: flex; width: 100%;">\
			<select id="compare" name="compare">\
			<option value="eq">=</option>\
			<option value="nq">außer</option>\
			<option value="lt">&lt;</option>\
			<option value="gt">&gt;</option>\
			</select>\
			<input type="text" id="datepicker" name="value" class="input_text">';
    	$( "#datepicker" ).datepicker({
        	changeYear: true,
	        yearRange: "c-20:c-10",
    	    dateFormat: "dd.mm.yy"
	    	});
		$("#datepicker").datepicker().datepicker("setDate", new Date());
		}
	if(selected == "tagfach") {
		showthird(selected)
	}else{
		document.getElementById("thirdinput").innerHTML = '';
	}
}

function showthird(selected) {
	if(selected == "tagfach") {
		document.getElementById("thirdinput").innerHTML = '<div id="zahl" style="display: flex; width: 100%;">\
			<select id="compare2" name="compare2">\
			<option value="eq">=</option>\
			<option value="nq">außer</option>\
			</select>\
			<select id="value2" name="value2" style="margin-left: 3%;"><option value="mo">Montag</option><option value="di">Dienstag</option>\
			<option value="mi">Mittwoch</option><option value="do">Donnerstag</option>\
			<option value="fr">Freitag</option>\
			</select></div>';
	}
}
</script>
<h1>Filterung der Ausgaben</h1>
<form onsubmit="filteroutput()">
	Filterung von Lehrern, Schülern, Paaren, Personen oder Mitarbeitern der Schülerfirma nach:
	<br>
	<br>
	<div style="display: flex;">
		<select id="filter" name="filter" onchange="showsecond(this.value)" style="width: 49%;">
			<option value="-1">Bitte wählen</option>
			<option value="fach">Nachhilfe nach Fach</option>
			<option value="tagfach">Nachhilfe nach Fach und Tag</option>
			<option value="zahlschueler">Anzahl der Schüler</option>
			<option value="klassenstufe">Klassenstufe</option>
			<option value="durchschnitt">Notenschnitt des Lehrers</option>
			<option value="hinzugefuegt">Datum des Mitarbeitsbeginn</option>
			<option value="geldeinausgabe">aus- und eingezahltes Geld</option>
			<option value="geldausgabe">ausgezahltes Geld</option>
			<!--		<option value="geldeingabe">eingezahltes Geld</option>---->
		</select>
		<div id="secondinput" style="margin-left: 2%; width: 49%; display: flex;"></div>
	</div>
	<br>
	<div id="thirdinput" style="margin-left: 2%; width: 90%;"></div>
	<script type="text/javascript" src="includes/jquery/jquery-ui-1.12.1/datepicker-de.js"></script>
	<script type="text/javascript" src="includes/javascript/javascript.js"></script>
	<script>
	  $( function() {
    	$( "#datepicker" ).datepicker({
        	changeYear: true,
	        yearRange: "2002:2017",
	    	});
	  } );
	</script>

	<input type="button" value="Filtern" onclick="filteroutput()" class="mybuttons">
</form>
<div id="result"></div>
<?php
}else {
	echo "<h1>Ein Fehler ist aufgetreten. Sie haben versucht, die Seite zu laden, ohne die Navigation zu benutzen!</h1>";
}
	