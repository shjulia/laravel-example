<script>
    import Swal from 'sweetalert2'
    import {ServerErrors} from '../mixins/ServerErrors'
    import ClipboardJS from 'clipboard'

    export default {
        props:[
            'inviteAction',
            'referredAmount',
            'invitesCountInit',
            'codeInit',
            'baseInviteUrl',
            'changeCodeAction'
        ],
        data() {
            return {
                copyButtonText: 'Copy Link',
                email: '',
                showInvite: false,
                invitesCount: this.invitesCountInit,
                code: this.codeInit
            }
        },
        mixins:[ServerErrors],
        computed: {
            inviteUrl() {
                return this.baseInviteUrl + '/' + this.code;
            }
        },
        methods: {
            copy() {
                /*let copyText = document.getElementById("link");
                copyText.select();
                document.execCommand("copy");
                window.getSelection().removeAllRanges();*/
                this.copyButtonText = 'Copied';
                setTimeout(() => {
                    this.copyButtonText = 'Copy Link';
                    window.getSelection().removeAllRanges();
                    }, 1500);
            },
            emailClick() {
                this.showInvite = !this.showInvite;
            },
            invite() {
                let loader = this.$loading.show();
                this.server_errors = {};
                axios({
                    method: 'POST',
                    data: {
                        'email': this.email,
                    },
                    url: this.inviteAction
                })
                    .then(response => {
                        this.email = '';
                        this.showInvite = false;
                        this.invitesCount = this.invitesCount +1;
                        Swal.fire({
                            title: 'Success!',
                            text: 'Invite was sent successfully',
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
            },
            openChange() {
                $('#changeModal').modal('show');
            },
            changeCode() {
                let loader = this.$loading.show();
                this.server_errors = {};
                axios({
                    method: 'POST',
                    data: {
                        'code': this.code,
                    },
                    url: this.changeCodeAction
                })
                    .then(response => {
                        $('#changeModal').modal('hide');
                        Swal.fire({
                            title: 'Success!',
                            text: 'Code changed successfully',
                            type: 'success'
                        });
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error ? error.response.data.error : 'Change error'
                        });
                    })
                    .finally(response => {
                        loader.hide();
                    });
            }
        },
        mounted() {
            $('#link').tooltip();
            new ClipboardJS('.btn-copy');
        }
    }
</script>
