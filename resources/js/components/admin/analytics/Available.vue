<template>
    <div class="analytics_div">
        <div class="row">
            <div class="col-md-12">
                <div>
                    <div class="form-group">
                        <select
                            id="state"
                            class="form-control select2"
                            v-model="state"
                            @change="change()"
                            v-select2
                        >
                            <option value="all">All States</option>
                            <option
                                v-for="st in states"
                                :value="st.short_title"
                            >{{ st.title }}</option>
                        </select>
                    </div>

                    <GChart
                        v-if="data"
                        type="ColumnChart"
                        :data="data"
                        :options="chartOptions"
                    />
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    export default {
        props: [
            'data',
            'states',
            'baseUrl',
            'stateInit'
        ],
        data () {
            return {
                state: this.stateInit,
                chartOptions: {
                    title: 'Available Providers',
                    legend: { position: 'top' },
                    //isStacked: true,
                    vAxis: {format: 'short', minValue: 0},
                    height: 400
                }
            }
        },
        methods: {
            change() {
                if (this.state === 'all') {
                    location.href = this.baseUrl;
                    return;
                }
                location.href = this.baseUrl + '/' + this.state;
            }
        },
        computed: {
        },
        mounted() {
            this.$nextTick(() => {
                $('.select2').select2({
                    width: '200px'
                });
            });
        }
    }
</script>
