<script>
    export default {
        props: [
            'providerUrl',
            'practiceUrl',
            'partnerUrl'
        ],
        data() {
            return {
                type: this.initType,
                showUserTypeError: false,
                providerCheckbox: false,
                practiceCheckbox: false,
                partnerCheckbox: false
            };
        },
        methods: {
            isProvider() {
                return this.type === 'provider';
            },
            isPractice() {
                return this.type === 'practice';
            },
            isPartner() {
                return this.type === 'partner';
            },
            selectUserType(type) {
                if (type === this.type) {
                    this.type = '';
                    this.providerCheckbox = false;
                    this.practiceCheckbox = false;
                    this.partnerCheckbox = false;
                    return;
                }
                this.type = type;
                if (this.isProvider()) {
                    this.providerCheckbox = true;
                    this.practiceCheckbox = false;
                    this.partnerCheckbox = false;
                } else if(this.isPractice()) {
                    this.providerCheckbox = false;
                    this.practiceCheckbox = true;
                    this.partnerCheckbox = false;
                } else if(this.isPartner()) {
                    this.providerCheckbox = false;
                    this.practiceCheckbox = false;
                    this.partnerCheckbox = true;
                }
                this.showUserTypeError = false;
            },
            submit(event) {
                if (!this.type) {
                    event.stopPropagation();
                    event.preventDefault();
                    this.showUserTypeError = !this.type;
                    this.showTermError = !this.accept;
                } else {
                    this.$loading.show();
                }
            }
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
        }
    }
</script>
