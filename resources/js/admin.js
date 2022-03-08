require('./bootstrap');
window.Vue = require('vue');

window.$ = window.jQuery = require('jquery');
require('summernote/dist/summernote-bs4');

require('./pages/admin/users/users');

import Loading from 'vue-loading-overlay';
import { VueMaskDirective } from 'v-mask'
import * as VueGoogleMaps from 'vue2-google-maps';
import VueGoogleCharts from 'vue-google-charts'
import AvatarCropper from "vue-avatar-cropper"
import vSelect from 'vue-select'
import "vue-select/src/scss/vue-select.scss";

Vue.use(Loading);
Vue.directive('mask', VueMaskDirective);
Vue.use(VueGoogleCharts);

Vue.component('user-component', require('./components/general/UserComponent').default);
Vue.component('Cinput', require('./components/general/Cinput').default);
Vue.component('license-types', require('./components/admin/license-type/LicenseTypes').default);
Vue.component('rate', require('./components/admin/rates/Rate').default);
Vue.component('areas', require('./components/data/Areas').default);
Vue.component('regions', require('./components/data/Regions').default);
Vue.component('notifications', require('./components/notifications/Notifications').default);
Vue.component('signups-areas-map', require('./components/admin/analytics/SignupsAreasMap').default);
Vue.component('signups-map', require('./components/admin/analytics/SignupsMap').default);
Vue.component('profit', require('./components/admin/analytics/Profit').default);
Vue.component('available', require('./components/admin/analytics/Available').default);
Vue.component('providers-positions', require('./components/admin/analytics/ProvidersPositions').default);
Vue.component('providers-revenue', require('./components/admin/analytics/Revenue').default);
Vue.component('analytics-time', require('./components/admin/analytics/AnalyticsTime').default);
Vue.component('worked', require('./components/admin/analytics/Worked').default);
Vue.component('worked-per-day', require('./components/admin/analytics/WorkedPerDay').default);
Vue.component('total-number', require('./components/admin/analytics/TotalNumber').default);
Vue.component('top-list', require('./components/admin/analytics/TopList').default);
Vue.component('cancell-anlytics', require('./components/admin/analytics/CancellAnalytics').default);
Vue.component('email-manager', require('./components/admin/mail/EmailManager').default);
Vue.component('register-step3', require('./components/register/provider/Step3').default);
Vue.component('register-step4', require('./components/register/provider/Step4').default);
Vue.component('practice-info', require('./components/register/practice/PracticeInfo').default);
Vue.component('insurance', require('./components/register/practice/Insurance').default);
Vue.component('account-details', require('./components/register/provider/Details/Detail').default);
Vue.component('pbase-details', require('./components/register/practice/details/BaseDetails').default);
Vue.component('team', require('./components/register/practice/Team').default);
Vue.component('provider-edit', require('./components/admin/users/ProviderEdit').default);
Vue.component('shift-invite', require('./components/admin/shifts/Invite').default);
Vue.component('shifttime', require('./components/shift/Time').default);
Vue.component('tracking-map', require('./components/admin/shifts/TrackingMap').default);
Vue.component('email-create', require('./components/admin/mail/EmailCreate').default);
Vue.component('newsletter-form', require('./components/admin/mail/NewsLetterForm').default);

Vue.component('avatar-cropper', AvatarCropper);
Vue.component('v-select', vSelect)

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyC4NUSy6uSiyoaCejEHIpsHEN8pIZf5V0g',
        libraries: 'places,drawing,visualization',
    },
});

Vue.directive('select2', {
    inserted(el) {
        $(el).on('select2:select', () => {
            const event = new Event('change', { bubbles: true, cancelable: true });
            el.dispatchEvent(event);
        });

        $(el).on('select2:unselect', () => {
            const event = new Event('change', {bubbles: true, cancelable: true});
            el.dispatchEvent(event);
        })
    },
    componentUpdated(el, q, w) {
        $(el).trigger('change');
    }
});

const admin = new Vue({
    el: '#app',
    mounted() {
    }
});

$(document).ready(function() {
    $('#summernote').summernote({
        height: 600
    });
    $(".select2m").each(function() {
        $(this).select2();
    });
});
