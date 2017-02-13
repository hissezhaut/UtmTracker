function getURLParams () {
	var temp = {};

	document.location.search.replace(/\??(?:([^=]+)=([^&]*)&?)/g, function() {
		var decode = function(s) {
			return decodeURIComponent(s.split("+").join(" "));
		};
		temp[decode(arguments[1])] = decode(arguments[2]);
	});

	return temp;
}

function utm () {
	var params = getURLParams();

	var result = {
		source: false,
		medium: false,
		campaign: false,
		term: false,
		content: false
	};

	if ( params.utm_source ) result.source = params.utm_source;
	if ( params.utm_medium ) result.medium = params.utm_medium;
	if ( params.utm_campaign ) result.campaign = params.utm_campaign;
	if ( params.utm_term ) result.term = params.utm_term;
	if ( params.utm_content ) result.content = params.utm_content;

	return result;
}