window._ = require('lodash');
window.Axios = require('axios');
window.Axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.ready = function(refCall=null){
    if(typeof refCall ==='function'){
        if (
            document.readyState === "complete" ||
            (document.readyState !== "loading" && !document.documentElement.doScroll)
        ) {
            refCall();
        } else {
            document.addEventListener("DOMContentLoaded", refCall);
        }
    };    
}