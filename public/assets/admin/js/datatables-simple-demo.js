window.addEventListener('DOMContentLoaded', event => {
    const billingsTable = document.getElementById('billingsTable');
    if (billingsTable) {
        new simpleDatatables.DataTable(billingsTable, {
            searchable: true,
            fixedHeight: true,
            perPage: 5
        });
    }
});
