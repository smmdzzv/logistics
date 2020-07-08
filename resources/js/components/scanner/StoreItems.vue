<template>
    <div class="container-fluid">
        <div class="row">
            <button class="btn btn-primary" @click="submit">
                Принять на склад  <strong>{{branch.name}}</strong>
            </button>
        </div>
    </div>
</template>

<script>
    export default {
        name: "StoreItems",
        props: {
            storedItems: {
                type: Array,
                required: true
            },
            branch: {
                type: Object,
                required: true
            }
        },
        methods: {
            async submit() {
                if (this.storedItems.length === 0)
                    return;
                try {
                    let data = {
                        storedItems: this.storedItems.map((selected) => {
                            return selected.id;
                        })
                    };

                    let action = `/branch/${this.branch.id}/stored-items`;
                    const response = await axios.post(action, data);
                    // let win = window.open(`/trips/${this.trip.id}`, '_blank');
                    // win.focus();
                } catch (e) {
                    this.$root.showErrorMsg(
                        'Ошибка сохранения',
                        'Не удалось принять товары. Повторите после обновления страницы'
                    );
                }
            }
        }
    }
</script>

<style scoped>

</style>
