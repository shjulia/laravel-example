<template>
    <div class="analytics_div">
        <ul class="nav nav-tabs" id="profTab" role="tablist">
            <li class="nav-item">
                <a class="nav-link active" id="profit-tab" data-toggle="tab" href="#profit" role="tab" aria-controls="profit" aria-selected="true">All</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="month-tab" data-toggle="tab" href="#month" role="tab" aria-controls="month" aria-selected="false">By months</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" id="future-tab" data-toggle="tab" href="#future" role="tab" aria-controls="future" aria-selected="false">Future</a>
            </li>
        </ul>
        <div class="tab-content dh" id="profTabContent">
            <div class="tab-pane show active" id="profit" role="tabpanel" aria-labelledby="providers-tab">
                <div class="mt-2">
                    <analytics-time
                        :url="''"
                    ></analytics-time>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            <GChart
                                v-if="data[1]"
                                type="BarChart"
                                :data="data"
                                :options="chartOptions"
                            />
                            <p v-else>No data found</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="month" role="tabpanel" aria-labelledby="month-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            <GChart
                                v-if="monthData[1]"
                                type="ColumnChart"
                                :data="monthData"
                                :options="monthsChartOptions"
                            />
                            <p v-else>No data found</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="future" role="tabpanel" aria-labelledby="future-tab">
                <div class="row">
                    <div class="col-md-12">
                        <div class="">
                            <GChart
                                v-if="futureData[1]"
                                type="ColumnChart"
                                :data="futureData"
                                :options="futureChartOptions"
                            />
                            <p v-else>No data found</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import Swal from 'sweetalert2'

    export default {
        props: [
            'url',
            'monthUrl',
            'futureUrl'
        ],
        data () {
            return {
                data: [],
                monthData: [],
                futureData: [],
                chartOptions: {
                    title: 'Show Total Revenue Generated compared to profit',
                    legend: { position: 'bottom' },
                    hAxis: {format: 'currency'}
                },
                monthsChartOptions: {
                    title: 'Show Total Revenue Generated compared to profit by months',
                    legend: { position: 'bottom' },
                    vAxis: {format: 'currency'}
                },
                futureChartOptions: {
                    title: 'Show Total Revenue Generated compared to profit for future shifts',
                    legend: { position: 'bottom' },
                    vAxis: {format: 'currency', minValue: 0}
                }
            }
        },
        methods: {
            request(url) {
                let loader = this.$loading.show();
                return axios({
                    method: 'GET',
                    url: url
                })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: "Something went wrong. Can't load data for profit chart"
                        });
                    })
                    .finally(m => {
                        loader.hide();
                    });
            },
            getData(extraUrl) {
                this.request(this.url + extraUrl)
                    .then(response => {
                        this.data = response.data;
                    });
                this.request(this.monthUrl + extraUrl)
                    .then(response => {
                        this.monthData = response.data;
                    });
                this.request(this.futureUrl + extraUrl)
                    .then(response => {
                        this.futureData = response.data;
                    });
            },
        },
        mounted() {
            this.getData('');
        }
    }
</script>
