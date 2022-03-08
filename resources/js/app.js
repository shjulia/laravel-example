require('./bootstrap');

window.Vue = require('vue');
window.io = require('socket.io-client');

import Loading from 'vue-loading-overlay';
import { VueMaskDirective } from 'v-mask'
import SocialSharing from 'vue-social-sharing';
import AvatarCropper from "vue-avatar-cropper"
import * as VueGoogleMaps from 'vue2-google-maps';


Vue.use(Loading);
Vue.use(SocialSharing);
Vue.directive('mask', VueMaskDirective);

Vue.component('account-details', require('./components/register/provider/Details/Detail').default);
Vue.component('pbase-details', require('./components/register/practice/details/BaseDetails').default);
Vue.component('team', require('./components/register/practice/Team').default);
Vue.component('locations', require('./components/register/practice/details/Locations').default);
Vue.component('tool-select', require('./components/register/practice/details/ToolSelect').default);
Vue.component('billing', require('./components/register/practice/Billing').default);
Vue.component('user-component', require('./components/general/UserComponent').default);
Vue.component('register-step3', require('./components/register/provider/Step3').default);
Vue.component('register-step4', require('./components/register/provider/Step4').default);
Vue.component('provider', require('./components/general/Provider').default);
Vue.component('practice', require('./components/general/Practice').default);
Vue.component('Cinput', require('./components/general/Cinput').default);
Vue.component('industry', require('./components/register/practice/Industry').default);
Vue.component('shifttime', require('./components/shift/Time').default);
Vue.component('shifttasks', require('./components/shift/Tasks').default);
Vue.component('cancelshift', require('./components/shift/CancelShift').default);
Vue.component('shift-location', require('./components/shift/ShiftLocation').default);
Vue.component('shiftresult', require('./components/shift/Result').default);
Vue.component('accept', require('./components/shift/provider/Accept').default);
Vue.component('shift-calendar', require('./components/shift/provider/Calendar').default);
Vue.component('review-to-provider', require('./components/shift/review/ReviewToProvider').default);
Vue.component('review-to-practice', require('./components/shift/review/ReviewToPractice').default);
Vue.component('referral', require('./components/referral/Referral').default);
Vue.component('invites', require('./components/referral/Invites').default);
Vue.component('invites', require('./components/referral/Invites').default);
Vue.component('provider-dashboard', require('./components/shift/provider/Dashboard').default);
Vue.component('provider-index', require('./components/shift/provider/Index').default);
Vue.component('search-provider-map', require('./components/shift/SearchProviderMap').default);
Vue.component('shift-details', require('./components/shift/ShiftDetails').default);
Vue.component('shift-multiple-details', require('./components/shift/ShiftMultipleDetails').default);
Vue.component('notifications', require('./components/notifications/Notifications').default);
Vue.component('shift-length', require('./components/register/provider/Onboarding/ShiftLength').default);
Vue.component('max-distance', require('./components/register/provider/Onboarding/MaxDistance').default);
Vue.component('bubble-input', require('./components/register/provider/Onboarding/BubbleInput').default);
Vue.component('holidays-availability', require('./components/register/provider/Onboarding/HolidaysAvailability').default);
Vue.component('onboarding-time-row', require('./components/register/provider/Onboarding/TimeRow').default);
Vue.component('get-paid', require('./components/register/provider/Onboarding/GetPaid').default);
Vue.component('no-provider', require('./components/shift/NoProvider').default);
Vue.component('avatar-cropper', AvatarCropper);

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

const app = new Vue({
    el: '#app',
    mounted() {

    }
});

$(document).ready(function () {
    $(".mat select").each(function() {
        if ($(this).val()) {
            upLabel($(this));
        }
    });
});
$(document).on('change', '.mat select', function () {
    if ($(this).val()) {
        upLabel($(this));
    }
});
function upLabel(elem) {
    elem.parent().find('label').addClass('label_up');
}

require('./pushNotifications/enable-push');
