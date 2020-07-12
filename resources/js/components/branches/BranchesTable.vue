<template>
    <b-table :fields="fields"
             :items="branches"
             borderless
             primary-key="id"
             striped
             :busy="isBusy"
             responsive>
        <template v-slot:table-busy>
            <div class="text-center text-info my-2">
                <b-spinner class="align-middle"></b-spinner>
            </div>
        </template>
        <template v-slot:cell(director.name)="data">
            <span v-if="data.item.director">{{data.item.director.name}}</span>
            <span v-else>Не назначен</span>
        </template>
        <template v-slot:cell(edit)="data">
            <button @click="onEditRequest(data.item)" class="btn btn-outline-secondary">Изменить</button>
        </template>
        <template v-slot:cell(remove)="data">
            <img @click="deleteBranch(data.item)"
                 alt="удалить тариф"
                 class="icon-btn-sm"
                 src="/svg/delete.svg">
        </template>
    </b-table>
</template>

<script>
    export default {
        name: "BranchesTable",
        props: {
            branches: {
                type: Array,
                required: true
            },
            onEditRequest: {
                type: Function,
                required: true
            }
        },
        methods: {
            async deleteBranch(branch) {
                let confirm = await this.$bvModal.msgBoxConfirm(
                    `Вы уверены что хотите удалить филиал ${branch.name}?`,
                    {
                        centered: true,
                        okTitle: 'Да',
                        cancelTitle: 'Отменить',
                        footerClass: 'border-0',
                        title: 'Подтверждение удаления'
                    });

                if (confirm) {
                    try {
                        const response = axios.delete('/branches/' + branch.id);
                        this.$emit('branchDeleted', branch)
                    } catch (e) {
                        this.$root.showErrorMsg('Ошибка удаления',
                            'Не удалось удалить филиал. Обновите страницу и попробуйте удалить еще раз')
                    }
                }

            }
        },
        data() {
            return {
                items: [],
                isBusy: false,
                fields: [
                    {
                        key: 'name',
                        label: 'Имя',
                        sortable: true
                    },
                    {
                        key: 'director.name',
                        label: 'Директор',
                        sortable: true
                    },
                    {
                        key: 'edit',
                        label: 'Изменить'
                    },
                    {
                        key: 'remove',
                        label: ''
                    }
                ]
            }
        }
    }
</script>
