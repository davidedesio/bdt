import 'bootstrap/dist/css/bootstrap.min.css';
import '@fortawesome/fontawesome-free/css/all.min.css';
import 'datatables.net-dt/css/jquery.dataTables.min.css';
import 'jquery-ui/themes/base/all.css';
import 'selectize/dist/css/selectize.default.css';
import 'timepicker/jquery.timepicker.min.css'
import 'jbox/dist/jBox.all.css';
import '../../css/restricted/restricted.css';
import '../../css/restricted/restricted-custom.css';
import '../../css/restricted/timeline.css';
import '../../css/commons/hourglass.css';

import $ from 'jquery';
import datepickerFactory from 'jquery-datepicker';
import datepickerITFactory from 'jquery-datepicker/i18n/jquery.ui.datepicker-it';
global.$ = global.jQuery = $;
datepickerFactory($);
datepickerITFactory($);

const timepicker = require('timepicker');
global.timepicker = timepicker;

const DataTable = require('datatables.net');
global.DataTable = DataTable;

import 'popper.js';
import 'bootstrap';

const selectize = require('selectize');
global.selectize = selectize;

const jBox = require('jbox/dist/jBox.all.min');
global.jBox = jBox;

const codiceFiscaleUtils = require('@marketto/codice-fiscale-utils');
const {Validator} = codiceFiscaleUtils;
global.Validator = Validator;

import { Calendar } from "@fullcalendar/core";
import interactionPlugin from "@fullcalendar/interaction";
import dayGridPlugin from "@fullcalendar/daygrid";
import timeGridPlugin from "@fullcalendar/timegrid";

import "@fullcalendar/daygrid/main.css";
import "@fullcalendar/timegrid/main.css";

require('./calendar.js');
require('./timeline.js');