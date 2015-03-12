function redirect(url, timeout){
	if(typeof timeout!="undefined"&&parseInt(timeout)>0){
		setTimeout("redirect('"+url+"')", parseInt(timeout)*1000);
	}else{
		if(typeof url=="undefined"||url==''){
			window.location.reload(true);
		}else{
			window.location=url;
		}
	}
}

function refresh(timeout){
	redirect(window.location, timeout);
}
