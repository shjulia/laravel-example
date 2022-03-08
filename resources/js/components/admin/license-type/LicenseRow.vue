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
            <div class="col-md-6">
                <div class="form-group">
                    <button @click.prevent.stop="selectAll()" class="btn btn-light">Select All States</button>
                    <label class="col-form-label">State</label>
                    <select
                            :id="'states' + i"
                            class="form-control select2-row"
                            :name="'states[' + i + '][]'"
                            v-model="states"
                            multiple
                            v-select2
                    >
                        <option></option>
                        <option
                                v-for="state in statesList"
                                :value="state.id"
                        >{{ state.title }}</option>
                    </select>
                    <span v-if="isHasError('states.' + i)" class="invalid-feedback" role="alert">
                    <strong>{{ getFirstError('states.' + i) }}</strong>
                </span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group form-check">
                    <label class="form-check-label">
                        <input type="checkbox"
                               class="form-check-input"
                               value="1"
                               :name="'required[' + i + ']'"
                               v-model="required"
                        />
                        License required
                    </label>
                    <span v-if="isHasError('required.' + i)" class="invalid-feedback" role="alert">
                    <strong>{{ getFirstError('required.' + i) }}</strong>
                </span>
                </div>
            </div>
            <div class="col-md-1">
                <button class="btn btn-danger" @click.prevent.stop="remove(i)"><i class="fa fa-trash"></i></button>
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
            'initStates',
            'initRequired'
        ],
        data () {
            return {
                position: this.initPosition,
                states: this.initStates,
                required: this.initRequired,
                removed: []
            }
        },
        computed: {
            positions() {
                return this.$parent.positions;
            },
            statesList() {
                return this.$parent.states;
            },
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
                this.$nextTick(() => {
                    $('#states' + i).select2();
                });
            },
            selectAll() {
                if (Object.keys(this.states).length == 0) {
                    this.states = this.$parent.statesIds();
                } else {
                    this.states = [];
                }
            }
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