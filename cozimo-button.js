var toolbar = document.getElementById("ed_toolbar");
if ( toolbar ) {
    edButtons[edButtons.length] =
	new edButton('ed_cozimo'
		     ,'cozimo'
		     ,'[cozimo filename="'
		     ,'" /]'
		     ,''
		    );
    var cozimoButton = document.createElement('input');
    cozimoButton.type = 'button';
    cozimoButton.value = 'cozimo';
    cozimoButton.onclick = Cozimo_buttonHandler;
    cozimoButton.className = 'ed_button';
    cozimoButton.title = "Insert a Cozimo Tag";
    cozimoButton.id = "ed_cozimo";
    cozimoButton.accessKey='c';
    toolbar.appendChild(cozimoButton);
}
function Cozimo_buttonHandler() {
    var j = edButtons.length - 1;
    for ( i = 0; i < edButtons.length; i++ ) {
	if ( edButtons[i].id == 'ed_cozimo' ) {
	    j=i;
	}
    }
    edInsertTag(edCanvas, j);
}
