<script>
    export default {
        methods: {
            submitForm() {
                this.$loading.show();
                this.$refs.form.submit();
            }
        },
        mounted() {
            $(this.$refs.select2_location).select2();
        }
    }
</script>
