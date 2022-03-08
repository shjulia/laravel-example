<template>
    <div class="analytics_div">
        <div class="row">
            <div class="col-6">
                <GChart
                        v-if="dataProviders"
                        type="Table"
                        :data="dataProviders"
                        :options="getOptionsProviders()"
                />
            </div>
            <div class="col-6">
                <GChart
                        v-if="dataPractice"
                        type="Table"
                        :data="dataPractice"
                        :options="getOptionsPractice()"
                />
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
                dataProviders: null,
                dataPractice: null
            }
        },
        methods: {
            getData(url)
            {
                let loader = this.$loading.show();
                axios({
                    method: 'GET',
                    url: url
                })
                    .then(response => {
                        this.dataProviders = response.data.providerData;
                        this.dataPractice = response.data.practiceData;
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
            getOptionsProviders()
            {
                return {
                    title: 'Providers Top List',
                    showRowNumber: true,
                    width: '100%'
                }
            },
            getOptionsPractice()
            {
                return {
                    title: 'Practice Top List',
                    showRowNumber: true,
                    width: '100%'
                }
            },
        },
        mounted() {
            this.getData(this.url);
        }
    }
</script>
