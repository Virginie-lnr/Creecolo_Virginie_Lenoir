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
const axios = require('axios');
const shareButtons = require('share-buttons');

// npm packages 
import { addBackToTop } from 'vanilla-back-to-top'
addBackToTop({
  diameter: 56,
  backgroundColor: '#585A88',
  textColor: '#fff'
})

// AJAX for likes 
function onClickBtnLike(event) {
  event.preventDefault();

  const url = this.href;
  const spanCount = this.querySelector('span.js-likes');
  const icon = this.querySelector('i');

  axios.get(url).then(function (response) {
    spanCount.textContent = response.data.likes;

    if (icon.classList.contains('fa')) {
      icon.classList.replace('fa', 'far');
    } else {
      icon.classList.replace('far', 'fa');
    }
  }).catch(function (error) {
    if (error.response.status === 403) {
      window.alert("You can't like a post if you are not connected!")
    } else {
      window.alert('An error occurred, please try again later')
    }
  });
};

document.querySelectorAll('a.js-like').forEach(function (link) {
  link.addEventListener('click', onClickBtnLike);
})
