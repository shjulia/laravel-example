@extends('layouts.main')

@section('content')
    <div class="hire hire-center review">
        <review-to-practice
                inline-template
                v-cloak
        >
            <div class="centralform">
                <a href="{{ route('shifts.provider.resultShow' , $shift) }}" class="back-x">
                    <i class="fa fa-times" aria-hidden="true"></i>
                </a>
                <form method="POST" action="{{ route('shifts.provider.reviews.createReview' , $shift) }}">
                    @csrf
                    <div class="inputs">
                        <div class="form-group">
                            <a href="{{ route('shifts.provider.resultShow' , $shift) }}" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                            <span class="title">Rating</span>
                            <hr class="full-hr"/>

                            <div class="starssdiv">
                                <p class="star-title">@{{ title }}</p>
                                <star-rating
                                        v-model="rating"
                                        :show-rating="false"
                                        :star-size="30"
                                        :padding="18"
                                        active-color="#f47f1e"
                                        :rounded-corners="true"
                                ></star-rating>
                                <input type="hidden" :value="rating" name="score"/>
                                @if ($errors->has('score'))
                                    <span class="invalid-feedback"><strong>{{ $errors->first('score') }}</strong></span>
                                @endif
                            </div>
                        </div>
                        <div class="bubles" v-if="rating >= 3">
                            <p class="star-title">@{{ questionTitle }}</p>
                            <span @click="selectBuble('score_friendly_team')" :class="{'active' : score_friendly_team}">Friendly team</span>
                            <input type="hidden" v-model="score_friendly_team" name="score_friendly_team" />
                            <span @click="selectBuble('score_cool_office')" :class="{'active' : score_cool_office}">Cool office</span>
                            <input type="hidden" v-model="score_cool_office" name="score_cool_office" />
                            <span @click="selectBuble('score_great_patient')" :class="{'active' : score_great_patient}">Great Patient</span>
                            <input type="hidden" v-model="score_great_patient" name="score_great_patient" />
                            <span @click="selectBuble('score_well_organized')" :class="{'active' : score_well_organized}">Well Organized</span>
                            <input type="hidden" v-model="score_well_organized" name="score_well_organized" />
                        </div>
                        <div class="form-group">
                            <textarea name="text" class="form-control" rows="3" placeholder="Comments" required></textarea>
                            @if ($errors->has('text'))
                                <span class="invalid-feedback"><strong>{{ $errors->first('text') }}</strong></span>
                            @endif
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn form-button">Continue</button>
                    </div>
                </form>
            </div>
        </review-to-practice>
    </div>
@endsection
