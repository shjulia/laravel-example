<script>
    import Swal from 'sweetalert2'

    export default {
        props: [
          'reinviteAction'
        ],
        methods: {
            reinvite(inviteId) {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    data: {
                        'invite': inviteId,
                    },
                    url: (this.reinviteAction).replace(/_invite_/gi, inviteId)
                })
                    .then(response => {
                        Swal.fire({
                            title: 'Success!',
                            text: 'Invite was resent successfully',
                            type: 'success'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error ? error.response.data.error : 'Sending error'
                        });
                    })
                    .finally(response => {
                        loader.hide();
                    });
            }
        }
    }
</script>