<template>
</template>
<script>
    export default {
        props: [
            'user',
            'action',
            'serverDate'
        ],
        methods: {
            setTimeDiff() {
                /*let time = parseInt(moment().format('H'));
                let date = moment().format('YYYY-MM-DD');
                if (this.serverDate !== date) {
                    time = this.serverDate > date ? time - 24 : time + 24;
                }*/
                let time = moment.tz.guess();
                axios({
                    method: 'POST',
                    url: this.action,
                    data: {
                        time: time
                    }
                })
                    .then(response => {
                    })
                    .catch(error => {
                    });
            }
        },
        mounted() {
            if (!sessionStorage.getItem('diff_set')) {
                this.setTimeDiff();
                sessionStorage.setItem('diff_set', 1);
            }

        }
    }
</script>
