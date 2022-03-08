<script>
    import RateRow from './RateRow'

    export default {
        props: [
            'old',
            'errors',
            'positions',
            'states',
            'initRowsCount'
        ],
        data () {
            return {
                rowsCount: this.initRowsCount,
                values: this.old
            }
        },
        components: {
            'rate-row': RateRow
        },
        methods: {
            add() {
                this.rowsCount++;
            },
            val(field, i) {
                if (this.values.hasOwnProperty(field) && this.values[field][i]) {
                    return this.values[field][i];
                }
                return '';
            },
            isHasError(field) {
                return this.errors.hasOwnProperty(field);
            },
            getFirstError(field) {
                return this.isHasError(field) ? this.errors[field][0] : '';
            }
        }
    }
</script>
