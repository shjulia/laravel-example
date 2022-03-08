<template>
    <div>
        <p
            v-for="(track, index) in tracks"
            class="mb-0"
        >
            <input type="checkbox" :checked="showTracks.indexOf(track.id) !== -1" @change="checkTrack(track.id)"/>
            Action: <b>{{ track.action}}</b> Location: <b>[lat: {{ track.lat }}, lng: {{ track.lng }}]</b> Time: <b>{{ formatedTime(track.created_at)}}</b>
        </p>
        <p
            class="mb-0"
        >
            Practice location: <b>[lat: {{ shift.practice_location.lat }}, lng: {{ shift.practice_location.lng }}]</b>
        </p>
        <gmap-map
            :center="findCenter"
            :zoom="zoom"
            style="width: 100%; min-height:200px"
            :options="{gestureHandling: 'cooperative'}"
            ref="map"
        >
            <gmap-marker
                v-for="(track, index) in tracks"
                v-if="track.lat && track.lng && showTracks.indexOf(track.id) !== -1"
                :position="findTrackPos(track)"
                :key="index"
                :z-index="2"
            />
            <gmap-marker
                :position="findCenter"
                :z-index="1"
                icon="/img/map-marker-icon.png"

            />
        </gmap-map>
    </div>
</template>

<script>
    export default {
        props: [
            'shift',
            'tracks',
            'tz'
        ],
        data () {
            return {
                zoom: 11,
                showTracks: []
            }
        },
        methods: {
            findTrackPos(track) {
                return {lat: track.lat, lng: track.lng};
            },
            formatedTime(time) {
                return moment(time).tz(this.tz).format('h:mm a YYYY-MM-DD')
            },
            checkTrack(id) {
                if (this.showTracks.indexOf(id) === -1) {
                    this.showTracks.push(id);
                    return;
                }
                this.showTracks = _.remove(this.showTracks, (track) => {
                    return track !== id;
                });
            }
        },
        computed: {
            findCenter() {
                return {lat: this.shift.practice_location.lat, lng: this.shift.practice_location.lng};
            }
        },
        mounted() {
            /*_.forEach(this.tracks, (marker) => {
                this.showTracks.push(marker.id);
            });*/
        }
    }
</script>
