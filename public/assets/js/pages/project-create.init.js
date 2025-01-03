/******/ (() => {
    // webpackBootstrap
    var __webpack_exports__ = {};
    /*!***************************************************!*\
  !*** ./resources/js/pages/project-create.init.js ***!
  \***************************************************/
    /*
Template Name: Velzon - Admin & Dashboard Template
Author: Themesbrand
Website: https://Themesbrand.com/
Contact: Themesbrand@gmail.com
File: Project create init js
*/
    // ckeditor
    var ckeditorClassic = document.querySelector("#ckeditor-classic");

    if (ckeditorClassic) {
        ClassicEditor.create(document.querySelector("#ckeditor-classic"))
            .then(function (editor) {
                editor.ui.view.editable.element.style.height = "200px";
            })
            ["catch"](function (error) {
                console.error(error);
            });
    } // Dropzone

    /******/
})();
