<script>

    import Swal from 'sweetalert2';

    export default {
        data(){
            return {
                selectedDay: 'All days',
                days: [],
                money: this.balance
            }
        },
        props: [
            'items',
            'withdrawUrl',
            'balance',
        ],
        methods: {
            selectDay(timestamp) {
                this.$parent.selectDay(timestamp);
                this.updateSelectedDay(timestamp);
                this.makeDayActive(timestamp);
            },
            updateSelectedDay(timestamp) {
                if(timestamp == null) {
                    this.selectedDay = 'All days';
                    return;
                }

                this.selectedDay = moment.unix(timestamp).format('MMM Do, dddd');
            },
            isDateHasShifts(date) {
                return this.items.some(item => item.date === date);
            },
            makeDayActive(timestamp) {
                let day = moment.unix(timestamp).format('D');
                this.days.forEach(item =>
                    item.active = (day === item.day)
                );
            },
            fillDays() {
                let days = [];
                for(let i=0; i<=13; i++) {
                    let mnt  = moment().add(i, 'days');
                    days.push({
                        day: mnt.format('D'),
                        timestamp: mnt.unix(),
                        hasShift: this.isDateHasShifts( mnt.format('YYYY-MM-DD') ),
                        active: false
                    });
                }
                this.days = days;
            },
            withdraw() {
                let loader = this.$loading.show();

                axios.post(this.withdrawUrl).then(response => {
                    this.money = 0;
                    loader.hide();
                    Swal.fire({
                        type: 'success',
                        title: 'Success',
                        text: 'Transfer was successful. You will receive your money soon.',
                    })
                }).catch(error => {
                    console.log(error.response.data);
                    loader.hide();
                    Swal.fire({
                        type: 'error',
                        title: 'Oops...',
                        text: 'Transfer was not successful. ' + error.response.data.error
                    })
                })
            }
        },
        created: function() {
            this.fillDays();
        }
    }
</script>
