<script>
    import {CropperMixin} from "../../../mixins/CropperMixin";
    import TimeRow from './TimeRow';

    export default {
        props: [
            'showHolidaysInit',
            'days',
            'initAvailabilities',
            'autocompleteAction',
            'placeAction',
            'provider_address',
            'provider_city',
            'provider_state',
            'provider_zip',
            'setTimeShowInit',
            'infoSet'
        ],
        data () {
            return {
                image: false,
                showHolidays: this.showHolidaysInit,
                showRows: [],
                availabilities: this.initAvailabilities,
                removed: [],
                setTimeShow: this.setTimeShowInit,
                addresses: [],
                showAutocompleteList: false,
                address: this.provider_address,
                city: this.provider_city,
                state: this.provider_state,
                zip: this.provider_zip,
                lat: null,
                lng: null,
                showBankFields: false
            }
        },
        mixins: [CropperMixin],
        components: {
            'time-row': TimeRow
        },
        methods: {
            setShowRow(iterator) {
                if (this.showRows.indexOf(iterator)) {
                    this.showRows.push(iterator);
                }
            },
            newInterval() {
                this.availabilities.push({'from': '07:00', 'to': '18:00', 'inDays': [], 'id': Math.random().toString(36).substring(7)});
            },
            removeInterval(id) {
                this.removed.push(id);
            },
            isRemoved(id) {
                return this.removed.indexOf(id) !== -1;
            },
            showSetTime() {
                this.setTimeShow = !this.setTimeShow;
            },
            getAddress() {
                if (this.address.length < 3) {
                    this.addresses = [];
                    return;
                }
                this.autocomplete();
            },
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
                    })
                    .catch(error => {})
                    .finally(response => {
                        loader.hide();
                        this.showAutocompleteList = false;
                    });
            },
            getPositions(position) {
                this.lat = position.coords.latitude;
                this.lng = position.coords.longitude;
            },
        },
        mounted() {
            this.$nextTick(() => {
                $('.select2').select2();
            });

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(this.getPositions);
            }
        }
    }
</script>
