    <template>
    <div>
        <vue-confirm-dialog></vue-confirm-dialog>
        <div class="card-body">
            <div class="d-flex justify-content-between">
                <div class="card-title h4 m-0">Customers list</div>
                <div class="form-group mt-3">
                <!-- <router-link :to="{name: 'createUser'}" class="btn btn-outline-dark">Create new user</router-link> -->
                </div>
            </div>            <table class="table">
                <thead class="thead-dark">
                <tr>
                    <th>Id</th>
                    <th>First name</th>
                    <th>Last name</th>
                    <th>Gender</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Birthday</th>
                    <!-- <th>Verify phone</th> -->
                    <th>Verify email</th>
                    <th>Create at</th>
                    <th class="text-danger">Status</th>
                </tr>
                </thead>
                <tbody>
                <tr v-for="user, index in users">
                    <td>{{ user.id }}</td>
                    <td>{{ user.firstname }}</td>
                    <td>{{ user.lastname }}</td>
                    <td>{{ user.gender == 1 ? "Female" : user.gender == -1 ? "Other" : "Nam" }}</td>
                    <td>{{ user.phone }}</td>
                    <td>{{ user.email }}</td>
                    <td>{{ user.dob }}</td>
                    <!-- <td>
                        <i v-if="user.firebase_uid.length" class="fas fa-check-circle text-success"></i>
                        <i v-if="!user.firebase_uid.length" class="fas fa-times-circle text-danger"></i>
                    </td> -->
                    <td>
                        <i v-if="user.email_verified_at" class="fas fa-check-circle text-success"></i>
                        <i v-if="!user.email_verified_at" class="fas fa-times-circle text-danger"></i>
                    </td>
                    <td>
                        {{ user.created_at }}
                    </td>
                    <td><div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" :id="'blockat_'+user.id" v-model="user.block" v-on:click="deleteEntry(user.id, index)">
                            <label class="custom-control-label custom-switch-danger" :for="'blockat_'+user.id">{{ user.block ? "Blocked" : "Unblocked" }}</label>
                        </div>
                        <!-- <router-link :to="{name: 'editUser', params: {id: user.id}}" class="btn btn-sm btn-block btn-outline-dark m-1">
                            Edit
                        </router-link> -->
                        <!-- <a href="#"
                           class="btn btn-sm btn-block btn-danger m-1"
                           v-on:click="deleteEntry(user.id, index)">
                            Block
                        </a> -->
                    </td>
                </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data: function () {
            return {
                users: [],
            }
        },
        mounted() {
            var app = this;
            axios.get('/api/v1/customers',{
                headers: app.$bearerAPITOKEN
            })
                .then(function (resp) {
                    app.users = resp.data;
                })
                .catch(function (resp) {
                    console.log(resp);
                    alert("Could not load users");
                });
        },
        methods: {
            deleteEntry(id, index) {
                var app = this;
                app.$swal.fire({
                    title: 'Are you sure?',
                    html: 'Are you sure you want to '+(app.users[index].block ? 'un' : '' )+'block <strong>'+app.users[index].firstname+'</strong> ?',
                    icon: 'warning',
                    showCancelButton: true,
                    showClass: {
                        popup: 'animate__animated animate__headShake',
                        icon: 'animate__animated animate__shakeX',
                    },
                    confirmButtonColor: 'orange',
                    confirmButtonText: 'Yes!'
                }).then((result) => {
                    var status = app.users[index].block;
                    if (result.isConfirmed) {
                        axios.patch('/api/v1/customers/' + id,{
                                block: status,
                                only_edit_block: true,
                            }
                            ,{
                                headers: app.$bearerAPITOKEN
                            })
                            .then(function (resp) {
                                app.$swal.fire(
                                    'Changed!',
                                    'Customer status has been changed.',
                                    'success'
                                )
                            })
                            .catch(function (resp) {
                                console.log(resp);
                                app.$swal.fire(
                                    'Error!',
                                    'Could not change customer status!',
                                    'error',
                                )
                            });
                        
                    }
                    else if (result.isDismissed) {
                        app.users[index].block = !status;
                    }
                })
            }
        }
    }
</script>