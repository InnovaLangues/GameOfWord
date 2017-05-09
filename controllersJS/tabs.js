"use strict";
class TabMenu{
	constructor(idList, titleList, tabPrefix = "onglet_", contentPrefix = "contenu_onglet_"){
		if(idList.length>0 && idList.length==titleList.length){
			this.ids = idList;
			this.titles = titleList;
			this.tabPrefix = tabPrefix;
			this.contentPrefix = contentPrefix
		}
		else{
			throw "Tabs arrays don't have the same length";
		}
	}

	switchTo(tabId){
		document.getElementById(this.tabPrefix+tabId).className = 'onglet_1 onglet active';
		document.getElementById(this.contentPrefix+tabId).style.display = 'block';
		for(let i = 0; i<this.ids.length;i++){
			if(this.ids[i] != tabId){
				document.getElementById(this.tabPrefix+this.ids[i]).className = 'onglet_0 onglet';
				document.getElementById(this.contentPrefix+this.ids[i]).style.display = 'none';
			}
		}
	}

	toString(){
		let res = '<ul class="nav nav-tabs">';
		for(let i = 0; i<this.ids.length;i++){
			res += "\n"+'<li role="presentation" class="onglet_0 onglet" id="'+
				this.tabPrefix+this.ids[i]+'"><a>'+this.titles[i]+'</a></li>';
		}
		res += "\n</ul>";
		return res;
	}

	init(containerID){
		var elt = document.getElementById(containerID);
		if(typeof elt != "undefined"){
			elt.innerHTML = ""+this;
			self = this;
			for(let i = 0; i<this.ids.length;i++){
				let id = this.ids[i];
				let tmpElt = document.getElementById(this.tabPrefix+id);
				if(typeof tmpElt != "undefined"){
					tmpElt.onclick = function(){self.switchTo(id);};
				}
				else{
					throw "Could not retrieve element "+id;
				}
			}
			this.switchTo(this.ids[0]);
		}
		else{
			throw "Could not retrieve element "+containerID;
		}
	}

}
