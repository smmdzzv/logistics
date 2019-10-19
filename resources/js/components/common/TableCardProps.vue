<script>
    export default {
        name: "TableCardProps",
        props: {
            tableHeight: {
                type: String,
                default: '75vh'
            },
            tablePrimaryKey: {
                type: String,
                default: 'id'
            },
            selectable: {
                type: Boolean,
                default: false
            },
            selectMode: {
                type: String,
                default: 'single'
            },
            striped: {
                type: Boolean,
                default: false
            },
            borderless: {
                type: Boolean,
                default: false
            },
            responsive: {
                type: Boolean,
                default: false
            },
            fixed: {
                type: Boolean,
                default: false
            },
            selectedRowClass: {
                type: String,
                default: 'table-success'
            },
            checkedClass: {
                type: String,
                default: 'text-success'
            },
            hover: {
                type: Boolean,
                default: false
            },
            tableBusy: {
                type: Boolean,
                default: false
            },
            tableCustomCells: {
                type: Array,
                default: () => []
            },
            tableProperties: {
                type: Object
            }
        },
    }
</script>
