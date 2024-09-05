<?php require_once('inc/farmTopBar.php') ?>

<div class="content py-3">
    <div class="card card-outline card-primary rounded-0 shadow">
        <div class="card-header">
            <h5 class="card-title"><b>Activity Suggestions</b></h5>
            <div class="card-tools">
			<a href="javascript:void(0)" class="btn btn-flat btn-primary" id="create_new"><span class="fas fa-plus"></span>  Create New</a>
		</div>
        </div>
        <div class="card-body">
            <div class="">
                <table class="table table-bordered">
                    <colgroup>
                        <col width="5%">
                        <col width="35%">
                        <col width="50%">
                        <col width="10%">
                    </colgroup>
                    <thead>
                        <tr>
                            <th class="p1 text-center">#</th>
                            <th class="p1 text-center">Title</th>
                            <th class="p1 text-center">Description</th>
                            <th class="p1 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $i = 1;
                        $suggestions = $conn->query("SELECT * FROM `crop_activity_suggestions` order by `date_created` desc ");
                        while($row = $suggestions->fetch_assoc()):
                        ?>
                        <tr>
                            <td data-label="#" class="px-2 py-1 align-middle text-center"><?= $i++; ?></td>
                            <td data-label="Title" class="px-2 py-1 align-middle"><?= $row['title'] ?></td>
                            <td data-label="Description" class="px-2 py-1 align-middle"><?= $row['description'] ?></td>
                            <td data-label="Action" class="px-2 py-1 align-middle text-center">
                                <div class="dropdown">
                                    <button type="button" class="btn btn-flat border btn-light btn-sm dropdown-toggle dropdown-icon" data-toggle="dropdown">
                                        Action
                                    <span class="sr-only">Toggle Dropdown</span>
                                    </button>
                                    <div class="dropdown-menu" role="menu">
                                        <a class="dropdown-item edit_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>" data-title="<?= $row['title'] ?>"><span class="fa fa-edit text-dark"></span> Edit</a>
                                        <a class="dropdown-item delete_data" href="javascript:void(0)" data-id="<?= $row['id'] ?>" data-title="<?= $row['title'] ?>"><span class="fa fa-trash text-dark"></span> Delete</a>
                                    </div>
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
			uni_modal('Add New Suggestions',"suggestions/manage_suggestions.php")
		})
        $('.view_data').click(function(){
            uni_modal("View Activity Suggestion - <b>"+($(this).attr('data-title'))+"</b>","activities/view_activity.php?id="+$(this).attr('data-id'),'mid-large')
        });
        $('.edit_data').click(function(){
            uni_modal("Edit Activity Suggestion - <b>"+($(this).attr('data-title'))+"</b>","suggestions/manage_suggestions.php?id="+$(this).attr('data-id'),'mid-large')
        });
        $('.delete_data').click(function(){
            _conf("Are you sure to delete this activity suggestion titled <b>"+($(this).attr('data-title'))+"</b>?","delete_activity",[$(this).attr('data-id')])
        });
        $('table').dataTable();
    });

    function delete_activity(id){
        start_loader();
        $.ajax({
            url:_base_url_+"classes/Master.php?f=delete_crop_activity_suggestion",
            method:"POST",
            data:{id:id},
            dataType:"json",
            error:err=>{
                console.log(err)
                alert_toast("An error occured.",'error');
                end_loader();
            },
            success:function(resp){
                if(typeof resp == 'object' && resp.status == 'success'){
                    location.reload();
                }else{
                    alert_toast("An error occured.",'error');
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
