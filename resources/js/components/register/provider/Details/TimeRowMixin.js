import {TimeConvertorMixin} from '../../../mixins/TimeConvertorMixin';
export const TimeRowMixin = {
    props: [
        'fromInit',
        'toInit',
        'inDaysInit',
        'id',
        'iterator'
    ],
    data () {
        return {
            from: this.format24To12(this.fromInit),
            to: this.format24To12(this.toInit),
            inDays: this.inDaysInit,
            formatedFrom: this.fromInit,
            formatedTo: this.toInit,
            error_time_from: false,
            error_time_to: false,
            error_day: false,
            try: 0,
            showEdit: false,
            //shortDays: {1: 'Mon', 2: 'Tue', 3: 'Wed', 4: 'Thu', 5: 'Fri', 6: 'Sat', 7: 'Sun'}
        }
    },
    mixins: [TimeConvertorMixin],
    computed: {
        days() {
            return this.$parent.days;
        },
        dayTitle() {
            if (Object.keys(this.inDays).length == 1) {
                return this.days[this.inDays[0]];
            }
            let title = '';
            _.forOwn(this.inDays, (value, key) => {
                title += this.days[this.inDays[key]] + ', ';
            });
            return title.substring(0, title.length - 2);
            //return 'Multiple';
        }
    },
    methods: {
        changeEdit() {
            this.showEdit = !this.showEdit;
            if (this.showEdit) {
                this.$nextTick(() => {
                    $(this.$refs.timeModal).modal('show');
                    this.initSelect2();
                });
            } else {
                if (this.isErrors()) {
                    this.showEdit = true;
                    return;
                }
                $(this.$refs.timeModal).modal('hide');
            }
        },
        formatedFromChange() {
            this.formatedFrom = this.format12To24(this.from);
            this.isErrors();
            this.try--;
        },
        formatedToChange() {
            this.formatedTo = this.format12To24(this.to);
            this.isErrors();
            this.try--;
        },
        changeDay(index) {
            index = parseInt(index);
            if (this.inDays.indexOf(index) === -1) {
                //this.inDays.push(index);
                this.$set(this.inDays, Object.keys(this.inDays).length, index);
            } else {
                //this.$set(this.inDays, this.inDays.indexOf(index), null);
                this.inDays = this.inDays.filter((day) => {
                    return day !== index;
                });
            }
            this.isErrors();
        },
        selectDay() {
            this.error_day = false;
        },
        clearErrors() {
            this.error_time_from = false;
            this.error_time_to = false;
            this.error_day = false;
        },
        isErrors() {
            if (this.try > 0) {
                return false;
            }
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
            if (!this.inDays.length) {
                this.error_day = "Days field is required";
                errors = true;
            }
            if (!errors) {
                return this.compareTimes(this.formatedFrom, this.formatedTo);
            }
            return errors;
        },
        compareTimes(time1, time2) {
            time1 = time1.split(':');
            time2 = time2.split(':');
            if (time2[0] == '00' && parseInt(time2[1], 10) > 0) {
                this.error_time_to = "Time to minutes must be '00'";
                return true;
            }
            time1 = parseInt(time1[0], 10) * 60 + parseInt(time1[1], 10);
            time2 = (parseInt(time2[0], 10) * 60 == 0 ? 24 * 60 : parseInt(time2[0], 10) * 60) + parseInt(time2[1], 10);
            if (time1 > time2) {
                this.error_time_to = "Time to field must be greater then time from field is required";
                return true;
            }
            if ((time2 - time1) < 120) {
                this.error_time_to = "Minimum shift is 2 hours";
                return true;
            } else if ((time2 - time1) > 960) {
                this.error_time_to = "Maximum shift is 16 hours";
                return true;
            }
            return false;
        },
        initSelect2() {
            /*this.$nextTick(() => {
                $(this.$refs.selecttwo).select2({
                    placeholder: '',
                    minimumResultsForSearch: -1,
                    width: '100%'
                });
            });*/
        },
        removeInterval(id) {
            $(this.$refs.timeModal).modal('hide');
            this.$parent.removeInterval(id);
        }
    },
    mounted() {
        if(!(this.fromInit && this.toInit && this.inDaysInit.length)) {
            this.changeEdit();
        }
    }
};
