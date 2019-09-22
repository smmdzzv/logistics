<template>
    <div class="container">
        <div class="card shadow">
            <div class="card-header">
                {{title}}
            </div>
                <b-table :fields="fields"
                         id="usersTable"
                         :items="users"
                         :busy="isBusy"
                         striped
                         borderless
                         primary-key="id"
                         responsive
                          >
                    <template v-slot:table-busy>
                        <div class="text-center text-info my-2">
                            <b-spinner class="align-middle"></b-spinner>
                        </div>
                    </template>

                    <template slot="roles" slot-scope="data">
                        <select class="form-control" v-if="data.item.roles.length > 0" aria-label="Роли пользователя">
                            <option v-for="(role, index) in data.item.roles" :key="index + data.item.id">{{role.title}}</option>
                        </select>
                        <span v-if="data.item.roles.length === 0">Нет ролей</span>
                    </template>
                    <template slot="id" slot-scope="data">
                        <a :href="getEditUrl(data.item)" class="btn btn-outline-secondary">Изменить</a>
                    </template>

                    <template slot="profile" slot-scope="data">
                        <a :href="getProfileUrl(data.item)" class="btn btn-primary">Профиль</a>
                    </template>
                </b-table>

            <div class="card-footer">
                <pagination :data="pagination" @pagination-change-page="getUsers"></pagination>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UsersTable",
        mounted(){
            this.getUsers();
        },
        props: {
            title:{
                type: String,
                required: false,
                default:"Список пользователей"
            },
            url:{
                type: String,
                required: true
            }
        },
        methods:{
            getEditUrl(item){
                return `/users/${item.id}/edit`;
            },
            getProfileUrl(item){
                return `/profile/${item.id}`;
            },
            getUsers(page = 1){
                this.isBusy = true;
                axios.get(this.url + '?page=' + page)
                    .then(response=>{
                        this.pagination = response.data;
                        this.users = response.data.data;
                        console.log(this.pagination.data[0].name);
                        this.$nextTick(()=>{
                            this.isBusy = false;
                        })
                    });
            }
        },
        data() {
            return {
                pagination:{},
                users:[],
                isBusy:false,
                fields: {
                    name: {
                        label: 'Имя',
                        sortable: true
                    },
                    code: {
                        label: 'Код',
                        sortable: true
                    },
                    email: {
                        label: 'Почта',
                        sortable: true
                    },
                    phone: {
                        label: 'Телефон',
                        sortable: true
                    },
                    'position.name': {
                        label: 'Должность',
                        sortable: true
                    },
                    'roles': {
                        label: 'Роли',
                        sortable: true
                    },
                    'profile':{
                        label:'Профиль'
                    },
                    id:{
                        label:'Редактировать'
                    }
                }
            }
        },
        components:{
            'Pagination': require('laravel-vue-pagination')
        }
    }
</script>
