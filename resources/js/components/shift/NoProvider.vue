<script>
    import Swal from "sweetalert2";
    import {CancelMixin} from './CancelMixin';
    export default {
        props: [
          'findNewAction',
          'resultAction',
        ],
        data () {
            return {
                is_rematch: true
            }
        },
        mixins: [CancelMixin],
        methods: {
            canCancel() {
                return true;
            },
            checkTimeFee() {
                return false;
            },
            noProviderModalShow() {
                $('#noProviderModal').modal('show');
            },
            findNewProvider() {
                console.log('findNewAction', this.findNewAction);
                console.log('this.resultAction', this.resultAction);
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.findNewAction,
                })
                    .then(response => {
                        location.href = this.resultAction;
                    })
                    .catch(error => {
                        console.log('error', error);
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error
                        });
                    })
                    .finally(response => {
                        loader.hide();
                    });
            },
            showButton() {
                let now = moment();
                let shift = this.$parent.shift;
                let start = moment(shift.date + ' ' + shift.from_time);
                let diff = moment.duration(start.diff(now));
                return diff._data.days <= 0 && diff._data.hours <= 0 && diff._data.minutes <= 15 && !shift.completed;
            }
        },
    }
</script>
