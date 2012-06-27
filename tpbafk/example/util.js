function getParams() {
        var idx = document.URL.indexOf('?');
        if (idx != -1) {
                var tempParams = new Object();
                var pairs = document.URL.substring(idx+1, document.URL.length).split('&');
                for (var i=0; i< pairs.length; i++) {
                        nameVal = pairs[i].split('=');
                        tempParams[nameVal[0]] = nameVal[1];
                }
                return tempParams;
        }
}
