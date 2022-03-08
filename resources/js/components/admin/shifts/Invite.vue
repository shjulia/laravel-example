<script>
    import Swal from 'sweetalert2';
    export default {
        props: [
            'checkUrl'
        ],
        data () {
            return {
                providerId: null
            }
        },
        methods: {
            invite(id) {
                let loader = this.$loading.show();
                axios.post(this.checkUrl, {
                    provider_id: this.providerId
                }).then(response => {
                    console.log(response.data);
                    if (response.data) {
                        this.$refs.form.submit();
                    } else {
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "Provider min rate is more than shift rate",
                            icon: 'warning',
                            showCancelButton: true
                        }).then((result) => {
                            if (result.value) {
                                this.$refs.form.submit();
                            }
                        })
                    }
                }).catch(error => {
                    Swal.fire({
                        title: 'Error',
                        text: error.response.error
                    });
                }).finally(() => {
                    loader.hide();
                });
            },
        },
    }
</script>
