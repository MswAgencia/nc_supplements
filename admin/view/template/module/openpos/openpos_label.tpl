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
                        <li class="active"><a href="#tab-general"  data-toggle="tab"><?php echo $tab_general; ?></a></li>
                        <li><a href="#tab-label"  data-toggle="tab">Label Setting</a></li>
                        <li><a href="#tab-barcode"  data-toggle="tab">Barcode Setting</a></li>
                    </ul>
                    <div class="tab-content">
                        <div id="tab-general" class="tab-pane active">
                            <div class="form-group">
                                <label class="col-sm-2 control-label"><span class="required">*</span>Label Templates:</label>
                                <div class="col-sm-10">
                                    <div>
                                        <textarea name="openposlabel_label_template" id="config_label_template" cols="40" rows="5"><?php echo $openposlabel['openposlabel_label_template']; ?></textarea>
                                    </div>
                                    <span class="help">Variable can use: {{barcode}} : barcode images, {{product_name}} : Product Name, {{product_price}}: Product Price, {{product_upc}} : Product upc code</span>

                                </div>
                            </div>

                        </div>
                        <div id="tab-label" class="tab-pane">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Sheet Size:
                                    <br/>
                                    <span>(Width x Height in mm)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_sheet_width" value="<?php echo $openposlabel['openposlabel_label_sheet_width']; ?>" placeholder="Width" id="input-width" class="form-control" />
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_sheet_height" value="<?php echo $openposlabel['openposlabel_label_sheet_height']; ?>" placeholder="Height" id="input-height" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Label Size:
                                    <br/>
                                    <span>(Width x Height in mm)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_width" value="<?php echo $openposlabel['openposlabel_label_width']; ?>" placeholder="Width" id="input-width" class="form-control" />
                                        </div>
                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_vertical_height" value="<?php echo $openposlabel['openposlabel_label_vertical_height']; ?>" placeholder="Height" id="input-height" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Sheet Margin:
                                    <br/>
                                    <span>(Top x Right x Bottom x Left in mm)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <input type="text" name="openposlabel_label_margin_top" value="<?php echo $openposlabel['openposlabel_label_margin_top']; ?>" placeholder="Top" id="input-width" class="form-control" />
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" name="openposlabel_label_margin_right" value="<?php echo $openposlabel['openposlabel_label_margin_right']; ?>" placeholder="Right" id="input-height" class="form-control" />
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" name="openposlabel_label_margin_bottom" value="<?php echo $openposlabel['openposlabel_label_margin_bottom']; ?>" placeholder="Bottom" id="input-width" class="form-control" />
                                        </div>
                                        <div class="col-sm-3">
                                            <input type="text" name="openposlabel_label_margin_left" value="<?php echo $openposlabel['openposlabel_label_margin_left']; ?>" placeholder="Left" id="input-height" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Label Corner Radius:
                                    <br/>
                                    <span>(in mm)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">
                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_corner_radius" value="<?php echo $openposlabel['openposlabel_label_corner_radius']; ?>" placeholder="Corner Radius" id="input-width" class="form-control" />
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Horizontal Spacing:
                                    <br/>
                                    <span>(in mm)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_hirizontal_space" value="<?php echo $openposlabel['openposlabel_label_hirizontal_space']; ?>" placeholder="Horizontal Spacing" id="input-height" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Vertical Spacing:
                                    <br/>
                                    <span>(in mm)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_label_vertical_space" value="<?php echo $openposlabel['openposlabel_label_vertical_space']; ?>" placeholder="Vertical Spacing" id="input-height" class="form-control" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div id="tab-barcode" class="tab-pane">
                            <div class="form-group">
                                <label class="col-sm-2 control-label" for="input-length"><span class="required">*</span>
                                    Barcode Height:
                                    <br/>
                                    <span>(in px)</span>
                                </label>
                                <div class="col-sm-10">
                                    <div class="row">

                                        <div class="col-sm-4">
                                            <input type="text" name="openposlabel_barcode_height" value="<?php echo $openposlabel['openposlabel_barcode_height']; ?>" placeholder="Barcode Height" id="input-height" class="form-control" />
                                        </div>
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