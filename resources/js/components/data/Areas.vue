<script>
    export default {
        props: [
            'savedCities', 'savedZips', 'allCities', 'allZips', 'savedTier'
        ],
        data() {
            return {
                cities: this.savedCities,
                zips: this.savedZips,
                citiesAll: this.allCities,
                zipsAll: this.allZips,
                cityMarkers: [],
                zipMarkers: [],
                allMarkers: [],
                tier: this.savedTier
            };
        },
        methods: {
            getLocation(marker) {
                return {lat: marker.lat, lng: marker.lng}
            },

            getMarkers() {
                this.cityMarkers = this.citiesAll.filter(city => {
                    return this.cities.indexOf(city.id) !== -1;
                });
                this.zipMarkers = _.filter(this.zipsAll, (zip) => {
                    return this.zips.indexOf(zip.id) !== -1;
                });

                this.allMarkers = this.cityMarkers.concat(this.zipMarkers);
            },

            getCenter() {
                let lat, lng, element;

                if(this.cities.length > 0) {
                    element = _.find(this.citiesAll, (city) => {
                        return this.cities[0] === city.id;
                    });
                } else if(this.zips.length > 0) {

                    element = _.find(this.zipsAll, (zip) => {
                        return this.zips[0] === zip.id;
                    });
                } else {
                    element = this.zipsAll[0];
                }
                lat = element.lat;
                lng = element.lng;
                return {lat: lat, lng: lng}
            },
            cityChange() {
                let cities = [];
                _.forOwn(this.cities, (value, key) => {
                    cities[key] = parseInt(value);
                });
                this.cities = cities;
                this.getMarkers();
                this.getCenter();
            },
            zipChange() {
                let zips = [];
                _.forOwn(this.zips, (value, key) => {
                    zips[key] = parseInt(value);
                });
                this.zips = zips;
                this.getMarkers();
                this.getCenter();
            },

            markerIcon(color) {
                return '/img/' + color + '-dot.png';
            },
        },
        mounted() {
            $(this.$refs.cities).select2();
            $(this.$refs.zip).select2();

            this.getMarkers();
        }
    }
</script>
