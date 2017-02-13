$(document).ready(function () {
	var utms = utm();

	if ( JSON.stringify( utms ) != '{"source":false,"medium":false,"campaign":false,"term":false,"content":false}' ) {
		Cookies.set( 'utms', utms );
	}
});