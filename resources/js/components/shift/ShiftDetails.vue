<script>
    import {CancelMixin} from './CancelMixin';

    export default {
        props: [
            'shift',
        ],
        data () {
            return {
                zoom: 14,
                changeZoom: false,
                showDetails: false,
                is_rematch: true,
                lat: this.shift.provider.last_lat ? this.shift.provider.last_lat : this.shift.provider.lat,
                lng: this.shift.provider.last_lng ? this.shift.provider.last_lng : this.shift.provider.lng,
                directionsDisplay: null,
                startedRoute: false
            }
        },
        mixins: [CancelMixin],
        methods: {
            getDirection(providerCoords) {
                let directionsService = new google.maps.DirectionsService;
                if (!this.directionsDisplay) {
                    this.directionsDisplay = new google.maps.DirectionsRenderer({polylineOptions:{strokeColor:"#4a4a4a",strokeWeight:5}, suppressMarkers:true });
                }
                this.directionsDisplay.setMap(this.$refs.map.$mapObject);
                this.calculateAndDisplayRoute(directionsService, this.directionsDisplay, providerCoords, this.findCenter);
            },
            calculateAndDisplayRoute(directionsService, directionsDisplay, start, destination) {
                directionsService.route({
                    origin: start,
                    destination: destination,
                    travelMode: 'DRIVING'
                }, (response, status) => {
                    if (status === 'OK') {
                        directionsDisplay.setDirections(response);
                    }
                    this.changeZoom = true;
                });
            },
            details() {
                this.showDetails = !this.showDetails;
            },
            getHoursString(hours) {
                let hoursString = ' hours ';
                if (hours === 1) {
                    hoursString = ' hour ';
                }

                return hoursString;
            },
            getMinutesSting(minutes) {
                let minutesString = ' minutes ';
                if(minutes === 1) {
                    minutesString = ' minute ';
                }

                return minutesString;
            },
            drawDirection() {
                this.getDirection(this.findProvider);
                let map = this.$refs.map.$mapObject;
                map.addListener('bounds_changed', () => {
                    if (this.changeZoom) {
                        let map = this.$refs.map.$mapObject;
                        map.setZoom(map.getZoom() - 2);
                        this.changeZoom = false;
                    }
                });
            },
            checkRouteStart(lat, lng) {
                if (lat !== this.shift.provider.lat && lng !== this.shift.provider.lng) {
                    this.startedRoute = true;
                }
            }
        },
        computed: {
            findCenter() {
                return {lat: this.shift.practice.lat, lng: this.shift.practice.lng};
            },
            findProvider() {
                //let lat = this.shift.provider.last_lat ? this.shift.provider.last_lat : this.shift.provider.lat;
                //let lng = this.shift.provider.last_lng ? this.shift.provider.last_lng : this.shift.provider.lng;
                return {lat: this.lat, lng: this.lng};
            },
            getStatus() {
                if (this.shift.status === 'finished') {
                    return 4;
                }
                if (moment().add(1, 'days').format('YYYY-MM-DD') <= this.shift.date) {
                    return 1;
                }
                let today = moment().format('YYYY-MM-DD');
                if (this.shift.end_date < moment().format('YYYY-MM-DD')) {
                    return 3;
                }
                if (this.shift.date === today && this.shift.from_time > moment().format('HH:mm')) {
                    return 2;
                }
                return 3;
            },
            arrivesIn() {
                let diff = this.timeBeforeStart;
                if (diff._data.days <= 0 && diff._data.hours <= 0 && diff._data.minutes <= 0) {
                    return 'Arrived';
                }
                if (diff._data.days && diff._data.days > 1) {

                    //return diff._data.days + ' days ' + diff._data.hours + ' hours ' + diff._data.minutes + ' minutes';
                    return moment(this.shift.date + ' ' + this.shift.from_time).format('DD MMM, YYYY');
                }
                if (diff._data.days && diff._data.days === 1) {
                    //return diff._data.days + ' days ' + diff._data.hours + ' hours ' + diff._data.minutes + ' minutes';
                    return 'Tomorrow'
                }
                if (diff._data.hours) {
                    return 'in ' + diff._data.hours +  this.getHoursString(diff._data.hours)  + diff._data.minutes +  this.getMinutesSting(diff._data.minutes);
                }
                if (diff._data.minutes) {
                    return 'in ' + diff._data.minutes +  this.getMinutesSting(diff._data.minutes);
                }
            },
            timeBeforeStart() {
                let now = moment();
                let start = moment(this.shift.date + ' ' + this.shift.from_time);
                return moment.duration(start.diff(now));
            },
            showProvider() {
                return this.timeBeforeStart.asMinutes() <= 60 && this.startedRoute;
            }
        },
        mounted() {
            this.checkRouteStart(this.lat, this.lng);
            if (this.showProvider) {
                this.$gmapApiPromiseLazy().then(() => {
                    this.drawDirection();
                });
            }

            let socket = io(process.env.MIX_SOCKET_SERVER_URL);
            socket.on("job-chanel.provider-location." + this.shift.provider.user_id + ":App\\Events\\User\\Provider\\LocationChangedEvent", (data) => {
                this.checkRouteStart(data.lat, data.lng);
                this.lat = data.lat;
                this.lng = data.lng;
                if (this.showProvider) {
                    this.drawDirection();
                }
            })
                .on("reconnect_error", (error) => {
                    socket.disconnect();
                });
        }
    }
</script>
