<template>
    <div class="row">
        <div class="col-sm-3">
            <Cinput
                    label="Start Date"
                    id="start_date"
                    type="date"
                    name="start_date"
                    :required="false"
                    :is-mat="true"
                    :init-model="start_date"
                    init-model-attr="start_date"
                    :has-errors="!!dateError"
                    :first-error="dateError"
                    :max="today"
            ></Cinput>
        </div>
        <div class="col-sm-3">
            <Cinput
                    label="End Date"
                    id="end_date"
                    type="date"
                    name="end_date"
                    :required="false"
                    :is-mat="true"
                    :init-model="end_date"
                    init-model-attr="end_date"
                    :max="today"
            ></Cinput>
        </div>
        <div class="col-sm-3">
            <button @click.stop.prevent="searchByDates()" class="btn btn-primary">Search</button>
        </div>
        <div class="col-sm-3">
            <button @click.stop.prevent="allTime()" class="btn btn-outline-success">All time</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'url'
        ],
        data() {
            return {
                start_date: null,
                end_date: moment().format('YYYY-MM-DD'),
                dateError: null
            }
        },
        methods: {
            checkErrors() {
                this.dateError = null;
                if (!this.start_date || !this.end_date) {
                    this.dateError = 'Date must me set';
                    return true;
                }
                if (this.start_date > this.end_date) {
                    this.dateError = 'End date must be grater than start date';
                    return true;
                }
                return false;
            },
            searchByDates() {
                if (this.checkErrors()) {
                    return;
                }
                this.$parent.getData(this.getUrl);
            },
            allTime() {
                this.start_date = null;
                this.$parent.getData(this.url);
            }
        },
        computed: {
            getUrl() {
                let url = this.url;
                if (this.start_date  && this.end_date) {
                    url += '?start_date=' + this.start_date + '&end_date=' + this.end_date;
                }
                return url;
            },
            today() {
                return moment().format('YYYY-MM-DD');
            }
        }
    }
</script>