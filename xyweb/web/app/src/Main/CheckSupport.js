var ua = navigator.userAgent;
if (window.ActiveXObject || "ActiveXObject" in window) {

    var url = window.location.href;

    if (url.search('/xyweb') >= 0) {
        var tmpPos = url.search('/xyweb');
        window.location.href = url.substring(0, tmpPos) + '/xyweb/unsupport.html';
    } else if (url.search('/app') >= 0) {
        var tmpPos = url.search('/app')
        window.location.href = url.substring(0, tmpPos) + '/app/unsupport.html';
    }
}