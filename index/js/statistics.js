var url = window.location.href;

if (url.search('localhost') < 0 && url.search('121') < 0) {
    // 百度统计
    var _blockt = _blockt || [];
    (function () {
        var block = document.createElement("script");
        block.src = "//block.baidu.com/block.js?1849d1158115681cf6c343bb3d262e1e";
        var s = document.getElementsByTagName("script")[0];
        s.parentNode.insertBefore(block, s);
    })();

    // GrowingIO
  var _vds = _vds || [];
  window._vds = _vds;
  (function(){
    _vds.push(['setAccountId', '848c64f42a61dda7']);
    (function() {
      var vds = document.createElement('script');
      vds.type='text/javascript';
      vds.async = true;
      vds.src = ('https:' == document.location.protocol ? 'https://' : 'http://') + 'dn-growing.qbox.me/vds.js';
      var s = document.getElementsByTagName('script')[0];
      s.parentNode.insertBefore(vds, s);
    })();
  })();
}