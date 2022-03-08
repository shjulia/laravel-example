<script>
    export default {
        props: [
            'user',
            'descriptions'
        ],
        data() {
            return {
                description: null,
                description_answer: '',
                description_answer2: ''
            };
        },
        methods: {
            change() {
                this.description_answer = '';
                if (this.description == 'sales') {
                    this.$nextTick(() => {
                        $(this.$refs.select2_description_answer).select2({
                            placeholder: '',
                            minimumResultsForSearch: -1,
                            width: '100%'
                        });
                    });
                } else {
                    $(this.$refs.select2_description_answer).select2('destroy');
                }
            }
        },
        mounted() {
            $(this.$refs.select2_description).select2({
                placeholder: '',
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }
    }
</script>