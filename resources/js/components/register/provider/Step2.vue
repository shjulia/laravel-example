<script>
    export default {
        props: [
            'user',
            'industries',
            'positions'
        ],
        data() {
            return {
                industry: this.user.specialist.industry_id,
                position: this.user.specialist.position_id,
                active: null,
                activeParentPositions: []
            };
        },
        computed: {
            filteredPositions() {
                return this.positions.filter((position) => {
                   return position.industry_id == this.industry;
                });
            }
        },
        mounted() {
            $(this.$refs.select2_industry).select2({
                placeholder: '',
                minimumResultsForSearch: -1,
                width: '100%'
            });
            $(this.$refs.select2_position).select2({
                placeholder: '',
                minimumResultsForSearch: -1,
                width: '100%'
            });
            if (this.user.specialist.position_id) {
                this.setActive(this.user.specialist.position_id);
            }
            if (process.env.MIX_ALLOW_FB_TRACK == 1) {
                this.logProvider_signup_startEvent(this.user.email, true);
            }
        },
        methods: {
            setActive(id) {
                this.active = id;
            },
            logProvider_signup_startEvent(email, valToSum) {
                let params = {};
                params['email'] = email;
                FB.AppEvents.logEvent('provider_signup_start', valToSum, params);
            },
            isHasChildren(pos) {
                return Object.keys(pos.children).length > 0;
            },
            displayChildren(id) {
                if (this.activeParentPositions.indexOf(id) === -1) {
                    this.activeParentPositions.push(id);
                }
            },
            isDisplayChildren(id) {
                return this.activeParentPositions.indexOf(id) !== -1;
            }
        }
    }
</script>
