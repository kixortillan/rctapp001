
window._ = require('lodash');

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    //window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;

    window.axios.interceptors.request.use(function (config) {
        // Do something before request is sent
        
        config.headers.common['Authorization'] = 'Bearer ' + localStorage.getItem('access_token')

        return config;
    }, function (error) {
        // Do something with request error
        return Promise.reject(error);
    });
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}

// Add a response interceptor
window.axios.interceptors.response.use(function (response) {
    
    // Do something with response data
    return response;

}, function (error) {
    
    // Do something with response error
    resp = error.response;
    
    if(resp.status === 401){
        //Unauthenticated
        
        //clear tokens
        localStorage.removeItem('access_token');
        localStorage.removeItem('refresh_token');
        localStorage.removeItem('expires_in');
        localStorage.removeItem('user');
        
        //try to refresh token
        window.location = 'login';
    }

    return Promise.reject(error);

});


/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

// import Echo from 'laravel-echo'

// window.Pusher = require('pusher-js');

// window.Echo = new Echo({
//     broadcaster: 'pusher',
//     key: 'your-pusher-key'
// });
// 

Storage.prototype.setObject = function(key, value) {
    this.setItem(key, JSON.stringify(value));
}

Storage.prototype.getObject = function(key) {
    var value = this.getItem(key);
    return value && JSON.parse(value);
}