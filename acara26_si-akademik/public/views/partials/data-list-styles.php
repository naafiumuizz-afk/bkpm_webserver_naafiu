<style>
    body {
        background: #f3f7f8;
        color: #17324d;
        font-family: "Segoe UI", Arial, sans-serif;
    }

    .dashboard-container {
        max-width: 1200px;
        padding-top: 2.5rem !important;
    }

    .page-heading {
        min-height: 136px;
        background: linear-gradient(120deg, #e1f3ed, #edf5fb);
        border: 1px solid #dcebe7;
    }

    .metric-card {
        min-height: 122px;
        border: 0;
        box-shadow: 0 .25rem 1rem rgba(23, 50, 77, .07);
    }

    .metric-value,
    .student-avatar {
        color: #176b5b;
    }

    .search-box {
        width: 100%;
        max-width: 365px;
    }

    .form-card {
        max-width: 760px;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #67b7a3;
        box-shadow: 0 0 0 .2rem rgba(23, 107, 91, .15);
    }

    .student-avatar {
        width: 2.35rem;
        height: 2.35rem;
        display: inline-grid;
        place-items: center;
        flex: 0 0 auto;
        border-radius: 50%;
        background: #e1f3ed;
        font-weight: 700;
    }

    .data-table thead th {
        background: #212529;
        color: #fff;
        white-space: nowrap;
    }

    .data-table tbody td {
        height: 50px;
        border-bottom-color: #e1e6e9;
    }

    @media (max-width: 575.98px) {
        .dashboard-container {
            padding-top: 1.5rem !important;
        }
    }
</style>
