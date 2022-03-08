<template>
    <div class="analytics_div mb-1">
        <p class="text-center mt-2"><b>{{ title}}</b></p>
        <h1 class="text-center mt-3"><span class="badge badge-success">{{ data }} {{ unit }}</span></h1>
        <a v-if="viewUrl" :href="viewUrl"><i class="fa fa-expand"></i></a>
    </div>
</template>

<script>
    import Swal from 'sweetalert2'

    export default {
        props: [
            'url',
            'title',
            'unit',
            'viewUrl'
        ],
        data () {
            return {
                data: []
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
