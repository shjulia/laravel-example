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
                <gmap-custom-marker
                        :marker="marker.geocode"
                        :key="index"
                        v-for="(marker, index) in areas"
                        :zIndex="99999999999"
                        @click.native.prevent="openInfo(marker)"

                >
                    <div class="custom-marker-div red">
                        <p class="pcustommarker">{{ marker.practices_count + marker.specialists_count}}</p>
                    </div>

                </gmap-custom-marker>
            </div>


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
                    <h5><b>Area: {{ selectedMarker.name }}</b></h5>
                    <h6><b>Practices amount: </b>{{ selectedMarker.practices_count }}</h6>
                    <h6><b>Providers amount: </b>{{ selectedMarker.specialists_count }}</h6>
                </div>
            </gmap-info-window>
        </gmap-map>
    </div>
</template>

<script>
    import GmapCustomMarker from 'vue2-gmap-custom-marker';
    export default {
        props: [
            'areas'
        ],
        data () {
            return {
                selectedMarker: null,
                infoPos: {lat: 10, lng: 10.0},
                isInfoOpen: false
            }
        },
        components: {
            'gmap-custom-marker': GmapCustomMarker
        },
        methods: {
            openInfo(marker) {
                this.isInfoOpen = true;
                this.infoPos = marker.geocode;
                this.selectedMarker = marker;
            },
            closeInfo() {
                this.isInfoOpen = false;
                this.infoPos = null;
            },
            fitBounds() {
                let map = this.$refs.map.$mapObject;
                map.fitBounds(this.getBounds);
            }
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
                _.forEach(this.areas, (marker) => {
                    bounds.extend(marker.geocode);
                });
                return bounds;
            }
        },
        mounted() {
            this.$gmapApiPromiseLazy().then(() => {
                this.fitBounds();
            });
        }
    }
</script>