<script>
    export default {
        props: [
            'providerUrl',
            'practiceUrl',
            'partnerUrl',
            'initType',
            'autoSaveAction',
            'termsUrl'
        ],
        data() {
            return {
                type: this.initType,
                accept: false,
                showUserTypeError: false,
                showTermError: false,
                providerCheckbox: this.initType === 'provider',
                practiceCheckbox: this.initType === 'practice',
                partnerCheckbox: this.initType === 'partner',
                email: '',
                first_name: '',
                last_name: '',
                termsHtml: '',
                lat: '',
                lng: ''
            };
        },
        methods: {
            isProvider() {
                return this.type === 'provider';
            },
            isPractice() {
                return this.type === 'practice';
            },
            selectUserType(type) {
                if (type === this.type) {
                    this.type = '';
                    this.providerCheckbox = false;
                    this.practiceCheckbox = false;
                    return;
                }
                this.type = type;
                if (this.isProvider()) {
                    this.providerCheckbox = true;
                    this.practiceCheckbox = false;
                } else if(this.isPractice()) {
                    this.providerCheckbox = false;
                    this.practiceCheckbox = true;
                }
                this.showUserTypeError = false;
            },
            submit(event) {
                if (!this.type || !this.accept) {
                    event.stopPropagation();
                    event.preventDefault();
                    this.showUserTypeError = !this.type;
                    this.showTermError = !this.accept;
                } else {
                    this.$loading.show();
                }
            },
            blurEmail() {
                let re = /\S+@\S+\.\S+/;
                if (!re.test(this.email)) {
                    return;
                }
                axios({
                    method: 'POST',
                    url: this.autoSaveAction,
                    data: { 'email': this.email, 'first_name': this.first_name, 'last_name': this.last_name }
                })
                    .then(response => {})
                    .catch(response => {});
            },
            getPositions(position) {
                this.lat = position.coords.latitude;
                this.lng = position.coords.longitude;
            },
        },
        computed: {
            action() {
                if (this.providerCheckbox) {
                    return this.providerUrl;
                } else if (this.practiceCheckbox) {
                    return this.practiceUrl;
                } else if (this.partnerCheckbox) {
                    return this.partnerUrl;
                }
                return '#';
            }
        },
        mounted() {
            this.$nextTick(() => {
                $('#termsmodal').popover();
            });
            axios({
                method: 'GET',
                url: this.termsUrl,
            })
                .then(response => {
                    this.termsHtml = response.data;
                })
                .catch(response => {});

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getPositions);
            }
        }
    }
</script>
