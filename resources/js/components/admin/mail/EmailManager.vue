<script>
    import Swal from 'sweetalert2';

    export default {
        data() {
            return {
                emails: []
            }
        },
        props: [
            'userEmails',
            'tz'
        ],
        methods: {
            resend(id) {
                let loader = this.$loading.show();
                axios.post('/admin/users/resend-message', {
                    id: id
                }).then(response => {
                    this.showMessage('Success', response.data, 'success');
                    loader.hide();
                }).catch(error => {
                    this.showMessage('Error', error.message, 'error');
                    loader.hide();
                });
            },
            showMessage(title, text, type) {

            },
            formatedTime(time) {
                return moment.tz(time, 'UTC').clone().tz(this.tz).format('HH:mm:ss YYYY-MM-DD');
            },
            show(email) {
                Swal.fire({
                    title: email.subject,
                    html: email.data,
                    width: '70%',
                    showCloseButton: true,
                    confirmButtonText: 'Close'
                });
            }
        },
        created() {
            this.emails = JSON.parse(this.userEmails);
        }
    }
</script>
