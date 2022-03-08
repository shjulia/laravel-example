<script>

    export default {
        props: [
        ],
        data() {
            return {
                accept: false,
                showAcceptError: false,
                acceptText: ''
            };
        },
        methods: {
            submit(event) {
                if (!this.accept) {
                    event.stopPropagation();
                    event.preventDefault();
                    this.showAcceptError = true;
                } else {
                    this.$loading.show();
                }
            }
        }
    }
</script>
