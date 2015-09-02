<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
    <div class="page-header">
        <div class="container-fluid">
            <div class="pull-right">
                <button type="submit" form="form-setting" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
                <a href="<?php echo $cancel; ?>" data-toggle="tooltip" title="<?php echo $button_cancel; ?>" class="btn btn-default"><i class="fa fa-reply"></i></a></div>
            <h1><?php echo $heading_title; ?></h1>
            <ul class="breadcrumb">
                <?php foreach ($breadcrumbs as $breadcrumb) { ?>
                <li><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a></li>
                <?php } ?>
            </ul>
        </div>
    </div>
    <div class="container-fluid">
        <?php if ($error_warning) { ?>
        <div class="alert alert-danger"><i class="fa fa-exclamation-circle"></i> <?php echo $error_warning; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>
        <?php if ($success) { ?>
        <div class="alert alert-success"><i class="fa fa-check-circle"></i> <?php echo $success; ?>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
        <?php } ?>

        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo 'Product Label Setting'; ?></h3>
            </div>
            <div class="panel-body">

                <form action="<?php echo $action; ?>" id="form-setting" method="post" enctype="multipart/form-data" id="form" class="form-horizontal">
                    <input type="hidden" name="store_id" value="<?php echo $store_id; ?>">
                    <ul class="nav nav-tabs">
                        <li class="active"><a href="#tab-general"  data-toggle="tab">Active</a></li>

                    </ul>
                    <div class="tab-content">
                        <div id="tab-general" class="tab-pane active">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="required">*</span>Your License Key:</label>
                                <div class="col-sm-10">
                                    <div>
                                        <input type="text" name="key" value="<?php echo isset($openposkey['key'])? $openposkey['key']: ''; ?>" >
                                    </div>

                                </div>
                            </div>

                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>
    <script type="text/javascript"><!--
        $('#config_label_template').summernote({height: 300});
        //--></script>
    <?php echo $footer; ?>