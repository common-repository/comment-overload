jQuery(document).ready( function($) {

	var co_max_nl    = commentOverload.max,
		co_alert_msg = commentOverload.alert,
		co_subtle    = commentOverload.subtle,
		co_content   = '',
		co_total     = 0,
		co_alerts    = 0;

	//do magic if 'return' key is pressed
	$( document.getElementById('comment') ).keyup( function( event ) {

		if ( event.keyCode == '13') {
			co_content = $(this).val(),
			co_content = co_content.split("\n").clean(''); // account for \n\n
			co_total   = co_content.length;

			if ( co_total >= co_max_nl ) {

				alert_msg = commentOverload.alert.replace(/%total%/g, co_total );
				alert_msg = alert_msg.replace(/%max%/g, co_max_nl );

				if ( co_alerts < 1 ) {
					if ( co_subtle == 1 ) {
						co_alerts++;
					}
					alert( alert_msg );
				} else if ( co_alerts >= 1 ) {
					$('#co-inline-warning').remove();
					$(this).after('<p id="co-inline-warning">'+ alert_msg +'</p>');
				}
			}

		}

	});

});

//http://stackoverflow.com/questions/281264/remove-empty-elements-from-an-array-in-javascript
Array.prototype.clean = function(deleteValue) {
  for (var i = 0; i < this.length; i++) {
    if (this[i] == deleteValue) {
      this.splice(i, 1);
      i--;
    }
  }
  return this;
};
