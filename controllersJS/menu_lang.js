function changeParameters(pageNot, action) {
	var req = new XMLHttpRequest();
	var address = "update_userlang_game.php?page_not=" + pageNot ;
	console.log(address);
	if(action == "lang"){
		address += "&userlang_game=" +
			document.getElementById("chooseLang").value ;
	}
	else if (action == "level"){
		address += "&game_lvl=" +
			document.getElementById("chooseLevel").value ;
	}
	console.log(address);
	req.open("GET",address, true);
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
