<template>
    <div class="row_av_time">
        <div class="row" v-if="from.A && to.A && inDays.length">
            <div class="col-1">
                <i class="fa fa-minus-circle remove-circle" @click="removeInterval(id)"></i>
            </div>
            <div class="col-8">
                <span class="time_base">{{ from.hh + ':' + from.mm}}</span><span class="time_a">{{ ' ' + from.A}}</span>
                <i class="fa fa-minus arrow-time"></i>
                <span class="time_base">{{ to.hh + ':' + to.mm}}</span><span class="time_a">{{ ' ' + to.A}}</span>
                <br/>
                <span class="day">{{ dayTitle }}</span>
                <input type="hidden" v-model="inDays" :name="'day[' + iterator + ']'"/>
                <input type="hidden" :value="formatedFrom" :name="'from[' + iterator + ']'"/>
                <input type="hidden" :value="formatedTo" :name="'to[' + iterator + ']'"/>
            </div>
            <div class="col-3 text-right">
                <a href="#" class="clock-time" @click.prevent="changeEdit()"><i class="fa fa-chevron-right"></i></a>
            </div>
        </div>
        <hr v-if="from.A && to.A && inDays.length"/>

        <div
            v-if="showEdit"
            class="modal fade"
            data-backdrop="static"
            data-keyboard="false"
            ref="timeModal"
            id="timeModal"
            tabindex="-1"
            role="dialog"
            aria-hidden="true"
        >
            <div class="modal-dialog modal-md modal-dialog-centered" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h2 class="modal-title" id="exampleModalLabel">Edit Your Availability</h2>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row edit-block">
                            <div class="col-5">
                                <div class="form-group fgs">
                                    <label class="col-form-label label">From</label>
                                    <select
                                        v-model="from"
                                        class="form-control"
                                        @change="formatedFromChange()"
                                    >
                                        <option
                                            v-for="time in times"
                                            :value="time"
                                        >
                                            {{ time.hh + ':' + time.mm + " " + time.A }}
                                        </option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" v-if="error_time_from">
                                        <strong>{{ error_time_from }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-2">
                                <i class="fa fa-clock-o grey"></i>
                            </div>
                            <div class="col-5">
                                <div class="form-group fgs">
                                    <label class="col-form-label label">To</label>
                                    <select
                                        v-model="to"
                                        class="form-control"
                                        @change="formatedToChange()"
                                    >
                                        <option
                                            v-for="time in times"
                                            :value="time"
                                        >
                                            {{ time.hh + ':' + time.mm + " " + time.A }}
                                        </option>
                                    </select>
                                    <span class="invalid-feedback" role="alert" v-if="error_time_to">
                                        <strong>{{ error_time_to }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group days-form-group">
                                    <label class="col-form-label label">For Days</label>
                                    <div class="bubbles d-flex">
                                        <div
                                            class="flex-fill"
                                        >
                                            <span
                                                @click="changeDay(Object.keys(days).length - 1)"
                                                class="bubble  flex-fill"
                                                :class="{'active' : isFieldExists(Object.keys(days).length - 1)}"
                                            >{{ days[Object.keys(days).length - 1].substring(0, 1) }}</span>
                                        </div>
                                        <div
                                            v-for="(bubble, key) in days"
                                            v-if="key != Object.keys(days).length - 1"
                                            class="flex-fill"
                                        >
                                            <span
                                                @click="changeDay(key)"
                                                class="bubble"
                                                :class="{'active' : isFieldExists(key)}"

                                            >{{ bubble.substring(0, 1) }}</span>
                                        </div>

                                    </div>
                                    <span class="invalid-feedback" role="alert" v-if="error_day">
                                        <strong>{{ error_day }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="col-12 text-center">
                            <button @click.prevent.stop="changeEdit()" class="btn btn-bg-grad mt-4">Save</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</template>

<script>
    import {TimeRowMixin} from '../Details/TimeRowMixin';
    export default {
        mixins: [TimeRowMixin],
        computed: {
            days() {
                return this.$parent.days;
            },
            dayTitle() {
                if (Object.keys(this.inDays).length == 1) {
                    return this.days[this.inDays[0]];
                }
                let title = '';
                _.forOwn(this.inDays, (value, key) => {
                    title += this.days[this.inDays[key]].substring(0, 3) + ', ';
                });
                return title.substring(0, title.length - 2);
            }
        },
        methods: {
            selectBuble(field) {
                if (!this.isFieldExists(field)) {
                    this.inDays.push(field);
                } else {
                    this.inDays = _.remove(this.inDays, (n) => {
                        return n !== field;
                    });
                }
            },
            isFieldExists(field) {
                field = parseInt(field);
                return this.inDays.indexOf(field) !== -1;
            }
        }
    }
</script>
