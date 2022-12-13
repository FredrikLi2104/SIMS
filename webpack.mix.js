const mix = require('laravel-mix');
const sassOptions = {
    precision: 5,
    includePaths: ['node_modules', 'resources/assets/']
};

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel applications. By default, we are compiling the CSS
 | file for the application as well as bundling up all the JS files.
 |
 */

// Assets
mix
    .copy('resources/data', 'public/data')
    .copy('resources/fonts', 'public/fonts')
    .copy('resources/images/', 'public/images/')
    .copy('resources/lang/', 'public/lang/');

// Sass
mix
    .copy('resources/vendors/css/charts/apexcharts.css', 'public/vendors/css/charts/apexcharts.css')
    .copy('resources/vendors/css/editors/quill/katex.min.css', 'public/vendors/css/editors/quill/katex.min.css')
    .copy('resources/vendors/css/editors/quill/monokai-sublime.min.css', 'public/vendors/css/editors/quill/monokai-sublime.min.css')
    .copy('resources/vendors/css/editors/quill/quill.snow.css', 'public/vendors/css/editors/quill/quill.snow.css')
    .copy('resources/vendors/css/editors/quill/quill.bubble.css', 'public/vendors/css/editors/quill/quill.bubble.css')
    .copy('resources/vendors/css/forms/select/select2.min.css', 'public/vendors/css/forms/select/select2.min.css')
    .copy('resources/vendors/css/tables/datatable/dataTables.bootstrap5.min.css', 'public/vendors/css/tables/datatable/dataTables.bootstrap5.min.css')
    .copy('resources/vendors/css/extensions/toastr.min.css', 'public/vendors/css/extensions/toastr.min.css')
    .copy('resources/vendors/css/pickers/flatpickr/flatpickr.min.css', 'public/vendors/css/pickers/flatpickr/flatpickr.min.css')
    .copy('resources/vendors/css/vendors.min.css', 'public/vendors/css/vendors.min.css')
    .sass('resources/scss/core.scss', 'public/css/core.css', {sassOptions})
    .sass('resources/scss/overrides.scss', 'public/css/overrides.css', {sassOptions})
    .sass('resources/scss/style.scss', 'public/css/style.css', {sassOptions})
    .sass('resources/scss/base/core/menu/menu-types/vertical-menu.scss', 'public/css/base/core/menu/menu-types/vertical-menu.css', {sassOptions})
    .sass('resources/scss/base/plugins/forms/pickers/form-flat-pickr.scss', 'public/css/base/plugins/forms/pickers/form-flat-pickr.css', {sassOptions})
    .sass('resources/scss/base/pages/authentication.scss', 'public/css/base/pages/authentication.css', {sassOptions})
    .sass('resources/scss/base/pages/dashboard-ecommerce.scss', 'public/css/base/pages/dashboard-ecommerce.css', {sassOptions})
    .sass('resources/scss/base/pages/app-invoice-list.scss', 'public/css/base/pages/app-invoice-list.css', {sassOptions})
    .sass('resources/scss/base/plugins/charts/chart-apex.scss', 'public/css/base/plugins/charts/chart-apex.css', {sassOptions})
    .sass('resources/scss/base/plugins/forms/form-quill-editor.scss', 'public/css/base/plugins/forms/form-quill-editor.css', {sassOptions})
    .sass('resources/scss/base/plugins/forms/form-validation.scss', 'public/css/base/plugins/forms/form-validation.css', {sassOptions})
    .sass('resources/scss/base/plugins/extensions/ext-component-toastr.scss', 'public/css/base/plugins/extensions/ext-component-toastr.css', {sassOptions})
    .sass('resources/scss/base/themes/bordered-layout.scss', 'public/css/base/themes/bordered-layout.css', {sassOptions})
    .sass('resources/scss/base/themes/dark-layout.scss', 'public/css/base/themes/dark-layout.css', {sassOptions})
    .sass('resources/scss/base/themes/semi-dark-layout.scss', 'public/css/base/themes/semi-dark-layout.css', {sassOptions})
    .sass('resources/scss/quill.scss', 'public/css/quill.css', {sassOptions});

// JS
mix
    .copy('resources/vendors/js/vendors.min.js', 'public/vendors/js/vendors.min.js')
    .copy('resources/vendors/js/charts/apexcharts.min.js', 'public/vendors/js/charts/apexcharts.min.js')
    .copy('resources/vendors/js/charts/chart.min.js', 'public/vendors/js/charts/chart.min.js')
    .copy('resources/vendors/js/editors/quill/katex.min.js', 'public/vendors/js/editors/quill/katex.min.js')
    .copy('resources/vendors/js/editors/quill/highlight.min.js', 'public/vendors/js/editors/quill/highlight.min.js')
    .copy('resources/vendors/js/editors/quill/quill.min.js', 'public/vendors/js/editors/quill/quill.min.js')
    .copy('resources/vendors/js/extensions/toastr.min.js', 'public/vendors/js/extensions/toastr.min.js')
    .copy('resources/vendors/js/forms/select/select2.full.min.js', 'public/vendors/js/forms/select/select2.full.min.js')
    .copy('resources/vendors/js/forms/validation/jquery.validate.min.js', 'public/vendors/js/forms/validation/jquery.validate.min.js')
    .copy('resources/vendors/js/pickers/flatpickr/flatpickr.min.js', 'public/vendors/js/pickers/flatpickr/flatpickr.min.js')
    .copy('resources/vendors/js/tables/datatable/dataTables.bootstrap5.min.js', 'public/vendors/js/tables/datatable/dataTables.bootstrap5.min.js')
    .copy('resources/vendors/js/tables/datatable/datatables.buttons.min.js', 'public/vendors/js/tables/datatable/datatables.buttons.min.js')
    .copy('resources/vendors/js/tables/datatable/dataTables.responsive.min.js', 'public/vendors/js/tables/datatable/dataTables.responsive.min.js')
    .copy('resources/vendors/js/tables/datatable/jquery.dataTables.min.js', 'public/vendors/js/tables/datatable/jquery.dataTables.min.js')
    .copy('resources/js/chartjs-plugin-datalabels.min.js', 'public/js/chartjs-plugin-datalabels.min.js')
    .js('resources/js/core/app.js', 'public/js/core/app.js')
    .js('resources/js/core/app-menu.js', 'public/js/core/app-menu.js')
    .js('resources/js/core/scripts.js', 'public/js/core/scripts.js')
    .js('resources/js/scripts/customizer.js', 'public/js/scripts/customizer.js')
    .js('resources/js/scripts/forms/form-select2.js', 'public/js/scripts/forms/form-select2.js')
    .js('resources/js/scripts/forms/pickers/form-pickers.js', 'public/js/scripts/forms/pickers/form-pickers.js')
    .js('resources/js/scripts/pages/auth-login.js', 'public/js/scripts/pages/auth-login.js')
    .js('resources/vendors/js/ui/jquery.sticky.js', 'public/vendors/js/ui/jquery.sticky.js')
    // models
    .js('resources/js/models/currencies/index/app.js', 'public/js/models/currencies/index/app.js').vue()
    .js('resources/js/models/dpas/index/app.js', 'public/js/models/dpas/index/app.js').vue()
    .js('resources/js/models/dpas/edit/app.js', 'public/js/models/dpas/edit/app.js').vue()
    .js('resources/js/models/kpis/index/app.js', 'public/js/models/kpis/index/app.js').vue()
    .js('resources/js/models/organisations/act/app.js', 'public/js/models/organisations/act/app.js').vue()
    .js('resources/js/models/organisations/plan/auditor/app.js', 'public/js/models/organisations/plan/auditor/app.js').vue()
    .js('resources/js/models/organisations/dashboard/app.js', 'public/js/models/organisations/dashboard/app.js').vue()
    .js('resources/js/models/organisations/do/app.js', 'public/js/models/organisations/do/app.js').vue()
    .js('resources/js/models/organisations/kpis/app.js', 'public/js/models/organisations/kpis/app.js').vue()
    .js('resources/js/models/organisations/plan/app.js', 'public/js/models/organisations/plan/app.js').vue()
    .js('resources/js/models/organisations/review/app.js', 'public/js/models/organisations/review/app.js').vue()
    .js('resources/js/models/risks/create/app.js', 'public/js/models/risks/create/app.js').vue()
    .js('resources/js/models/risks/edit/app.js', 'public/js/models/risks/edit/app.js').vue()
    .js('resources/js/models/risks/index/app.js', 'public/js/models/risks/index/app.js').vue()
    .js('resources/js/models/sanctions/index/app.js', 'public/js/models/sanctions/index/app.js').vue()
    .js('resources/js/models/sanctions/edit/app.js', 'public/js/models/sanctions/edit/app.js').vue()
    .js('resources/js/models/tags/index/app.js', 'public/js/models/tags/index/app.js').vue()
    // services
    .js('resources/js/services/countries/seed/app.js', 'public/js/services/countries/seed/app.js').vue()

// Js validation
mix
    .copy('resources/views/vendor/jsvalidation', 'public/resources/views/vendor/jsvalidation')
    .copy('vendor/proengsoft/laravel-jsvalidation/resources/views', 'resources/views/vendor/jsvalidation')
    .copy('vendor/proengsoft/laravel-jsvalidation/public', 'public/vendor/jsvalidation')
    .copy('resources/views/vendor/jsvalidation', 'public/resources/views/vendor/jsvalidation');

// Cache versioning
if (mix.inProduction()) {
    mix.version();
}
