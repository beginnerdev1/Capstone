<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/js/all.min.js"></script>

<script src="<?= base_url('assets/admin/js/scripts.js') ?>"></script>

<!-- Print Function (Global) -->
<script>
function printTable() {
        const printArea = document.querySelector('.card-body');
        if (!printArea) {
            alert('No printable content found on this page.');
            return;
        }

        // Open a new window for printing
        const printWindow = window.open('', '_blank', 'width=900,height=700');

        printWindow.document.write(`
            <html>
                <head>
                    <title>Registered Users – Barangay Borlongan</title>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            margin: 20px;
                            color: #000;
                        }
                        h2, p {
                            text-align: center;
                        }
                        table {
                            width: 100%;
                            border-collapse: collapse;
                            margin-top: 20px;
                        }
                        th, td {
                            border: 1px solid #000;
                            padding: 6px;
                            text-align: center;
                        }
                        th {
                            background-color: #f0f0f0;
                        }
                    </style>
                </head>
                <body>
                    <h2>Registered Users – Barangay Borlongan</h2>
                    <p>Printed on: ${new Date().toLocaleString()}</p>
                    ${printArea.outerHTML}
                </body>
            </html>
        `);

        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
    }
</script>

<!-- Print Styles -->
<style>
@media print {
    body {
        font-family: Arial, sans-serif;
        background: white;
        color: #000;
    }

    /* Hide navbar, footer, buttons, filters, etc. */
    nav, footer, form, .btn, .breadcrumb, .card-header {
        display: none !important;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12pt;
    }

    th, td {
        border: 1px solid #000;
        padding: 6px;
    }

    th {
        background-color: #f0f0f0 !important;
    }

    .card-body {
        margin: 20px;
    }
}
</style>

 