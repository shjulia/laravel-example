<script>
    export default {
        props: [
            'initType',
            'infoSet'
        ],
        data() {
            return {
                type: this.initType,
                standardCheckbox: this.initType === 'standard',
                expeditedCheckbox: this.initType === 'expedited',
                showBankFields: false
            };
        },
        methods: {
            isStandard() {
                return this.type === 'standard';
            },
            isExpedited() {
                return this.type === 'expedited';
            },
            selectType(type) {
                if (type === this.type) {
                    this.type = '';
                    this.standardCheckbox = false;
                    this.expeditedCheckbox = false;
                    return;
                }
                this.type = type;
                if (this.isStandard()) {
                    this.standardCheckbox = true;
                    this.expeditedCheckbox = false;
                } else if(this.isExpedited()) {
                    this.standardCheckbox = false;
                    this.expeditedCheckbox = true;
                }
            },
        }
    }
</script>
