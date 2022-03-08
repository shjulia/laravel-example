<script>
    import {ServerErrors} from "../../mixins/ServerErrors";

    export default {
        props: [
            'creator',
            'initUsers',
            'action',
            'roles',
            'actionDelete'
        ],
        data () {
            return {
                users: this.initUsers,
                showForm: false,
                first_name: '',
                last_name: '',
                email: '',
                role: '',
                user_id: null
            }
        },
        mixins: [ServerErrors],
        methods: {
            addNewUser() {
                this.setFieldstoEmpty();
                $(this.$refs.teamModal).modal('show');
            },
            deleteUser() {
                let loader = this.$loading.show();
                axios({
                    method: 'DELETE',
                    url: this.actionDelete,
                    data: {
                        member: this.user_id
                    }
                })
                    .then(response => {
                        this.users = _.remove(this.users, (user) => {
                            return user.id !== this.user_id;
                        });
                        $(this.$refs.teamModal).modal('hide');
                        this.setFieldstoEmpty();
                    })
                    .catch(error => {
                    })
                    .finally(response => {
                        loader.hide()
                    });
            },
            editUser(user) {
                this.user_id = user.id;
                this.first_name = user.first_name;
                this.last_name = user.last_name;
                this.email = user.email;
                this.role = user.pivot.practice_role;
                $(this.$refs.teamModal).modal('show');
            },
            saveUser() {
                let loader = this.$loading.show();
                axios({
                    method: 'POST',
                    url: this.action,
                    data: {
                        first_name: this.first_name,
                        last_name: this.last_name,
                        email: this.email,
                        role: this.role,
                        user_id: this.user_id
                    }
                })
                    .then(response => {
                        if (this.user_id) {
                            this.users = _.remove(this.users, (user) => {
                                return user.id !== this.user_id;
                            });
                        }
                        this.users.push(response.data.user);
                        $(this.$refs.teamModal).modal('hide');
                        this.setFieldstoEmpty();
                    })
                    .catch(error => {
                    })
                    .finally(response => {
                        loader.hide()
                    });
            },
            setFieldstoEmpty() {
                this.user_id = null;
                this.first_name = '';
                this.last_name = '';
                this.email = '';
                this.role = '';
            }
        },
        mounted() {
            $(this.$refs.select2_role).select2({
                placeholder: '',
                minimumResultsForSearch: -1,
                width: '100%'
            });
        }
    }
</script>