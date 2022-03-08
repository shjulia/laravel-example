<script>
    import VueSlider from 'vue-slider-component'

    export default {
        props: [
            'distanceInit',
            'durationInit',
            'initType',
        ],
        data() {
            return {
                type: this.initType,
                durationCheckbox: this.initType === 'duration',
                distanceCheckbox: this.initType === 'distance',
                distance: this.distanceInit,
                duration: this.durationInit,
                durationMarks: {60: {label: '60',}},
                distanceMarks: {73: {label: '73',}}
            };
        },
        components: {
            VueSlider
        },
        computed: {

        },
        methods: {
            isDuration() {
                return this.type === 'duration';
            },
            isDistance() {
                return this.type === 'distance';
            },
            selectType(type) {
                if (type === this.type) {
                    this.type = '';
                    this.durationCheckbox = false;
                    this.distanceCheckbox = false;
                    return;
                }
                this.type = type;
                if (this.isDuration()) {
                    this.durationCheckbox = true;
                    this.distanceCheckbox = false;
                } else if(this.isDistance()) {
                    this.durationCheckbox = false;
                    this.distanceCheckbox = true;
                }
            },
        }
    }
</script>
