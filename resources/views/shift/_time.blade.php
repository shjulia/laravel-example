<shifttime
    inline-template
    start-date-init="{{ old('date', $shift->date ?: date('Y-m-d')) }}"
    end-date-init="{{ old('end_date', $shift->end_date ?: date('Y-m-d')) }}"
    time-from-init="{{ old('time_from', $shift->from_time ?: '') }}"
    time-to-init="{{ old('time_to', $shift->to_time ?: '') }}"
    is-admin="{{ $isAdmin }}"
    :lunch-times="{{ collect($lunchTimes) }}"
    lunch-break-init="{{ old('lunch_break', $shift->lunch_break) }}"
    v-cloak
>
    <div class="centralform centralform-long">
        <form method="POST" action="{{ $action }}">
            @csrf
            <div class="inputs">
                <a href="{{ $previousRoute }}" @click="$loading.show()" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                <span class="title">Enter Shift Details for Your Provider</span>
                <hr class="full-hr"/>
                <div class="row">
                    <div class="col-md-12 text-center">
                        <h5>
                            <span>@{{ startTitle }}</span>
                            <span v-if="start_date !== end_date"> - </span>
                            <span v-if="start_date !== end_date">@{{ endTitle }}</span>
                            <a href="#" class="boon-link" @click.prevent="showStartDate()">Edit</a>
                        </h5>
                        <p></p>
                    </div>
                </div>

                <div class="row" v-if="multiDays">
                    <div class="col-md-6 mb-2">
                        <label class="mb-0">Start date</label>
                        <input
                            type="date"
                            class="datepicker-custom form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                            name="start_date"
                            v-model="start_date"
                            min="{{ $isAdmin ? null : date('Y-m-d') }}"
                            :data-content="startTitle"
                            @change="startDateChange()"
                        >
                        @if ($errors->has('start_date'))
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <label class="mb-0">End date</label>
                        <input
                            type="date"
                            class="datepicker-custom form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                            name="end_date"
                            v-model="end_date"
                            :min="minEndDate"
                            :max="maxEndDate"
                            :data-content="endTitle"
                            @change="endDateChange()"
                        >
                        @if ($errors->has('end_date'))
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('end_date') }}</strong>
                                </span>
                        @endif
                    </div>
                </div>
                <div v-else class="row">
                    <div class="col-md-6 mb-2" v-if="startDateShow">
                        <label class="mb-0">Start date</label>
                        <input
                            type="date"
                            class="datepicker-custom form-control{{ $errors->has('start_date') ? ' is-invalid' : '' }}"
                            name="start_date"
                            v-model="start_date"
                            min="{{ $isAdmin ? null : date('Y-m-d') }}"
                            :data-content="startTitle"
                            @change="startDateChange()"
                        >
                        @if ($errors->has('start_date'))
                            <span class="invalid-feedback" role="alert">
                                    <strong>{{ $errors->first('start_date') }}</strong>
                                </span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-2" v-if="startDateShow && start_date != end_date">
                        <label class="mb-0">End date</label>
                        <input
                            type="date"
                            class="datepicker-custom form-control{{ $errors->has('end_date') ? ' is-invalid' : '' }}"
                            name="end_date"
                            v-model="end_date"
                            :min="minEndDate"
                            :max="maxEndDate"
                            :data-content="endTitle"
                            @change="endDateChange()"
                        >
                        @if ($errors->has('end_date'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('end_date') }}</strong>
                            </span>
                        @endif
                    </div>
                    <input type="hidden" v-model="start_date" name="start_date">
                    <input type="hidden" v-model="end_date" name="end_date">
                </div>
                <br>
                <div class="row">
                    <div class="col-sm-6 mb-2">
                        <label class="mb-0"><span v-if="multiDays">Daily </span>Start Time</label>
                        <select
                            v-model="time_from"
                            class="form-control"
                            @change="formatedFromChange(), getShiftLength()"
                        >
                            <option
                                v-for="(time, index) in timesByDate(start_date, true)"
                                :value="time"
                            >
                                @{{ (today === start_date && index == 0) ? 'ASAP' : time.hh + ':' + time.mm + " " + time.A }}
                            </option>
                        </select>
                        <input type="hidden" :value="formatedFrom" name="time_from"/>
                        @if ($errors->has('time_from'))
                            <span class="invalid-feedback" role="alert">
                                        <strong>{{ $errors->first('time_from') }}</strong>
                                    </span>
                        @endif
                        <span class="invalid-feedback" role="alert" v-if="error_time_from">
                                    <strong>@{{ error_time_from }}</strong>
                                </span>
                    </div>
                    <div class="col-sm-6">
                        <label class="mb-0"><span v-if="multiDays">Daily </span>End time</label>
                        <select
                            v-model="time_to"
                            class="form-control"
                            @change="formatedToChange(), getShiftLength()"
                        >
                            <option
                                v-for="(time, index) in timesByDate(end_date)"
                                :value="time"
                                v-if="today !== start_date || index !== 0"
                            >
                                @{{ time.hh + ':' + time.mm + " " + time.A }}
                            </option>
                        </select>
                        <input type="hidden" :value="formatedTo" name="time_to"/>
                        @if ($errors->has('time_to'))
                            <span class="invalid-feedback" role="alert">
                                <strong>{{ $errors->first('time_to') }}</strong>
                            </span>
                        @endif
                        <span class="invalid-feedback" role="alert" v-if="error_time_to">
                            <strong>@{{ error_time_to }}</strong>
                        </span>
                    </div>
                </div>
                <div class="row" v-if="isLunch">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="lunch_break">Lunch break</label>
                            <select v-model="lunchBreak" class="form-control" id="lunch_break">
                                <option
                                    v-for="(time) in lunchTimes"
                                    :value="time"
                                >
                                    @{{ time }} min.
                                </option>
                            </select>
                            <input type="hidden" :value="lunchBreak" name="lunch_break" />
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <a href="#" @click.prevent="setMultiDay()" class="boon-link mb-1">@{{ multiDaysTitle }}</a>
                    </div>
                    <div class="col-md-6 text-right" v-if="needsLunch">
                        <div class="custom-control custom-checkbox">
                            <input v-model="isLunch" type="checkbox" class="custom-control-input" id="customCheck1">
                            <label class="custom-control-label" for="customCheck1">Lunch break</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <button type="submit" @click="submit($event)" class="btn form-button">Continue</button>
            </div>
        </form>
        @include('shift._cancel-form')
    </div>
</shifttime>
