<team
    :creator="{{ $user }}"
    :init-users="{{ $users }}"
    :roles="{{ collect($roles) }}"
    action="{{ $action }}"
    action-delete="{{ $deleteAction }}"
    inline-template
    v-cloak
>
    <div>
        <div v-for="user in users" :key="user.id" class="row one-member">
            <div class="col-sm-5">
                <p class="name">@{{ user.first_name + ' ' + user.last_name }}</p>
                <p class="email">@{{ user.email }}</p>
            </div>
            <div class="col-sm-5">
                <span class="role">@{{ roles[user.pivot.practice_role] }}</span>
            </div>
            <div class="col-sm-2">
                <button
                    v-if="creator.id != user.id"
                    class="btn edit"
                    @click="editUser(user)"
                ><i class="fa fa-pencil"></i></button>
            </div>
        </div>
        <a class="add-member" href="#" @click.prevent="addNewUser()"><i class="fa fa-plus" aria-hidden="true"></i> <span>Add member</span></a>

        <div ref="teamModal" class="modal fade boon-modal" id="teamModal" tabindex="-1" role="dialog" aria-labelledby="teamModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-sm" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="teamModalLabel">@{{ user_id ? 'Edit member' : 'Add a new member' }}</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="row">
                            <div class="col-sm-6">
                                {{--<div class="form-group">
                                    <label for="first_name" class="col-form-label">First name</label>
                                    <input id="first_name" class="form-control" name="first_name" v-model="first_name" @input="inpChanged('first_name')">
                                    <div class="invalid-feedback" v-if="server_errors.first_name">@{{ server_errors.first_name }}</div>
                                </div>--}}
                                <Cinput
                                    label="First name"
                                    id="first_name"
                                    type="text"
                                    name="first_name"
                                    :has-errors="!!server_errors.first_name"
                                    :first-error="server_errors.first_name"
                                    :required="false"
                                    :is-mat="true"
                                    :prepend="true"
                                    prepend-icon="user"
                                    :init-model="first_name"
                                    :init-model-attr="'first_name'"
                                ></Cinput>
                            </div>
                            <div class="col-sm-6">
                                <Cinput
                                    label="Last name"
                                    id="last_name"
                                    type="text"
                                    name="last_name"
                                    :has-errors="!!server_errors.last_name"
                                    :first-error="server_errors.last_name"
                                    :required="false"
                                    :is-mat="true"
                                    :prepend="true"
                                    prepend-icon="user"
                                    :init-model="last_name"
                                    :init-model-attr="'last_name'"
                                ></Cinput>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <Cinput
                                    label="Email Address"
                                    id="email"
                                    type="email"
                                    name="email"
                                    :has-errors="!!server_errors.email"
                                    :first-error="server_errors.email"
                                    :required="false"
                                    :is-mat="true"
                                    :prepend="true"
                                    prepend-icon="user"
                                    :init-model="email"
                                    :init-model-attr="'email'"
                                ></Cinput>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-12">
                                <div class="form-group mat">
                                    <label for="role" >Role</label>
                                    <select id="role" ref="select2_role" class="form-control" name="role" v-model="role" @change="inpChanged('role')" v-select2>
                                        <option
                                            v-for="(role_title, role_key) in roles"
                                            :value="role_key"
                                        >@{{ role_title }}</option>
                                    </select>
                                    <div class="invalid-feedback" v-if="server_errors.role">@{{ server_errors.role }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button v-if="user_id" type="button" class="btn delete-button" @click="deleteUser()">Delete <i class="fa fa-trash"></i></button>
                        <button type="button" class="btn form-button" @click="saveUser()">Save</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</team>
