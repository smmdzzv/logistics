<template>
    <div class="p3">
        <div class="card shadow">
            <div class="card-header">
                <div class="row align-items-baseline">
                    <div class="col-sm-6 col-md-4">{{title}}</div>
                    <div class="col-sm-6 col-md-8 text-sm-right pt-2" v-if="roles.length > 0">
                        <select class="form-control col-sm-6 col-md-4 d-inline-flex " v-model="selectedRole">
                            <option :value="null">Все роли</option>
                            <option :value="role" v-for="role in roles">{{role.title}}</option>
                        </select>
                    </div>
                </div>
            </div>
            <b-table :busy="isBusy"
                     :fields="fields"
                     :items="users"
                     borderless
                     id="usersTable"
                     primary-key="id"
                     responsive
                     striped>
                <template v-slot:table-busy>
                    <div class="text-center text-info my-2">
                        <b-spinner class="align-middle"></b-spinner>
                    </div>
                </template>

                <template slot="roles" slot-scope="data">
                    <select aria-label="Роли пользователя" class="form-control" v-if="data.item.roles.length > 0">
                        <option :key="index + data.item.id" v-for="(role, index) in data.item.roles">{{role.title}}
                        </option>
                    </select>
                    <span v-if="data.item.roles.length === 0">Нет ролей</span>
                </template>
                <template slot="id" slot-scope="data">
                    <a :href="getEditUrl(data.item)" class="btn btn-outline-secondary">Изменить</a>
                </template>

                <template slot="profile" slot-scope="data">
                    <a :href="getProfileUrl(data.item)" class="btn btn-primary">Профиль</a>
                </template>

                <template slot="delete" slot-scope="data">
                    <button @click="deleteUser(data.item)" class="btn btn-danger">Удалить</button>
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
        mounted() {
            this.getUsers();
        },
        props: {
            title: {
                type: String,
                required: false,
                default: "Список пользователей"
            },
            url: {
                type: String,
                required: true
            },
            roles: {
                type: Array,
                default: () => []
            },
        },
        methods: {
            getEditUrl(item) {
                return `/users/${item.id}/edit`;
            },
            getProfileUrl(item) {
                return `/profile/${item.id}`;
            },
            deleteUser(item) {
                this.$bvModal.msgBoxConfirm('Удалить пользователя ' + item.code + ' ?', {
                    title: 'Подтверждение удаления',
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'danger',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true,
                    okTitle: 'Да',
                    cancelTitle: 'Отмена'
                })
                    .then(confirm => {
                        if (confirm)
                        {
                            tShowSpinner();
                            axios.delete('/users/' + item.id)
                                .then(_ => this.users = this.users.filter(u => u.id !== item.id));
                        }
                    })
                    .catch(err => {
                        // An error occurred
                    })
                    .finally(_ => {
                        tHideSpinner();
                    })
            },
            async getUsers(page = 1) {
                this.isBusy = true;
                let action = this.url;
                if (this.selectedRole)
                    action = `/concrete/${this.selectedRole.name}/all`;
                action += '?page=' + page;
                try {
                    const response = await axios.get(action);
                    this.pagination = response.data;
                    this.users = response.data.data;
                } catch (e) {
                    let message = 'Не удалось загрузить список пользователей. Повторите попытку после перезагрузки страницы';
                    if (e.response.status === 403)
                        message = e.response.data.message;

                    this.$root.showErrorMsg(
                        'Ошибка загрузки',
                        message
                    )

                }

                this.$nextTick(() => {
                    this.isBusy = false;
                })
            }
        },
        watch: {
            selectedRole() {
                this.getUsers();
            }
        },
        data() {
            return {
                pagination: {},
                users: [],
                selectedRole: null,
                isBusy: false,
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
                    'profile': {
                        label: 'Профиль'
                    },
                    id: {
                        label: 'Редактировать'
                    },
                    'delete': {
                        label: 'Удалить'
                    }
                }
            }
        },
        components: {
            'Pagination': require('laravel-vue-pagination')
        }
    }
</script>
