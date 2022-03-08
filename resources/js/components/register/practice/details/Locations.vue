<script>
    import {ServerErrors} from "../../../mixins/ServerErrors";
    import Swal from 'sweetalert2'

    export default {
        props: [
            'autocompleteAction',
            'placeAction',
            'locations',
            'createAction',
            'editAction',
            'editCurrentAction',
            'practice'
        ],
        data () {
            return {
                isShowForm: false,
                names: {},
                name: '',
                address: '',
                city: '',
                state: null,
                zip: '',
                url: '',
                phone: '',
                block: false,
                justSelect: false,
                lat: null,
                lng: null,
                action: this.createAction
            }
        },
        mixins: [ServerErrors],
        methods: {
            showForm() {
                this.isShowForm = !this.isShowForm;
                this.$nextTick(() => {
                    $(this.$refs.select2_state).select2({
                        placeholder: ''
                    });
                });
            },
            create() {
                this.showForm();
                this.name = '';
                this.address = '';
                this.city = '';
                this.zip = '';
                this.state = null;
                this.url = '';
                this.phone = '';
                this.action = this.createAction;
            },
            blurName() {
                setTimeout(() => {
                    this.names = {}
                }, 1000);
            },
            edit(id) {
                let location  =_.findLast(this.locations, function(n) {
                    return n.id == id;
                });
                this.name = location.practice_name;
                this.address = location.address;
                this.city = location.city;
                this.zip = location.zip;
                this.state = location.state;
                this.url = location.url;
                this.phone = location.practice_phone;
                this.action = this.editAction.replace(/_/gi, id);
                this.showForm();
                this.blurName();
            },
            editCurrent() {
                this.name = this.practice.practice_name;
                this.address = this.practice.address;
                this.city = this.practice.city;
                this.zip = this.practice.zip;
                this.state = this.practice.state;
                this.url = this.practice.url;
                this.phone = this.practice.practice_phone;
                this.action = this.editCurrentAction;
                this.showForm();
                this.blurName();
            },
            submit() {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.action,
                    data: {
                        name: this.name,
                        address: this.address,
                        city: this.city,
                        zip: this.zip,
                        state: this.state,
                        url: this.url,
                        phone: this.phone
                    }
                })
                    .then(response => {
                        document.location.reload(true);
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
            selectPlace(key, value) {
                this.justSelect = true;
                this.name = value;
                let loader = this.$loading.show();
                axios({
                    method: 'GET',
                    url: this.placeAction + '/' + key,
                })
                    .then(response => {
                        let data = response.data;
                        this.address = data.address;
                        this.city = data.city;
                        this.zip = data.zip;
                        this.state = data.state;
                        this.url = data.url;
                        this.phone = data.phone;
                        this.names = {};
                    })
                    .catch(error => {})
                    .finally(response => {
                        this.block = false;
                        loader.hide()
                    });
            },
            autocomplete() {
                let url = this.autocompleteAction + '/' + (this.name).replace(' ', '+');
                if (this.lat) {
                    url = url + '/' + this.lat + '/' + this.lng;
                }
                axios({
                    method: 'GET',
                    url: url,
                })
                    .then(response => {
                        this.names = response.data;
                    })
                    .catch(error => {})
                    .finally(response => {
                        this.block = false;
                    });
            },
            getPositions(position) {
                this.lat = position.coords.latitude;
                this.lng = position.coords.longitude;
            },
            formatedName(value) {
                let formated = value.split(', ');
                return '<b>' + formated.shift() + '</b>, ' + formated.join(', ');
            }
        },
        watch:{
            name(event) {
                if ((this.name).length < 3) {
                    this.names = {};
                    return;
                }
                this.autocomplete();
            }
        },
        mounted() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getPositions);
            }
        }
    }
</script>
