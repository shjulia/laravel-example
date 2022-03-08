<template>
    <div>
        <gmap-map
                :center="findCenter"
                :zoom="zoom"
                ref="map"
                :style="'width: 100%; min-height:calc(100vh - ' + mapHeightDiff + 'px)'"
                :options="{gestureHandling: 'cooperative'}"
        >
            <gmap-marker
                    :position="findCenter"
                    :key="1000000"
                    icon="/img/map-marker-icon.png"
            />
            <div
                    v-if="!isDetails"
            >
                <gmap-custom-marker
                        :marker="findPlace(marker.practice_location)"
                        :key="index"
                        v-for="(marker, index) in filteredItems"
                        :zIndex="99999999999"
                        @click.native.prevent="openInfo(marker)"

                >
                    <div class="custom-marker-div" :class="borderClass(marker.date)">
                        <img :src="marker.practice.practice_photo_url" class="custom-marker"/>
                    </div>

                </gmap-custom-marker>
            </div>

            <gmap-marker
                    :position="practiceCords"
                    :z-index="2"
                    icon="/img/map-marker-icon-red.png"
                    v-if="isDetails"
                    :key="1000001"
            />


            <gmap-info-window
                    @closeclick="closeInfo()"
                    :opened="isInfoOpen"
                    :position="infoPos"
                    :options="{
                      pixelOffset: {
                        width: 0,
                        height: -35
                      }
                    }"
            >
                <div v-if="selectedMarker" class="text-center info-window-content">
                    <h5><b>Practice: </b>{{ selectedMarker.practice_location.practiceName }}</h5>
                    <p class="p">{{ getShiftDateTime(selectedMarker) }}</p>
<!--                    <a :href="getAction(selectedMarker)" class="btn form-button" v-if="date <= selectedMarker.date">1Job Details</a>-->
                    <a :href="detailsUrl.replace(/_/gi, selectedMarker.id)" class="btn form-button">
                        Job Details
                    </a>
                </div>
            </gmap-info-window>
        </gmap-map>
    </div>
</template>

<script>
    import {eventEmmiter} from "../../../buss";
    import GmapCustomMarker from 'vue2-gmap-custom-marker';
    import {TimeConvertorMixin} from "../../mixins/TimeConvertorMixin";

    export default {
        props: [
            'items',
            'date',
            'action',
            'acceptAction',
            'specialist',
            'item',
            'detailsUrl'
        ],
        data () {
            return {
                selectedMarker: null,
                infoPos: {lat: 10, lng: 10.0},
                isInfoOpen: false,
                filteredItems: this.items,
                isDetails: false,
                changeZoom: false,
                lat: this.specialist.last_lat ? this.specialist.last_lat : this.specialist.lat,
                lng: this.specialist.last_lng ? this.specialist.last_lng : this.specialist.lng,
                directionsDisplay: null
            }
        },
        components: {
            'gmap-custom-marker': GmapCustomMarker
        },
        mixins: [TimeConvertorMixin],
        methods: {
            getDirection(coords) {
                let directionsService = new google.maps.DirectionsService;
                if (!this.directionsDisplay) {
                    this.directionsDisplay = new google.maps.DirectionsRenderer({polylineOptions:{strokeColor:"#4a4a4a",strokeWeight:5}, suppressMarkers:true });
                }
                this.directionsDisplay.setMap(this.$refs.map.$mapObject);
                this.calculateAndDisplayRoute(directionsService, this.directionsDisplay, coords, this.findCenter);
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
            findPlace(practiceLocation) {
                return {lat: practiceLocation.lat, lng: practiceLocation.lng};
            },
            openInfo(marker) {
                //if (window.innerWidth > 767) {
                    this.isInfoOpen = true;
                    this.infoPos = this.findPlace(marker.practice_location);
                    this.selectedMarker = marker;
                /*} else {
                    this.selectedMarker = marker;
                    this.showDetails();
                }*/
            },
            closeInfo() {
                this.isInfoOpen = false;
                this.infoPos = null;
                //this.selectedMarker = null;
            },
            getShiftDateTime(shift) {
                //return shift.date + ' (' + shift.from_time + ' - ' + shift.to_time + ')';
                let time = this.format24To12Formated(shift.from_time)
                    + ' - ' + this.format24To12Formated(shift.to_time);
                if (shift.date === shift.end_date) {
                    return this.dateToHuman(shift.date) + ' | ' + time;
                }
                return this.dateToHuman(shift.date) + ' ' + time + ' ' + this.dateToHuman(shift.end_date);
            },
            borderClass(date) {
                if (this.date == date) {
                    return 'green'
                }
                if (this.date < date) {
                    return 'blue';
                }
                return 'red';
            },
            getAction(shift) {
                if (shift.status == 'accepted by practice') {
                    return (this.acceptAction).replace(/_/gi, shift.id);
                }
                return (this.action).replace(/_/gi, shift.id);
            },

            //________________filter markers by date________________
            updateMarkers(timestamp) {
                if(timestamp ==  null) {
                    this.filteredItems = this.items;
                    return;
                }

                let date = moment.unix(timestamp).format('Y-MM-DD');

                this.filteredItems = [];
                let showed = false;
                for(let i = 0; i <= this.items.length - 1; i++) {
                    if(this.items[i].date == date) {
                        this.filteredItems.push(this.items[i]);
                        if (!showed) {
                            this.openInfo(this.items[i]);
                            showed = true;
                        }
                    }
                }
            },
            showDetails() {
                this.isDetails = true;
                eventEmmiter.$emit('showDetails', this.selectedMarker);
                this.closeInfo();
                this.$gmapApiPromiseLazy().then(() => {
                    this.getDirection(this.practiceCords);
                    let map = this.$refs.map.$mapObject;
                    map.addListener('bounds_changed', () => {
                        if (this.changeZoom) {
                            map.setZoom(map.getZoom() - 2);
                            this.changeZoom = false;
                        }
                    });
                });
            },
            fitBounds() {
                let map = this.$refs.map.$mapObject;
                map.fitBounds(this.getBounds);
                map.setCenter(this.findCenter);
                map.addListener('click', function (event) {
                    if (event.placeId) {
                        event.stop();
                        //console.log('You clicked on place:' + event.placeId + ', location: ' + event.latLng);
                    }
                });
                /*let noPoi = [
                    {
                        featureType: "poi",
                        stylers: [
                            { visibility: "off" }
                        ]
                    }
                ];
                map.setOptions({styles: noPoi});*/
            }
        },
        computed: {
            mapHeightDiff() {
                //@todo: make it better in future
                return $('.main-navbar').height() + $('.availability').height();
            },
            practiceCords() {
                return {lat: this.selectedMarker.practice_location.lat, lng: this.selectedMarker.practice_location.lng};
            },
            zoom() {
                return 14;
            },
            findCenter() {
                return {lat: this.lat, lng: this.lng};
            },
            getBounds() {
                let bounds = new google.maps.LatLngBounds();
                bounds.extend(this.findCenter);
                _.forEach(this.filteredItems, (marker) => {
                    bounds.extend(this.findPlace(marker.practice_location));
                });
                return bounds;
            }
        },
        mounted() {
            this.$gmapApiPromiseLazy().then(() => {
                this.fitBounds();
                if (this.item) {
                    this.selectedMarker = this.item;
                    this.showDetails();
                }
            });
            let socket = io(process.env.MIX_SOCKET_SERVER_URL);
            socket.on("job-chanel.provider-location." + this.specialist.user_id + ":App\\Events\\User\\Provider\\LocationChangedEvent", (data) => {
                this.lat = data.lat;
                this.lng = data.lng;
                if (this.selectedMarker) {
                    this.getDirection(this.practiceCords);
                }
            })
                .on("reconnect_error", (error) => {
                    socket.disconnect();
                });
        }
    }
</script>
