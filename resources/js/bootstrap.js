/**
 * Bootstrap Configuration Module
 *
 * Handles the setup and configuration of Bootstrap UI framework.
 * Makes Bootstrap methods globally available for use throughout the application.
 *
 * Note: This file also contains commented-out configurations for:
 * - Lodash utilities
 * - Popper.js (tooltip positioning)
 * - Axios HTTP client with CSRF token handling
 * - Laravel Echo for WebSocket/broadcasting support
 *
 * These can be enabled by uncommenting the relevant sections as needed.
 */

//import { debounce } from 'lodash-es';
//window.debounce = debounce;

//import '@popperjs/core';
//window.Popper = Popper

import * as bootstrap from 'bootstrap';

/**
 * Expose Bootstrap globally for use outside Vite module system.
 * This allows access to Bootstrap's JavaScript components (Modal, Collapse, etc.)
 * from anywhere in the application.
 * @global
 */
window.bootstrap = bootstrap;

/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */

/*
import axios from 'axios';
window.axios = axios;

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
*/

/**
 * Next we will register the CSRF Token as a common header with Axios so that
 * all outgoing HTTP requests automatically have it attached. This is just
 * a simple convenience so we don't have to attach every token manually.
 */

/*
let token = document.head.querySelector('meta[name="csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
} else {
    console.error('CSRF token not found: https://laravel.com/docs/csrf#csrf-x-csrf-token');
}
*/

/**
 * Echo exposes an expressive API for subscribing to channels and listening
 * for events that are broadcast by Laravel. Echo and event broadcasting
 * allows your team to easily build robust real-time web applications.
 */

/*
 import Echo from 'laravel-echo'

 window.Pusher = require('pusher-js');

 window.Echo = new Echo({
     broadcaster: 'pusher',
     key: process.env.MIX_PUSHER_APP_KEY,
     cluster: process.env.MIX_PUSHER_APP_CLUSTER,
     encrypted: true
 });
*/
