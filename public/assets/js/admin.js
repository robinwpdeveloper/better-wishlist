/******/ (function(modules) { // webpackBootstrap
/******/ 	// The module cache
/******/ 	var installedModules = {};
/******/
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/
/******/ 		// Check if module is in cache
/******/ 		if(installedModules[moduleId]) {
/******/ 			return installedModules[moduleId].exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = installedModules[moduleId] = {
/******/ 			i: moduleId,
/******/ 			l: false,
/******/ 			exports: {}
/******/ 		};
/******/
/******/ 		// Execute the module function
/******/ 		modules[moduleId].call(module.exports, module, module.exports, __webpack_require__);
/******/
/******/ 		// Flag the module as loaded
/******/ 		module.l = true;
/******/
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/
/******/
/******/ 	// expose the modules object (__webpack_modules__)
/******/ 	__webpack_require__.m = modules;
/******/
/******/ 	// expose the module cache
/******/ 	__webpack_require__.c = installedModules;
/******/
/******/ 	// define getter function for harmony exports
/******/ 	__webpack_require__.d = function(exports, name, getter) {
/******/ 		if(!__webpack_require__.o(exports, name)) {
/******/ 			Object.defineProperty(exports, name, { enumerable: true, get: getter });
/******/ 		}
/******/ 	};
/******/
/******/ 	// define __esModule on exports
/******/ 	__webpack_require__.r = function(exports) {
/******/ 		if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 			Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 		}
/******/ 		Object.defineProperty(exports, '__esModule', { value: true });
/******/ 	};
/******/
/******/ 	// create a fake namespace object
/******/ 	// mode & 1: value is a module id, require it
/******/ 	// mode & 2: merge all properties of value into the ns
/******/ 	// mode & 4: return value when already ns object
/******/ 	// mode & 8|1: behave like require
/******/ 	__webpack_require__.t = function(value, mode) {
/******/ 		if(mode & 1) value = __webpack_require__(value);
/******/ 		if(mode & 8) return value;
/******/ 		if((mode & 4) && typeof value === 'object' && value && value.__esModule) return value;
/******/ 		var ns = Object.create(null);
/******/ 		__webpack_require__.r(ns);
/******/ 		Object.defineProperty(ns, 'default', { enumerable: true, value: value });
/******/ 		if(mode & 2 && typeof value != 'string') for(var key in value) __webpack_require__.d(ns, key, function(key) { return value[key]; }.bind(null, key));
/******/ 		return ns;
/******/ 	};
/******/
/******/ 	// getDefaultExport function for compatibility with non-harmony modules
/******/ 	__webpack_require__.n = function(module) {
/******/ 		var getter = module && module.__esModule ?
/******/ 			function getDefault() { return module['default']; } :
/******/ 			function getModuleExports() { return module; };
/******/ 		__webpack_require__.d(getter, 'a', getter);
/******/ 		return getter;
/******/ 	};
/******/
/******/ 	// Object.prototype.hasOwnProperty.call
/******/ 	__webpack_require__.o = function(object, property) { return Object.prototype.hasOwnProperty.call(object, property); };
/******/
/******/ 	// __webpack_public_path__
/******/ 	__webpack_require__.p = "";
/******/
/******/
/******/ 	// Load entry module and return exports
/******/ 	return __webpack_require__(__webpack_require__.s = "./jsrc/index.js");
/******/ })
/************************************************************************/
/******/ ({

/***/ "./jsrc/components/page.js":
/*!*********************************!*\
  !*** ./jsrc/components/page.js ***!
  \*********************************/
/*! exports provided: default */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\nfunction _typeof(obj) { \"@babel/helpers - typeof\"; if (typeof Symbol === \"function\" && typeof Symbol.iterator === \"symbol\") { _typeof = function _typeof(obj) { return typeof obj; }; } else { _typeof = function _typeof(obj) { return obj && typeof Symbol === \"function\" && obj.constructor === Symbol && obj !== Symbol.prototype ? \"symbol\" : typeof obj; }; } return _typeof(obj); }\n\nfunction _classCallCheck(instance, Constructor) { if (!(instance instanceof Constructor)) { throw new TypeError(\"Cannot call a class as a function\"); } }\n\nfunction _defineProperties(target, props) { for (var i = 0; i < props.length; i++) { var descriptor = props[i]; descriptor.enumerable = descriptor.enumerable || false; descriptor.configurable = true; if (\"value\" in descriptor) descriptor.writable = true; Object.defineProperty(target, descriptor.key, descriptor); } }\n\nfunction _createClass(Constructor, protoProps, staticProps) { if (protoProps) _defineProperties(Constructor.prototype, protoProps); if (staticProps) _defineProperties(Constructor, staticProps); return Constructor; }\n\nfunction _inherits(subClass, superClass) { if (typeof superClass !== \"function\" && superClass !== null) { throw new TypeError(\"Super expression must either be null or a function\"); } subClass.prototype = Object.create(superClass && superClass.prototype, { constructor: { value: subClass, writable: true, configurable: true } }); if (superClass) _setPrototypeOf(subClass, superClass); }\n\nfunction _setPrototypeOf(o, p) { _setPrototypeOf = Object.setPrototypeOf || function _setPrototypeOf(o, p) { o.__proto__ = p; return o; }; return _setPrototypeOf(o, p); }\n\nfunction _createSuper(Derived) { var hasNativeReflectConstruct = _isNativeReflectConstruct(); return function _createSuperInternal() { var Super = _getPrototypeOf(Derived), result; if (hasNativeReflectConstruct) { var NewTarget = _getPrototypeOf(this).constructor; result = Reflect.construct(Super, arguments, NewTarget); } else { result = Super.apply(this, arguments); } return _possibleConstructorReturn(this, result); }; }\n\nfunction _possibleConstructorReturn(self, call) { if (call && (_typeof(call) === \"object\" || typeof call === \"function\")) { return call; } return _assertThisInitialized(self); }\n\nfunction _assertThisInitialized(self) { if (self === void 0) { throw new ReferenceError(\"this hasn't been initialised - super() hasn't been called\"); } return self; }\n\nfunction _isNativeReflectConstruct() { if (typeof Reflect === \"undefined\" || !Reflect.construct) return false; if (Reflect.construct.sham) return false; if (typeof Proxy === \"function\") return true; try { Date.prototype.toString.call(Reflect.construct(Date, [], function () {})); return true; } catch (e) { return false; } }\n\nfunction _getPrototypeOf(o) { _getPrototypeOf = Object.setPrototypeOf ? Object.getPrototypeOf : function _getPrototypeOf(o) { return o.__proto__ || Object.getPrototypeOf(o); }; return _getPrototypeOf(o); }\n\nvar __ = wp.i18n.__;\nvar _wp$components = wp.components,\n    BaseControl = _wp$components.BaseControl,\n    Button = _wp$components.Button,\n    PanelBody = _wp$components.PanelBody,\n    PanelRow = _wp$components.PanelRow,\n    ToggleControl = _wp$components.ToggleControl,\n    TextControl = _wp$components.TextControl,\n    ColorPicker = _wp$components.ColorPicker;\nvar _wp$element = wp.element,\n    Component = _wp$element.Component,\n    Fragment = _wp$element.Fragment;\n\nvar Page = /*#__PURE__*/function (_Component) {\n  _inherits(Page, _Component);\n\n  var _super = _createSuper(Page);\n\n  function Page() {\n    var _this;\n\n    _classCallCheck(this, Page);\n\n    _this = _super.apply(this, arguments);\n    _this.state = {\n      tab: \"general\"\n    };\n    return _this;\n  }\n\n  _createClass(Page, [{\n    key: \"render\",\n    value: function render() {\n      var tab = this.state.tab;\n      return wp.element.createElement(Fragment, null, wp.element.createElement(\"div\", {\n        className: \"better-wishlist-admin-header\"\n      }, wp.element.createElement(\"h2\", {\n        className: \"better-wishlist-admin-header-title\"\n      }, __(\"Better Wishlist Settings\"))), wp.element.createElement(\"div\", {\n        className: \"better-wishlist-admin-content\"\n      }, wp.element.createElement(PanelBody, {\n        title: __(\"General\")\n      }, wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"redirect-to-wishlist\",\n        label: \"Redirect to wishlist\",\n        help: \"Redirect to wishlist page after adding a product to wishlist.\"\n      }, wp.element.createElement(ToggleControl, {\n        checked: false,\n        onChange: function onChange() {\n          return console.log(\"\");\n        }\n      }))), wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"remove-from-wishlist\",\n        label: \"Remove from wishlist\",\n        help: \"Remove from wishlist after adding a product to cart.\"\n      }, wp.element.createElement(ToggleControl, {\n        checked: false,\n        onChange: function onChange() {\n          return console.log(\"\");\n        }\n      }))), wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"redirect-to-cart\",\n        label: \"Redirect to cart\",\n        help: \"Redirect to cart page after adding a product to cart.\"\n      }, wp.element.createElement(ToggleControl, {\n        checked: false,\n        onChange: function onChange() {\n          return console.log(\"\");\n        }\n      })))), wp.element.createElement(PanelBody, {\n        title: __(\"Button Text\"),\n        initialOpen: false\n      }, wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"add-to-cart-text\",\n        label: \"Add to cart\"\n      }, wp.element.createElement(TextControl, {\n        value: \"Hello\",\n        onChange: function onChange(value) {\n          return console.log(value);\n        }\n      }))), wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"add-to-wishlist-text\",\n        label: \"Add to wishlist\"\n      }, wp.element.createElement(TextControl, {\n        value: \"Hello\",\n        onChange: function onChange(value) {\n          return console.log(value);\n        }\n      }))), wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"add-all-to-wishlist-text\",\n        label: \"Add all to wishlist\"\n      }, wp.element.createElement(TextControl, {\n        value: \"Hello\",\n        onChange: function onChange(value) {\n          return console.log(value);\n        }\n      })))), wp.element.createElement(PanelBody, {\n        title: __(\"Style\"),\n        initialOpen: false\n      }, wp.element.createElement(PanelRow, null, wp.element.createElement(BaseControl, {\n        id: \"button-color\",\n        label: \"Button color\"\n      }, wp.element.createElement(ColorPicker, {\n        color: \"#dd102d\",\n        onChangeComplete: function onChangeComplete(value) {\n          return console.log(value.hex);\n        },\n        disableAlpha: true\n      }))))));\n    }\n  }]);\n\n  return Page;\n}(Component);\n\n/* harmony default export */ __webpack_exports__[\"default\"] = (Page);\n\n//# sourceURL=webpack:///./jsrc/components/page.js?");

/***/ }),

/***/ "./jsrc/index.js":
/*!***********************!*\
  !*** ./jsrc/index.js ***!
  \***********************/
/*! no exports provided */
/***/ (function(module, __webpack_exports__, __webpack_require__) {

"use strict";
eval("__webpack_require__.r(__webpack_exports__);\n/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./index.scss */ \"./jsrc/index.scss\");\n/* harmony import */ var _index_scss__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(_index_scss__WEBPACK_IMPORTED_MODULE_0__);\n/* harmony import */ var _components_page__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./components/page */ \"./jsrc/components/page.js\");\nvar render = wp.element.render;\n\n\nrender(wp.element.createElement(_components_page__WEBPACK_IMPORTED_MODULE_1__[\"default\"], null), document.getElementById(\"better-wishlist-admin\"));\n\n//# sourceURL=webpack:///./jsrc/index.js?");

/***/ }),

/***/ "./jsrc/index.scss":
/*!*************************!*\
  !*** ./jsrc/index.scss ***!
  \*************************/
/*! no static exports found */
/***/ (function(module, exports, __webpack_require__) {

eval("// extracted by mini-css-extract-plugin\n\n//# sourceURL=webpack:///./jsrc/index.scss?");

/***/ })

/******/ });