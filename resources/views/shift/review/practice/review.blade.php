@extends('layouts.main')

@section('content')
    <div class="hire hire-center review">
        <review-to-provider
                inline-template
                v-cloak
                :scores="{{ $scores }}"
        >
            <div class="rel">
                <gmap-map
                        :center="{{ collect(['lat' => $shift->practice->lat, 'lng' => $shift->practice->lng]) }}"
                        :zoom="14"
                        style="width: 100%; min-height:calc(100vh - 88px)"
                        :options="{gestureHandling: 'cooperative'}"
                        ref="map"
                >
                </gmap-map>
                <div class="centralform abs">
                    <form method="POST" action="{{ route('shifts.reviews.createReview' , $shift) }}">
                        @csrf
                        <div class="inputs">
                            <div class="row">
                                <div class="col-1">
                                    <a href="{{ route('shifts.details' , $shift) }}" class="back-chevron"><i class="fa fa-chevron-left" aria-hidden="true"></i></a>
                                </div>
                                <div class="col-10 text-center">
                                    <p class="title mb-0">Rating</p>
                                </div>
                            </div>
                            <div class="form-group">
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
                                <span
                                        v-for="one_score in scores"
                                        @click="selectBuble(one_score.id)"
                                        :class="{'active' : isFieldExists(one_score.id)}"
                                >@{{ one_score.title }}</span>

                                <input type="hidden" v-model="scoreMarks" name="score_marks" />
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
            </div>
        </review-to-provider>
    </div>
@endsection