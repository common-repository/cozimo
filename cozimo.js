function attachCallbacks(){var A=navigator.userAgent.toLowerCase();if(A.indexOf("linux")!=-1&&A.indexOf("firefox")!=-1){setTimeout("attachICEInterfaceCallbacks()",1000)}else{attachICEInterfaceCallbacks()}}function getFlashObject(A){if(navigator.appName.indexOf("Microsoft")!=-1){return window[A]}else{return document[A]}}function cleanupEmbededFlash(){var B=getElementsByClassName("cozimoPlayer");if(B){for(var C=0;C<B.length;C++){var A=B[C];if(A){if(A.iceUnload){A.iceUnload()}}}}}function getElementsByClassName(E,K,C){K=K||"*";C=C||document;var M=(K=="*"&&document.all&&!window.opera)?document.all:C.getElementsByTagName(K);var L=new Array();var A=E.indexOf("|")!=-1?"|":" ";var N=E.split(A);for(var J=0,I=M.length;J<I;J++){var H=M[J].className.split(" ");if(A==" "&&N.length>H.length){continue}var O=0;comparisonLoop:for(var G=0,F=H.length;G<F;G++){for(var D=0,B=N.length;D<B;D++){if(N[D]==H[G]){O++}if((A=="|"&&O==1)||(A==" "&&O==N.length)){L.push(M[J]);break comparisonLoop}}}}return L}function attachICEInterfaceCallbacks(){var iceInterfaceTriggers=getElementsByClassName("iceInterface");if(iceInterfaceTriggers){for(var t=0;t<iceInterfaceTriggers.length;t++){var trigger=iceInterfaceTriggers[t];var triggerID=trigger.id.split(".")[1];addEventSimple(trigger,"click",eval(triggerID))}}}function iceGetThis(B){var A;if(this.hasAttribute){A=this}else{if(!B){var B=window.event}if(B.target){A=B.target}else{if(B.srcElement){A=B.srcElement}}if(A.nodeType==3){A=A.parentNode}}return A}function iceToggleTools(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceToggleTools){C.iceToggleTools()}}}else{alert("Cozimo control has no defined relation.")}return false}function iceToggleChat(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceToggleChat){C.iceToggleChat()}}}return false}function iceToggleCollaborators(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceToggleCollaborators){C.iceToggleCollaborators()}}}return false}function iceTogglePresenterMode(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceTogglePresenterMode){C.iceTogglePresenterMode()}}}}function iceClearLast(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceClearLast){C.iceClearLast()}}}return false}function iceClearSession(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceClearSession){C.iceClearSession()}}}return false}function iceClearAll(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceClearAll){C.iceClearAll()}}}return false}function iceResetAll(D){var B=iceGetThis(D);var A=B.getAttribute("rel");if(A){var C=getFlashObject("fo_"+A);if(C){if(C.iceResetAll){C.iceResetAll()}}}return false}function iceEvent(A,C,B){switch(A){case"PRESENTATION_MODE_ON":setPresentationModeOn(C,B);break;case"PRESENTATION_MODE_OFF":setPresentationModeOff(C,B);break;case"CONTENT_LOADED":setSyncVisibility(C,B);break}}function setSyncVisibility(E,D){var B=getElementsByClassName("iceInterface");if(B){for(var C=0;C<B.length;C++){var A=B[C];var G=A.getAttribute("rel");if(G){var F=A.id.split(".")[1];if(G==D&&F=="iceTogglePresenterMode"){if(E.indexOf("video")!=-1){A.className="iceInterface "}else{A.className="iceInterface invisible "}break}}}}}function setPresentationModeOn(G,D){var B=getElementsByClassName("iceInterface");if(B){for(var C=0;C<B.length;C++){var A=B[C];var F=A.getAttribute("rel");if(F){var E=A.id.split(".")[1];if(F==D&&E=="iceTogglePresenterMode"){A.innerHTML="Sync:&nbsp;"+G;break}}}}}function setPresentationModeOff(G,D){var B=getElementsByClassName("iceInterface");if(B){for(var C=0;C<B.length;C++){var A=B[C];var F=A.getAttribute("rel");if(F){var E=A.id.split(".")[1];if(F==D&&E=="iceTogglePresenterMode"){A.innerHTML="Sync:&nbsp;OFF";break}}}}}function addEventSimple(C,A,B){if(C.addEventListener){C.addEventListener(A,B,false)}else{if(C.attachEvent){C.attachEvent("on"+A,B)}}}addEventSimple(window,"load",attachCallbacks);addEventSimple(window,"unload",cleanupEmbededFlash);