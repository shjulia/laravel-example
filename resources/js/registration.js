require('./bootstrap');

window.Vue = require('vue');

import Loading from 'vue-loading-overlay';
import { VueMaskDirective } from 'v-mask'

Vue.use(Loading);
Vue.directive('mask', VueMaskDirective);

Vue.component('wellcome', require('./components/Wellcome').default);
Vue.component('Cinput', require('./components/general/Cinput').default);
Vue.component('register-step2', require('./components/register/provider/Step2').default);
Vue.component('register-step3', require('./components/register/provider/Step3').default);
Vue.component('register-step4', require('./components/register/provider/Step4').default);
Vue.component('chekr-step', require('./components/register/provider/ChekrStep').default);
Vue.component('disclosure-step', require('./components/register/provider/Checkr/Disclosure').default);
Vue.component('insurance', require('./components/register/practice/Insurance').default);
Vue.component('industry', require('./components/register/practice/Industry').default);
Vue.component('practice-info', require('./components/register/practice/PracticeInfo').default);
Vue.component('user-base', require('./components/register/UserBase').default);
Vue.component('success', require('./components/register/Success').default);
Vue.component('partner-details', require('./components/register/partner/Details').default);
Vue.component('ios', require('./components/general/Ios').default);

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
$('[data-toggle="tooltip"]').tooltip()
$('[data-toggle="popover"]').popover()
