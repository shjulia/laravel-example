<script>
    import VueSlider from 'vue-slider-component'

    export default {
        props: [
            'minInit',
            'maxInit'
        ],
        data() {
            return {
                lengthVal: [this.minInit, this.maxInit]
            };
        },
        components: {
            VueSlider
        },
        computed: {
            minVal() {
                return this.lengthVal[0];
            },
            maxVal() {
                return this.lengthVal[1];
            }
        }
    }
</script>
