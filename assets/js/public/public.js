/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'magnific-popup/dist/magnific-popup.css';
import 'swiper/swiper-bundle.css';
import '../../css/public/show-password-toggle.css';
import '../../css/public/public.css';
import '../../css/public/public-custom.css';
import '../../css/public/auth.css';
import '../../css/commons/hourglass.css';

// Need jQuery? Install it with "yarn add jquery", then uncomment to import it.

const $ = require('jquery');
global.$ = global.jQuery = $;

import 'popper.js';
import 'bootstrap';
import 'jquery-easing';

import Swiper from 'swiper';
global.Swiper = Swiper;

import magnificPopup from 'magnific-popup';
global.magnificPopup = magnificPopup;

require('../../js/public/validator.min.js');
require('../../js/public/scripts.js');
require('../../js/public/request-form.js');
require('../../js/public/show-password-toggle.js');

