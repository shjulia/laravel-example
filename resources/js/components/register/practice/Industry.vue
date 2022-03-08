<script>
    export default {
        methods: {
            submitForm() {
                this.$loading.show();
                this.$refs.form.submit();
            }
        },
        mounted() {
            $(this.$refs.select2_industry).select2({
                placeholder: '',
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }
    }
</script>
