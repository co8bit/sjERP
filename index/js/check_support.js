var ua = navigator.userAgent;
if (window.ActiveXObject || "ActiveXObject" in window) {

    var url = window.location.href;

    if (url.search('/index') >= 0) {
        window.location.href = '/index/unsupport.html';
    } else {
        window.location.href = '/unsupport.html';
    }
}