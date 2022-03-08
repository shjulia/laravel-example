<template>
    <div class="analytics_div">
        <analytics-time
                :url="url"
        ></analytics-time>
        <GChart
                v-if="data"
                type="LineChart"
                :data="data"
                :options="getOptions()"
        />
    </div>
</template>
<script>
    import Swal from 'sweetalert2'
    import {StringToDateMixin} from '../../mixins/StringToDateMixin';
    export default {
        props: [
            'url'
        ],
        data () {
            return {
                data: null,
            }
        },
        mixins: [StringToDateMixin],
        methods: {
            getData(url)
            {
                let loader = this.$loading.show();
                axios({
                    method: 'GET',
                    url: url
                })
                    .then(response => {
                        this.data = this.stringToDate(response.data.result, 3, 0);
                    })
                    .catch(error => {
                        Swal.fire({
                            type: 'error',
                            title: 'Oops...',
                            text: error.response.data.error ? error.response.data.error : 'Retrieving data error'
                        });
                    })
                    .finally(response => {
                        loader.hide();
                    });
            },
            getOptions()
            {
                return {
                    title: 'Total Number of Providers and Practices',
                    legend: {
                        'position': 'bottom'
                    },
                    pointSize: 5
                }
            }
        },
        mounted() {
            this.getData(this.url);
        }
    }
</script>