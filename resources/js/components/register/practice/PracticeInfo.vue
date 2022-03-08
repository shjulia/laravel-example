<script>
    export default {
        props: [
            'autocompleteAction',
            'placeAction',
            'nameInit',
            'addressInit',
            'cityInit',
            'stateInit',
            'zipInit',
            'urlInit',
            'phoneInit',
            'user'
        ],
        data () {
            return {
                names: {},
                name: this.nameInit,
                address: this.addressInit,
                city: this.cityInit,
                state: this.stateInit,
                zip: this.zipInit,
                url: this.urlInit,
                phone: this.phoneInit,
                block: false,
                justSelect: false,
                lat: null,
                lng: null
            }
        },
        methods: {
            blurName() {
                setTimeout(() => {
                    this.names = {}
                }, 1000);
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
            },
            logPractice_signup_startEvent(email, valToSum) {
                var params = {};
                params['email'] = email;
                FB.AppEvents.logEvent('practice_signup_start', valToSum, params);
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
            $(this.$refs.select2_state).select2({
                placeholder: ''
            });
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getPositions);
            }
            if (process.env.MIX_ALLOW_FB_TRACK == 1) {
                this.logPractice_signup_startEvent(this.user.email, true);
            }
        }
    }
</script>
