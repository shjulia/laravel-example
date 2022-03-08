<template>
    <div class="analytics_div">
        <analytics-time
                :url="url"
        ></analytics-time>
        <div class="row">
            <div class="col-md-12">
                <div class="">
                    <GChart
                            v-if="data"
                            type="PieChart"
                            :data="data"
                            :options="chartOptions"
                    />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'

    export default {
        props: [
            'url'
        ],
        data () {
            return {
                data: [],
                chartOptions: {
                    title: 'Pie Chart of Providers',
                    legend: { position: 'bottom' }
                }
            }
        },
        methods: {
            getData(url) {
                let loader = this.$loading.show();
                axios({
                    method: 'GET',
                    url: url
                })
                    .then(response => {
                        this.data = response.data;
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: "Something went wrong. Can't load data."
                        });
                    })
                    .finally(m => {
                        loader.hide();
                    });
            }
        },
        mounted() {
            this.getData(this.url);
        }
    }
</script>
