<template>
    <div>
        <div class="row" :class="'row-' + i" :key="i" v-if="removed.indexOf(i) == -1">
            <div class="col-md-3">
                <div class="form-group">
                    <label class="col-form-label">Position</label>
                    <select
                        :id="'position' + i"
                        class="form-control select2-row"
                        :name="'position[' + i + ']'"
                        v-model="position"
                        v-select2
                    >
                        <option></option>
                        <option
                            v-for="pos in positions"
                            :value="pos.id"
                        >{{ pos.title }}</option>
                    </select>
                    <span v-if="isHasError('position.' + i)" class="invalid-feedback" role="alert">
                    <strong>{{ getFirstError('position.' + i) }}</strong>
                </span>
                </div>
            </div>
            <div class="col-md-2">
                <label> </label>
                <Cinput
                    label="Rate"
                    :id="'rate[' + i + ']'"
                    type="text"
                    :name="'rate[' + i + ']'"
                    :has-errors="isHasError('rate.' + i)"
                    :first-error="getFirstError('rate.' + i)"
                    :is-mat="true"
                    :init-model="rate"
                    init-model-attr="rate"
                ></Cinput>
            </div>
            <div class="col-md-2">
                <label> </label>
                <Cinput
                    label="Minimum profit"
                    :id="'minimum_profit[' + i + ']'"
                    type="text"
                    :name="'minimum_profit[' + i + ']'"
                    :has-errors="isHasError('minimum_profit.' + i)"
                    :first-error="getFirstError('minimum_profit.' + i)"
                    :is-mat="true"
                    :init-model="minimum_profit"
                    init-model-attr="minimum_profit"
                ></Cinput>
            </div>
            <div class="col-md-2">
                <label> </label>
                <Cinput
                    label="Surge price"
                    :id="'surge_price[' + i + ']'"
                    type="text"
                    :name="'surge_price[' + i + ']'"
                    :has-errors="isHasError('surge_price.' + i)"
                    :first-error="getFirstError('surge_price.' + i)"
                    :is-mat="true"
                    :init-model="surge_price"
                    init-model-attr="surge_price"
                ></Cinput>
            </div>
            <div class="col-md-2">
                <label> </label>
                <Cinput
                    label="Max day rate"
                    :id="'max_day_rate[' + i + ']'"
                    type="text"
                    :name="'max_day_rate[' + i + ']'"
                    :has-errors="isHasError('max_day_rate.' + i)"
                    :first-error="getFirstError('max_day_rate.' + i)"
                    :is-mat="true"
                    :init-model="max_day_rate"
                    init-model-attr="max_day_rate"
                ></Cinput>
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger mt-4" @click.prevent.stop="remove(i)"><i class="fa fa-trash"></i></button>
            </div>
        </div>
        <div v-else>
            <button class="btn btn-success" @click.prevent.stop="returnRow(i)">return row</button>
        </div>
    </div>
</template>

<script>
    export default {
        props: [
            'i',
            'initPosition',
            'initRate',
            'initMinimumProfit',
            'initSurgePrice',
            'initMaxDayRate'
        ],
        data () {
            return {
                position: this.initPosition,
                removed: [],
                minimum_profit: this.initMinimumProfit,
                rate: this.initRate,
                surge_price: this.initSurgePrice,
                max_day_rate: this.initMaxDayRate
            }
        },
        computed: {
            positions() {
                return this.$parent.positions;
            }
        },
        methods: {
            isHasError(field) {
                return this.$parent.isHasError(field);
            },
            getFirstError(field) {
                return this.$parent.getFirstError(field);
            },
            remove(i) {
                this.removed.push(i);
            },
            returnRow(i) {
                this.removed = _.remove(this.removed, (n) => {
                    return n != i;
                });
            },
        },
        mounted() {
            this.$nextTick(() => {
                $('.row-' + this.i + ' .select2-row').each(function () {
                    $(this).select2();
                });
            });
        }
    }
</script>
