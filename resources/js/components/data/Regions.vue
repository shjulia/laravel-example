<script>
    export default {
        props: [
            'savedStates'
        ],
        data() {
            return {
                states: this.savedStates,
            };
        },
        mounted() {
            $(this.$refs.states).select2();
        }
    }
</script>

<style scoped>

</style>