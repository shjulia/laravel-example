<script>
    export default {
        props: [
            'type',
            'email'
        ],
        mounted() {
            if (!process.env.MIX_ALLOW_FB_TRACK == 1) {
                return;
            }
            if (this.type === "provider") {
                this.logProvider_signup_finishEvent(this.email, true);
            }
            if (this.type === "practice") {
                this.logPractice_signup_finishEvent(this.email, true);
            }
        },
        methods: {
            logProvider_signup_finishEvent(email, valToSum) {
                var params = {};
                params['email'] = email;
                FB.AppEvents.logEvent('provider_signup_finish', valToSum, params);
            },
            logPractice_signup_finishEvent(email, valToSum) {
                var params = {};
                params['email'] = email;
                FB.AppEvents.logEvent('practice_signup_finish', valToSum, params);
            }
        }
    }
</script>
