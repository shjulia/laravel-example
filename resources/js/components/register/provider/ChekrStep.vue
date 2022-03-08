<script>
    import Swal from 'sweetalert2'
    import {ServerErrors} from '../../mixins/ServerErrors';
    import {AutocompleteMixin} from '../../mixins/AutocompleteMixin';

    export default {
        props: [
            'specialist',
            'editUrl',
            'acceptInit',
            'user',
            'acceptTextUrl'
        ],
        data() {
            return {
                accept: !!this.acceptInit,
                showAcceptError: false,
                first_name_start: this.specialist.driver_first_name,
                last_name_start: this.specialist.driver_last_name,
                middle_name_start: this.specialist.driver_middle_name,
                has_middle_name_start: !this.specialist.driver_middle_name,
                first_name: this.specialist.driver_first_name,
                last_name: this.specialist.driver_last_name,
                middle_name: this.specialist.driver_middle_name,
                has_middle_name: !this.specialist.driver_middle_name,
                address_start: this.specialist.driver_address,
                city_start: this.specialist.driver_city,
                state_start: this.specialist.driver_state,
                zip_start: this.specialist.driver_zip,
                dob_start: this.specialist.dob,
                dob: this.specialist.dob,
                phone_start: this.user.phone,
                address: this.specialist.driver_address,
                city: this.specialist.driver_city,
                state: this.specialist.driver_state,
                zip: this.specialist.driver_zip,
                phone: this.user.phone,
                acceptText: ''
            };
        },
        computed: {
            getName() {
                return this.first_name_start
                    + (!this.has_middle_name_start ? ' ' + (this.middle_name_start ? this.middle_name_start : '') + ' ' : ' <b>No Middle Name</b> ')
                    + this.last_name_start;
            },
            getFullAddress() {
                return this.address_start
                    + ', ' + this.city_start
                    + ', ' + this.state_start + ' ' + this.zip_start
                    + ', USA';
            }
        },
        mixins: [ServerErrors, AutocompleteMixin],
        methods: {
            submit(event) {
                if (!this.accept) {
                    event.stopPropagation();
                    event.preventDefault();
                    this.showAcceptError = true;
                } else {
                    this.$loading.show();
                }
            },
            submitEdits() {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.editUrl,
                    data: {
                        'first_name': this.first_name,
                        'last_name': this.last_name,
                        'middle_name': !this.has_middle_name ? this.middle_name: '',
                        'address': this.address,
                        'city': this.city,
                        'zip': this.zip,
                        'state': this.state,
                        'phone': this.phone,
                        'dob': this.dob,
                        'isApi': true
                    }
                })
                    .then(response => {
                        this.first_name_start = this.first_name;
                        this.last_name_start = this.last_name;
                        this.middle_name_start = !this.has_middle_name ? this.middle_name: '';
                        this.has_middle_name_start = this.has_middle_name;
                        this.address_start = this.address;
                        this.city_start = this.city;
                        this.state_start = this.state;
                        this.zip_start = this.zip;
                        this.phone_start = this.phone;
                        this.dob_start = this.dob;
                        $("#editModal").modal("hide");
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error
                        });
                    })
                    .finally(response => {
                        loader.hide()
                    });
            },
            openAgreementModal()
            {
                Swal.fire({
                    title: '',
                    html: this.acceptText,
                    type: '',
                    showCancelButton: true,
                    confirmButtonText: 'Agree',
                    cancelButtonText: 'Cancel',
                    confirmButtonColor: '#61f5a4',
                    cancelButtonColor: '#999',
                    reverseButtons: true,
                    showCloseButton: true,
                    width: '80%',
                }).then((result) => {
                    Swal.close();
                    if (result.value) {
                        this.accept = true;
                    } else {
                        this.accept = false;
                    }
                });
            }
        },
        mounted() {
            $(this.$refs.select2_state).select2({
                placeholder: '',
                width: '100%'
            });
            axios({
                method: 'GET',
                url: this.acceptTextUrl,
            })
                .then(response => {
                    this.acceptText = response.data;
                })
                .catch(response => {});
        }
    }
</script>
