document.addEventListener( 'DOMContentLoaded', () => {
	if ( ! document.querySelector( '.rgbcss' ) ) {
		return false;
	}

	const socialLinks = document.querySelectorAll( '.rgbcss' );

	const openLink = ( evt ) => {
		const _self = evt.currentTarget;
		const url   = _self.dataset.url.replace( '[page_url]', location.origin + location.pathname );
		let w = 800,
			h = 500;
		let top  = ( screen.height - h )/2,
			left = ( screen.width - w )/2;
		if ( top < 0 )  top = 0;
		if ( left < 0 ) left = 0;
		const features = 'top=' + top + ',left=' + left + ',height=' + h + ',width=' + w + ',resizable=no';
		open( url, 'displayWindow', features );
	}

	socialLinks.forEach( link => {
		link.addEventListener( 'click', openLink );
	} )

} );