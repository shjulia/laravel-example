<script>
    import Swal from "sweetalert2";
    export default {
        props: [
            'checkUrl'
        ],
        data() {
            return {
                declineAction: '#',
                approval_reason: ''
            };
        },
        methods: {
            declineModal(link) {
                $("#declineModal").modal('show');
                this.declineAction = link;
            },
            approve() {
                let loader = this.$loading.show();
                axios.post(this.checkUrl, {})
                    .then(response => {
                        console.log(response.data);
                        if (response.data) {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: '',
                                type: 'warning',
                                showCancelButton: true,
                                confirmButtonColor: '#3085d6',
                                cancelButtonColor: '#d33',
                                confirmButtonText: 'Yes'
                            }).then((result) => {
                                if (result.value) {
                                    this.$refs.approveform.submit();
                                }
                            });
                        } else {
                            Swal.fire({
                                title: 'Are you sure?',
                                text: "Provider data doesn\'t match approval status",
                                icon: 'warning',
                                showCancelButton: true
                            }).then((result) => {
                                if (result.value) {
                                    //this.$refs.approveform.submit();
                                    this.approveHowever();
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
            approveHowever() {
                Swal.fire({
                    title: 'Input approval reason',
                    input: 'text',
                    inputValidator: (value) => {
                        if (!value) {
                            return 'You need to write approval reason'
                        }
                    }
                }).then((result) => {
                    if (result.value) {
                        this.approval_reason = result.value;
                        this.$nextTick(() => {
                            this.$refs.approveform.submit();
                        });
                    }
                });
            }
        }
    }
</script>
