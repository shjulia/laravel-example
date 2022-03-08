<script>
    import Swal from 'sweetalert2'
    export default {
        props: [
            'settasksInit',
            'hasSameTime',
            'previous'
        ],
        data () {
            return {
                settasks: this.settasksInit
            }
        },
        watch: {
            settasks() {
                if (this.settasks) {
                    this.$nextTick(() => {
                        $(this.$refs.select2).select2();
                    })
                }
            }
        },
        mounted() {
            $(this.$refs.select2).select2();
            if (this.hasSameTime) {
                const swalWithBootstrapButtons = Swal.mixin({
                    confirmButtonClass: 'btn btn-yes',
                    cancelButtonClass: 'btn btn-no',
                    buttonsStyling: false,
                })
                swalWithBootstrapButtons.fire({
                    title: '',
                    text: "You already have a Provider scheduled for that time." + " Are you sure you’d like to proceed?",
                    type: '',
                    showCancelButton: true,
                    confirmButtonText: 'Yes',
                    cancelButtonText: 'No',
                    reverseButtons: true
                }).then((result) => {
                    if (result.value) {
                        Swal.close();
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        location.href = this.previous;
                    }
                })
            }
        }
    }
</script>
