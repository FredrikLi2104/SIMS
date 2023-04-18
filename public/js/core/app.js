/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/axios/index.js":
/*!*************************************!*\
  !*** ./node_modules/axios/index.js ***!
  \*************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

module.exports = __webpack_require__(/*! ./lib/axios */ "./node_modules/axios/lib/axios.js");

/***/ }),

/***/ "./node_modules/axios/lib/adapters/xhr.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/adapters/xhr.js ***!
  \************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var settle = __webpack_require__(/*! ./../core/settle */ "./node_modules/axios/lib/core/settle.js");
var cookies = __webpack_require__(/*! ./../helpers/cookies */ "./node_modules/axios/lib/helpers/cookies.js");
var buildURL = __webpack_require__(/*! ./../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var buildFullPath = __webpack_require__(/*! ../core/buildFullPath */ "./node_modules/axios/lib/core/buildFullPath.js");
var parseHeaders = __webpack_require__(/*! ./../helpers/parseHeaders */ "./node_modules/axios/lib/helpers/parseHeaders.js");
var isURLSameOrigin = __webpack_require__(/*! ./../helpers/isURLSameOrigin */ "./node_modules/axios/lib/helpers/isURLSameOrigin.js");
var createError = __webpack_require__(/*! ../core/createError */ "./node_modules/axios/lib/core/createError.js");
var defaults = __webpack_require__(/*! ../defaults */ "./node_modules/axios/lib/defaults.js");
var Cancel = __webpack_require__(/*! ../cancel/Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");

module.exports = function xhrAdapter(config) {
  return new Promise(function dispatchXhrRequest(resolve, reject) {
    var requestData = config.data;
    var requestHeaders = config.headers;
    var responseType = config.responseType;
    var onCanceled;
    function done() {
      if (config.cancelToken) {
        config.cancelToken.unsubscribe(onCanceled);
      }

      if (config.signal) {
        config.signal.removeEventListener('abort', onCanceled);
      }
    }

    if (utils.isFormData(requestData)) {
      delete requestHeaders['Content-Type']; // Let the browser set it
    }

    var request = new XMLHttpRequest();

    // HTTP basic authentication
    if (config.auth) {
      var username = config.auth.username || '';
      var password = config.auth.password ? unescape(encodeURIComponent(config.auth.password)) : '';
      requestHeaders.Authorization = 'Basic ' + btoa(username + ':' + password);
    }

    var fullPath = buildFullPath(config.baseURL, config.url);
    request.open(config.method.toUpperCase(), buildURL(fullPath, config.params, config.paramsSerializer), true);

    // Set the request timeout in MS
    request.timeout = config.timeout;

    function onloadend() {
      if (!request) {
        return;
      }
      // Prepare the response
      var responseHeaders = 'getAllResponseHeaders' in request ? parseHeaders(request.getAllResponseHeaders()) : null;
      var responseData = !responseType || responseType === 'text' ||  responseType === 'json' ?
        request.responseText : request.response;
      var response = {
        data: responseData,
        status: request.status,
        statusText: request.statusText,
        headers: responseHeaders,
        config: config,
        request: request
      };

      settle(function _resolve(value) {
        resolve(value);
        done();
      }, function _reject(err) {
        reject(err);
        done();
      }, response);

      // Clean up request
      request = null;
    }

    if ('onloadend' in request) {
      // Use onloadend if available
      request.onloadend = onloadend;
    } else {
      // Listen for ready state to emulate onloadend
      request.onreadystatechange = function handleLoad() {
        if (!request || request.readyState !== 4) {
          return;
        }

        // The request errored out and we didn't get a response, this will be
        // handled by onerror instead
        // With one exception: request that using file: protocol, most browsers
        // will return status as 0 even though it's a successful request
        if (request.status === 0 && !(request.responseURL && request.responseURL.indexOf('file:') === 0)) {
          return;
        }
        // readystate handler is calling before onerror or ontimeout handlers,
        // so we should call onloadend on the next 'tick'
        setTimeout(onloadend);
      };
    }

    // Handle browser request cancellation (as opposed to a manual cancellation)
    request.onabort = function handleAbort() {
      if (!request) {
        return;
      }

      reject(createError('Request aborted', config, 'ECONNABORTED', request));

      // Clean up request
      request = null;
    };

    // Handle low level network errors
    request.onerror = function handleError() {
      // Real errors are hidden from us by the browser
      // onerror should only fire if it's a network error
      reject(createError('Network Error', config, null, request));

      // Clean up request
      request = null;
    };

    // Handle timeout
    request.ontimeout = function handleTimeout() {
      var timeoutErrorMessage = config.timeout ? 'timeout of ' + config.timeout + 'ms exceeded' : 'timeout exceeded';
      var transitional = config.transitional || defaults.transitional;
      if (config.timeoutErrorMessage) {
        timeoutErrorMessage = config.timeoutErrorMessage;
      }
      reject(createError(
        timeoutErrorMessage,
        config,
        transitional.clarifyTimeoutError ? 'ETIMEDOUT' : 'ECONNABORTED',
        request));

      // Clean up request
      request = null;
    };

    // Add xsrf header
    // This is only done if running in a standard browser environment.
    // Specifically not if we're in a web worker, or react-native.
    if (utils.isStandardBrowserEnv()) {
      // Add xsrf header
      var xsrfValue = (config.withCredentials || isURLSameOrigin(fullPath)) && config.xsrfCookieName ?
        cookies.read(config.xsrfCookieName) :
        undefined;

      if (xsrfValue) {
        requestHeaders[config.xsrfHeaderName] = xsrfValue;
      }
    }

    // Add headers to the request
    if ('setRequestHeader' in request) {
      utils.forEach(requestHeaders, function setRequestHeader(val, key) {
        if (typeof requestData === 'undefined' && key.toLowerCase() === 'content-type') {
          // Remove Content-Type if data is undefined
          delete requestHeaders[key];
        } else {
          // Otherwise add header to the request
          request.setRequestHeader(key, val);
        }
      });
    }

    // Add withCredentials to request if needed
    if (!utils.isUndefined(config.withCredentials)) {
      request.withCredentials = !!config.withCredentials;
    }

    // Add responseType to request if needed
    if (responseType && responseType !== 'json') {
      request.responseType = config.responseType;
    }

    // Handle progress if needed
    if (typeof config.onDownloadProgress === 'function') {
      request.addEventListener('progress', config.onDownloadProgress);
    }

    // Not all browsers support upload events
    if (typeof config.onUploadProgress === 'function' && request.upload) {
      request.upload.addEventListener('progress', config.onUploadProgress);
    }

    if (config.cancelToken || config.signal) {
      // Handle cancellation
      // eslint-disable-next-line func-names
      onCanceled = function(cancel) {
        if (!request) {
          return;
        }
        reject(!cancel || (cancel && cancel.type) ? new Cancel('canceled') : cancel);
        request.abort();
        request = null;
      };

      config.cancelToken && config.cancelToken.subscribe(onCanceled);
      if (config.signal) {
        config.signal.aborted ? onCanceled() : config.signal.addEventListener('abort', onCanceled);
      }
    }

    if (!requestData) {
      requestData = null;
    }

    // Send the request
    request.send(requestData);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/axios.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/axios.js ***!
  \*****************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");
var Axios = __webpack_require__(/*! ./core/Axios */ "./node_modules/axios/lib/core/Axios.js");
var mergeConfig = __webpack_require__(/*! ./core/mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
var defaults = __webpack_require__(/*! ./defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Create an instance of Axios
 *
 * @param {Object} defaultConfig The default config for the instance
 * @return {Axios} A new instance of Axios
 */
function createInstance(defaultConfig) {
  var context = new Axios(defaultConfig);
  var instance = bind(Axios.prototype.request, context);

  // Copy axios.prototype to instance
  utils.extend(instance, Axios.prototype, context);

  // Copy context to instance
  utils.extend(instance, context);

  // Factory for creating new instances
  instance.create = function create(instanceConfig) {
    return createInstance(mergeConfig(defaultConfig, instanceConfig));
  };

  return instance;
}

// Create the default instance to be exported
var axios = createInstance(defaults);

// Expose Axios class to allow class inheritance
axios.Axios = Axios;

// Expose Cancel & CancelToken
axios.Cancel = __webpack_require__(/*! ./cancel/Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");
axios.CancelToken = __webpack_require__(/*! ./cancel/CancelToken */ "./node_modules/axios/lib/cancel/CancelToken.js");
axios.isCancel = __webpack_require__(/*! ./cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");
axios.VERSION = (__webpack_require__(/*! ./env/data */ "./node_modules/axios/lib/env/data.js").version);

// Expose all/spread
axios.all = function all(promises) {
  return Promise.all(promises);
};
axios.spread = __webpack_require__(/*! ./helpers/spread */ "./node_modules/axios/lib/helpers/spread.js");

// Expose isAxiosError
axios.isAxiosError = __webpack_require__(/*! ./helpers/isAxiosError */ "./node_modules/axios/lib/helpers/isAxiosError.js");

module.exports = axios;

// Allow use of default import syntax in TypeScript
module.exports["default"] = axios;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/Cancel.js":
/*!*************************************************!*\
  !*** ./node_modules/axios/lib/cancel/Cancel.js ***!
  \*************************************************/
/***/ ((module) => {

"use strict";


/**
 * A `Cancel` is an object that is thrown when an operation is canceled.
 *
 * @class
 * @param {string=} message The message.
 */
function Cancel(message) {
  this.message = message;
}

Cancel.prototype.toString = function toString() {
  return 'Cancel' + (this.message ? ': ' + this.message : '');
};

Cancel.prototype.__CANCEL__ = true;

module.exports = Cancel;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/CancelToken.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/cancel/CancelToken.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var Cancel = __webpack_require__(/*! ./Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");

/**
 * A `CancelToken` is an object that can be used to request cancellation of an operation.
 *
 * @class
 * @param {Function} executor The executor function.
 */
function CancelToken(executor) {
  if (typeof executor !== 'function') {
    throw new TypeError('executor must be a function.');
  }

  var resolvePromise;

  this.promise = new Promise(function promiseExecutor(resolve) {
    resolvePromise = resolve;
  });

  var token = this;

  // eslint-disable-next-line func-names
  this.promise.then(function(cancel) {
    if (!token._listeners) return;

    var i;
    var l = token._listeners.length;

    for (i = 0; i < l; i++) {
      token._listeners[i](cancel);
    }
    token._listeners = null;
  });

  // eslint-disable-next-line func-names
  this.promise.then = function(onfulfilled) {
    var _resolve;
    // eslint-disable-next-line func-names
    var promise = new Promise(function(resolve) {
      token.subscribe(resolve);
      _resolve = resolve;
    }).then(onfulfilled);

    promise.cancel = function reject() {
      token.unsubscribe(_resolve);
    };

    return promise;
  };

  executor(function cancel(message) {
    if (token.reason) {
      // Cancellation has already been requested
      return;
    }

    token.reason = new Cancel(message);
    resolvePromise(token.reason);
  });
}

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
CancelToken.prototype.throwIfRequested = function throwIfRequested() {
  if (this.reason) {
    throw this.reason;
  }
};

/**
 * Subscribe to the cancel signal
 */

CancelToken.prototype.subscribe = function subscribe(listener) {
  if (this.reason) {
    listener(this.reason);
    return;
  }

  if (this._listeners) {
    this._listeners.push(listener);
  } else {
    this._listeners = [listener];
  }
};

/**
 * Unsubscribe from the cancel signal
 */

CancelToken.prototype.unsubscribe = function unsubscribe(listener) {
  if (!this._listeners) {
    return;
  }
  var index = this._listeners.indexOf(listener);
  if (index !== -1) {
    this._listeners.splice(index, 1);
  }
};

/**
 * Returns an object that contains a new `CancelToken` and a function that, when called,
 * cancels the `CancelToken`.
 */
CancelToken.source = function source() {
  var cancel;
  var token = new CancelToken(function executor(c) {
    cancel = c;
  });
  return {
    token: token,
    cancel: cancel
  };
};

module.exports = CancelToken;


/***/ }),

/***/ "./node_modules/axios/lib/cancel/isCancel.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/cancel/isCancel.js ***!
  \***************************************************/
/***/ ((module) => {

"use strict";


module.exports = function isCancel(value) {
  return !!(value && value.__CANCEL__);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/Axios.js":
/*!**********************************************!*\
  !*** ./node_modules/axios/lib/core/Axios.js ***!
  \**********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var buildURL = __webpack_require__(/*! ../helpers/buildURL */ "./node_modules/axios/lib/helpers/buildURL.js");
var InterceptorManager = __webpack_require__(/*! ./InterceptorManager */ "./node_modules/axios/lib/core/InterceptorManager.js");
var dispatchRequest = __webpack_require__(/*! ./dispatchRequest */ "./node_modules/axios/lib/core/dispatchRequest.js");
var mergeConfig = __webpack_require__(/*! ./mergeConfig */ "./node_modules/axios/lib/core/mergeConfig.js");
var validator = __webpack_require__(/*! ../helpers/validator */ "./node_modules/axios/lib/helpers/validator.js");

var validators = validator.validators;
/**
 * Create a new instance of Axios
 *
 * @param {Object} instanceConfig The default config for the instance
 */
function Axios(instanceConfig) {
  this.defaults = instanceConfig;
  this.interceptors = {
    request: new InterceptorManager(),
    response: new InterceptorManager()
  };
}

/**
 * Dispatch a request
 *
 * @param {Object} config The config specific for this request (merged with this.defaults)
 */
Axios.prototype.request = function request(configOrUrl, config) {
  /*eslint no-param-reassign:0*/
  // Allow for axios('example/url'[, config]) a la fetch API
  if (typeof configOrUrl === 'string') {
    config = config || {};
    config.url = configOrUrl;
  } else {
    config = configOrUrl || {};
  }

  if (!config.url) {
    throw new Error('Provided config url is not valid');
  }

  config = mergeConfig(this.defaults, config);

  // Set config.method
  if (config.method) {
    config.method = config.method.toLowerCase();
  } else if (this.defaults.method) {
    config.method = this.defaults.method.toLowerCase();
  } else {
    config.method = 'get';
  }

  var transitional = config.transitional;

  if (transitional !== undefined) {
    validator.assertOptions(transitional, {
      silentJSONParsing: validators.transitional(validators.boolean),
      forcedJSONParsing: validators.transitional(validators.boolean),
      clarifyTimeoutError: validators.transitional(validators.boolean)
    }, false);
  }

  // filter out skipped interceptors
  var requestInterceptorChain = [];
  var synchronousRequestInterceptors = true;
  this.interceptors.request.forEach(function unshiftRequestInterceptors(interceptor) {
    if (typeof interceptor.runWhen === 'function' && interceptor.runWhen(config) === false) {
      return;
    }

    synchronousRequestInterceptors = synchronousRequestInterceptors && interceptor.synchronous;

    requestInterceptorChain.unshift(interceptor.fulfilled, interceptor.rejected);
  });

  var responseInterceptorChain = [];
  this.interceptors.response.forEach(function pushResponseInterceptors(interceptor) {
    responseInterceptorChain.push(interceptor.fulfilled, interceptor.rejected);
  });

  var promise;

  if (!synchronousRequestInterceptors) {
    var chain = [dispatchRequest, undefined];

    Array.prototype.unshift.apply(chain, requestInterceptorChain);
    chain = chain.concat(responseInterceptorChain);

    promise = Promise.resolve(config);
    while (chain.length) {
      promise = promise.then(chain.shift(), chain.shift());
    }

    return promise;
  }


  var newConfig = config;
  while (requestInterceptorChain.length) {
    var onFulfilled = requestInterceptorChain.shift();
    var onRejected = requestInterceptorChain.shift();
    try {
      newConfig = onFulfilled(newConfig);
    } catch (error) {
      onRejected(error);
      break;
    }
  }

  try {
    promise = dispatchRequest(newConfig);
  } catch (error) {
    return Promise.reject(error);
  }

  while (responseInterceptorChain.length) {
    promise = promise.then(responseInterceptorChain.shift(), responseInterceptorChain.shift());
  }

  return promise;
};

Axios.prototype.getUri = function getUri(config) {
  if (!config.url) {
    throw new Error('Provided config url is not valid');
  }
  config = mergeConfig(this.defaults, config);
  return buildURL(config.url, config.params, config.paramsSerializer).replace(/^\?/, '');
};

// Provide aliases for supported request methods
utils.forEach(['delete', 'get', 'head', 'options'], function forEachMethodNoData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, config) {
    return this.request(mergeConfig(config || {}, {
      method: method,
      url: url,
      data: (config || {}).data
    }));
  };
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  /*eslint func-names:0*/
  Axios.prototype[method] = function(url, data, config) {
    return this.request(mergeConfig(config || {}, {
      method: method,
      url: url,
      data: data
    }));
  };
});

module.exports = Axios;


/***/ }),

/***/ "./node_modules/axios/lib/core/InterceptorManager.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/core/InterceptorManager.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

function InterceptorManager() {
  this.handlers = [];
}

/**
 * Add a new interceptor to the stack
 *
 * @param {Function} fulfilled The function to handle `then` for a `Promise`
 * @param {Function} rejected The function to handle `reject` for a `Promise`
 *
 * @return {Number} An ID used to remove interceptor later
 */
InterceptorManager.prototype.use = function use(fulfilled, rejected, options) {
  this.handlers.push({
    fulfilled: fulfilled,
    rejected: rejected,
    synchronous: options ? options.synchronous : false,
    runWhen: options ? options.runWhen : null
  });
  return this.handlers.length - 1;
};

/**
 * Remove an interceptor from the stack
 *
 * @param {Number} id The ID that was returned by `use`
 */
InterceptorManager.prototype.eject = function eject(id) {
  if (this.handlers[id]) {
    this.handlers[id] = null;
  }
};

/**
 * Iterate over all the registered interceptors
 *
 * This method is particularly useful for skipping over any
 * interceptors that may have become `null` calling `eject`.
 *
 * @param {Function} fn The function to call for each interceptor
 */
InterceptorManager.prototype.forEach = function forEach(fn) {
  utils.forEach(this.handlers, function forEachHandler(h) {
    if (h !== null) {
      fn(h);
    }
  });
};

module.exports = InterceptorManager;


/***/ }),

/***/ "./node_modules/axios/lib/core/buildFullPath.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/buildFullPath.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var isAbsoluteURL = __webpack_require__(/*! ../helpers/isAbsoluteURL */ "./node_modules/axios/lib/helpers/isAbsoluteURL.js");
var combineURLs = __webpack_require__(/*! ../helpers/combineURLs */ "./node_modules/axios/lib/helpers/combineURLs.js");

/**
 * Creates a new URL by combining the baseURL with the requestedURL,
 * only when the requestedURL is not already an absolute URL.
 * If the requestURL is absolute, this function returns the requestedURL untouched.
 *
 * @param {string} baseURL The base URL
 * @param {string} requestedURL Absolute or relative URL to combine
 * @returns {string} The combined full path
 */
module.exports = function buildFullPath(baseURL, requestedURL) {
  if (baseURL && !isAbsoluteURL(requestedURL)) {
    return combineURLs(baseURL, requestedURL);
  }
  return requestedURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/createError.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/createError.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var enhanceError = __webpack_require__(/*! ./enhanceError */ "./node_modules/axios/lib/core/enhanceError.js");

/**
 * Create an Error with the specified message, config, error code, request and response.
 *
 * @param {string} message The error message.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The created error.
 */
module.exports = function createError(message, config, code, request, response) {
  var error = new Error(message);
  return enhanceError(error, config, code, request, response);
};


/***/ }),

/***/ "./node_modules/axios/lib/core/dispatchRequest.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/core/dispatchRequest.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var transformData = __webpack_require__(/*! ./transformData */ "./node_modules/axios/lib/core/transformData.js");
var isCancel = __webpack_require__(/*! ../cancel/isCancel */ "./node_modules/axios/lib/cancel/isCancel.js");
var defaults = __webpack_require__(/*! ../defaults */ "./node_modules/axios/lib/defaults.js");
var Cancel = __webpack_require__(/*! ../cancel/Cancel */ "./node_modules/axios/lib/cancel/Cancel.js");

/**
 * Throws a `Cancel` if cancellation has been requested.
 */
function throwIfCancellationRequested(config) {
  if (config.cancelToken) {
    config.cancelToken.throwIfRequested();
  }

  if (config.signal && config.signal.aborted) {
    throw new Cancel('canceled');
  }
}

/**
 * Dispatch a request to the server using the configured adapter.
 *
 * @param {object} config The config that is to be used for the request
 * @returns {Promise} The Promise to be fulfilled
 */
module.exports = function dispatchRequest(config) {
  throwIfCancellationRequested(config);

  // Ensure headers exist
  config.headers = config.headers || {};

  // Transform request data
  config.data = transformData.call(
    config,
    config.data,
    config.headers,
    config.transformRequest
  );

  // Flatten headers
  config.headers = utils.merge(
    config.headers.common || {},
    config.headers[config.method] || {},
    config.headers
  );

  utils.forEach(
    ['delete', 'get', 'head', 'post', 'put', 'patch', 'common'],
    function cleanHeaderConfig(method) {
      delete config.headers[method];
    }
  );

  var adapter = config.adapter || defaults.adapter;

  return adapter(config).then(function onAdapterResolution(response) {
    throwIfCancellationRequested(config);

    // Transform response data
    response.data = transformData.call(
      config,
      response.data,
      response.headers,
      config.transformResponse
    );

    return response;
  }, function onAdapterRejection(reason) {
    if (!isCancel(reason)) {
      throwIfCancellationRequested(config);

      // Transform response data
      if (reason && reason.response) {
        reason.response.data = transformData.call(
          config,
          reason.response.data,
          reason.response.headers,
          config.transformResponse
        );
      }
    }

    return Promise.reject(reason);
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/core/enhanceError.js":
/*!*****************************************************!*\
  !*** ./node_modules/axios/lib/core/enhanceError.js ***!
  \*****************************************************/
/***/ ((module) => {

"use strict";


/**
 * Update an Error with the specified config, error code, and response.
 *
 * @param {Error} error The error to update.
 * @param {Object} config The config.
 * @param {string} [code] The error code (for example, 'ECONNABORTED').
 * @param {Object} [request] The request.
 * @param {Object} [response] The response.
 * @returns {Error} The error.
 */
module.exports = function enhanceError(error, config, code, request, response) {
  error.config = config;
  if (code) {
    error.code = code;
  }

  error.request = request;
  error.response = response;
  error.isAxiosError = true;

  error.toJSON = function toJSON() {
    return {
      // Standard
      message: this.message,
      name: this.name,
      // Microsoft
      description: this.description,
      number: this.number,
      // Mozilla
      fileName: this.fileName,
      lineNumber: this.lineNumber,
      columnNumber: this.columnNumber,
      stack: this.stack,
      // Axios
      config: this.config,
      code: this.code,
      status: this.response && this.response.status ? this.response.status : null
    };
  };
  return error;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/mergeConfig.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/core/mergeConfig.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");

/**
 * Config-specific merge-function which creates a new config-object
 * by merging two configuration objects together.
 *
 * @param {Object} config1
 * @param {Object} config2
 * @returns {Object} New object resulting from merging config2 to config1
 */
module.exports = function mergeConfig(config1, config2) {
  // eslint-disable-next-line no-param-reassign
  config2 = config2 || {};
  var config = {};

  function getMergedValue(target, source) {
    if (utils.isPlainObject(target) && utils.isPlainObject(source)) {
      return utils.merge(target, source);
    } else if (utils.isPlainObject(source)) {
      return utils.merge({}, source);
    } else if (utils.isArray(source)) {
      return source.slice();
    }
    return source;
  }

  // eslint-disable-next-line consistent-return
  function mergeDeepProperties(prop) {
    if (!utils.isUndefined(config2[prop])) {
      return getMergedValue(config1[prop], config2[prop]);
    } else if (!utils.isUndefined(config1[prop])) {
      return getMergedValue(undefined, config1[prop]);
    }
  }

  // eslint-disable-next-line consistent-return
  function valueFromConfig2(prop) {
    if (!utils.isUndefined(config2[prop])) {
      return getMergedValue(undefined, config2[prop]);
    }
  }

  // eslint-disable-next-line consistent-return
  function defaultToConfig2(prop) {
    if (!utils.isUndefined(config2[prop])) {
      return getMergedValue(undefined, config2[prop]);
    } else if (!utils.isUndefined(config1[prop])) {
      return getMergedValue(undefined, config1[prop]);
    }
  }

  // eslint-disable-next-line consistent-return
  function mergeDirectKeys(prop) {
    if (prop in config2) {
      return getMergedValue(config1[prop], config2[prop]);
    } else if (prop in config1) {
      return getMergedValue(undefined, config1[prop]);
    }
  }

  var mergeMap = {
    'url': valueFromConfig2,
    'method': valueFromConfig2,
    'data': valueFromConfig2,
    'baseURL': defaultToConfig2,
    'transformRequest': defaultToConfig2,
    'transformResponse': defaultToConfig2,
    'paramsSerializer': defaultToConfig2,
    'timeout': defaultToConfig2,
    'timeoutMessage': defaultToConfig2,
    'withCredentials': defaultToConfig2,
    'adapter': defaultToConfig2,
    'responseType': defaultToConfig2,
    'xsrfCookieName': defaultToConfig2,
    'xsrfHeaderName': defaultToConfig2,
    'onUploadProgress': defaultToConfig2,
    'onDownloadProgress': defaultToConfig2,
    'decompress': defaultToConfig2,
    'maxContentLength': defaultToConfig2,
    'maxBodyLength': defaultToConfig2,
    'transport': defaultToConfig2,
    'httpAgent': defaultToConfig2,
    'httpsAgent': defaultToConfig2,
    'cancelToken': defaultToConfig2,
    'socketPath': defaultToConfig2,
    'responseEncoding': defaultToConfig2,
    'validateStatus': mergeDirectKeys
  };

  utils.forEach(Object.keys(config1).concat(Object.keys(config2)), function computeConfigValue(prop) {
    var merge = mergeMap[prop] || mergeDeepProperties;
    var configValue = merge(prop);
    (utils.isUndefined(configValue) && merge !== mergeDirectKeys) || (config[prop] = configValue);
  });

  return config;
};


/***/ }),

/***/ "./node_modules/axios/lib/core/settle.js":
/*!***********************************************!*\
  !*** ./node_modules/axios/lib/core/settle.js ***!
  \***********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var createError = __webpack_require__(/*! ./createError */ "./node_modules/axios/lib/core/createError.js");

/**
 * Resolve or reject a Promise based on response status.
 *
 * @param {Function} resolve A function that resolves the promise.
 * @param {Function} reject A function that rejects the promise.
 * @param {object} response The response.
 */
module.exports = function settle(resolve, reject, response) {
  var validateStatus = response.config.validateStatus;
  if (!response.status || !validateStatus || validateStatus(response.status)) {
    resolve(response);
  } else {
    reject(createError(
      'Request failed with status code ' + response.status,
      response.config,
      null,
      response.request,
      response
    ));
  }
};


/***/ }),

/***/ "./node_modules/axios/lib/core/transformData.js":
/*!******************************************************!*\
  !*** ./node_modules/axios/lib/core/transformData.js ***!
  \******************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");
var defaults = __webpack_require__(/*! ./../defaults */ "./node_modules/axios/lib/defaults.js");

/**
 * Transform the data for a request or a response
 *
 * @param {Object|String} data The data to be transformed
 * @param {Array} headers The headers for the request or response
 * @param {Array|Function} fns A single function or Array of functions
 * @returns {*} The resulting transformed data
 */
module.exports = function transformData(data, headers, fns) {
  var context = this || defaults;
  /*eslint no-param-reassign:0*/
  utils.forEach(fns, function transform(fn) {
    data = fn.call(context, data, headers);
  });

  return data;
};


/***/ }),

/***/ "./node_modules/axios/lib/defaults.js":
/*!********************************************!*\
  !*** ./node_modules/axios/lib/defaults.js ***!
  \********************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";
/* provided dependency */ var process = __webpack_require__(/*! process/browser.js */ "./node_modules/process/browser.js");


var utils = __webpack_require__(/*! ./utils */ "./node_modules/axios/lib/utils.js");
var normalizeHeaderName = __webpack_require__(/*! ./helpers/normalizeHeaderName */ "./node_modules/axios/lib/helpers/normalizeHeaderName.js");
var enhanceError = __webpack_require__(/*! ./core/enhanceError */ "./node_modules/axios/lib/core/enhanceError.js");

var DEFAULT_CONTENT_TYPE = {
  'Content-Type': 'application/x-www-form-urlencoded'
};

function setContentTypeIfUnset(headers, value) {
  if (!utils.isUndefined(headers) && utils.isUndefined(headers['Content-Type'])) {
    headers['Content-Type'] = value;
  }
}

function getDefaultAdapter() {
  var adapter;
  if (typeof XMLHttpRequest !== 'undefined') {
    // For browsers use XHR adapter
    adapter = __webpack_require__(/*! ./adapters/xhr */ "./node_modules/axios/lib/adapters/xhr.js");
  } else if (typeof process !== 'undefined' && Object.prototype.toString.call(process) === '[object process]') {
    // For node use HTTP adapter
    adapter = __webpack_require__(/*! ./adapters/http */ "./node_modules/axios/lib/adapters/xhr.js");
  }
  return adapter;
}

function stringifySafely(rawValue, parser, encoder) {
  if (utils.isString(rawValue)) {
    try {
      (parser || JSON.parse)(rawValue);
      return utils.trim(rawValue);
    } catch (e) {
      if (e.name !== 'SyntaxError') {
        throw e;
      }
    }
  }

  return (encoder || JSON.stringify)(rawValue);
}

var defaults = {

  transitional: {
    silentJSONParsing: true,
    forcedJSONParsing: true,
    clarifyTimeoutError: false
  },

  adapter: getDefaultAdapter(),

  transformRequest: [function transformRequest(data, headers) {
    normalizeHeaderName(headers, 'Accept');
    normalizeHeaderName(headers, 'Content-Type');

    if (utils.isFormData(data) ||
      utils.isArrayBuffer(data) ||
      utils.isBuffer(data) ||
      utils.isStream(data) ||
      utils.isFile(data) ||
      utils.isBlob(data)
    ) {
      return data;
    }
    if (utils.isArrayBufferView(data)) {
      return data.buffer;
    }
    if (utils.isURLSearchParams(data)) {
      setContentTypeIfUnset(headers, 'application/x-www-form-urlencoded;charset=utf-8');
      return data.toString();
    }
    if (utils.isObject(data) || (headers && headers['Content-Type'] === 'application/json')) {
      setContentTypeIfUnset(headers, 'application/json');
      return stringifySafely(data);
    }
    return data;
  }],

  transformResponse: [function transformResponse(data) {
    var transitional = this.transitional || defaults.transitional;
    var silentJSONParsing = transitional && transitional.silentJSONParsing;
    var forcedJSONParsing = transitional && transitional.forcedJSONParsing;
    var strictJSONParsing = !silentJSONParsing && this.responseType === 'json';

    if (strictJSONParsing || (forcedJSONParsing && utils.isString(data) && data.length)) {
      try {
        return JSON.parse(data);
      } catch (e) {
        if (strictJSONParsing) {
          if (e.name === 'SyntaxError') {
            throw enhanceError(e, this, 'E_JSON_PARSE');
          }
          throw e;
        }
      }
    }

    return data;
  }],

  /**
   * A timeout in milliseconds to abort a request. If set to 0 (default) a
   * timeout is not created.
   */
  timeout: 0,

  xsrfCookieName: 'XSRF-TOKEN',
  xsrfHeaderName: 'X-XSRF-TOKEN',

  maxContentLength: -1,
  maxBodyLength: -1,

  validateStatus: function validateStatus(status) {
    return status >= 200 && status < 300;
  },

  headers: {
    common: {
      'Accept': 'application/json, text/plain, */*'
    }
  }
};

utils.forEach(['delete', 'get', 'head'], function forEachMethodNoData(method) {
  defaults.headers[method] = {};
});

utils.forEach(['post', 'put', 'patch'], function forEachMethodWithData(method) {
  defaults.headers[method] = utils.merge(DEFAULT_CONTENT_TYPE);
});

module.exports = defaults;


/***/ }),

/***/ "./node_modules/axios/lib/env/data.js":
/*!********************************************!*\
  !*** ./node_modules/axios/lib/env/data.js ***!
  \********************************************/
/***/ ((module) => {

module.exports = {
  "version": "0.25.0"
};

/***/ }),

/***/ "./node_modules/axios/lib/helpers/bind.js":
/*!************************************************!*\
  !*** ./node_modules/axios/lib/helpers/bind.js ***!
  \************************************************/
/***/ ((module) => {

"use strict";


module.exports = function bind(fn, thisArg) {
  return function wrap() {
    var args = new Array(arguments.length);
    for (var i = 0; i < args.length; i++) {
      args[i] = arguments[i];
    }
    return fn.apply(thisArg, args);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/buildURL.js":
/*!****************************************************!*\
  !*** ./node_modules/axios/lib/helpers/buildURL.js ***!
  \****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

function encode(val) {
  return encodeURIComponent(val).
    replace(/%3A/gi, ':').
    replace(/%24/g, '$').
    replace(/%2C/gi, ',').
    replace(/%20/g, '+').
    replace(/%5B/gi, '[').
    replace(/%5D/gi, ']');
}

/**
 * Build a URL by appending params to the end
 *
 * @param {string} url The base of the url (e.g., http://www.google.com)
 * @param {object} [params] The params to be appended
 * @returns {string} The formatted url
 */
module.exports = function buildURL(url, params, paramsSerializer) {
  /*eslint no-param-reassign:0*/
  if (!params) {
    return url;
  }

  var serializedParams;
  if (paramsSerializer) {
    serializedParams = paramsSerializer(params);
  } else if (utils.isURLSearchParams(params)) {
    serializedParams = params.toString();
  } else {
    var parts = [];

    utils.forEach(params, function serialize(val, key) {
      if (val === null || typeof val === 'undefined') {
        return;
      }

      if (utils.isArray(val)) {
        key = key + '[]';
      } else {
        val = [val];
      }

      utils.forEach(val, function parseValue(v) {
        if (utils.isDate(v)) {
          v = v.toISOString();
        } else if (utils.isObject(v)) {
          v = JSON.stringify(v);
        }
        parts.push(encode(key) + '=' + encode(v));
      });
    });

    serializedParams = parts.join('&');
  }

  if (serializedParams) {
    var hashmarkIndex = url.indexOf('#');
    if (hashmarkIndex !== -1) {
      url = url.slice(0, hashmarkIndex);
    }

    url += (url.indexOf('?') === -1 ? '?' : '&') + serializedParams;
  }

  return url;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/combineURLs.js":
/*!*******************************************************!*\
  !*** ./node_modules/axios/lib/helpers/combineURLs.js ***!
  \*******************************************************/
/***/ ((module) => {

"use strict";


/**
 * Creates a new URL by combining the specified URLs
 *
 * @param {string} baseURL The base URL
 * @param {string} relativeURL The relative URL
 * @returns {string} The combined URL
 */
module.exports = function combineURLs(baseURL, relativeURL) {
  return relativeURL
    ? baseURL.replace(/\/+$/, '') + '/' + relativeURL.replace(/^\/+/, '')
    : baseURL;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/cookies.js":
/*!***************************************************!*\
  !*** ./node_modules/axios/lib/helpers/cookies.js ***!
  \***************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs support document.cookie
    (function standardBrowserEnv() {
      return {
        write: function write(name, value, expires, path, domain, secure) {
          var cookie = [];
          cookie.push(name + '=' + encodeURIComponent(value));

          if (utils.isNumber(expires)) {
            cookie.push('expires=' + new Date(expires).toGMTString());
          }

          if (utils.isString(path)) {
            cookie.push('path=' + path);
          }

          if (utils.isString(domain)) {
            cookie.push('domain=' + domain);
          }

          if (secure === true) {
            cookie.push('secure');
          }

          document.cookie = cookie.join('; ');
        },

        read: function read(name) {
          var match = document.cookie.match(new RegExp('(^|;\\s*)(' + name + ')=([^;]*)'));
          return (match ? decodeURIComponent(match[3]) : null);
        },

        remove: function remove(name) {
          this.write(name, '', Date.now() - 86400000);
        }
      };
    })() :

  // Non standard browser env (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return {
        write: function write() {},
        read: function read() { return null; },
        remove: function remove() {}
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAbsoluteURL.js":
/*!*********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAbsoluteURL.js ***!
  \*********************************************************/
/***/ ((module) => {

"use strict";


/**
 * Determines whether the specified URL is absolute
 *
 * @param {string} url The URL to test
 * @returns {boolean} True if the specified URL is absolute, otherwise false
 */
module.exports = function isAbsoluteURL(url) {
  // A URL is considered absolute if it begins with "<scheme>://" or "//" (protocol-relative URL).
  // RFC 3986 defines scheme name as a sequence of characters beginning with a letter and followed
  // by any combination of letters, digits, plus, period, or hyphen.
  return /^([a-z][a-z\d+\-.]*:)?\/\//i.test(url);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isAxiosError.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isAxiosError.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

/**
 * Determines whether the payload is an error thrown by Axios
 *
 * @param {*} payload The value to test
 * @returns {boolean} True if the payload is an error thrown by Axios, otherwise false
 */
module.exports = function isAxiosError(payload) {
  return utils.isObject(payload) && (payload.isAxiosError === true);
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/isURLSameOrigin.js":
/*!***********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/isURLSameOrigin.js ***!
  \***********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

module.exports = (
  utils.isStandardBrowserEnv() ?

  // Standard browser envs have full support of the APIs needed to test
  // whether the request URL is of the same origin as current location.
    (function standardBrowserEnv() {
      var msie = /(msie|trident)/i.test(navigator.userAgent);
      var urlParsingNode = document.createElement('a');
      var originURL;

      /**
    * Parse a URL to discover it's components
    *
    * @param {String} url The URL to be parsed
    * @returns {Object}
    */
      function resolveURL(url) {
        var href = url;

        if (msie) {
        // IE needs attribute set twice to normalize properties
          urlParsingNode.setAttribute('href', href);
          href = urlParsingNode.href;
        }

        urlParsingNode.setAttribute('href', href);

        // urlParsingNode provides the UrlUtils interface - http://url.spec.whatwg.org/#urlutils
        return {
          href: urlParsingNode.href,
          protocol: urlParsingNode.protocol ? urlParsingNode.protocol.replace(/:$/, '') : '',
          host: urlParsingNode.host,
          search: urlParsingNode.search ? urlParsingNode.search.replace(/^\?/, '') : '',
          hash: urlParsingNode.hash ? urlParsingNode.hash.replace(/^#/, '') : '',
          hostname: urlParsingNode.hostname,
          port: urlParsingNode.port,
          pathname: (urlParsingNode.pathname.charAt(0) === '/') ?
            urlParsingNode.pathname :
            '/' + urlParsingNode.pathname
        };
      }

      originURL = resolveURL(window.location.href);

      /**
    * Determine if a URL shares the same origin as the current location
    *
    * @param {String} requestURL The URL to test
    * @returns {boolean} True if URL shares the same origin, otherwise false
    */
      return function isURLSameOrigin(requestURL) {
        var parsed = (utils.isString(requestURL)) ? resolveURL(requestURL) : requestURL;
        return (parsed.protocol === originURL.protocol &&
            parsed.host === originURL.host);
      };
    })() :

  // Non standard browser envs (web workers, react-native) lack needed support.
    (function nonStandardBrowserEnv() {
      return function isURLSameOrigin() {
        return true;
      };
    })()
);


/***/ }),

/***/ "./node_modules/axios/lib/helpers/normalizeHeaderName.js":
/*!***************************************************************!*\
  !*** ./node_modules/axios/lib/helpers/normalizeHeaderName.js ***!
  \***************************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ../utils */ "./node_modules/axios/lib/utils.js");

module.exports = function normalizeHeaderName(headers, normalizedName) {
  utils.forEach(headers, function processHeader(value, name) {
    if (name !== normalizedName && name.toUpperCase() === normalizedName.toUpperCase()) {
      headers[normalizedName] = value;
      delete headers[name];
    }
  });
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/parseHeaders.js":
/*!********************************************************!*\
  !*** ./node_modules/axios/lib/helpers/parseHeaders.js ***!
  \********************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var utils = __webpack_require__(/*! ./../utils */ "./node_modules/axios/lib/utils.js");

// Headers whose duplicates are ignored by node
// c.f. https://nodejs.org/api/http.html#http_message_headers
var ignoreDuplicateOf = [
  'age', 'authorization', 'content-length', 'content-type', 'etag',
  'expires', 'from', 'host', 'if-modified-since', 'if-unmodified-since',
  'last-modified', 'location', 'max-forwards', 'proxy-authorization',
  'referer', 'retry-after', 'user-agent'
];

/**
 * Parse headers into an object
 *
 * ```
 * Date: Wed, 27 Aug 2014 08:58:49 GMT
 * Content-Type: application/json
 * Connection: keep-alive
 * Transfer-Encoding: chunked
 * ```
 *
 * @param {String} headers Headers needing to be parsed
 * @returns {Object} Headers parsed into an object
 */
module.exports = function parseHeaders(headers) {
  var parsed = {};
  var key;
  var val;
  var i;

  if (!headers) { return parsed; }

  utils.forEach(headers.split('\n'), function parser(line) {
    i = line.indexOf(':');
    key = utils.trim(line.substr(0, i)).toLowerCase();
    val = utils.trim(line.substr(i + 1));

    if (key) {
      if (parsed[key] && ignoreDuplicateOf.indexOf(key) >= 0) {
        return;
      }
      if (key === 'set-cookie') {
        parsed[key] = (parsed[key] ? parsed[key] : []).concat([val]);
      } else {
        parsed[key] = parsed[key] ? parsed[key] + ', ' + val : val;
      }
    }
  });

  return parsed;
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/spread.js":
/*!**************************************************!*\
  !*** ./node_modules/axios/lib/helpers/spread.js ***!
  \**************************************************/
/***/ ((module) => {

"use strict";


/**
 * Syntactic sugar for invoking a function and expanding an array for arguments.
 *
 * Common use case would be to use `Function.prototype.apply`.
 *
 *  ```js
 *  function f(x, y, z) {}
 *  var args = [1, 2, 3];
 *  f.apply(null, args);
 *  ```
 *
 * With `spread` this example can be re-written.
 *
 *  ```js
 *  spread(function(x, y, z) {})([1, 2, 3]);
 *  ```
 *
 * @param {Function} callback
 * @returns {Function}
 */
module.exports = function spread(callback) {
  return function wrap(arr) {
    return callback.apply(null, arr);
  };
};


/***/ }),

/***/ "./node_modules/axios/lib/helpers/validator.js":
/*!*****************************************************!*\
  !*** ./node_modules/axios/lib/helpers/validator.js ***!
  \*****************************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var VERSION = (__webpack_require__(/*! ../env/data */ "./node_modules/axios/lib/env/data.js").version);

var validators = {};

// eslint-disable-next-line func-names
['object', 'boolean', 'number', 'function', 'string', 'symbol'].forEach(function(type, i) {
  validators[type] = function validator(thing) {
    return typeof thing === type || 'a' + (i < 1 ? 'n ' : ' ') + type;
  };
});

var deprecatedWarnings = {};

/**
 * Transitional option validator
 * @param {function|boolean?} validator - set to false if the transitional option has been removed
 * @param {string?} version - deprecated version / removed since version
 * @param {string?} message - some message with additional info
 * @returns {function}
 */
validators.transitional = function transitional(validator, version, message) {
  function formatMessage(opt, desc) {
    return '[Axios v' + VERSION + '] Transitional option \'' + opt + '\'' + desc + (message ? '. ' + message : '');
  }

  // eslint-disable-next-line func-names
  return function(value, opt, opts) {
    if (validator === false) {
      throw new Error(formatMessage(opt, ' has been removed' + (version ? ' in ' + version : '')));
    }

    if (version && !deprecatedWarnings[opt]) {
      deprecatedWarnings[opt] = true;
      // eslint-disable-next-line no-console
      console.warn(
        formatMessage(
          opt,
          ' has been deprecated since v' + version + ' and will be removed in the near future'
        )
      );
    }

    return validator ? validator(value, opt, opts) : true;
  };
};

/**
 * Assert object's properties type
 * @param {object} options
 * @param {object} schema
 * @param {boolean?} allowUnknown
 */

function assertOptions(options, schema, allowUnknown) {
  if (typeof options !== 'object') {
    throw new TypeError('options must be an object');
  }
  var keys = Object.keys(options);
  var i = keys.length;
  while (i-- > 0) {
    var opt = keys[i];
    var validator = schema[opt];
    if (validator) {
      var value = options[opt];
      var result = value === undefined || validator(value, opt, options);
      if (result !== true) {
        throw new TypeError('option ' + opt + ' must be ' + result);
      }
      continue;
    }
    if (allowUnknown !== true) {
      throw Error('Unknown option ' + opt);
    }
  }
}

module.exports = {
  assertOptions: assertOptions,
  validators: validators
};


/***/ }),

/***/ "./node_modules/axios/lib/utils.js":
/*!*****************************************!*\
  !*** ./node_modules/axios/lib/utils.js ***!
  \*****************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

"use strict";


var bind = __webpack_require__(/*! ./helpers/bind */ "./node_modules/axios/lib/helpers/bind.js");

// utils is a library of generic helper functions non-specific to axios

var toString = Object.prototype.toString;

/**
 * Determine if a value is an Array
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Array, otherwise false
 */
function isArray(val) {
  return Array.isArray(val);
}

/**
 * Determine if a value is undefined
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if the value is undefined, otherwise false
 */
function isUndefined(val) {
  return typeof val === 'undefined';
}

/**
 * Determine if a value is a Buffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Buffer, otherwise false
 */
function isBuffer(val) {
  return val !== null && !isUndefined(val) && val.constructor !== null && !isUndefined(val.constructor)
    && typeof val.constructor.isBuffer === 'function' && val.constructor.isBuffer(val);
}

/**
 * Determine if a value is an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an ArrayBuffer, otherwise false
 */
function isArrayBuffer(val) {
  return toString.call(val) === '[object ArrayBuffer]';
}

/**
 * Determine if a value is a FormData
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an FormData, otherwise false
 */
function isFormData(val) {
  return toString.call(val) === '[object FormData]';
}

/**
 * Determine if a value is a view on an ArrayBuffer
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a view on an ArrayBuffer, otherwise false
 */
function isArrayBufferView(val) {
  var result;
  if ((typeof ArrayBuffer !== 'undefined') && (ArrayBuffer.isView)) {
    result = ArrayBuffer.isView(val);
  } else {
    result = (val) && (val.buffer) && (isArrayBuffer(val.buffer));
  }
  return result;
}

/**
 * Determine if a value is a String
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a String, otherwise false
 */
function isString(val) {
  return typeof val === 'string';
}

/**
 * Determine if a value is a Number
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Number, otherwise false
 */
function isNumber(val) {
  return typeof val === 'number';
}

/**
 * Determine if a value is an Object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is an Object, otherwise false
 */
function isObject(val) {
  return val !== null && typeof val === 'object';
}

/**
 * Determine if a value is a plain Object
 *
 * @param {Object} val The value to test
 * @return {boolean} True if value is a plain Object, otherwise false
 */
function isPlainObject(val) {
  if (toString.call(val) !== '[object Object]') {
    return false;
  }

  var prototype = Object.getPrototypeOf(val);
  return prototype === null || prototype === Object.prototype;
}

/**
 * Determine if a value is a Date
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Date, otherwise false
 */
function isDate(val) {
  return toString.call(val) === '[object Date]';
}

/**
 * Determine if a value is a File
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a File, otherwise false
 */
function isFile(val) {
  return toString.call(val) === '[object File]';
}

/**
 * Determine if a value is a Blob
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Blob, otherwise false
 */
function isBlob(val) {
  return toString.call(val) === '[object Blob]';
}

/**
 * Determine if a value is a Function
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Function, otherwise false
 */
function isFunction(val) {
  return toString.call(val) === '[object Function]';
}

/**
 * Determine if a value is a Stream
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a Stream, otherwise false
 */
function isStream(val) {
  return isObject(val) && isFunction(val.pipe);
}

/**
 * Determine if a value is a URLSearchParams object
 *
 * @param {Object} val The value to test
 * @returns {boolean} True if value is a URLSearchParams object, otherwise false
 */
function isURLSearchParams(val) {
  return toString.call(val) === '[object URLSearchParams]';
}

/**
 * Trim excess whitespace off the beginning and end of a string
 *
 * @param {String} str The String to trim
 * @returns {String} The String freed of excess whitespace
 */
function trim(str) {
  return str.trim ? str.trim() : str.replace(/^\s+|\s+$/g, '');
}

/**
 * Determine if we're running in a standard browser environment
 *
 * This allows axios to run in a web worker, and react-native.
 * Both environments support XMLHttpRequest, but not fully standard globals.
 *
 * web workers:
 *  typeof window -> undefined
 *  typeof document -> undefined
 *
 * react-native:
 *  navigator.product -> 'ReactNative'
 * nativescript
 *  navigator.product -> 'NativeScript' or 'NS'
 */
function isStandardBrowserEnv() {
  if (typeof navigator !== 'undefined' && (navigator.product === 'ReactNative' ||
                                           navigator.product === 'NativeScript' ||
                                           navigator.product === 'NS')) {
    return false;
  }
  return (
    typeof window !== 'undefined' &&
    typeof document !== 'undefined'
  );
}

/**
 * Iterate over an Array or an Object invoking a function for each item.
 *
 * If `obj` is an Array callback will be called passing
 * the value, index, and complete array for each item.
 *
 * If 'obj' is an Object callback will be called passing
 * the value, key, and complete object for each property.
 *
 * @param {Object|Array} obj The object to iterate
 * @param {Function} fn The callback to invoke for each item
 */
function forEach(obj, fn) {
  // Don't bother if no value provided
  if (obj === null || typeof obj === 'undefined') {
    return;
  }

  // Force an array if not already something iterable
  if (typeof obj !== 'object') {
    /*eslint no-param-reassign:0*/
    obj = [obj];
  }

  if (isArray(obj)) {
    // Iterate over array values
    for (var i = 0, l = obj.length; i < l; i++) {
      fn.call(null, obj[i], i, obj);
    }
  } else {
    // Iterate over object keys
    for (var key in obj) {
      if (Object.prototype.hasOwnProperty.call(obj, key)) {
        fn.call(null, obj[key], key, obj);
      }
    }
  }
}

/**
 * Accepts varargs expecting each argument to be an object, then
 * immutably merges the properties of each object and returns result.
 *
 * When multiple objects contain the same key the later object in
 * the arguments list will take precedence.
 *
 * Example:
 *
 * ```js
 * var result = merge({foo: 123}, {foo: 456});
 * console.log(result.foo); // outputs 456
 * ```
 *
 * @param {Object} obj1 Object to merge
 * @returns {Object} Result of all merge properties
 */
function merge(/* obj1, obj2, obj3, ... */) {
  var result = {};
  function assignValue(val, key) {
    if (isPlainObject(result[key]) && isPlainObject(val)) {
      result[key] = merge(result[key], val);
    } else if (isPlainObject(val)) {
      result[key] = merge({}, val);
    } else if (isArray(val)) {
      result[key] = val.slice();
    } else {
      result[key] = val;
    }
  }

  for (var i = 0, l = arguments.length; i < l; i++) {
    forEach(arguments[i], assignValue);
  }
  return result;
}

/**
 * Extends object a by mutably adding to it the properties of object b.
 *
 * @param {Object} a The object to be extended
 * @param {Object} b The object to copy properties from
 * @param {Object} thisArg The object to bind function to
 * @return {Object} The resulting value of object a
 */
function extend(a, b, thisArg) {
  forEach(b, function assignValue(val, key) {
    if (thisArg && typeof val === 'function') {
      a[key] = bind(val, thisArg);
    } else {
      a[key] = val;
    }
  });
  return a;
}

/**
 * Remove byte order marker. This catches EF BB BF (the UTF-8 BOM)
 *
 * @param {string} content with BOM
 * @return {string} content value without BOM
 */
function stripBOM(content) {
  if (content.charCodeAt(0) === 0xFEFF) {
    content = content.slice(1);
  }
  return content;
}

module.exports = {
  isArray: isArray,
  isArrayBuffer: isArrayBuffer,
  isBuffer: isBuffer,
  isFormData: isFormData,
  isArrayBufferView: isArrayBufferView,
  isString: isString,
  isNumber: isNumber,
  isObject: isObject,
  isPlainObject: isPlainObject,
  isUndefined: isUndefined,
  isDate: isDate,
  isFile: isFile,
  isBlob: isBlob,
  isFunction: isFunction,
  isStream: isStream,
  isURLSearchParams: isURLSearchParams,
  isStandardBrowserEnv: isStandardBrowserEnv,
  forEach: forEach,
  merge: merge,
  extend: extend,
  trim: trim,
  stripBOM: stripBOM
};


/***/ }),

/***/ "./resources/js/core/app.js":
/*!**********************************!*\
  !*** ./resources/js/core/app.js ***!
  \**********************************/
/***/ ((__unused_webpack_module, __unused_webpack_exports, __webpack_require__) => {

/*=========================================================================================
  File Name: app.js
  Description: Template related app JS.
  ----------------------------------------------------------------------------------------
  Item Name: Vuexy  - Vuejs, HTML & Laravel Admin Dashboard Template
  Author: Pixinvent
  Author URL: hhttp://www.themeforest.net/user/pixinvent
==========================================================================================*/
window.axios = __webpack_require__(/*! axios */ "./node_modules/axios/index.js");
window.colors = {
  solid: {
    primary: "#7367F0",
    secondary: "#82868b",
    success: "#28C76F",
    info: "#00cfe8",
    warning: "#FF9F43",
    danger: "#EA5455",
    dark: "#4b4b4b",
    black: "#000",
    white: "#fff",
    body: "#f8f8f8"
  },
  light: {
    primary: "#7367F01a",
    secondary: "#82868b1a",
    success: "#28C76F1a",
    info: "#00cfe81a",
    warning: "#FF9F431a",
    danger: "#EA54551a",
    dark: "#4b4b4b1a"
  }
};
(function (window, document, $) {
  "use strict";

  var $html = $("html");
  var $body = $("body");
  var $textcolor = "#4e5154";
  var assetPath = "../../../app-assets/";
  if ($("body").attr("data-framework") === "laravel") {
    assetPath = $("body").attr("data-asset-path");
  }

  // to remove sm control classes from datatables
  if ($.fn.dataTable) {
    $.extend($.fn.dataTable.ext.classes, {
      sFilterInput: "form-control",
      sLengthSelect: "form-select"
    });
  }
  $(window).on("load", function () {
    var rtl;
    var compactMenu = false;
    if ($body.hasClass("menu-collapsed") || localStorage.getItem("menuCollapsed") === "true") {
      compactMenu = true;
    }
    if ($("html").data("textdirection") == "rtl") {
      rtl = true;
    }
    setTimeout(function () {
      $html.removeClass("loading").addClass("loaded");
    }, 1200);
    $.app.menu.init(compactMenu);

    // Navigation configurations
    var config = {
      speed: 300 // set speed to expand / collapse menu
    };

    if ($.app.nav.initialized === false) {
      $.app.nav.init(config);
    }
    Unison.on("change", function (bp) {
      $.app.menu.change(compactMenu);
    });

    // Tooltip Initialization
    // $('[data-bs-toggle="tooltip"]').tooltip({
    //   container: 'body'
    // });
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
      return new bootstrap.Tooltip(tooltipTriggerEl, {
        html: true
      });
    });

    // Collapsible Card
    $('a[data-action="collapse"]').on("click", function (e) {
      e.preventDefault();
      $(this).closest(".card").children(".card-content").collapse("toggle");
      $(this).closest(".card").find('[data-action="collapse"]').toggleClass("rotate");
    });

    // Cart dropdown touchspin
    if ($(".touchspin-cart").length > 0) {
      $(".touchspin-cart").TouchSpin({
        buttondown_class: "btn btn-primary",
        buttonup_class: "btn btn-primary",
        buttondown_txt: feather.icons["minus"].toSvg(),
        buttonup_txt: feather.icons["plus"].toSvg()
      });
    }

    // Do not close cart or notification dropdown on click of the items
    $(".dropdown-notification .dropdown-menu, .dropdown-cart .dropdown-menu").on("click", function (e) {
      e.stopPropagation();
    });

    //  Notifications & messages scrollable
    $(".scrollable-container").each(function () {
      var scrollable_container = new PerfectScrollbar($(this)[0], {
        wheelPropagation: false
      });
    });

    // Reload Card
    $('a[data-action="reload"]').on("click", function () {
      var block_ele = $(this).closest(".card");
      var reloadActionOverlay;
      if ($html.hasClass("dark-layout")) {
        var reloadActionOverlay = "#10163a";
      } else {
        var reloadActionOverlay = "#fff";
      }
      // Block Element
      block_ele.block({
        message: feather.icons["refresh-cw"].toSvg({
          "class": "font-medium-1 spinner text-primary"
        }),
        timeout: 2000,
        //unblock after 2 seconds
        overlayCSS: {
          backgroundColor: reloadActionOverlay,
          cursor: "wait"
        },
        css: {
          border: 0,
          padding: 0,
          backgroundColor: "none"
        }
      });
    });

    // Close Card
    $('a[data-action="close"]').on("click", function () {
      $(this).closest(".card").removeClass().slideUp("fast");
    });
    $('.card .heading-elements a[data-action="collapse"]').on("click", function () {
      var $this = $(this),
        card = $this.closest(".card");
      var cardHeight;
      if (parseInt(card[0].style.height, 10) > 0) {
        cardHeight = card.css("height");
        card.css("height", "").attr("data-height", cardHeight);
      } else {
        if (card.data("height")) {
          cardHeight = card.data("height");
          card.css("height", cardHeight).attr("data-height", "");
        }
      }
    });

    // Add disabled class to input group when input is disabled
    $("input:disabled, textarea:disabled").closest(".input-group").addClass("disabled");

    // Add sidebar group active class to active menu
    $(".main-menu-content").find("li.active").parents("li").addClass("sidebar-group-active");

    // Add open class to parent list item if subitem is active except compact menu
    var menuType = $body.data("menu");
    if (menuType != "horizontal-menu" && compactMenu === false) {
      $(".main-menu-content").find("li.active").parents("li").addClass("open");
    }
    if (menuType == "horizontal-menu") {
      $(".main-menu-content").find("li.active").parents("li:not(.nav-item)").addClass("open");
      $(".main-menu-content").find("li.active").closest("li.nav-item").addClass("sidebar-group-active open");
      // $(".main-menu-content")
      //   .find("li.active")
      //   .parents("li")
      //   .addClass("active");
    }

    //  Dynamic height for the chartjs div for the chart animations to work
    var chartjsDiv = $(".chartjs"),
      canvasHeight = chartjsDiv.children("canvas").attr("height"),
      mainMenu = $(".main-menu");
    chartjsDiv.css("height", canvasHeight);
    if ($body.hasClass("boxed-layout")) {
      if ($body.hasClass("vertical-overlay-menu")) {
        var menuWidth = mainMenu.width();
        var contentPosition = $(".app-content").position().left;
        var menuPositionAdjust = contentPosition - menuWidth;
        if ($body.hasClass("menu-flipped")) {
          mainMenu.css("right", menuPositionAdjust + "px");
        } else {
          mainMenu.css("left", menuPositionAdjust + "px");
        }
      }
    }

    /* Text Area Counter Set Start */

    $(".char-textarea").on("keyup", function (event) {
      checkTextAreaMaxLength(this, event);
      // to later change text color in dark layout
      $(this).addClass("active");
    });

    /*
    Checks the MaxLength of the Textarea
    -----------------------------------------------------
    @prerequisite:  textBox = textarea dom element
        e = textarea event
                length = Max length of characters
    */
    function checkTextAreaMaxLength(textBox, e) {
      var maxLength = parseInt($(textBox).data("length")),
        counterValue = $(".textarea-counter-value"),
        charTextarea = $(".char-textarea");
      if (!checkSpecialKeys(e)) {
        if (textBox.value.length < maxLength - 1) textBox.value = textBox.value.substring(0, maxLength);
      }
      $(".char-count").html(textBox.value.length);
      if (textBox.value.length > maxLength) {
        counterValue.css("background-color", window.colors.solid.danger);
        charTextarea.css("color", window.colors.solid.danger);
        // to change text color after limit is maxedout out
        charTextarea.addClass("max-limit");
      } else {
        counterValue.css("background-color", window.colors.solid.primary);
        charTextarea.css("color", $textcolor);
        charTextarea.removeClass("max-limit");
      }
      return true;
    }
    /*
    Checks if the keyCode pressed is inside special chars
    -------------------------------------------------------
    @prerequisite:  e = e.keyCode object for the key pressed
    */
    function checkSpecialKeys(e) {
      if (e.keyCode != 8 && e.keyCode != 46 && e.keyCode != 37 && e.keyCode != 38 && e.keyCode != 39 && e.keyCode != 40) return false;else return true;
    }
    $(".content-overlay").on("click", function () {
      $(".search-list").removeClass("show");
      var searchInput = $(".search-input-close").closest(".search-input");
      if (searchInput.hasClass("open")) {
        searchInput.removeClass("open");
        searchInputInputfield.val("");
        searchInputInputfield.blur();
        searchList.removeClass("show");
      }
      $(".app-content").removeClass("show-overlay");
      $(".bookmark-wrapper .bookmark-input").removeClass("show");
    });

    // To show shadow in main menu when menu scrolls
    var container = document.getElementsByClassName("main-menu-content");
    if (container.length > 0) {
      container[0].addEventListener("ps-scroll-y", function () {
        if ($(this).find(".ps__thumb-y").position().top > 0) {
          $(".shadow-bottom").css("display", "block");
        } else {
          $(".shadow-bottom").css("display", "none");
        }
      });
    }
  });

  // Hide overlay menu on content overlay click on small screens
  $(document).on("click", ".sidenav-overlay", function (e) {
    // Hide menu
    $.app.menu.hide();
    return false;
  });

  // Execute below code only if we find hammer js for touch swipe feature on small screen
  if (typeof Hammer !== "undefined") {
    var rtl;
    if ($("html").data("textdirection") == "rtl") {
      rtl = true;
    }

    // Swipe menu gesture
    var swipeInElement = document.querySelector(".drag-target"),
      swipeInAction = "panright",
      swipeOutAction = "panleft";
    if (rtl === true) {
      swipeInAction = "panleft";
      swipeOutAction = "panright";
    }
    if ($(swipeInElement).length > 0) {
      var swipeInMenu = new Hammer(swipeInElement);
      swipeInMenu.on(swipeInAction, function (ev) {
        if ($body.hasClass("vertical-overlay-menu")) {
          $.app.menu.open();
          return false;
        }
      });
    }

    // menu swipe out gesture
    setTimeout(function () {
      var swipeOutElement = document.querySelector(".main-menu");
      var swipeOutMenu;
      if ($(swipeOutElement).length > 0) {
        swipeOutMenu = new Hammer(swipeOutElement);
        swipeOutMenu.get("pan").set({
          direction: Hammer.DIRECTION_ALL,
          threshold: 250
        });
        swipeOutMenu.on(swipeOutAction, function (ev) {
          if ($body.hasClass("vertical-overlay-menu")) {
            $.app.menu.hide();
            return false;
          }
        });
      }
    }, 300);

    // menu close on overlay tap
    var swipeOutOverlayElement = document.querySelector(".sidenav-overlay");
    if ($(swipeOutOverlayElement).length > 0) {
      var swipeOutOverlayMenu = new Hammer(swipeOutOverlayElement);
      swipeOutOverlayMenu.on("tap", function (ev) {
        if ($body.hasClass("vertical-overlay-menu")) {
          $.app.menu.hide();
          return false;
        }
      });
    }
  }
  $(document).on("click", ".menu-toggle, .modern-nav-toggle", function (e) {
    e.preventDefault();

    // Toggle menu
    $.app.menu.toggle();
    setTimeout(function () {
      $(window).trigger("resize");
    }, 200);
    if ($("#collapse-sidebar-switch").length > 0) {
      setTimeout(function () {
        if ($body.hasClass("menu-expanded") || $body.hasClass("menu-open")) {
          $("#collapse-sidebar-switch").prop("checked", false);
        } else {
          $("#collapse-sidebar-switch").prop("checked", true);
        }
      }, 50);
    }

    // Save menu collapsed status in localstorage
    if ($body.hasClass("menu-expanded") || $body.hasClass("menu-open")) {
      localStorage.setItem("menuCollapsed", false);
    } else {
      localStorage.setItem("menuCollapsed", true);
    }

    // Hides dropdown on click of menu toggle
    // $('[data-bs-toggle="dropdown"]').dropdown('hide');

    return false;
  });

  // Add Children Class
  $(".navigation").find("li").has("ul").addClass("has-sub");
  // Update manual scroller when window is resized
  $(window).resize(function () {
    $.app.menu.manualScroller.updateHeight();
  });
  $("#sidebar-page-navigation").on("click", "a.nav-link", function (e) {
    e.preventDefault();
    e.stopPropagation();
    var $this = $(this),
      href = $this.attr("href");
    var offset = $(href).offset();
    var scrollto = offset.top - 80; // minus fixed header height
    $("html, body").animate({
      scrollTop: scrollto
    }, 0);
    setTimeout(function () {
      $this.parent(".nav-item").siblings(".nav-item").children(".nav-link").removeClass("active");
      $this.addClass("active");
    }, 100);
  });

  // main menu internationalization

  // init i18n and load language file
  if ($body.attr("data-framework") === "laravel") {
    // change language according to data-language of dropdown item
    var language = $("html")[0].lang;
    if (language !== null) {
      // get the selected flag class
      var selectedLang = $(".dropdown-language").find("a[data-language=" + language + "]").text();
      var selectedFlag = $(".dropdown-language").find("a[data-language=" + language + "] .flag-icon").attr("class");
      // set the class in button
      //$('#dropdown-flag .selected-language').text(selectedLang);
      //$('#dropdown-flag .flag-icon').removeClass().addClass(selectedFlag);
    }
  } else {
    i18next.use(window.i18nextXHRBackend).init({
      debug: false,
      fallbackLng: "en",
      backend: {
        loadPath: assetPath + "data/locales/{{lng}}.json"
      },
      returnObjects: true
    }, function (err, t) {
      // resources have been loaded
      jqueryI18next.init(i18next, $);
    });

    // change language according to data-language of dropdown item
    $(".dropdown-language .dropdown-item").on("click", function () {
      var $this = $(this);
      $this.siblings(".selected").removeClass("selected");
      $this.addClass("selected");
      var selectedLang = $this.text();
      var selectedFlag = $this.find(".flag-icon").attr("class");
      $("#dropdown-flag .selected-language").text(selectedLang);
      $("#dropdown-flag .flag-icon").removeClass().addClass(selectedFlag);
      var currentLanguage = $this.data("language");
      i18next.changeLanguage(currentLanguage, function (err, t) {
        $(".main-menu, .horizontal-menu-wrapper").localize();
      });
    });
  }

  /********************* Bookmark & Search ***********************/
  // This variable is used for mouseenter and mouseleave events of search list
  var $filename = $(".search-input input").data("search"),
    bookmarkWrapper = $(".bookmark-wrapper"),
    bookmarkStar = $(".bookmark-wrapper .bookmark-star"),
    bookmarkInput = $(".bookmark-wrapper .bookmark-input"),
    navLinkSearch = $(".nav-link-search"),
    searchInput = $(".search-input"),
    searchInputInputfield = $(".search-input input"),
    searchList = $(".search-input .search-list"),
    appContent = $(".app-content"),
    bookmarkSearchList = $(".bookmark-input .search-list");

  // Bookmark icon click
  bookmarkStar.on("click", function (e) {
    e.stopPropagation();
    bookmarkInput.toggleClass("show");
    bookmarkInput.find("input").val("");
    bookmarkInput.find("input").blur();
    bookmarkInput.find("input").focus();
    bookmarkWrapper.find(".search-list").addClass("show");
    var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
      $arrList = "",
      $activeItemClass = "";
    $("ul.search-list li").remove();
    for (var i = 0; i < arrList.length; i++) {
      if (i === 0) {
        $activeItemClass = "current_item";
      } else {
        $activeItemClass = "";
      }
      var iconName = "",
        className = "";
      if ($(arrList[i].firstChild.firstChild).hasClass("feather")) {
        var classString = arrList[i].firstChild.firstChild.getAttribute("class");
        iconName = classString.split("feather-")[1].split(" ")[0];
        className = classString.split("feather-")[1].split(" ")[1];
      }
      $arrList += '<li class="auto-suggestion ' + $activeItemClass + '">' + '<a class="d-flex align-items-center justify-content-between w-100" href=' + arrList[i].firstChild.href + ">" + '<div class="d-flex justify-content-start align-items-center">' + feather.icons[iconName].toSvg({
        "class": "me-75 " + className
      }) + "<span>" + arrList[i].firstChild.dataset.bsOriginalTitle + "</span>" + "</div>" + feather.icons["star"].toSvg({
        "class": "text-warning bookmark-icon float-end"
      }) + "</a>" + "</li>";
    }
    $("ul.search-list").append($arrList);
  });

  // Navigation Search area Open
  navLinkSearch.on("click", function () {
    var $this = $(this);
    var searchInput = $(this).parent(".nav-search").find(".search-input");
    searchInput.addClass("open");
    searchInputInputfield.focus();
    searchList.find("li").remove();
    bookmarkInput.removeClass("show");
  });

  // Navigation Search area Close
  $(".search-input-close").on("click", function () {
    var $this = $(this),
      searchInput = $(this).closest(".search-input");
    if (searchInput.hasClass("open")) {
      searchInput.removeClass("open");
      searchInputInputfield.val("");
      searchInputInputfield.blur();
      searchList.removeClass("show");
      appContent.removeClass("show-overlay");
    }
  });

  // Filter
  if ($(".search-list-main").length) {
    var searchListMain = new PerfectScrollbar(".search-list-main", {
      wheelPropagation: false
    });
  }
  if ($(".search-list-bookmark").length) {
    var searchListBookmark = new PerfectScrollbar(".search-list-bookmark", {
      wheelPropagation: false
    });
  }
  // update Perfect Scrollbar on hover
  $(".search-list-main").mouseenter(function () {
    searchListMain.update();
  });
  searchInputInputfield.on("keyup", function (e) {
    $(this).closest(".search-list").addClass("show");
    if (e.keyCode !== 38 && e.keyCode !== 40 && e.keyCode !== 13) {
      if (e.keyCode == 27) {
        appContent.removeClass("show-overlay");
        bookmarkInput.find("input").val("");
        bookmarkInput.find("input").blur();
        searchInputInputfield.val("");
        searchInputInputfield.blur();
        searchInput.removeClass("open");
        if (searchInput.hasClass("show")) {
          $(this).removeClass("show");
          searchInput.removeClass("show");
        }
      }

      // Define variables
      var value = $(this).val().toLowerCase(),
        //get values of input on keyup
        activeClass = "",
        bookmark = false,
        liList = $("ul.search-list li"); // get all the list items of the search
      liList.remove();
      // To check if current is bookmark input
      if ($(this).parent().hasClass("bookmark-input")) {
        bookmark = true;
      }

      // If input value is blank
      if (value != "") {
        appContent.addClass("show-overlay");

        // condition for bookmark and search input click
        if (bookmarkInput.focus()) {
          bookmarkSearchList.addClass("show");
        } else {
          searchList.addClass("show");
          bookmarkSearchList.removeClass("show");
        }
        if (bookmark === false) {
          searchList.addClass("show");
          bookmarkSearchList.removeClass("show");
        }
        var $startList = "",
          $otherList = "",
          $htmlList = "",
          $bookmarkhtmlList = "",
          $pageList = '<li class="d-flex align-items-center">' + '<a href="#">' + '<h6 class="section-label mt-75 mb-0">Pages</h6>' + "</a>" + "</li>",
          $activeItemClass = "",
          $bookmarkIcon = "",
          $defaultList = "",
          a = 0;

        // getting json data from file for search results
        $.getJSON(assetPath + "data/" + $filename + ".json", function (data) {
          for (var i = 0; i < data.listItems.length; i++) {
            // if current is bookmark then give class to star icon
            // for laravel
            if ($("body").attr("data-framework") === "laravel") {
              data.listItems[i].url = assetPath + data.listItems[i].url;
            }
            if (bookmark === true) {
              activeClass = ""; // resetting active bookmark class
              var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
                $arrList = "";
              // Loop to check if current seach value match with the bookmarks already there in navbar
              for (var j = 0; j < arrList.length; j++) {
                if (data.listItems[i].name === arrList[j].firstChild.dataset.bsOriginalTitle) {
                  activeClass = " text-warning";
                  break;
                } else {
                  activeClass = "";
                }
              }
              $bookmarkIcon = feather.icons["star"].toSvg({
                "class": "bookmark-icon float-end" + activeClass
              });
            }
            // Search list item start with entered letters and create list
            if (data.listItems[i].name.toLowerCase().indexOf(value) == 0 && a < 5) {
              if (a === 0) {
                $activeItemClass = "current_item";
              } else {
                $activeItemClass = "";
              }
              $startList += '<li class="auto-suggestion ' + $activeItemClass + '">' + '<a class="d-flex align-items-center justify-content-between w-100" href=' + data.listItems[i].url + ">" + '<div class="d-flex justify-content-start align-items-center">' + feather.icons[data.listItems[i].icon].toSvg({
                "class": "me-75 "
              }) + "<span>" + data.listItems[i].name + "</span>" + "</div>" + $bookmarkIcon + "</a>" + "</li>";
              a++;
            }
          }
          for (var i = 0; i < data.listItems.length; i++) {
            if (bookmark === true) {
              activeClass = ""; // resetting active bookmark class
              var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
                $arrList = "";
              // Loop to check if current search value match with the bookmarks already there in navbar
              for (var j = 0; j < arrList.length; j++) {
                if (data.listItems[i].name === arrList[j].firstChild.dataset.bsOriginalTitle) {
                  activeClass = " text-warning";
                } else {
                  activeClass = "";
                }
              }
              $bookmarkIcon = feather.icons["star"].toSvg({
                "class": "bookmark-icon float-end" + activeClass
              });
            }
            // Search list item not start with letters and create list
            if (!(data.listItems[i].name.toLowerCase().indexOf(value) == 0) && data.listItems[i].name.toLowerCase().indexOf(value) > -1 && a < 5) {
              if (a === 0) {
                $activeItemClass = "current_item";
              } else {
                $activeItemClass = "";
              }
              $otherList += '<li class="auto-suggestion ' + $activeItemClass + '">' + '<a class="d-flex align-items-center justify-content-between w-100" href=' + data.listItems[i].url + ">" + '<div class="d-flex justify-content-start align-items-center">' + feather.icons[data.listItems[i].icon].toSvg({
                "class": "me-75 "
              }) + "<span>" + data.listItems[i].name + "</span>" + "</div>" + $bookmarkIcon + "</a>" + "</li>";
              a++;
            }
          }
          $defaultList = $(".main-search-list-defaultlist").html();
          if ($startList == "" && $otherList == "") {
            $otherList = $(".main-search-list-defaultlist-other-list").html();
          }
          // concatinating startlist, otherlist, defalutlist with pagelist
          $htmlList = $pageList.concat($startList, $otherList, $defaultList);
          $("ul.search-list").html($htmlList);
          // concatinating otherlist with startlist
          $bookmarkhtmlList = $startList.concat($otherList);
          $("ul.search-list-bookmark").html($bookmarkhtmlList);
          // Feather Icons
          // if (feather) {
          //   featherSVG();
          // }
        });
      } else {
        if (bookmark === true) {
          var arrList = $("ul.nav.navbar-nav.bookmark-icons li"),
            $arrList = "";
          for (var i = 0; i < arrList.length; i++) {
            if (i === 0) {
              $activeItemClass = "current_item";
            } else {
              $activeItemClass = "";
            }
            var iconName = "",
              className = "";
            if ($(arrList[i].firstChild.firstChild).hasClass("feather")) {
              var classString = arrList[i].firstChild.firstChild.getAttribute("class");
              iconName = classString.split("feather-")[1].split(" ")[0];
              className = classString.split("feather-")[1].split(" ")[1];
            }
            $arrList += '<li class="auto-suggestion">' + '<a class="d-flex align-items-center justify-content-between w-100" href=' + arrList[i].firstChild.href + ">" + '<div class="d-flex justify-content-start align-items-center">' + feather.icons[iconName].toSvg({
              "class": "me-75 "
            }) + "<span>" + arrList[i].firstChild.dataset.bsOriginalTitle + "</span>" + "</div>" + feather.icons["star"].toSvg({
              "class": "text-warning bookmark-icon float-end"
            }) + "</a>" + "</li>";
          }
          $("ul.search-list").append($arrList);
          // Feather Icons
          // if (feather) {
          //   featherSVG();
          // }
        } else {
          // if search input blank, hide overlay
          if (appContent.hasClass("show-overlay")) {
            appContent.removeClass("show-overlay");
          }
          // If filter box is empty
          if (searchList.hasClass("show")) {
            searchList.removeClass("show");
          }
        }
      }
    }
  });

  // Add class on hover of the list
  $(document).on("mouseenter", ".search-list li", function (e) {
    $(this).siblings().removeClass("current_item");
    $(this).addClass("current_item");
  });
  $(document).on("click", ".search-list li", function (e) {
    e.stopPropagation();
  });
  $("html").on("click", function ($this) {
    if (!$($this.target).hasClass("bookmark-icon")) {
      if (bookmarkSearchList.hasClass("show")) {
        bookmarkSearchList.removeClass("show");
      }
      if (bookmarkInput.hasClass("show")) {
        bookmarkInput.removeClass("show");
        appContent.removeClass("show-overlay");
      }
    }
  });

  // Prevent closing bookmark dropdown on input textbox click
  $(document).on("click", ".bookmark-input input", function (e) {
    bookmarkInput.addClass("show");
    bookmarkSearchList.addClass("show");
  });

  // Favorite star click
  $(document).on("click", ".bookmark-input .search-list .bookmark-icon", function (e) {
    e.stopPropagation();
    if ($(this).hasClass("text-warning")) {
      $(this).removeClass("text-warning");
      var arrList = $("ul.nav.navbar-nav.bookmark-icons li");
      for (var i = 0; i < arrList.length; i++) {
        if (arrList[i].firstChild.dataset.bsOriginalTitle == $(this).parent()[0].innerText) {
          arrList[i].remove();
        }
      }
      e.preventDefault();
    } else {
      var arrList = $("ul.nav.navbar-nav.bookmark-icons li");
      $(this).addClass("text-warning");
      e.preventDefault();
      var $url = $(this).parent()[0].href,
        $name = $(this).parent()[0].innerText,
        $listItem = "",
        $listItemDropdown = "",
        iconName = $(this).parent()[0].firstChild.firstChild.dataset.icon;
      if ($($(this).parent()[0].firstChild.firstChild).hasClass("feather")) {
        var classString = $(this).parent()[0].firstChild.firstChild.getAttribute("class");
        iconName = classString.split("feather-")[1].split(" ")[0];
      }
      $listItem = '<li class="nav-item d-none d-lg-block">' + '<a class="nav-link" href="' + $url + '" data-bs-toggle="tooltip" data-bs-placement="bottom" title="' + $name + '">' + feather.icons[iconName].toSvg({
        "class": "ficon"
      }) + "</a>" + "</li>";
      $("ul.nav.bookmark-icons").append($listItem);
      $('[data-bs-toggle="tooltip"]').tooltip();
    }
  });

  // If we use up key(38) Down key (40) or Enter key(13)
  $(window).on("keydown", function (e) {
    var $current = $(".search-list li.current_item"),
      $next,
      $prev;
    if (e.keyCode === 40) {
      $next = $current.next();
      $current.removeClass("current_item");
      $current = $next.addClass("current_item");
    } else if (e.keyCode === 38) {
      $prev = $current.prev();
      $current.removeClass("current_item");
      $current = $prev.addClass("current_item");
    }
    if (e.keyCode === 13 && $(".search-list li.current_item").length > 0) {
      var selected_item = $(".search-list li.current_item a");
      window.location = selected_item.attr("href");
      $(selected_item).trigger("click");
    }
  });

  // Waves Effect
  Waves.init();
  Waves.attach(".btn:not([class*='btn-relief-']):not([class*='btn-gradient-']):not([class*='btn-outline-']):not([class*='btn-flat-'])", ["waves-float", "waves-light"]);
  Waves.attach("[class*='btn-outline-']");
  Waves.attach("[class*='btn-flat-']");
  $(".form-password-toggle .input-group-text").on("click", function (e) {
    e.preventDefault();
    var $this = $(this),
      inputGroupText = $this.closest(".form-password-toggle"),
      formPasswordToggleIcon = $this,
      formPasswordToggleInput = inputGroupText.find("input");
    if (formPasswordToggleInput.attr("type") === "text") {
      formPasswordToggleInput.attr("type", "password");
      if (feather) {
        formPasswordToggleIcon.find("svg").replaceWith(feather.icons["eye"].toSvg({
          "class": "font-small-4"
        }));
      }
    } else if (formPasswordToggleInput.attr("type") === "password") {
      formPasswordToggleInput.attr("type", "text");
      if (feather) {
        formPasswordToggleIcon.find("svg").replaceWith(feather.icons["eye-off"].toSvg({
          "class": "font-small-4"
        }));
      }
    }
  });

  // on window scroll button show/hide
  $(window).on("scroll", function () {
    if ($(this).scrollTop() > 400) {
      $(".scroll-top").fadeIn();
    } else {
      $(".scroll-top").fadeOut();
    }

    // On Scroll navbar color on horizontal menu
    if ($body.hasClass("navbar-static")) {
      var scroll = $(window).scrollTop();
      if (scroll > 65) {
        $("html:not(.dark-layout) .horizontal-menu .header-navbar.navbar-fixed").css({
          background: "#fff",
          "box-shadow": "0 4px 20px 0 rgba(0,0,0,.05)"
        });
        $(".horizontal-menu.dark-layout .header-navbar.navbar-fixed").css({
          background: "#161d31",
          "box-shadow": "0 4px 20px 0 rgba(0,0,0,.05)"
        });
        $("html:not(.dark-layout) .horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "#fff");
        $(".dark-layout .horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "#161d31");
      } else {
        $("html:not(.dark-layout) .horizontal-menu .header-navbar.navbar-fixed").css({
          background: "#f8f8f8",
          "box-shadow": "none"
        });
        $(".dark-layout .horizontal-menu .header-navbar.navbar-fixed").css({
          background: "#161d31",
          "box-shadow": "none"
        });
        $("html:not(.dark-layout) .horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "#fff");
        $(".dark-layout .horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "#161d31");
      }
    }
  });

  // Click event to scroll to top
  $(".scroll-top").on("click", function () {
    $("html, body").animate({
      scrollTop: 0
    }, 75);
  });
  function getCurrentLayout() {
    var currentLayout = "";
    if ($html.hasClass("dark-layout")) {
      currentLayout = "dark-layout";
    } else if ($html.hasClass("bordered-layout")) {
      currentLayout = "bordered-layout";
    } else if ($html.hasClass("semi-dark-layout")) {
      currentLayout = "semi-dark-layout";
    } else {
      currentLayout = "light-layout";
    }
    return currentLayout;
  }

  // Get the data layout, for blank set to light layout
  var dataLayout = $html.attr("data-layout") ? $html.attr("data-layout") : "light-layout";

  // Navbar Dark / Light Layout Toggle Switch
  $(".nav-link-style").on("click", function () {
    var currentLayout = getCurrentLayout(),
      switchToLayout = "",
      prevLayout = localStorage.getItem(dataLayout + "-prev-skin", currentLayout);

    // If currentLayout is not dark layout
    if (currentLayout !== "dark-layout") {
      // Switch to dark
      switchToLayout = "dark-layout";
    } else {
      // Switch to light
      // switchToLayout = prevLayout ? prevLayout : 'light-layout';
      if (currentLayout === prevLayout) {
        switchToLayout = "light-layout";
      } else {
        switchToLayout = prevLayout ? prevLayout : "light-layout";
      }
    }
    // Set Previous skin in local db
    localStorage.setItem(dataLayout + "-prev-skin", currentLayout);
    // Set Current skin in local db
    localStorage.setItem(dataLayout + "-current-skin", switchToLayout);

    // Call set layout
    setLayout(switchToLayout);

    // set the value in session
    axios.post("/theme-switch", {
      theme: currentLayout
    }).then(function (response) {
      console.log(response.data);
    })["catch"](function (error) {
      console.log(error.response);
    });

    // ToDo: Customizer fix
    $(".horizontal-menu .header-navbar.navbar-fixed").css({
      background: "inherit",
      "box-shadow": "inherit"
    });
    $(".horizontal-menu .horizontal-menu-wrapper.header-navbar").css("background", "inherit");
  });

  // Get current local storage layout
  var currentLocalStorageLayout = localStorage.getItem(dataLayout + "-current-skin");

  // Set layout on screen load
  //? Comment it if you don't want to sync layout with local db
  setLayout(currentLocalStorageLayout);
  function setLayout(currentLocalStorageLayout) {
    var navLinkStyle = $(".nav-link-style"),
      currentLayout = getCurrentLayout(),
      mainMenu = $(".main-menu"),
      navbar = $(".header-navbar"),
      // Witch to local storage layout if we have else current layout
      switchToLayout = currentLocalStorageLayout ? currentLocalStorageLayout : currentLayout;
    $html.removeClass("semi-dark-layout dark-layout bordered-layout");
    if (switchToLayout === "dark-layout") {
      $html.addClass("dark-layout");
      mainMenu.removeClass("menu-light").addClass("menu-dark");
      navbar.removeClass("navbar-light").addClass("navbar-dark");
      navLinkStyle.find(".ficon").replaceWith(feather.icons["sun"].toSvg({
        "class": "ficon"
      }));
    } else if (switchToLayout === "bordered-layout") {
      $html.addClass("bordered-layout");
      mainMenu.removeClass("menu-dark").addClass("menu-light");
      navbar.removeClass("navbar-dark").addClass("navbar-light");
      navLinkStyle.find(".ficon").replaceWith(feather.icons["moon"].toSvg({
        "class": "ficon"
      }));
    } else if (switchToLayout === "semi-dark-layout") {
      $html.addClass("semi-dark-layout");
      mainMenu.removeClass("menu-dark").addClass("menu-light");
      navbar.removeClass("navbar-dark").addClass("navbar-light");
      navLinkStyle.find(".ficon").replaceWith(feather.icons["moon"].toSvg({
        "class": "ficon"
      }));
    } else {
      $html.addClass("light-layout");
      mainMenu.removeClass("menu-dark").addClass("menu-light");
      navbar.removeClass("navbar-dark").addClass("navbar-light");
      navLinkStyle.find(".ficon").replaceWith(feather.icons["moon"].toSvg({
        "class": "ficon"
      }));
    }
    // Set radio in customizer if we have
    if ($("input:radio[data-layout=" + switchToLayout + "]").length > 0) {
      setTimeout(function () {
        $("input:radio[data-layout=" + switchToLayout + "]").prop("checked", true);
      });
    }
  }
})(window, document, jQuery);

// To use feather svg icons with different sizes
function featherSVG(iconSize) {
  // Feather Icons
  if (iconSize == undefined) {
    iconSize = "14";
  }
  return feather.replace({
    width: iconSize,
    height: iconSize
  });
}

// jQuery Validation Global Defaults
if (typeof jQuery.validator === "function") {
  jQuery.validator.setDefaults({
    errorElement: "span",
    errorPlacement: function errorPlacement(error, element) {
      if (element.parent().hasClass("input-group") || element.hasClass("select2") || element.attr("type") === "checkbox") {
        error.insertAfter(element.parent());
      } else if (element.hasClass("form-check-input")) {
        error.insertAfter(element.parent().siblings(":last"));
      } else {
        error.insertAfter(element);
      }
      if (element.parent().hasClass("input-group")) {
        element.parent().addClass("is-invalid");
      }
    },
    highlight: function highlight(element, errorClass, validClass) {
      $(element).addClass("error");
      if ($(element).parent().hasClass("input-group")) {
        $(element).parent().addClass("is-invalid");
      }
    },
    unhighlight: function unhighlight(element, errorClass, validClass) {
      $(element).removeClass("error");
      if ($(element).parent().hasClass("input-group")) {
        $(element).parent().removeClass("is-invalid");
      }
    }
  });
}

// Add validation class to input-group (input group validation fix, currently disabled but will be useful in future)
/* function inputGroupValidation(el) {
  var validEl,
    invalidEl,
    elem = $(el);

  if (elem.hasClass('form-control')) {
    if ($(elem).is('.form-control:valid, .form-control.is-valid')) {
      validEl = elem;
    }
    if ($(elem).is('.form-control:invalid, .form-control.is-invalid')) {
      invalidEl = elem;
    }
  } else {
    validEl = elem.find('.form-control:valid, .form-control.is-valid');
    invalidEl = elem.find('.form-control:invalid, .form-control.is-invalid');
  }
  if (validEl !== undefined) {
    validEl.closest('.input-group').removeClass('.is-valid is-invalid').addClass('is-valid');
  }
  if (invalidEl !== undefined) {
    invalidEl.closest('.input-group').removeClass('.is-valid is-invalid').addClass('is-invalid');
  }
} */

/***/ }),

/***/ "./resources/scss/base/plugins/forms/pickers/form-flat-pickr.scss":
/*!************************************************************************!*\
  !*** ./resources/scss/base/plugins/forms/pickers/form-flat-pickr.scss ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/pages/authentication.scss":
/*!*******************************************************!*\
  !*** ./resources/scss/base/pages/authentication.scss ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/pages/dashboard-ecommerce.scss":
/*!************************************************************!*\
  !*** ./resources/scss/base/pages/dashboard-ecommerce.scss ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/pages/app-invoice-list.scss":
/*!*********************************************************!*\
  !*** ./resources/scss/base/pages/app-invoice-list.scss ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/plugins/charts/chart-apex.scss":
/*!************************************************************!*\
  !*** ./resources/scss/base/plugins/charts/chart-apex.scss ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/plugins/forms/form-file-uploader.scss":
/*!*******************************************************************!*\
  !*** ./resources/scss/base/plugins/forms/form-file-uploader.scss ***!
  \*******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/plugins/forms/form-quill-editor.scss":
/*!******************************************************************!*\
  !*** ./resources/scss/base/plugins/forms/form-quill-editor.scss ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/plugins/forms/form-validation.scss":
/*!****************************************************************!*\
  !*** ./resources/scss/base/plugins/forms/form-validation.scss ***!
  \****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/plugins/extensions/ext-component-toastr.scss":
/*!**************************************************************************!*\
  !*** ./resources/scss/base/plugins/extensions/ext-component-toastr.scss ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/themes/bordered-layout.scss":
/*!*********************************************************!*\
  !*** ./resources/scss/base/themes/bordered-layout.scss ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/themes/dark-layout.scss":
/*!*****************************************************!*\
  !*** ./resources/scss/base/themes/dark-layout.scss ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/themes/semi-dark-layout.scss":
/*!**********************************************************!*\
  !*** ./resources/scss/base/themes/semi-dark-layout.scss ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/quill.scss":
/*!***********************************!*\
  !*** ./resources/scss/quill.scss ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/core.scss":
/*!**********************************!*\
  !*** ./resources/scss/core.scss ***!
  \**********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/overrides.scss":
/*!***************************************!*\
  !*** ./resources/scss/overrides.scss ***!
  \***************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/style.scss":
/*!***********************************!*\
  !*** ./resources/scss/style.scss ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./resources/scss/base/core/menu/menu-types/vertical-menu.scss":
/*!*********************************************************************!*\
  !*** ./resources/scss/base/core/menu/menu-types/vertical-menu.scss ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/process/browser.js":
/*!*****************************************!*\
  !*** ./node_modules/process/browser.js ***!
  \*****************************************/
/***/ ((module) => {

// shim for using process in browser
var process = module.exports = {};

// cached from whatever global is present so that test runners that stub it
// don't break things.  But we need to wrap it in a try catch in case it is
// wrapped in strict mode code which doesn't define any globals.  It's inside a
// function because try/catches deoptimize in certain engines.

var cachedSetTimeout;
var cachedClearTimeout;

function defaultSetTimout() {
    throw new Error('setTimeout has not been defined');
}
function defaultClearTimeout () {
    throw new Error('clearTimeout has not been defined');
}
(function () {
    try {
        if (typeof setTimeout === 'function') {
            cachedSetTimeout = setTimeout;
        } else {
            cachedSetTimeout = defaultSetTimout;
        }
    } catch (e) {
        cachedSetTimeout = defaultSetTimout;
    }
    try {
        if (typeof clearTimeout === 'function') {
            cachedClearTimeout = clearTimeout;
        } else {
            cachedClearTimeout = defaultClearTimeout;
        }
    } catch (e) {
        cachedClearTimeout = defaultClearTimeout;
    }
} ())
function runTimeout(fun) {
    if (cachedSetTimeout === setTimeout) {
        //normal enviroments in sane situations
        return setTimeout(fun, 0);
    }
    // if setTimeout wasn't available but was latter defined
    if ((cachedSetTimeout === defaultSetTimout || !cachedSetTimeout) && setTimeout) {
        cachedSetTimeout = setTimeout;
        return setTimeout(fun, 0);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedSetTimeout(fun, 0);
    } catch(e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't trust the global object when called normally
            return cachedSetTimeout.call(null, fun, 0);
        } catch(e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error
            return cachedSetTimeout.call(this, fun, 0);
        }
    }


}
function runClearTimeout(marker) {
    if (cachedClearTimeout === clearTimeout) {
        //normal enviroments in sane situations
        return clearTimeout(marker);
    }
    // if clearTimeout wasn't available but was latter defined
    if ((cachedClearTimeout === defaultClearTimeout || !cachedClearTimeout) && clearTimeout) {
        cachedClearTimeout = clearTimeout;
        return clearTimeout(marker);
    }
    try {
        // when when somebody has screwed with setTimeout but no I.E. maddness
        return cachedClearTimeout(marker);
    } catch (e){
        try {
            // When we are in I.E. but the script has been evaled so I.E. doesn't  trust the global object when called normally
            return cachedClearTimeout.call(null, marker);
        } catch (e){
            // same as above but when it's a version of I.E. that must have the global object for 'this', hopfully our context correct otherwise it will throw a global error.
            // Some versions of I.E. have different rules for clearTimeout vs setTimeout
            return cachedClearTimeout.call(this, marker);
        }
    }



}
var queue = [];
var draining = false;
var currentQueue;
var queueIndex = -1;

function cleanUpNextTick() {
    if (!draining || !currentQueue) {
        return;
    }
    draining = false;
    if (currentQueue.length) {
        queue = currentQueue.concat(queue);
    } else {
        queueIndex = -1;
    }
    if (queue.length) {
        drainQueue();
    }
}

function drainQueue() {
    if (draining) {
        return;
    }
    var timeout = runTimeout(cleanUpNextTick);
    draining = true;

    var len = queue.length;
    while(len) {
        currentQueue = queue;
        queue = [];
        while (++queueIndex < len) {
            if (currentQueue) {
                currentQueue[queueIndex].run();
            }
        }
        queueIndex = -1;
        len = queue.length;
    }
    currentQueue = null;
    draining = false;
    runClearTimeout(timeout);
}

process.nextTick = function (fun) {
    var args = new Array(arguments.length - 1);
    if (arguments.length > 1) {
        for (var i = 1; i < arguments.length; i++) {
            args[i - 1] = arguments[i];
        }
    }
    queue.push(new Item(fun, args));
    if (queue.length === 1 && !draining) {
        runTimeout(drainQueue);
    }
};

// v8 likes predictible objects
function Item(fun, array) {
    this.fun = fun;
    this.array = array;
}
Item.prototype.run = function () {
    this.fun.apply(null, this.array);
};
process.title = 'browser';
process.browser = true;
process.env = {};
process.argv = [];
process.version = ''; // empty string to avoid regexp issues
process.versions = {};

function noop() {}

process.on = noop;
process.addListener = noop;
process.once = noop;
process.off = noop;
process.removeListener = noop;
process.removeAllListeners = noop;
process.emit = noop;
process.prependListener = noop;
process.prependOnceListener = noop;

process.listeners = function (name) { return [] }

process.binding = function (name) {
    throw new Error('process.binding is not supported');
};

process.cwd = function () { return '/' };
process.chdir = function (dir) {
    throw new Error('process.chdir is not supported');
};
process.umask = function() { return 0; };


/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = __webpack_modules__;
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/chunk loaded */
/******/ 	(() => {
/******/ 		var deferred = [];
/******/ 		__webpack_require__.O = (result, chunkIds, fn, priority) => {
/******/ 			if(chunkIds) {
/******/ 				priority = priority || 0;
/******/ 				for(var i = deferred.length; i > 0 && deferred[i - 1][2] > priority; i--) deferred[i] = deferred[i - 1];
/******/ 				deferred[i] = [chunkIds, fn, priority];
/******/ 				return;
/******/ 			}
/******/ 			var notFulfilled = Infinity;
/******/ 			for (var i = 0; i < deferred.length; i++) {
/******/ 				var [chunkIds, fn, priority] = deferred[i];
/******/ 				var fulfilled = true;
/******/ 				for (var j = 0; j < chunkIds.length; j++) {
/******/ 					if ((priority & 1 === 0 || notFulfilled >= priority) && Object.keys(__webpack_require__.O).every((key) => (__webpack_require__.O[key](chunkIds[j])))) {
/******/ 						chunkIds.splice(j--, 1);
/******/ 					} else {
/******/ 						fulfilled = false;
/******/ 						if(priority < notFulfilled) notFulfilled = priority;
/******/ 					}
/******/ 				}
/******/ 				if(fulfilled) {
/******/ 					deferred.splice(i--, 1)
/******/ 					var r = fn();
/******/ 					if (r !== undefined) result = r;
/******/ 				}
/******/ 			}
/******/ 			return result;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/jsonp chunk loading */
/******/ 	(() => {
/******/ 		// no baseURI
/******/ 		
/******/ 		// object to store loaded and loading chunks
/******/ 		// undefined = chunk not loaded, null = chunk preloaded/prefetched
/******/ 		// [resolve, reject, Promise] = chunk loading, 0 = chunk loaded
/******/ 		var installedChunks = {
/******/ 			"/js/core/app": 0,
/******/ 			"css/quill": 0,
/******/ 			"css/base/core/menu/menu-types/vertical-menu": 0,
/******/ 			"css/style": 0,
/******/ 			"css/overrides": 0,
/******/ 			"css/core": 0,
/******/ 			"css/base/themes/semi-dark-layout": 0,
/******/ 			"css/base/themes/dark-layout": 0,
/******/ 			"css/base/themes/bordered-layout": 0,
/******/ 			"css/base/plugins/extensions/ext-component-toastr": 0,
/******/ 			"css/base/plugins/forms/form-validation": 0,
/******/ 			"css/base/plugins/forms/form-quill-editor": 0,
/******/ 			"css/base/plugins/forms/file-uploader": 0,
/******/ 			"css/base/plugins/charts/chart-apex": 0,
/******/ 			"css/base/pages/app-invoice-list": 0,
/******/ 			"css/base/pages/dashboard-ecommerce": 0,
/******/ 			"css/base/pages/authentication": 0,
/******/ 			"css/base/plugins/forms/pickers/form-flat-pickr": 0
/******/ 		};
/******/ 		
/******/ 		// no chunk on demand loading
/******/ 		
/******/ 		// no prefetching
/******/ 		
/******/ 		// no preloaded
/******/ 		
/******/ 		// no HMR
/******/ 		
/******/ 		// no HMR manifest
/******/ 		
/******/ 		__webpack_require__.O.j = (chunkId) => (installedChunks[chunkId] === 0);
/******/ 		
/******/ 		// install a JSONP callback for chunk loading
/******/ 		var webpackJsonpCallback = (parentChunkLoadingFunction, data) => {
/******/ 			var [chunkIds, moreModules, runtime] = data;
/******/ 			// add "moreModules" to the modules object,
/******/ 			// then flag all "chunkIds" as loaded and fire callback
/******/ 			var moduleId, chunkId, i = 0;
/******/ 			if(chunkIds.some((id) => (installedChunks[id] !== 0))) {
/******/ 				for(moduleId in moreModules) {
/******/ 					if(__webpack_require__.o(moreModules, moduleId)) {
/******/ 						__webpack_require__.m[moduleId] = moreModules[moduleId];
/******/ 					}
/******/ 				}
/******/ 				if(runtime) var result = runtime(__webpack_require__);
/******/ 			}
/******/ 			if(parentChunkLoadingFunction) parentChunkLoadingFunction(data);
/******/ 			for(;i < chunkIds.length; i++) {
/******/ 				chunkId = chunkIds[i];
/******/ 				if(__webpack_require__.o(installedChunks, chunkId) && installedChunks[chunkId]) {
/******/ 					installedChunks[chunkId][0]();
/******/ 				}
/******/ 				installedChunks[chunkId] = 0;
/******/ 			}
/******/ 			return __webpack_require__.O(result);
/******/ 		}
/******/ 		
/******/ 		var chunkLoadingGlobal = self["webpackChunk"] = self["webpackChunk"] || [];
/******/ 		chunkLoadingGlobal.forEach(webpackJsonpCallback.bind(null, 0));
/******/ 		chunkLoadingGlobal.push = webpackJsonpCallback.bind(null, chunkLoadingGlobal.push.bind(chunkLoadingGlobal));
/******/ 	})();
/******/ 	
/************************************************************************/
/******/ 	
/******/ 	// startup
/******/ 	// Load entry module and return exports
/******/ 	// This entry module depends on other loaded chunks and execution need to be delayed
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/js/core/app.js")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/core.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/overrides.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/style.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/core/menu/menu-types/vertical-menu.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/plugins/forms/pickers/form-flat-pickr.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/pages/authentication.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/pages/dashboard-ecommerce.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/pages/app-invoice-list.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/plugins/charts/chart-apex.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/plugins/forms/form-file-uploader.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/plugins/forms/form-quill-editor.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/plugins/forms/form-validation.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/plugins/extensions/ext-component-toastr.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/themes/bordered-layout.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/themes/dark-layout.scss")))
/******/ 	__webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/base/themes/semi-dark-layout.scss")))
/******/ 	var __webpack_exports__ = __webpack_require__.O(undefined, ["css/quill","css/base/core/menu/menu-types/vertical-menu","css/style","css/overrides","css/core","css/base/themes/semi-dark-layout","css/base/themes/dark-layout","css/base/themes/bordered-layout","css/base/plugins/extensions/ext-component-toastr","css/base/plugins/forms/form-validation","css/base/plugins/forms/form-quill-editor","css/base/plugins/forms/file-uploader","css/base/plugins/charts/chart-apex","css/base/pages/app-invoice-list","css/base/pages/dashboard-ecommerce","css/base/pages/authentication","css/base/plugins/forms/pickers/form-flat-pickr"], () => (__webpack_require__("./resources/scss/quill.scss")))
/******/ 	__webpack_exports__ = __webpack_require__.O(__webpack_exports__);
/******/ 	
/******/ })()
;