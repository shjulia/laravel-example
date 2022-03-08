import Swal from 'sweetalert2'

export const CancelMixin = {
    props: [
        'cancelAction',
        'indexUrl'
    ],
    data() {
        return {
            cancel_reason: '',
            reason_text: '',
            reason_error: null,
            is_rematch: false
        }
    },
    methods: {
        canCancel() {
            let now = moment().format('YYYY-MM-DD HH:mm:ss');
            return (this.shift.date + ' ' + this.shift.from_time + ':00') > now;
        },
        checkTimeFee() {
            let createdP10 = moment(this.shift.created_at).add(10, 'minutes').format('YYYY-MM-DD HH:mm:ss');
            if (createdP10 > moment().utc().format('YYYY-MM-DD HH:mm:ss')) {
                return false;
            }
            let nowP24 = moment().add(24, 'hours').format('YYYY-MM-DD HH:mm:ss');

            if (nowP24 < (this.shift.date + ' ' + this.shift.from_time + ':00')) {
                return false;
            }
            return true;
        },
        cancel() {
            if (!this.canCancel()) {
                return;
            }
            if (!this.checkTimeFee()) {
                this.reason();
                return;
            }
            Swal.fire({
                title: 'Are you sure you want to cancel?',
                text: 'Since we have already matched a Provider to fill your shift, a $50 cancellation fee will be charged to your account. Are you sure you would like to proceed?',
                type: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#989898',
                cancelButtonColor: '#b35300',
                confirmButtonText: 'Yes, Cancel the Shift',
                cancelButtonText: 'I’ll Keep the Shift'
            }).then((result) => {
                if (result.value) {
                    this.reason();
                }
            });
        },
        reason() {
            $('#reasonModal').modal('show');
        },
        reasonSubmit() {
            if (!this.cancel_reason && !this.reason_text) {
                this.reason_error = "Reason must be set.";
                return;
            }
            $('#reasonModal').modal('hide');
            this.cancelShift(this.reason_text ? this.reason_text : this.cancel_reason);
        },
        cancelShift(reason_text) {
            let loader = this.$loading.show();
            axios({
                method: 'POST',
                url: this.cancelAction,
                data: {
                    reason: reason_text,
                    is_rematch: this.is_rematch
                }
            })
                .then(response => {
                    location.href = this.indexUrl;
                })
                .catch(error => {
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: error.response.data.error
                    });
                })
                .finally(response => {
                    loader.hide();
                });
        }
    }
};
