<script>
    import StarRating from 'vue-star-rating';

    export default {
        props: [
            'initRating',
            'scores'
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
                    'What areas could the provider improve on?',
                    'What did the provider do well?',
                    'What were some of the provider\'s strengths?'
                ],
                scoreMarks: []
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
                    return '';
                }
                return this.questions[this.rating - 1];
            }
        },
        methods: {
            selectBuble(field) {
                if (!this.isFieldExists(field)) {
                    this.scoreMarks.push(field);
                } else {
                    this.scoreMarks = _.remove(this.scoreMarks, (n) => {
                        return n !== field;
                    });
                }
            },
            isFieldExists(field) {
                return this.scoreMarks.indexOf(field) !== -1;
            }
        }
    }
</script>