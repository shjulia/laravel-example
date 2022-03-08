<script>

    import JobMap from './JobMap';
    import {eventEmmiter} from "../../../buss";
    import StarRating from 'vue-star-rating';
    import {TimeConvertorMixin} from "../../mixins/TimeConvertorMixin";
    import Swal from 'sweetalert2'

    export default {
        props: [
            'items',
            'date',
            'changeAvailabilityLink',
            'state',
            'publicPath',
            'formAction',
            'review',
            'shiftId',
            'scores',
            'shift',
            'startUrl',
            'finishUrl'
        ],
        data () {
            return {
                range: this.items,
                available: this.state,
                showCalendar: true,
                showDetails: false,
                showTracking: false,
                showReview: false,
                selectedShift: null/*{
                    practice: {},
                    practice_location: {}
                }*/,
                rating: 0,
                starTitle: '',
                questions: [
                    '',
                    '',
                    'What areas could the practice improve on?',
                    'What did the practice do well?',
                    'What were some of the practice\'s strengths?'
                ],
                scoreMarks: []
            }
        },
        components: {
            'job-map': JobMap,
            'star-rating': StarRating
        },
        mixins: [TimeConvertorMixin],
        methods: {
            selectDay(timestamp) {
                this.$refs.map.updateMarkers(timestamp);
                this.updateEarnings(timestamp);
            },
            hire() {
                axios.get(this.changeAvailabilityLink, {
                    params: {
                        available: this.available ? 1 : 0
                    }
                })
            },
            updateEarnings(timestamp) {
                let range = [];

                if(timestamp == null) {
                    range = this.items;
                } else {
                    let date = moment.unix(timestamp).format('Y-MM-DD');
                    this.items.forEach(function(item){
                        if(item.date == date) {
                            range.push(item);
                        }
                    });
                }

                this.range = range;
            },
            showTrackingBlock() {
                this.showTracking = true;
                this.showDetails = false;
            },
            showDetailsBlock() {
                this.showCalendar = false;
                this.showTracking = false;
                this.showReview   = false;
                this.showDetails  = true;
            },
            getStatus() {
                if (this.selectedShift.status === 'finished' || this.selectedShift.processed == 1) {
                    return 4;
                }
                if (moment().add(1, 'days').format('YYYY-MM-DD') <= this.selectedShift.date) {
                    return 1;
                }
                let today = moment().format('YYYY-MM-DD');
                if (this.selectedShift.end_date < moment().format('YYYY-MM-DD')) {
                    return 3;
                }
                if (this.selectedShift.date === today && this.selectedShift.from_time > moment().format('HH:mm')) {
                    return 2;
                }
                if (this.selectedShift.end_date <= today && this.selectedShift.to_time <= moment().format('HH:mm')) {
                    return 4;
                }
                return 3;
            },
            status() {
                if(this.getStatus() == 1) {
                    return 'Accepted Request';
                }
                if(this.getStatus() == 2) {
                    return this.isArrived ? 'Shift in Progress' : 'You is On The Way';
                }
                if(this.getStatus() == 3) {
                    return 'Shift in Progress';
                }
                if(this.getStatus() == 4) {
                    return 'Shift Completed';
                }
            },
            endShift() {
                this.showDetails = false;
                this.showReview = true;
            },
            backToDetails() {
                this.showDetails = true;
                this.showReview = false;
            },
            submit() {
                this.$loading.show();
                this.$refs.reviewForm.submit();
            },
            selectBuble(field) {
                if (!this.isFieldExists(field)) {
                    this.scoreMarks.push(field);
                } else {
                    this.scoreMarks = _.remove(this.scoreMarks, (n) => {
                        return n !== field;
                    });
                }
            },
            isFieldExists(field) {
                return this.scoreMarks.indexOf(field) !== -1;
            },
            arrived() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        this.sendTracking(position.coords.latitude, position.coords.longitude);
                    }, (error) => {
                        this.sendTracking(null, null);
                    });
                } else {
                    this.sendTracking(null, null);
                }
            },
            sendTracking(lat, lng) {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.startUrl,
                    data: {'lat': lat, 'lng': lng}
                })
                    .then(response => {
                        window.location.reload();
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error
                        });
                    })
                    .finally(response => {
                        loader.hide()
                    });
            },
            finishClick() {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition((position) => {
                        this.finish(position.coords.latitude, position.coords.longitude);
                    }, (error) => {
                        this.finish(null, null);
                    });
                } else {
                    this.finish(null, null);
                }
            },
            finish(lat, lng) {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.finishUrl,
                    data: {'lat': lat, 'lng': lng}
                })
                    .then(response => {
                        window.location.href = response.data.route;
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error
                        });
                    })
                    .finally(response => {
                        loader.hide()
                    });
            }
        },
        computed: {
            earnings: function() {
                let sum = 0;
                this.range.forEach(function(item){
                    sum += item.cost;
                });
                return sum;
            },
            questionTitle() {
                if (this.rating < 3) {
                    return null;
                }
                return this.questions[this.rating - 1];
            },
            replacedFormAction() {
                if(!this.review) {
                    return this.formAction.replace('//reviews', '/' + this.selectedShift.id + '/reviews');
                } else {
                    return this.formAction.replace('//reviews', '/' + this.shiftId + '/reviews');
                }
            },
            shiftAddress() {
                let address = this.selectedShift.practice_location;
                return address.address + ', ' + address.city;
            },
            shiftTime() {
                let time = this.format24To12Formated(this.selectedShift.from_time)
                    + ' - ' + this.format24To12Formated(this.selectedShift.to_time);
                if (this.selectedShift.date === this.selectedShift.end_date) {
                    return this.dateToHuman(this.selectedShift.date) + ' | ' + time;
                }
                return this.dateToHuman(this.selectedShift.date) + ' ' + time + ' ' + this.dateToHuman(this.selectedShift.end_date);
            },
            arrivesIn() {
                let now = moment();
                let start = moment(this.selectedShift.date + ' ' + this.selectedShift.from_time);
                let diff = moment.duration(start.diff(now));
                if (this.selectedShift.is_started_by_provider || (diff._data.days <= 0 && diff._data.hours <= 0 && diff._data.minutes <= 0)) {
                    return 'Arrived';
                }
                if (diff._data.days) {
                    return diff._data.days + ' days ' + diff._data.hours + ' hours ' + diff._data.minutes + ' minutes';
                }
                if (diff._data.hours) {
                    return diff._data.hours + ' hours ' + diff._data.minutes + ' minutes';
                }
                if (diff._data.minutes) {
                    return diff._data.minutes + ' minutes';
                }
            },
            isArrived() {
                return this.arrivesIn === "Arrived";
            },
            isHasReviewForPractice() {
                let has = false;
                _.forOwn(this.selectedShift.reviews, (review) => {
                    if (review.hasOwnProperty('practice_review')) {
                        has = true;
                    }
                });
                return has;
            }
        },
        watch: {
            rating() {
                if(this.rating === null) {
                    this.starTitle = '';
                } else if (this.rating == 1) {
                    this.starTitle = 'Bad Experience';
                } else if (this.rating == 2) {
                    this.starTitle = 'Below Average';
                } else if (this.rating == 3) {
                    this.starTitle = 'Average';
                } else if (this.rating == 4) {
                    this.starTitle = 'Met Expectations';
                } else if (this.rating == 5) {
                    this.starTitle = 'Exceeded Expectations';
                }
            }
        },
        mounted() {
            eventEmmiter.$on('showDetails', (selectedShift) => {
                this.showDetailsBlock();
                this.selectedShift = selectedShift;
            });
            //console.log(this.selectedShift);
        },
        created() {
            if (this.review) {
                this.selectedShift = this.shift;
                this.showCalendar = false;
                this.showDetails = false;
                this.showReview = true;
            }
        }
    }
</script>
