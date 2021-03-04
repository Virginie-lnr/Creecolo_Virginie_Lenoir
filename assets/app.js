/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.scss';

// start the Stimulus application
import './bootstrap';
const $ = require('jquery');
import 'popper.js';
import 'bootstrap/dist/css/bootstrap.css';
import 'font-awesome/css/font-awesome.css';

// npm packages 
import { addBackToTop } from 'vanilla-back-to-top'
addBackToTop({
  diameter: 56,
  backgroundColor: '#585A88',
  textColor: '#fff'
})

console.log('hello from app.js');

