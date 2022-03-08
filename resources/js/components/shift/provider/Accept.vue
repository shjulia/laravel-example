<script>
    import Swal from 'sweetalert2'
    export default {
        data() {
            return {
                isAccepted: false,
                isMultipleDeclined: false
            }
        },
        props: [
            'acceptRoute',
            'successRedirectRoute',
            'shift',
            'freeChildrenAmount',
            'firstChild',
            'viewInviteUrl'
        ],
        methods: {
            accept() {
                if (this.shift.multi_days) {
                    Swal.fire({
                        title: "Are you sure that you will work " + this.freeChildrenAmount + " days?",
                        type: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#19F4A5',
                        cancelButtonColor: '#fd3030',
                        confirmButtonText: 'Yes, accept',
                        cancelButtonText: 'No, decline'
                    }).then((result) => {
                        if (result.value) {
                            this.setLeavingInfo(this.firstChild);
                        } else {
                            this.declineMultiple();
                        }
                    });
                } else  {
                    this.setLeavingInfo(this.shift);
                }
            },
            setLeavingInfo(shift) {
                if (shift.date == moment().format('YYYY-MM-DD') && shift.from_time <= moment().add(1, 'hour').format('HH:mm')) {
                    this.isAccepted = true;
                } else {
                    this.leaving('waiting')
                }
            },
            leaving(answer) {
                let loader = this.$loading.show();
                axios.post(this.acceptRoute, {
                    param: {
                        'answer': answer
                    }
                }).then(response => {
                    if(response.data == 'Success') {
                        window.location.replace(this.successRedirectRoute)
                    }
                });
            },
            declineMultiple() {
                this.isMultipleDeclined = true;
            }
        },
        computed: {
        },
        mounted() {
            axios.post(this.viewInviteUrl)
                .then(response => {})
                .catch(error => {});
        }
    }
</script>
