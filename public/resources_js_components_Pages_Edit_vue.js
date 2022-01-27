"use strict";
(self["webpackChunk"] = self["webpackChunk"] || []).push([["resources_js_components_Pages_Edit_vue"],{

/***/ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/Pages/Edit.vue?vue&type=script&lang=js&":
/*!*****************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/Pages/Edit.vue?vue&type=script&lang=js& ***!
  \*****************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
//
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  data: function data() {
    return {
      data: [],
      newData: {},
      resource: this.activeTool(),
      errors: [],
      canUpdate: false
    };
  },
  computed: {
    couldUpdate: function couldUpdate() {
      if (!this.canUpdate) {
        return true;
      }

      return this.newData.length === 0;
    }
  },
  mounted: function mounted() {
    var _this = this;

    this.errors = [];
    this.$http.post("/api/form", {
      resource: this.resource.resource,
      search: decodeURIComponent(this.$route.params.primaryKey),
      primary_key: this.resource.primaryKey
    }).then(function (res) {
      _this.data = res.data;
    });
  },
  methods: {
    changed: function changed(value, column) {
      this.canUpdate = true;
      console.log(this.newData);
      this.$set(this.newData, column, value);
    },
    update: function update() {
      var _this2 = this;

      this.$http.post("/api/update", {
        resource: this.resource.resource,
        data: this.newData,
        primary_key: this.resource.primaryKey,
        search: decodeURIComponent(this.$route.params.primaryKey)
      }).then(function (res) {
        if (res) {
          Lava.toast(res.data.message, "success");
          _this2.canUpdate = false;
        }
      })["catch"](function (error) {
        _this2.errors = error.response.data.errors || [];
        _this2.canUpdate = false;
      });
    }
  }
});

/***/ }),

/***/ "./resources/js/components/Pages/Edit.vue":
/*!************************************************!*\
  !*** ./resources/js/components/Pages/Edit.vue ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Edit_vue_vue_type_template_id_282d5080___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Edit.vue?vue&type=template&id=282d5080& */ "./resources/js/components/Pages/Edit.vue?vue&type=template&id=282d5080&");
/* harmony import */ var _Edit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Edit.vue?vue&type=script&lang=js& */ "./resources/js/components/Pages/Edit.vue?vue&type=script&lang=js&");
/* harmony import */ var _node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! !../../../../node_modules/vue-loader/lib/runtime/componentNormalizer.js */ "./node_modules/vue-loader/lib/runtime/componentNormalizer.js");





/* normalize component */
;
var component = (0,_node_modules_vue_loader_lib_runtime_componentNormalizer_js__WEBPACK_IMPORTED_MODULE_2__["default"])(
  _Edit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_1__["default"],
  _Edit_vue_vue_type_template_id_282d5080___WEBPACK_IMPORTED_MODULE_0__.render,
  _Edit_vue_vue_type_template_id_282d5080___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns,
  false,
  null,
  null,
  null
  
)

/* hot reload */
if (false) { var api; }
component.options.__file = "resources/js/components/Pages/Edit.vue"
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (component.exports);

/***/ }),

/***/ "./resources/js/components/Pages/Edit.vue?vue&type=script&lang=js&":
/*!*************************************************************************!*\
  !*** ./resources/js/components/Pages/Edit.vue?vue&type=script&lang=js& ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Edit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Edit.vue?vue&type=script&lang=js& */ "./node_modules/babel-loader/lib/index.js??clonedRuleSet-5[0].rules[0].use[0]!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/Pages/Edit.vue?vue&type=script&lang=js&");
 /* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (_node_modules_babel_loader_lib_index_js_clonedRuleSet_5_0_rules_0_use_0_node_modules_vue_loader_lib_index_js_vue_loader_options_Edit_vue_vue_type_script_lang_js___WEBPACK_IMPORTED_MODULE_0__["default"]); 

/***/ }),

/***/ "./resources/js/components/Pages/Edit.vue?vue&type=template&id=282d5080&":
/*!*******************************************************************************!*\
  !*** ./resources/js/components/Pages/Edit.vue?vue&type=template&id=282d5080& ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Edit_vue_vue_type_template_id_282d5080___WEBPACK_IMPORTED_MODULE_0__.render),
/* harmony export */   "staticRenderFns": () => (/* reexport safe */ _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Edit_vue_vue_type_template_id_282d5080___WEBPACK_IMPORTED_MODULE_0__.staticRenderFns)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_lib_loaders_templateLoader_js_vue_loader_options_node_modules_vue_loader_lib_index_js_vue_loader_options_Edit_vue_vue_type_template_id_282d5080___WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!../../../../node_modules/vue-loader/lib/index.js??vue-loader-options!./Edit.vue?vue&type=template&id=282d5080& */ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/Pages/Edit.vue?vue&type=template&id=282d5080&");


/***/ }),

/***/ "./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/Pages/Edit.vue?vue&type=template&id=282d5080&":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/lib/loaders/templateLoader.js??vue-loader-options!./node_modules/vue-loader/lib/index.js??vue-loader-options!./resources/js/components/Pages/Edit.vue?vue&type=template&id=282d5080& ***!
  \**********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render),
/* harmony export */   "staticRenderFns": () => (/* binding */ staticRenderFns)
/* harmony export */ });
var render = function () {
  var _vm = this
  var _h = _vm.$createElement
  var _c = _vm._self._c || _h
  return !_vm._.isEmpty(_vm.data)
    ? _c("div", { staticClass: "flex flex-col" }, [
        _c("div", { staticClass: "flex items-center justify-between" }, [
          _c("i", {
            staticClass: "ri-arrow-left-line cursor-pointer text-lg w-fit",
            on: {
              click: function ($event) {
                return _vm.goToBack()
              },
            },
          }),
          _vm._v(" "),
          _c(
            "div",
            { staticClass: "flex justify-end" },
            [
              _c(
                "lava-button",
                {
                  attrs: { disabled: _vm.couldUpdate },
                  on: { click: _vm.update },
                },
                [_vm._v("Update\n      ")]
              ),
            ],
            1
          ),
        ]),
        _vm._v(" "),
        _c(
          "div",
          { staticClass: "p-2 text-lg bg-white shadow rounded-md my-2" },
          [
            _vm._l(_vm.resource.fields, function (field, index) {
              return [
                field.showOnForm
                  ? _c(
                      "div",
                      { key: index },
                      [
                        field.forDesign
                          ? _c(
                              field.component,
                              _vm._b(
                                {
                                  tag: "component",
                                  attrs: { data: field },
                                  scopedSlots: _vm._u(
                                    [
                                      {
                                        key: "header",
                                        fn: function () {
                                          return [_vm._v(_vm._s(field.title))]
                                        },
                                        proxy: true,
                                      },
                                      {
                                        key: "body",
                                        fn: function () {
                                          return [
                                            _vm._l(
                                              field.fields,
                                              function (designField, i) {
                                                return _c(
                                                  "div",
                                                  {
                                                    key: i,
                                                    staticClass:
                                                      "flex justify-start p-2 text-lg",
                                                  },
                                                  [
                                                    _c(
                                                      "div",
                                                      {
                                                        staticStyle: {
                                                          width: "18vw",
                                                        },
                                                      },
                                                      [
                                                        _vm._v(
                                                          _vm._s(
                                                            designField.name
                                                          ) + " "
                                                        ),
                                                        designField.rules.includes(
                                                          "required"
                                                        )
                                                          ? _c(
                                                              "span",
                                                              {
                                                                staticClass:
                                                                  "text-danger",
                                                              },
                                                              [_vm._v("*")]
                                                            )
                                                          : _vm._e(),
                                                      ]
                                                    ),
                                                    _vm._v(" "),
                                                    designField.showOnForm
                                                      ? _c(
                                                          designField.component +
                                                            "-edit",
                                                          _vm._b(
                                                            {
                                                              key: i,
                                                              tag: "component",
                                                              staticClass:
                                                                "w-full",
                                                              attrs: {
                                                                data: designField,
                                                                value:
                                                                  _vm.resourceValue(
                                                                    _vm.data,
                                                                    designField.column
                                                                  ),
                                                              },
                                                              on: {
                                                                "on-change":
                                                                  _vm.changed,
                                                              },
                                                            },
                                                            "component",
                                                            designField.attributes,
                                                            false
                                                          )
                                                        )
                                                      : _vm._e(),
                                                  ],
                                                  1
                                                )
                                              }
                                            ),
                                            _vm._v(" "),
                                            _vm.errors[field.column]
                                              ? _c("form-error", {
                                                  attrs: {
                                                    errors:
                                                      _vm.errors[field.column],
                                                  },
                                                })
                                              : _vm._e(),
                                          ]
                                        },
                                        proxy: true,
                                      },
                                    ],
                                    null,
                                    true
                                  ),
                                },
                                "component",
                                field.attributes,
                                false
                              )
                            )
                          : _c(
                              "div",
                              { staticClass: "flex justify-start p-2 text-lg" },
                              [
                                _c("div", { staticStyle: { width: "18vw" } }, [
                                  _vm._v(_vm._s(field.name) + " "),
                                  field.rules.includes("required")
                                    ? _c(
                                        "span",
                                        { staticClass: "text-danger" },
                                        [_vm._v("*")]
                                      )
                                    : _vm._e(),
                                ]),
                                _vm._v(" "),
                                field.showOnForm
                                  ? _c(
                                      field.component + "-edit",
                                      _vm._b(
                                        {
                                          key: index,
                                          tag: "component",
                                          staticClass: "w-full",
                                          attrs: {
                                            data: field,
                                            value: _vm.resourceValue(
                                              _vm.data,
                                              field.column
                                            ),
                                          },
                                          on: { "on-change": _vm.changed },
                                        },
                                        "component",
                                        field.attributes,
                                        false
                                      )
                                    )
                                  : _vm._e(),
                              ],
                              1
                            ),
                        _vm._v(" "),
                        _vm.errors[field.column]
                          ? _c("form-error", {
                              attrs: { errors: _vm.errors[field.column] },
                            })
                          : _vm._e(),
                      ],
                      1
                    )
                  : _vm._e(),
              ]
            }),
          ],
          2
        ),
      ])
    : _vm._e()
}
var staticRenderFns = []
render._withStripped = true



/***/ })

}]);