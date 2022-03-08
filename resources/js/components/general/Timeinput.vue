<template>
    <div>
        <input type="text" :name="minName" v-model="minVal"/>
        <input type="text" :name="maxName" v-model="maxVal"/>
        <i class="fa fa-clock-o" @click="$parent.openClock(minVal, maxVal)"></i>
        <div id="clock" v-show="showClock"></div>
    </div>
</template>

<script>
    export default {
        props: [
            'day',
            'minValueInit',
            'maxValueInit',
            'minName',
            'maxName'
        ],
        data () {
            return {
                minVal: this.minValueInit,
                maxVal: this.maxValueInit,
                showClock: false
            }
        },
        methods: {
            openClock(minVal, maxVal) {
                this.showClock = !this.showClock;
                if (this.showClock) {
                    //$('#' + this.idName).timerangewheel({
                    $('#clock').timerangewheel({
                        width: 200,
                        height: 200,
                        indicatorWidth: 12,
                        handleRadius: 15,
                        handleStrokeWidth: 1,
                        accentColor: '#f37d1f',
                        handleIconColor: "#fff",
                        handleStrokeColor: "#fff",
                        handleFillColorStart: "#374149",
                        handleFillColorEnd: "#374149",
                        tickColor: "#1f2021",
                        indicatorBackgroundColor: "#999",
                        data: {"start": minVal, "end": maxVal},
                        onChange: (timeObj) => {
                            this.minVal = timeObj.start;
                            this.maxVal = timeObj.end;
                        }
                    });
                } else {
                    $('#clock').html('');
                }
            }
        },
        mounted() {

        }
    }
</script>