<script>

    import {ServerErrors} from '../../mixins/ServerErrors';
    import Swal from 'sweetalert2'
    import Compressor from 'compressorjs';

    export default {
        props: [
            'showforminit',
            'action',
            'nextAction',
            'user',
            'oldFileInit',
            'autocompleteAction',
            'placeAction',
            'phoneAction',
            'phoneErrorInit'
        ],
        data () {
            return {
                image: false,
                oldFile: this.oldFileInit,
                showForm: this.showforminit,
                phone: this.user.phone ? this.user.phone : '',
                showPhoneError: this.phoneErrorInit,
                address: this.user.specialist.driver_address,
                dob: this.user.specialist.dob,
                city: this.user.specialist.driver_city,
                state: this.user.specialist.driver_state,
                zip: this.user.specialist.driver_zip,
                first_name: this.user.specialist.driver_first_name,
                last_name: this.user.specialist.driver_last_name,
                middle_name: this.user.specialist.driver_middle_name,
                has_middle_name: !this.user.specialist.driver_middle_name,
                expiration_date: this.user.specialist.driver_expiration_date,
                license: this.user.specialist.driver_license_number,
                gender: this.user.specialist.driver_gender,
                fullPage: true,
                namesSame: true,
                addresses: [],
                justSelect: false,
                modalShow: false,
                market_open: false,
                area_name: '',
                showAutocompleteList: false,
                lat: null,
                lng: null,
            }
        },
        mixins: [ServerErrors],
        methods: {
            onChange () {
                let loader = this.$loading.show();
                let waitPopup = this.wait();
                this.image = true;
                let file = this.$refs.photo.files[0];
                const self = this;
                new Compressor(file, {
                    quality: 0.8,
                    maxWidth: 500,
                    success(result) {
                        const formData = new FormData();
                        formData.append('photo', result, result.name);
                        self.sendPhoto(formData, loader, waitPopup);
                    },
                    error(err) {
                        self.problems();
                    },
                });
            },
            sendPhoto(formData, loader, waitPopup) {
                axios({
                    method: 'POST',
                    url: this.action,
                    data: formData
                })
                    .then(response => {
                        let data = response.data;
                        this.oldFile = data.photo_url;
                        this.city = data.address.city;
                        this.address = data.address.address;
                        this.state = data.address.state;
                        this.zip = data.address.zip;
                        this.dob = data.birthDate;
                        this.first_name = data.name.first;
                        this.last_name = data.name.last;
                        this.middle_name = data.name.middle;
                        this.has_middle_name = !data.name.middle;
                        this.expiration_date = data.expirationDate;
                        this.license = data.number;
                        this.gender = data.gender;
                        this.market_open = data.market_open ? data.market_open : false;
                        this.area_name = data.area_name ? data.area_name : this.city;

                        this.isNamesTheSame();
                        if (!(this.city || this.address || this.state || this.zip) || !(this.dob || this.expiration_date)) {
                            this.problems();
                        }
                    })
                    .catch(response => {
                        this.problems();
                    })
                    .finally(response => {
                        this.showForm = true;
                        waitPopup.close()
                        loader.hide()
                    });
            },
            problems() {
                Swal.fire({
                    imageUrl: "/img/loop.png",
                    imageWidth: 90,
                    imageHeight: 90,
                    title: 'Still working...',
                    text: 'Thank you for uploading your driver\'s license. It was a bit tricky for us to capture the data from your license automatically. If you could provide a few more details for verification, that would be great.'
                });
            },
            wait() {
                return Swal.fire({
                    imageUrl: "/img/loop.png",
                    imageWidth: 90,
                    imageHeight: 90,
                    title: 'Thank you for joining Boon!',
                    text: 'Thank you for your patience! Your license is being scanned into our system. ',
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'OK'
                });
            },
            showFile() {
                window.open(this.oldFile, 'file of driver license', "width=500,height=500,top=40,left=40");
            },
            isNamesTheSame() {
                if (!this.first_name && !this.last_name) {
                    this.namesSame = true;
                    return;
                }
                if (this.first_name.toLowerCase() !== this.user.first_name.toLowerCase()
                    || this.last_name.toLowerCase() !== this.user.last_name.toLowerCase()) {
                    this.namesSame = false;
                }
            },
            //search places by user query
            autocomplete() {
                let url = this.autocompleteAction + '/' +
                          this.address + '/' +
                          this.lat + '/' +
                          this.lng;

                axios.get(url).then(response => {
                    this.addresses = response.data;
                    this.showAutocompleteList = true;
                });
            },
            formatedName(value) {

                let formated = value.split(', ');
                return '<b>' + formated.shift() + '</b>, ' + formated.join(', ');
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
                        this.address = data.address.split(',')[0];
                        this.city = data.city;
                        this.zip = data.zip;
                        this.state = data.state;
                        this.url = data.url;
                        this.phone = data.phone;
                        this.names = {};
                        this.modalShow = false;
                    })
                    .catch(error => {})
                    .finally(response => {
                        loader.hide();
                        this.showAutocompleteList = false;
                    });
            },
            submit() {
                this.$loading.show();
                this.$refs.identityForm.submit();
            },
            getAddress() {
                if (this.address.length < 3) {
                    this.addresses = [];
                    return;
                }
                this.autocomplete();
            },
            getPositions(position) {
                this.lat = position.coords.latitude;
                this.lng = position.coords.longitude;
            },
            phoneChanged() {
                if (this.phone.length !== 14) {
                    return;
                }
                axios({
                    method: 'POST',
                    url: this.phoneAction,
                    data: {'phone': this.phone}
                })
                    .then(response => {
                        this.showPhoneError = false;
                    })
                    .catch(response => {
                    });
            },
            fileInputClick(event) {
                if (this.phone.length !== 14) {
                    this.showPhoneError = true;
                } else {
                    this.showPhoneError = false;
                }
            }
        },
        mounted() {
            $(this.$refs.select2_state).select2({
                placeholder: '',
                width: '100%'
            });
            $(this.$refs.select2_gender).select2({
                placeholder: '',
                minimumResultsForSearch: -1,
                width: '100%'
            });
            if (this.user.specialist.driver_photo) {
                this.isNamesTheSame();
            }
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getPositions);
            }
        }
    }
</script>
