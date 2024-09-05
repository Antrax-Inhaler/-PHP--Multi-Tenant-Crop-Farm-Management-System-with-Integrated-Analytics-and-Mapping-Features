<?php require_once('inc/farmTopBar.php') ?>
<div class="content py-3">
    <div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
            <h5 class="card-title"><b>Pest and Disease Archive</b></h5>
            <div class="card-tools">
			<a href="javascript:void(0)" class="btn btn-flat btn-primary" id="create_new"><span class="fas fa-plus"></span>  Create New</a>
		</div>
        </div>
        <div class="card-body">
            <div class="">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="5%">
                        <col width="15%">
                        <col width="15%">
                        <col width="20%">
                        <col width="20%">
                        <col width="20%">
                        <col width="5%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="p1 text-center">#</th>
                            <th class="p1 text-center">Name</th>
                            <th class="p1 text-center">Management</th>
                            <th class="p1 text-center">Symptoms</th>
                            <th class="p1 text-center">Preventive Measures</th>
                            <th class="p1 text-center">Curative Measures</th>
                            <th class="p1 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $pests = $conn->query("SELECT * FROM `pest_disease_archive` ORDER BY `name` ASC, `created_at` DESC ");
                        while($row = $pests->fetch_assoc()):
                        ?>
                        <tr>
                            <td data-label="#" class="px-2 py-1 align-middle text-center"><?= $i++; ?></td>
                            <td data-label="Name" class="px-2 py-1 align-middle"><?= $row['name'] ?></td>
                            <td data-label="Management" class="px-2 py-1 align-middle"><?= $row['management'] ?></td>
                            <td data-label="Symptoms" class="px-2 py-1 align-middle"><?= $row['symptoms'] ?></td>
                            <td data-label="Preventive Measures" class="px-2 py-1 align-middle"><?= $row['preventive_measures'] ?></td>
                            <td data-label="Curative Measures" class="px-2 py-1 align-middle"><?= $row['curative_measures'] ?></td>
                            <td data-label="Action" class="px-2 py-1 align-middle text-center">
                                <button type="button" class="btn btn-flat border btn-light btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                    Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                </button>
                                <div class="dropdown-menu" role="menu">
                                    <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>"><span class="fa fa-edit text-dark"></span> Edit</a>
                                    <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>" data-title="<?= $row['name'] ?>"><span class="fa fa-trash text-dark"></span> Delete</a>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script>
    $(function(){
        $('#create_new').click(function(){
			uni_modal('Add New',"archive/manage_archive.php")
		})
        $('.view_data').click(function(){
            uni_modal("View Pest/Disease Details", "pest_disease/view_pest.php?id=" + $(this).attr('data-id'), 'mid-large')
        });
        $('.edit_data').click(function(){
            uni_modal("Edit Pest/Disease Details", "archive/manage_archive.php?id=" + $(this).attr('data-id'), 'mid-large')
        });
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this entry titled <b>" + ($(this).attr('data-title')) + "</b>?", "delete_archive", [$(this).attr('data-id')])
        });
        $('table').dataTable();
    });

    function delete_archive(id){
        start_loader();
        $.ajax({
            url: _base_url_ + "classes/Master.php?f=delete_archive",
            method: "POST",
            data: {id: id},
            dataType: "json",
            error: err => {
                console.log(err);
                alert_toast("An error occurred.", 'error');
                end_loader();
            },
            success: function(resp){
                if (typeof resp == 'object' && resp.status == 'success') {
                    location.reload();
                } else {
                    alert_toast("An error occurred.", 'error');
                    end_loader();
                }
            }
        })
    }
</script>
<style>
    body {
        font-family: Arial, sans-serif;
    }

    .card-body {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        max-width: 100%;
        margin-bottom: 1rem;
        background-color: transparent;
    }

    .table th,
    .table td {
        vertical-align: middle;
        padding: 0.75rem;
        border-top: 1px solid #dee2e6;
    }

    @media (max-width: 768px) {
        .table thead {
            display: none;
        }

        .table, .table tbody, .table tr, .table td {
            display: block;
            width: 100%;
        }

        .table tr {
            margin-bottom: 15px;
        }

        .table td {
            text-align: right;
            padding-left: 50%;
            position: relative;
        }

        .table td::before {
            content: attr(data-label);
            position: absolute;
            left: 0;
            width: 50%;
            padding-left: 15px;
            font-weight: bold;
            text-align: left;
        }

        .card-tools {
            margin-bottom: 10px;
        }
    }
</style>
