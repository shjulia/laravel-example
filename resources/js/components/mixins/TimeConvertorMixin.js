export const TimeConvertorMixin = {
    data () {
        return {
            times: [
                {hh: "12", mm: "00", A: "AM"},
                {hh: "12", mm: "30", A: "AM"},
                {hh: "01", mm: "00", A: "AM"},
                {hh: "01", mm: "30", A: "AM"},
                {hh: "02", mm: "00", A: "AM"},
                {hh: "02", mm: "30", A: "AM"},
                {hh: "03", mm: "00", A: "AM"},
                {hh: "03", mm: "30", A: "AM"},
                {hh: "04", mm: "00", A: "AM"},
                {hh: "04", mm: "30", A: "AM"},
                {hh: "05", mm: "00", A: "AM"},
                {hh: "05", mm: "30", A: "AM"},
                {hh: "06", mm: "00", A: "AM"},
                {hh: "06", mm: "30", A: "AM"},
                {hh: "07", mm: "00", A: "AM"},
                {hh: "07", mm: "30", A: "AM"},
                {hh: "08", mm: "00", A: "AM"},
                {hh: "08", mm: "30", A: "AM"},
                {hh: "09", mm: "00", A: "AM"},
                {hh: "09", mm: "30", A: "AM"},
                {hh: "10", mm: "00", A: "AM"},
                {hh: "10", mm: "30", A: "AM"},
                {hh: "11", mm: "00", A: "AM"},
                {hh: "11", mm: "30", A: "AM"},
                {hh: "12", mm: "00", A: "PM"},
                {hh: "12", mm: "30", A: "PM"},
                {hh: "01", mm: "00", A: "PM"},
                {hh: "01", mm: "30", A: "PM"},
                {hh: "02", mm: "00", A: "PM"},
                {hh: "02", mm: "30", A: "PM"},
                {hh: "03", mm: "00", A: "PM"},
                {hh: "03", mm: "30", A: "PM"},
                {hh: "04", mm: "00", A: "PM"},
                {hh: "04", mm: "30", A: "PM"},
                {hh: "05", mm: "00", A: "PM"},
                {hh: "05", mm: "30", A: "PM"},
                {hh: "06", mm: "00", A: "PM"},
                {hh: "06", mm: "30", A: "PM"},
                {hh: "07", mm: "00", A: "PM"},
                {hh: "07", mm: "30", A: "PM"},
                {hh: "08", mm: "00", A: "PM"},
                {hh: "08", mm: "30", A: "PM"},
                {hh: "09", mm: "00", A: "PM"},
                {hh: "09", mm: "30", A: "PM"},
                {hh: "10", mm: "00", A: "PM"},
                {hh: "10", mm: "30", A: "PM"},
                {hh: "11", mm: "00", A: "PM"},
                {hh: "11", mm: "30", A: "PM"}
            ]
        }
    },
    methods: {
        format12To24(time) {
            if (!time) {
                return '';
            }
            if (time.A == 'AM' && time.hh == 12) {
                return '00' + ':' + time.mm;
            }
            if (time.A == 'PM' && time.hh == 12) {
                return time.hh + ':' + time.mm;
            }
            if (time.A == 'AM') {
                return time.hh + ':' + time.mm;
            } else if (time.A == 'PM') {
                return parseInt(time.hh) + 12 + ':' + time.mm;
            }
            return '';
        },
        format24To12(time) {
            if (!time) {
                return {hh: "", mm: "", A: ""};
            }
            time = time.split(':');
            if (!time[0] || !time[1]) {
                return {hh: "", mm: "", A: ""};
            }
            let hh = time[0], mm = time[1], A;
            if (time[0] == '00') {
                A = 'AM';
                hh = 12;
            } else if (time[0] == '12') {
                A = 'PM';
                hh = 12;
            } else if ((time[0] > 12) || (time[0] == 12 && parseInt(time[1]) >= 0)) {
                A = 'PM';
                hh = time[0] - 12;
            } else {
                A = 'AM';
            }
            return {hh: hh.toString().length == 1 ? '0' + hh : hh, mm: mm, A: A};
        },
        format24To12Formated(time) {
            time = this.format24To12(time);
            return time.hh + ':' + time.mm + ' ' + time.A;
        },
        dateToHuman(date) {
            let today = moment().format('YYYY-MM-DD');
            if (date == today) {
                return 'Today';
            }
            let tomorrow = moment().add(1, 'days').format('YYYY-MM-DD');
            if (date == tomorrow) {
                return 'Tomorrow';
            }
            let yesterday = moment().add(-1, 'days').format('YYYY-MM-DD');
            if (date == yesterday) {
                return 'Yesterday';
            }
            return moment(date).format('MMM D, YYYY');
        }
    },
    computed: {
        timesFromNow() {
            let now = moment.utc(moment().format('hh:mm A'), "hh:mm A");
            return this.times.filter((time) => {
                return (moment.utc(time.hh + ':' + time.mm + ' ' + time.A, "hh:mm A")).diff(now, 'minutes') >= 30;
            });
        }
    }
};
