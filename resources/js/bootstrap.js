//import { debounce } from 'lodash-es';
//window.debounce = debounce;

//import '@popperjs/core';
//window.Popper = Popper

import * as bootstrap from 'bootstrap';
//window.bootstrap = bootstrap;

// import jQuery from 'jquery';
// window.$ = window.jQuery = jQuery;

import 'jquery-ui/ui/widgets/autocomplete';

/**
 * We'll load jQuery and the Bootstrap jQuery plugin which provides support
 * for JavaScript based Bootstrap features such as modals and tabs. This
 * code may be modified to fit the specific needs of your application.
 */


  // Registering the CSRF Token as a common header with jQuery Ajax so that all
  // outgoing HTTP requests automatically have it attached.
  /*
  let token = document.head.querySelector('meta[name="csrf-token"]');
  $.ajaxSetup({
    headers: {
      'X-CSRF-TOKEN': token.content
    }
  });
  */

  // Adapt jquery-ui to use Bootstrap
(function( $, undefined ) {
	// Conversion of menu classes to Bootstrap
  $.ui.menu.prototype.options.classes[ "ui-widget" ] = "";
  $.ui.menu.prototype.options.classes[ "ui-widget-content" ] = "";
  $.ui.menu.prototype.options.classes[ "ui-autocomplete" ] = "dropdown";
  $.ui.menu.prototype.options.classes[ "ui-autocomplete-input" ] = "form-control";
	$.ui.menu.prototype.options.classes[ "ui-menu" ] = "dropdown-menu";
	$.ui.menu.prototype.options.classes[ "ui-menu-icons" ] = "";
	$.ui.menu.prototype.options.classes[ "ui-menu-icon" ] = "";
	$.ui.menu.prototype.options.classes[ "ui-menu-item" ] = "dropdown-item";
	$.ui.menu.prototype.options.classes[ "ui-menu-divider" ] = "";
	$.ui.menu.prototype.options.classes[ "ui-menu-item-wrapper" ] = "";
})(jQuery);


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
