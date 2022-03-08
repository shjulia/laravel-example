export const AutocompleteMixin = {
    props: [
        'autocompleteAction',
        'placeAction'
    ],
    data () {
        return {
            addresses: [],
            showAutocompleteList: false,
            lat: null,
            lng: null,
        }
    },
    methods: {
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
        selectPlace(key, value) {
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
                })
                .catch(error => {})
                .finally(response => {
                    loader.hide();
                    this.showAutocompleteList = false;
                });
        },
    },
    mounted() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(this.getPositions);
        }
    }
};
