<script>
    import {TimeConvertorMixin} from '../mixins/TimeConvertorMixin';
    import Swal from 'sweetalert2'
    export default {
        props: [
            'timeFromInit',
            'timeToInit',
            'startDateInit',
            'endDateInit',
            'isAdmin',
            'lunchTimes',
            'lunchBreakInit'
        ],
        data () {
            return {
                time_from: this.format24To12(this.timeFromInit),
                time_to: this.format24To12(this.timeToInit),
                start_date: this.startDateInit,
                end_date: this.endDateInit,
                formatedFrom: this.timeFromInit,
                formatedTo: this.timeToInit,
                error_time_from: false,
                error_time_to: false,
                multiDays: this.startDateInit !== this.endDateInit,
                startDateShow: false,
                isLunch: !!this.lunchBreakInit,
                lunchBreak: this.lunchBreakInit ? this.lunchBreakInit : 0,
                needsLunch: false
            }
        },
        mixins: [TimeConvertorMixin],
        methods: {
            showStartDate() {
                this.startDateShow = !this.startDateShow;
            },
            clearErrors() {
                this.error_time_from = false;
                this.error_time_to = false;
            },
            isErrors() {
                let errors = false;
                this.clearErrors();
                if (!this.formatedFrom) {
                    this.error_time_from = "Time from field is required";
                    errors = true;
                }
                if (!this.formatedTo) {
                    this.error_time_to = "Time to field is required";
                    errors = true;
                }
                //if (!errors) {
                    return this.compareTimes(this.formatedFrom, this.formatedTo);
                //}
                return errors;
            },
            compareTimes(time1, time2) {
                time1 = time1.split(':');
                time1 = parseInt(time1[0], 10) * 60 + parseInt(time1[1], 10);
                time2 = time2.split(':');
                time2 = parseInt(time2[0], 10) * 60 + parseInt(time2[1], 10);
                if (time1 >= time2) {
                    /*this.error_time_to = "Time to field must be greater then time from field is required";
                    return true;*/
                    this.setNextDay();
                    time2 += 24 * 60;
                }
                if (moment(this.start_date).add(1, 'weeks').format('YYYY-MM-DD') <= this.end_date) {
                    this.error_time_to = this.error_time_to ? this.error_time_to : "Maximum shift is 1 week";
                    return true;
                }
                if (this.start_date == this.end_date && (time2 - time1) < 120) {
                    this.error_time_to = this.error_time_to ? this.error_time_to : "Minimum shift is 2 hours";
                    return true;
                }
                /*if ((time2 - time1) < 120) {
                    this.error_time_to = this.error_time_to ? this.error_time_to : "Minimum shift is 2 hours";
                    return true;
                } else if ((time2 - time1) > 960) {
                    this.error_time_to = this.error_time_to ? this.error_time_to : "Maximum shift is 16 hours";
                    return true;
                }*/
                return false;
            },
            setNextDay() {
                if (this.end_date == this.start_date || this.start_date > this.end_date) {

                    Swal.fire({
                        imageUrl: "/img/loop.png",
                        imageWidth: 90,
                        imageHeight: 90,
                        title: '',
                        text: 'This is an overnight shift, do you wish to proceed?',
                        showCancelButton: true,
                        cancelButtonText: 'No',
                        confirmButtonText: 'Yes'
                    })
                        .then((result) => {
                            if (result.value) {
                                this.end_date = moment(this.start_date).add(1, 'days').format('YYYY-MM-DD');
                            } else {
                                this.time_to = null;
                                this.formatedToChange();
                            }
                        });
                }
            },
            getShiftLength() {
                let time1 = this.formatedFrom.split(':');
                time1 = parseInt(time1[0], 10) * 60 + parseInt(time1[1], 10);
                let time2 = this.formatedTo.split(':');
                time2 = parseInt(time2[0], 10) * 60 + parseInt(time2[1], 10);
                let diff = time2 - time1;
                if (diff >= 300) {
                    this.needsLunch = true;
                } else {
                    this.needsLunch = false;
                    this.isLunch = false;
                }
            },
            submit(event) {
                if (this.isErrors()) {
                    event.stopPropagation();
                    event.preventDefault();
                } else {
                    this.$loading.show();
                }
            },
            formatedFromChange() {
                this.formatedFrom = this.format12To24(this.time_from);
                this.isErrors();
            },
            formatedToChange() {
                this.formatedTo = this.format12To24(this.time_to);
                this.isErrors();
            },
            startDateChange() {
                if (this.start_date > this.end_date) {
                    this.end_date = this.start_date;
                }
                this.isErrors();
            },
            endDateChange() {
                if (this.start_date > this.end_date) {
                    this.end_date = this.start_date;
                }
                this.isErrors();
            },
            setMultiDay() {
                this.multiDays = !this.multiDays;
                if (!this.multiDays) {
                    this.end_date = this.start_date;
                }
            },
            timesByDate(date) {
                if (this.isAdmin) {
                    return this.times;
                }
                if (this.today == date) {
                    return this.timesFromNow;
                }
                return this.times;
            }
        },
        computed: {
            today() {
                return moment().format('YYYY-MM-DD');
            },
            tomorrow() {
                return moment().add(1, 'days').format('YYYY-MM-DD');
            },
            startTitle() {
                if (this.start_date == this.today) {
                    return 'Today';
                }
                if (this.start_date == this.tomorrow) {
                    return 'Tomorrow';
                }
                return moment(this.start_date).format('dddd, MMMM D, YYYY');
            },
            endTitle() {
                if (this.end_date == this.today) {
                    return 'Today';
                }
                if (this.end_date == this.tomorrow) {
                    return 'Tomorrow';
                }
                return moment(this.end_date).format('dddd, MMMM D, YYYY');
            },
            multiDaysTitle() {
                return !this.multiDays ? 'Switch to Multi-Day' : 'Switch to One-Day';
            },
            minEndDate() {
                return this.start_date;
            },
            maxEndDate() {
                return moment(this.start_date).add(6, 'days').format('YYYY-MM-DD');
            }
        },
        mounted() {
            this.getShiftLength();
        }
    }
</script>
