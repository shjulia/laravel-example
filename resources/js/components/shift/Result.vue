<script>
    import Swal from 'sweetalert2'
    import {CancelMixin} from './CancelMixin';

    export default {
        props: [
            'requestAction',
            'shift',
            'checkAction',
            'indexUrl',
            'toBackgroundAction'
        ],
        data () {
            return {
                provider: {},
                provider_photo: '',
                isResult: false,
                noResult: false,
                minutes: 3,
                seconds: 30,
                globalMinutes: 5,
                globalSeconds: 0,
                globalTimerStarted: false,
                potentialProviderId: this.shift.potential_provider_id
            }
        },
        mixins: [CancelMixin],
        methods: {
            startTimer() {
                if (this.isResult) {
                    return;
                }
                let m = this.minutes;
                let s = this.seconds;
                if (s == 0) {
                    if (m == 0) {
                        this.checkProviders();
                        return;
                    }
                    m--;
                    s = 59;
                } else {
                    s--;
                }
                this.minutes = m;
                this.seconds = s;
                setTimeout(this.startTimer, 1000);
            },
            startGlobalTimer() {
                if (this.isResult) {
                    return;
                }
                if (this.globalSeconds === 0) {
                    if (this.globalMinutes === 0) {
                        Swal.fire({
                            imageUrl: "/img/loop.png",
                            imageWidth: 90,
                            imageHeight: 90,
                            title: '',
                            text: 'It appears all Providers are already placed today. We will continue to search to try and find a perfect match.',
                        }).then((result) => {
                            location.href = this.indexUrl;
                        });
                        this.noResult = true;
                        return;
                    }
                    this.globalMinutes--;
                    this.globalSeconds = 59;
                } else {
                    this.globalSeconds--;
                }
                setTimeout(this.startGlobalTimer, 1000);
            },
            initNext() {
                this.minutes = 3;
                this.seconds = 30;
            },
            checkProviders() {
                axios({
                    method: 'GET',
                    url: this.checkAction
                })
                    .then(response => {
                        let newPotential = response.data.potential_provider_id;
                        if (this.potentialProviderId !== newPotential) {
                            this.globalMinutes += 4;
                            this.potentialProviderId = newPotential;
                            this.initNext();
                            this.startTimer();
                        }
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error
                        });
                    });
            },
            checkTimeFee() {
                return false;
            },
        },
        mounted() {
            if (this.shift.status === 'waiting') {
                Swal.fire({
                    imageUrl: "/img/loop.png",
                    imageWidth: 90,
                    imageHeight: 90,
                    title: '',
                    text: 'We will notify you 72 hours before the shift starts with information on your provider.',
                }).then((result) => {
                    location.href = this.indexUrl;
                });
            } else {
                this.startGlobalTimer();
                this.startTimer();
            }

            let socket = io(process.env.MIX_SOCKET_SERVER_URL);
            socket.on("job-chanel.provider." + this.shift.creator_id + ":App\\Events\\Shift\\MatchedEvent", (data) => {
                this.isResult = true;
                this.provider = data.provider;
                this.provider_photo = data.provider_photo;
                $('#acceptModal').modal('show');
            })
                .on("reconnect_error", (error) => {
                    Swal.fire({
                        title: 'Oops',
                        text: 'Search server error. Please try again.',
                        type: 'error'
                    });
                    socket.disconnect();
                });

            /*socket.on("job-chanel.provider.decline." + this.shift.practice_id + ":App\\Events\\Shift\\Provider\\DeclineShiftEvent", (data) => {
                console.log(data);
                //this.requestAnother();
            })
                .on("reconnect_error", (error) => {
                    socket.disconnect();
                });*/
        }
    }
</script>
