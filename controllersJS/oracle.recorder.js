var fileName;

function OracleRecorder(recordRTCOptions){
	//Constructor of a recorder that requires RecordRTC and recordRTCOptions which are mono 44,1kHz by default
	if(typeof recordRTCOptions == "undefined"){
		recordRTCOptions = {type: 'audio',
							bufferSize: 16384,
							sampleRate: 44100,
							leftChannel: false,
							disableLogs: true};
	}
	this.recordRTCOptions = recordRTCOptions ;
	this.recorder = false ;
	this.stream = false ;

	var self = this ;

//events
	this.onStart = function(callback){
		//a function with no parameters that's executed when recording succesfully starts
		self.startRecordingSuccess = callback ;
	};

	this.onStop = function(callback){
		//a function (that takes the url of the audio and (optionnally) the blob of the recording) which is executed when recording succesfully stops
		self.stopRecordingSuccess = callback ;
	};

	this.onMikeError = function(callback){
		//a fuction called, when the microphone is not shared
		self.mikeError = function(err){
			console.error(err);
			callback() ;
			self.recorder = false;
		}
	};

//actual processes
	this.stopRecording = function(){
		self.recorder.stopRecording(function(audioURL){
			self.stopRecordingSuccess(audioURL, self.recorder.blob);
			previewRec(audioURL);
			fileName = 'rec_' + Math.round((Date.now()-1400000000000)/92) + "" + Math.round(Math.random() * 99);
			var fileReader = new FileReader();
			fileReader.onload = function(event) {
				var newBlob = new Blob([event.target.result], {type:"audio/ogg", endings:"native"});
				//~ POST_using_XHR( newBlob );
				PostBlob(newBlob, 'audio', fileName + ".ogg");
			};
			fileReader.readAsArrayBuffer(self.recorder.blob);
		});
	};

	this.startRecording = function(){
		if(self.stream !== false){
			//clearRecordedData, seems to lose the second parameter of getUserMedia
			//so we start a brand new recorder with the same stream
			self.recorder = RecordRTC(self.stream, self.recordRTCOptions);
			self.recorder.startRecording();
			self.startRecordingSuccess();
		}
		else{
			navigator.getUserMedia(
				{video: false, audio: true},
				function(stream){
					self.stream = stream;
					if(window.isChrome){
						self.stream = new window.MediaStream(stream.getAudioTracks());
					}
					if (stream.getAudioTracks().length === 0) {
						console.log('you have no webcam nore mic available on your device');
					}
					else {
						self.recorder = RecordRTC(self.stream, self.recordRTCOptions);
						self.recorder.startRecording();
						self.startRecordingSuccess();
					}
				},
				self.mikeError);
		}
	};
}


// Fonction qui permet de supprimer l'enregistrement du serveur s'il ne nous convient pas lors de l'écoute
function deleteAudioVideoFiles() {
	if (!fileName) return;
	var formData = new FormData();
	formData.append('delete-file', fileName);

	xhr('delete.php', formData, null, null, function(response) {
		console.log(response+"ici");
	});
	fileName = null;
	container.innerHTML = '';
}

function previewRec (url){
	container.appendChild(document.createElement('hr'));

	var mediaElement = document.createElement("audio");
	var source = document.createElement('source');
	source.src =  url;
    source.type = 'audio/mp3';

		mediaElement.appendChild(source);
		mediaElement.muted = false;
		mediaElement.controls = true;
		container.appendChild(mediaElement);
		mediaElement.play();
}





// PostBlob method uses XHR2 and FormData to submit
// recorded blob to the PHP server
function PostBlob(blob, fileType, fileName) {
	// FormData
	var formData = new FormData();
	formData.append(fileType + '-filename', fileName);
	formData.append(fileType + '-blob', blob);


	// progress-bar
	var hr = document.createElement('hr');
	container.appendChild(hr);
	var strong = document.createElement('strong');
	strong.id = 'percentage';
	strong.innerHTML = fileType + ' oracleupload progress: ';
	container.appendChild(strong);
	var progress = document.createElement('progress');
	container.appendChild(progress);


//Récupération des identifiants pour insertion dans la table enregistrement
var userid = $("#userid").attr("data-userid");
var userlang = $("#lang").attr("data-userlang");
var gamelang = $("#lang").attr("data-gamelang");
var cardid = $("#cardid").attr("data-cardid");
var gamelevel = $("#level").attr("data-gamelevel");
var levelcard = $("#level").attr("data-levelcard");

	// POST the Blob using XHR2
xhr('save.php?userid="'+userid+'"&userlang="'+userlang+'"&gamelang="'+gamelang+'"&cardid="'+cardid+'"&levelcard="'+levelcard+'"'+'&gamelevel="'+gamelevel+'"', formData, progress, percentage, function(fileURL) {
	var href = location.href.substr(0, location.href.lastIndexOf('/') + 1);
		progress.parentNode.removeChild(progress);
		strong.parentNode.removeChild(strong);
		hr.parentNode.removeChild(hr);
	});

}



function xhr(url, data, progress, percentage, callback) {
	var request = new XMLHttpRequest();
	request.onreadystatechange = function() {
		if (request.readyState == 4 && request.status == 200) {
			callback(request.responseText);
		}
	};

	// TODO : si firefox alors addeventlistener ; si chrome alors onloadstart/progress/load


	if (url.indexOf('delete.php') == -1) {
		if(!isChrome){
			request.upload.addEventListener("loadstart", function() {
			percentage.innerHTML = 'Upload started...';
			},false);

		     request.upload.addEventListener("progress", function(event){
                      progress.max = event.total;
                      progress.value = event.loaded;
                      percentage.innerHTML = 'Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%    "; },false);

	      request.upload.addEventListener("load",function(){
                      percentage.innerHTML = 'Saved!';
                      $("#form-cmd").show("slow");

              },false);
		}
		else {
			request.upload.onloadstart = function() {
			percentage.innerHTML = 'Upload started...';
			};
	        request.upload.onprogress = function(event) {
		        progress.max = event.total;
		        progress.value = event.loaded;
       			percentage.innerHTML = 'Upload Progress ' + Math.round(event.loaded / event.total * 100) + "%";
			};
			request.upload.onload = function() {
    	    	percentage.innerHTML = 'Saved!';
    	    	$("#form-cmd").show("slow");
       		};
		}
	}
	request.open('POST', url);
	request.send(data);
}
