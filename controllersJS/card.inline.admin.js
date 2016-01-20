<script>
	function error(cardId, message){
		$("#carte"+cardId+" .tabous").show().after(message);
		$("#carte"+cardId+" .overlay-loader").fadeOut(500);
	}

	function deleteCard(elt){
		var cardId = $(elt).attr('data-id'),
			url = "./controllers/hide.card.php?user_id="+$(elt).attr('data-user')+"&card_id="+cardId,
			req = new XMLHttpRequest();
		req.open("GET", url, true);
		$("#carte"+cardId+" .tabous").hide();
		$("#carte"+cardId+" .overlay-loader").fadeIn(500);
		req.onreadystatechange = function(aEvt){
			if (req.readyState == 4) {
				if(req.status == 200){
					if(req.responseText=="OK"){
						$("#carte"+cardId+" .tabous").after("Carte supprimée");
						$("#carte"+cardId).fadeOut(2000, function(){$(this).remove();});
					}
					else{
						error(cardId, "<p>"+req.responseText+"</p>");
					}
				}
				else{
					error(cardId, "<p>erreur de suppression</p>");
				}
			}
		};
		req.send(null);

	}

	function init(){
		$('.close').click(function(){deleteCard(this);});
		$('.overlay-loader').hide();
	}
	window.onload = init;
</script>