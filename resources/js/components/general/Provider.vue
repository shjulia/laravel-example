<template>

</template>

<script>
    import {eventEmmiter} from "../../buss";

    export default {
        props: [
            'user',
            'updateLocationUrl'
        ],
        methods: {
            getPositions(position) {
                let last_lat = sessionStorage.getItem('last_lat');
                let last_lng = sessionStorage.getItem('last_lng');
                if (position.coords.latitude == last_lat && position.coords.longitude == last_lng) {
                    //console.log('same geo', {'lat': position.coords.latitude, 'lng': position.coords.longitude});
                    this.sendLocation();
                    return;
                }
                sessionStorage.setItem('last_lat', position.coords.latitude);
                sessionStorage.setItem('last_lng', position.coords.longitude);
                axios({
                    method: 'POST',
                    url: this.updateLocationUrl,
                    data: {'lat': position.coords.latitude, 'lng': position.coords.longitude}
                })
                    .then(response => {
                        console.log('sent', {'lat': position.coords.latitude, 'lng': position.coords.longitude});
                        this.sendLocation();
                    })
                    .catch(error => {
                        console.log('error', {'lat': position.coords.latitude, 'lng': position.coords.longitude});
                    });
            },
            startLocationDetect() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(this.getPositions);
                }
            },
            sendLocation() {
                setTimeout(() => {
                    if (navigator.geolocation) {
                        navigator.geolocation.getCurrentPosition(this.getPositions);
                    }
                }, 20000);
            }
        },
        mounted() {
            let userId = this.user.id;
            let socket = io(process.env.MIX_SOCKET_SERVER_URL);
            socket.on("job-chanel." + userId + ":App\\Events\\Shift\\NotifyShiftEvent", (data) => {
                eventEmmiter.$emit('newJob', data.notification);
            })
                .on("reconnect_error", (error) => {
                    socket.disconnect();
                });
            this.startLocationDetect();
        }
    }
</script>
