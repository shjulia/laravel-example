<script>
    import {CancelMixin} from './CancelMixin';
    export default {
        props: [
            'shift',
        ],
        data () {
            return {
                zoom: 14
            }
        },
        mixins: [CancelMixin],
        methods: {
        },
        computed: {
            findCenter() {
                return {lat: this.shift.practice.lat, lng: this.shift.practice.lng};
            }
        },
    }
</script>
