<script>
	function addLanguage(){
		var t = document.getElementById("userlang_spoken");
		var count = t.getElementsByTagName("tr").length;
		if (count === 10) { return;}

		// spoken language
		var options = document.getElementById('languages').innerHTML;
		var tr = t.insertRow(count);
		var td = tr.insertCell(0);
		td.align = "center";
		var s = document.createElement('select');
		s.required="required";
		s.setAttribute('name','choix_langs_'.concat(count));
		s.setAttribute('onchange', 'updateRadio(this);');
		s.innerHTML = options;
		s.selectedIndex = 0;
		td.appendChild(s);

		// level
		var td = tr.insertCell(1);
		options = document.getElementById('levels').innerHTML;
		td.align="center";
		s = document.createElement('select');
		s.required="required";
		s.setAttribute('name','choix_niveau_'.concat(count));
		s.innerHTML = options;
		s.selectedIndex = 0;
		td.appendChild(s);

		// Game language
		var td = tr.insertCell(2);
		td.align="center";
		i = document.createElement('input');
		i.type="radio";
		i.setAttribute('name','lang_game');
		td.appendChild(i);
	}

	function removeLastLanguage(){
		var t = document.getElementById("userlang_spoken");
		var count = t.getElementsByTagName("tr").length;
		if (count > 2) {
			t.deleteRow(count-1);
		}
	}

	function updateRadio(l) {
		var tr = l.parentNode.parentNode;
		var game = tr.cells[2].children[0];
		game.setAttribute('value', l.value);
	}
</script>
