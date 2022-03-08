<template>

</template>

<script>
    import {eventEmmiter} from "../../buss";

    export default {
        props: [
            'user'
        ],
        mounted() {
            let userId = this.user.id;
            let socket = io(process.env.MIX_SOCKET_SERVER_URL);
            socket.on("job-chanel.provider-notification." + userId + ":App\\Events\\Shift\\NotifyShiftEvent", (data) => {
                eventEmmiter.$emit('inviteAccepted', data.notification);
            })
                .on("reconnect_error", (error) => {
                    socket.disconnect();
                });
        }
    }
</script>