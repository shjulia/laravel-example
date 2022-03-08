<template>
    <div class="analytics_div">
        <analytics-time
                :url="url"
        ></analytics-time>
        <div class="dropdown">
            <button class="btn btn-secondary dropdown-toggle" type="button" id="posMenuButton" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                {{ getPosition }}
            </button>
            <div class="dropdown-menu" aria-labelledby="posMenuButton">
                <a
                    class="dropdown-item"
                    href="#"
                    @click.prevent="selectPosAll()"
                >All</a>
                <a
                   v-for="(pos, index) in positions"
                   class="dropdown-item"
                   href="#"
                   @click.prevent="selectPos(index)"
                >{{ pos.title }}</a>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="">
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
    import Swal from 'sweetalert2'
    import {StringToDateMixin} from '../../mixins/StringToDateMixin';

    export default {
        props: [
            'url',
            'positions'
        ],
        data () {
            return {
                data: [],
                chartOptions: {
                    title: 'Total Hours worked per day',
                    legend: { position: 'left' }
                },
                position: null,
                lastUrl: null
            }
        },
        mixins: [StringToDateMixin],
        methods: {
            getData(url) {
                let loader = this.$loading.show();
                if (url != this.url) {
                    this.lastUrl = url;
                }
                if (this.position) {
                    let separator = url === this.url ? '?' : '&';
                    url += separator + 'position=' + this.position;
                }
                axios({
                    method: 'GET',
                    url: url
                })
                    .then(response => {
                        this.data = this.stringToDate(response.data, 2, 0);
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
            },
            selectPos(id) {
                this.position = id;
                this.getData(this.lastUrl ? this.lastUrl : this.url);
            },
            selectPosAll() {
                this.position = null;
                this.getData(this.lastUrl ? this.lastUrl : this.url);
            }
        },
        computed: {
            getPosition() {
                return this.position ? this.positions[this.position].title : 'All positions';
            }
        },
        mounted() {
            this.getData(this.url);
        }
    }
</script>