<script>
    import {CancelMixin} from './CancelMixin';

    export default {
        mixins: [CancelMixin],
        methods: {
            checkTimeFee() {
                return false;
            },
            canCancel() {
                return true;
            },
        }
    }
</script>
