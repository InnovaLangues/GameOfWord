function changeParameters(pageNot) {
	var choixLang = document.getElementById("chooseLang").value;
	alert('ça marche');

	var req = new XMLHttpRequest();
	req.open("GET","update_userlang_game.php?userlang_game="+choixLang+"&page_not="+pageNot, true);
	req.onreadystatechange = function(aEvt){
		if (req.readyState == 4) {
			if(req.status == 200){
				console.log(req.responseText);
				void window.location.reload();
		   }
		   else{
		      console.log("Erreur pendant le chargement de la page.\n");
		  	}
		}
	};
	req.send(null);
}
