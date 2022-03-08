<script>
    import StarRating from 'vue-star-rating';

    export default {
        props: [
            'initRating'
        ],
        data() {
            return {
                rating: this.initRating ? this.initRating : 1,
                titles: [
                    'Bad Experience',
                    'Below Average',
                    'Average',
                    'Met Expectations',
                    'Exceeded Expectations'
                ],
                questions: [
                    '',
                    '',
                    'What areas could the practice improve on?',
                    'What did the practice do well?',
                    'What were some of the practice\'s strengths?'
                ],
                score_friendly_team: 0,
                score_cool_office: 0,
                score_great_patient: 0,
                score_well_organized: 0
            }
        },
        components: {
            'star-rating': StarRating
        },
        computed: {
            title() {
                return this.titles[this.rating - 1];
            },
            questionTitle() {
                if (this.rating < 3) {
                    return null;
                }
                return this.questions[this.rating - 1];
            }
        },
        methods: {
            selectBuble(field) {
                this[field] = this[field] === 0 ? 1 : 0;
            }
        }
    }
</script>