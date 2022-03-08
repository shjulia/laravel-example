<template>
    <div class="row_av_time">
        <div class="row" v-if="from.A && to.A && inDays.length">
            <div class="col-12 col-md-3">
                <span class="day">{{ dayTitle }} <!--<i class="fa fa-calendar" aria-hidden="true"></i>--></span>
                <input type="hidden" v-model="inDays" :name="'day[' + iterator + ']'"/>
            </div>
            <div class="col-8 col-md-6 text-center">
                <span class="time_base">{{ from.hh + ':' + from.mm}}</span><span class="time_a">{{ ' ' + from.A}}</span>
                <i class="fa fa-arrow-right arrow-time"></i>
                <span class="time_base">{{ to.hh + ':' + to.mm}}</span><span class="time_a">{{ ' ' + to.A}}</span>
                <input type="hidden" :value="formatedFrom" :name="'from[' + iterator + ']'"/>
                <input type="hidden" :value="formatedTo" :name="'to[' + iterator + ']'"/>
            </div>
            <div class="col-4 col-md-3 text-right">
                <a href="#" class="clock-time" @click.prevent="changeEdit()"><i class="fa fa-clock-o" ></i><span>Edit</span></a>
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
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        <div class="row edit-block">
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">From</label>
                                    <!--<vue-timepicker
                                            v-model="from"
                                            :minute-interval="10"
                                            format="hh:mm A"
                                            @change="formatedFromChange()"
                                    ></vue-timepicker>-->
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
                            <div class="col-6">
                                <div class="form-group">
                                    <label class="col-form-label">To</label>
                                    <!--<vue-timepicker
                                            v-model="to"
                                            :minute-interval="10"
                                            format="hh:mm A"
                                            @change="formatedToChange()"
                                    ></vue-timepicker>-->
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
                            <div class="col-10">
                                <div class="form-group">
                                    <label class="col-form-label">For Day</label>
                                    <!--<select
                                            class="form-control select2-row"
                                            v-model="day"
                                            ref="selecttwo"
                                            v-select2
                                            @change="selectDay()"
                                    >
                                        <option></option>
                                        <option
                                                v-for="(title, index) in days"
                                                :value="index"
                                        >{{ title }}</option>
                                    </select>-->
                                    <div v-for="(title, index)  in days" class="custom-control custom-checkbox">
                                        <input
                                                type="checkbox"
                                                class="custom-control-input"
                                                :id="'day' + index"
                                                @change="changeDay(index)"
                                                :checked="inDays.indexOf(parseInt(index)) !== -1"
                                        >
                                        <label class="custom-control-label" :for="'day' + index">{{ title }}</label>
                                    </div>

                                    <span class="invalid-feedback" role="alert" v-if="error_day">
                                        <strong>{{ error_day }}</strong>
                                    </span>
                                </div>
                            </div>
                            <div class="col-2">
                                <div class="form-group">
                                    <label class="col-form-label"> </label>
                                    <i class="fa fa-trash remove-a" @click="removeInterval(id)"></i>
                                </div>
                            </div>
                            <div class="col-12">
                                <button @click.prevent.stop="changeEdit()" class="btn form-button">Apply</button>
                                <a href="#" @click.prevent="changeEdit()" class="pull-right cancel">Cancel</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</template>

<script>
    import {TimeRowMixin} from './TimeRowMixin';
    export default {
        mixins: [TimeRowMixin]
    }
</script>
