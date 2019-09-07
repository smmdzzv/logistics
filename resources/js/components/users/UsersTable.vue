<template>
    <div class="container">
        <div class="card">
            <div class="card-header">
                {{title}}
            </div>
            <div class="card-body">
                <b-table :fields="fields"
                         :items="users"
                         hover
                         outlined
                         primary-key="id"
                         responsive
                         striped>
                    <template slot="roles" slot-scope="data">
                        <select class="form-control" v-if="data.item.roles.length > 0">
                            <option v-for="role in data.item.roles">{{role.title}}</option>
                        </select>
                        <span v-if="data.item.roles.length === 0">Нет ролей</span>
                    </template>
                </b-table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: "UsersTable",
        props: {
            users: {
                type: Array,
                required: true
            },
            type: {
                type: String,
                required: true
            }
        },
        computed: {
            title: function () {
                if (this.type === 'clients')
                    return 'Список клиентов';
                if(this.type === 'stuff')
                    return 'Список сотрдуников';
                else
                    return 'Список пользователйе';
            }
        },
        data() {
            return {
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
                    }
                }
            }
        }
    }
</script>

<style scoped>

</style>
