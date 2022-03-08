<template>
    <div>
        <gmap-map
            :center="findCenter"
            :zoom="zoom"
            ref="map"
            style="width: 100%; min-height:calc(100vh - 88px)"
            :options="{gestureHandling: 'cooperative'}"
        >
            <div>
                <gmap-marker
                    :key="index"
                    v-for="(marker, index) in practices"
                    :position="findPlace(marker)"
                    :icon="markerIcon('red')"
                    @click="openInfo(marker, 'practice')"
                />
                <gmap-marker
                    :key="index + practicesLength"
                    v-for="(marker, index) in providers"
                    :position="findPlace(marker)"
                    :icon="markerIcon('blue')"
                    @click="openInfo(marker, 'provider')"
                />
                <gmap-info-window
                    @closeclick="closeInfo()"
                    v-if="selectedMarker"
                    :opened="isInfoOpen"
                    :position="infoPos"
                    :options="{
                      pixelOffset: {
                        width: 0,
                        height: -35
                      }
                    }"
                >
                    <div class="text-center info-window-content">
                        <p>{{ selectedMarker.name }}</p>
                        <a v-if="type === 'provider'" :href="'/admin/users/' + selectedMarker.id" target="_blank">details</a>
                        <a v-if="type === 'practice'" :href="'/admin/practice/' + selectedMarker.id" target="_blank">details</a>
                    </div>
                    <br/>
                    <div v-if="type === 'provider'">
                        <p><b>Available: </b><span v-if="selectedMarker.available">YES</span><span v-else >NO</span></p>
                        <hr/>
                        <div v-if="selectedMarker.availabilities !== null">
                            <p v-for="av in selectedMarker.availabilities">
                                <b>Day: </b><span>{{ days[av.day] }}</span><b> From: </b><span>{{ format24To12Formated(av.from_hour) }}</span><b> To: </b><span>{{ format24To12Formated(av.to_hour) }}</span>
                            </p>
                        </div>
                    </div>
                </gmap-info-window>
            </div>
        </gmap-map>
        <div class="legend">
            <ul>
                <li><img src="/img/red-dot.png"/> - Practice</li>
                <li><img src="/img/blue-dot.png"/> - Provider</li>
            </ul>
        </div>
    </div>
</template>

<script>
    import {TimeConvertorMixin} from '../../mixins/TimeConvertorMixin'
    export default {
        props: [
            'practices',
            'providers',
            'days'
        ],
        data () {
            return {
                selectedMarker: null,
                infoPos: {lat: 10, lng: 10.0},
                isInfoOpen: false,
                type: null
            }
        },
        mixins: [TimeConvertorMixin],
        methods: {
            fitBounds() {
                let map = this.$refs.map.$mapObject;
                map.fitBounds(this.getBounds);
            },
            findPlace(address) {
                return {lat: parseFloat(address.lat), lng: parseFloat(address.lng)};
            },
            markerIcon(color) {
                return '/img/' + color + '-dot.png';
            },
            openInfo(marker, type) {
                this.isInfoOpen = true;
                this.infoPos = marker;
                this.selectedMarker = marker;
                this.type = type;
            },
            closeInfo() {
                this.isInfoOpen = false;
                this.infoPos = null;
            },
        },
        computed: {
            zoom() {
                return 5;
            },
            findCenter() {
                return {lat:41.850033, lng: -87.6500523};
            },
            getBounds() {
                let bounds = new google.maps.LatLngBounds();
                _.forEach(this.practices, (marker) => {
                    bounds.extend(marker);
                });
                return bounds;
            },
            practicesLength() {
                return this.practices.length;
            }
        },
        mounted() {
            this.$gmapApiPromiseLazy().then(() => {
                this.fitBounds();
            });
            console.log(this.days);
        }
    }
</script>

<style>
    .legend {
        position: absolute;
        bottom: 11px;
        background: #fff;
        padding: 5px;
    }
    .legend ul {
        list-style: none;
    }
    .legend img {
        height:20px;
    }
</style>
