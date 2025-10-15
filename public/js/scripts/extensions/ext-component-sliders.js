/******/ (() => { // webpackBootstrap
var __webpack_exports__ = {};
/*!******************************************************************!*\
  !*** ./resources/js/scripts/extensions/ext-component-sliders.js ***!
  \******************************************************************/
function _defineProperty(obj, key, value) { key = _toPropertyKey(key); if (key in obj) { Object.defineProperty(obj, key, { value: value, enumerable: true, configurable: true, writable: true }); } else { obj[key] = value; } return obj; }
function _toPropertyKey(arg) { var key = _toPrimitive(arg, "string"); return _typeof(key) === "symbol" ? key : String(key); }
function _toPrimitive(input, hint) { if (_typeof(input) !== "object" || input === null) return input; var prim = input[Symbol.toPrimitive]; if (prim !== undefined) { var res = prim.call(input, hint || "default"); if (_typeof(res) !== "object") return res; throw new TypeError("@@toPrimitive must return a primitive value."); } return (hint === "string" ? String : Number)(input); }
function _typeof(obj) { "@babel/helpers - typeof"; return _typeof = "function" == typeof Symbol && "symbol" == typeof Symbol.iterator ? function (obj) { return typeof obj; } : function (obj) { return obj && "function" == typeof Symbol && obj.constructor === Symbol && obj !== Symbol.prototype ? "symbol" : typeof obj; }, _typeof(obj); }
/*=========================================================================================
	File Name: ext-component-sliders.js
	Description: noUiSlider is a lightweight JavaScript range slider library.
	----------------------------------------------------------------------------------------
	Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
	Author: PIXINVENT
	Author URL: http://www.themeforest.net/user/pixinvent
==========================================================================================*/

$(function () {
  'use strict';

  // RTL Support
  var _colorOptions;
  var direction = 'ltr';
  if ($('html').data('textdirection') == 'rtl') {
    direction = 'rtl';
  }

  /********************************************
   *				Slider values				*
   ********************************************/

  // Handles
  var handlesSlider = document.getElementById('slider-handles'),
    snapSlider = document.getElementById('slider-snap'),
    tapSlider = document.getElementById('tap'),
    dragSlider = document.getElementById('drag'),
    dragFixedSlider = document.getElementById('drag-fixed'),
    hoverSlider = document.getElementById('hover'),
    field = document.getElementById('hover-val'),
    dragTapSlider = document.getElementById('combined'),
    pipsRange = document.getElementById('pips-range');
  if (_typeof(handlesSlider) !== undefined && handlesSlider !== null) {
    noUiSlider.create(handlesSlider, {
      start: [4000, 8000],
      direction: direction,
      range: {
        min: [2000],
        max: [10000]
      }
    });
  }

  // Snapping between steps

  if (_typeof(snapSlider) !== undefined && snapSlider !== null) {
    noUiSlider.create(snapSlider, {
      start: [0, 500],
      direction: direction,
      snap: true,
      connect: true,
      range: {
        min: 0,
        '10%': 50,
        '20%': 100,
        '30%': 150,
        '40%': 500,
        '50%': 800,
        max: 1000
      }
    });
  }

  /************************************************
   *				Slider behaviour				*
   ************************************************/

  // Tap
  if (_typeof(tapSlider) !== undefined && tapSlider !== null) {
    noUiSlider.create(tapSlider, {
      start: [20, 40],
      direction: direction,
      behaviour: 'tap',
      connect: true,
      range: {
        min: 10,
        max: 50
      }
    });
  }

  // Drag
  if (_typeof(dragSlider) !== undefined && dragSlider !== null) {
    noUiSlider.create(dragSlider, {
      start: [40, 60],
      direction: direction,
      behaviour: 'drag',
      connect: true,
      range: {
        min: 20,
        max: 80
      }
    });
  }

  // Fixed dragging
  if (_typeof(dragFixedSlider) !== undefined && dragFixedSlider !== null) {
    noUiSlider.create(dragFixedSlider, {
      start: [40, 60],
      direction: direction,
      behaviour: 'drag-fixed',
      connect: true,
      range: {
        min: 20,
        max: 80
      }
    });
  }

  // Hover
  if (_typeof(hoverSlider) !== undefined && hoverSlider !== null) {
    noUiSlider.create(hoverSlider, {
      start: 20,
      direction: direction,
      behaviour: 'hover-snap',
      range: {
        min: 0,
        max: 100
      }
    });
    hoverSlider.noUiSlider.on('hover', function (value) {
      field.innerHTML = value;
    });
  }

  // Combined options
  if (_typeof(dragTapSlider) !== undefined && dragTapSlider !== null) {
    noUiSlider.create(dragTapSlider, {
      start: [40, 60],
      direction: direction,
      behaviour: 'drag-tap',
      connect: true,
      range: {
        min: 20,
        max: 80
      }
    });
  }

  /****************************************************
   *				Slider Scales / Pips				*
   ****************************************************/

  if (_typeof(pipsRange) !== undefined && pipsRange !== null) {
    // Range
    noUiSlider.create(pipsRange, {
      start: 10,
      step: 10,
      range: {
        min: 0,
        max: 100
      },
      tooltips: true,
      direction: direction,
      pips: {
        mode: 'steps',
        stepped: true,
        density: 5
      }
    });
  }

  /********************************************
   *				Slider Colors				*
   ********************************************/

  var defaultColorSlider = document.getElementById('default-color-slider'),
    secondaryColorSlider = document.getElementById('secondary-color-slider'),
    successColorSlider = document.getElementById('success-color-slider'),
    infoColorSlider = document.getElementById('info-color-slider'),
    warningColorSlider = document.getElementById('warning-color-slider'),
    dangerColorSlider = document.getElementById('danger-color-slider');
  var colorOptions = (_colorOptions = {
    start: [40, 60],
    connect: true,
    behaviour: 'drag'
  }, _defineProperty(_colorOptions, "connect", true), _defineProperty(_colorOptions, "step", 10), _defineProperty(_colorOptions, "tooltips", true), _defineProperty(_colorOptions, "range", {
    min: 0,
    max: 100
  }), _defineProperty(_colorOptions, "pips", {
    mode: 'steps',
    stepped: true,
    density: 5
  }), _defineProperty(_colorOptions, "direction", direction), _colorOptions);
  if (_typeof(defaultColorSlider) !== undefined && defaultColorSlider !== null) {
    noUiSlider.create(defaultColorSlider, colorOptions);
  }
  if (_typeof(secondaryColorSlider) !== undefined && secondaryColorSlider !== null) {
    noUiSlider.create(secondaryColorSlider, colorOptions);
  }
  if (_typeof(successColorSlider) !== undefined && successColorSlider !== null) {
    noUiSlider.create(successColorSlider, colorOptions);
  }
  if (_typeof(infoColorSlider) !== undefined && infoColorSlider !== null) {
    noUiSlider.create(infoColorSlider, colorOptions);
  }
  if (_typeof(warningColorSlider) !== undefined && warningColorSlider !== null) {
    noUiSlider.create(warningColorSlider, colorOptions);
  }
  if (_typeof(dangerColorSlider) !== undefined && dangerColorSlider !== null) {
    noUiSlider.create(dangerColorSlider, colorOptions);
  }

  /********************************************
   *				Vertical Slider				*
   ********************************************/

  // Default
  var verticalSlider = document.getElementById('slider-vertical'),
    connectUpperSlider = document.getElementById('connect-upper'),
    tooltipSlider = document.getElementById('slider-tooltips'),
    verticalLimitSlider = document.getElementById('vertical-limit');
  if (_typeof(verticalSlider) !== undefined && verticalSlider !== null) {
    verticalSlider.style.height = '200px';
    noUiSlider.create(verticalSlider, {
      start: 20,
      direction: direction,
      orientation: 'vertical',
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Connect to upper
  if (_typeof(connectUpperSlider) !== undefined && connectUpperSlider !== null) {
    connectUpperSlider.style.height = '200px';
    noUiSlider.create(connectUpperSlider, {
      start: 30,
      direction: direction,
      orientation: 'vertical',
      connect: 'upper',
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Tooltips
  if (_typeof(tooltipSlider) !== undefined && tooltipSlider !== null) {
    tooltipSlider.style.height = '200px';
    noUiSlider.create(tooltipSlider, {
      start: [20, 80],
      direction: direction,
      orientation: 'vertical',
      tooltips: [wNumb({
        decimals: 1
      }), wNumb({
        decimals: 1
      })],
      range: {
        min: 0,
        max: 100
      }
    });
  }

  // Limit
  if (_typeof(verticalLimitSlider) !== undefined && verticalLimitSlider !== null) {
    verticalLimitSlider.style.height = '200px';
    noUiSlider.create(verticalLimitSlider, {
      start: [40, 60],
      direction: direction,
      orientation: 'vertical',
      limit: 40,
      behaviour: 'drag',
      connect: true,
      range: {
        min: 0,
        max: 100
      }
    });
  }

  /****************************************************
   *				 Slider With Input				*
   ****************************************************/

  var select = document.getElementById('slider-select'),
    sliderWithInput = document.getElementById('slider-with-input'),
    inputNumber = document.getElementById('slider-input-number');
  if (_typeof(sliderWithInput) !== undefined && sliderWithInput !== null) {
    noUiSlider.create(sliderWithInput, {
      start: [10, 30],
      direction: direction,
      connect: true,
      range: {
        min: -20,
        max: 40
      }
    });
    sliderWithInput.noUiSlider.on('update', function (values, handle) {
      var value = values[handle];
      if (handle) {
        inputNumber.value = value;
      } else {
        select.value = Math.round(value);
      }
    });
  }
  if (_typeof(sliderWithInput) !== undefined && sliderWithInput !== null) {
    // Append the option elements
    for (var i = -20; i <= 40; i++) {
      var option = document.createElement('option');
      option.text = i;
      option.value = i;
      select.appendChild(option);
    }
    select.addEventListener('change', function () {
      sliderWithInput.noUiSlider.set([this.value, null]);
    });
  }
  if (_typeof(inputNumber) !== undefined && inputNumber !== null) {
    inputNumber.addEventListener('change', function () {
      sliderWithInput.noUiSlider.set([null, this.value]);
    });
  }
});
/******/ })()
;