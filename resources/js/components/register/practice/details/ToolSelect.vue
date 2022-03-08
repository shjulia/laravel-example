<script>
    export default {
        props: [
            'tools',
            'initActive'
        ],
        data() {
            return {
                active: this.initActive,
                showOther: false
            };
        },
        methods: {
            setActive(id) {
                this.active = id;
            },
            setOther() {
                this.setActive('other');
                this.showOther = true;
            }
        }
    }
</script>
