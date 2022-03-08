<div v-if="selectedShift">
    <form method="POST" v-show="showReview" class="review-block" :action="replacedFormAction" ref="reviewForm">
        @csrf
        <div class="back-button">
            <a href="#" class="back-chevron" @click.prevent="backToDetails()">
                <i class="fa fa-chevron-left" aria-hidden="true"></i>
            </a>
        </div>
        <div class="title">
            <span>Rating</span>
        </div>
        <hr>
        <div class="stars-block">
            <img :src="selectedShift.practice.practice_photo_url" alt="" class="practice-photo">
            <p class="star-title">@{{ starTitle }}</p>
            <div>
                <star-rating
                    v-model="rating"
                    :show-rating="false"
                    :star-size="30"
                    :padding="18"
                    active-color="#f47f1e"
                    :rounded-corners="true"
                    class="star-rating"
                ></star-rating>
            </div>
            <input type="hidden" :value="rating" name="score"/>
            @if ($errors->has('score'))
                <span class="invalid-feedback"><strong>{{ $errors->first('score') }}</strong></span>
            @endif
        </div>
        <div class="bubles" v-show="rating >= 3">
            <p class="star-title">@{{ questionTitle }}</p>
            <span
                v-for="one_score in scores"
                @click="selectBuble(one_score.id)"
                :class="{'active' : isFieldExists(one_score.id)}"
            >@{{ one_score.title }}</span>

            <input type="hidden" v-model="scoreMarks" name="score_marks" />
        </div>

        <textarea name="text" class="form-control" rows="3" placeholder="Comments" required></textarea>
        @if ($errors->has('text'))
            <span class="invalid-feedback"><strong>{{ $errors->first('text') }}</strong></span>
        @endif

        <button type="submit" class="btn form-button" @click.prevent="submit()">Submit</button>
    </form>
</div>
