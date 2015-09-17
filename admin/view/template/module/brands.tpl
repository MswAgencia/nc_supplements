<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-category" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
    <div class="panel panel-default">
      <div class="panel-heading">
        <h3 class="panel-title"><i class="fa fa-pencil"></i> <?php echo $text_edit; ?></h3>
      </div>
      <div class="panel-body">
        <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-category" class="form-horizontal">
          <div class="form-group required">
            <label class="col-sm-2 control-label" for="input-name"><?php echo $entry_name; ?></label>
            <div class="col-sm-10">
              <input type="text" name="name" value="<?php echo $name; ?>" placeholder="<?php echo $entry_name; ?>" id="input-name" class="form-control" />
              <?php if ($error_name) { ?>
              <div class="text-danger"><?php echo $error_name; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="status" id="input-status" class="form-control">
                <?php if ($status) { ?>
                <option value="1" selected="selected"><?php echo $text_enabled; ?></option>
                <option value="0"><?php echo $text_disabled; ?></option>
                <?php } else { ?>
                <option value="1"><?php echo $text_enabled; ?></option>
                <option value="0" selected="selected"><?php echo $text_disabled; ?></option>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_type; ?></label>
            <div class="col-sm-10">
              <select name="type" id="input-status" class="form-control">
                <option value="div" <?php echo ($type == "div") ? 'selected="selected"' : ""; ?> ><?php echo $text_div; ?></option>
                <option value="link" <?php echo ($type == "link") ? 'selected="selected"' : ""; ?> ><?php echo $text_link; ?></option>
              </select>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-view"><?php echo $entry_view; ?></label>
            <div class="col-sm-10">
				<select name="view" id="view" class="form-control">
					<option value="basic" <?php echo ($view == "basic") ? 'selected="selected"' : ""; ?> ><?php echo $text_basic; ?></option>
					<option value="self" <?php echo ($view == "self") ? 'selected="selected"' : ""; ?> ><?php echo $text_self; ?></option>
					<option value="modal" <?php echo ($view == "modal") ? 'selected="selected"' : ""; ?> ><?php echo $text_modal; ?></option>
				</select>
            </div>
          </div>
			 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-title"><?php echo $entry_title; ?></label>
            <div class="col-sm-5">
				<select name="title" id="input-title" class="form-control">
					<option value="0" <?php echo ($title == 0) ? 'selected="selected"' : ""; ?> ><?php echo $text_disabled; ?></option>
					<option value="1" <?php echo ($title == 1) ? 'selected="selected"' : ""; ?> ><?php echo $text_enabled; ?></option>
				</select>
            </div>

            <div class="col-sm-5">
			  <div class="input-group">
				  <span class="input-group-addon" id="addon-class-icon"><i class="fa fa-css3" data-toggle="tooltip" title="<?php echo $entry_class; ?>"></i> </span>
					<input type="text" class="form-control" name="class" id="class-icon" aria-describedby="addon-class-icon" placeholder="<?php echo $entry_class; ?>" value="<?php echo $class; ?>" />
				</div>
            </div>
		</div>
			
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-breadcrumbs"><?php echo $entry_visible; ?></label>
            <div class="col-sm-4">
				<div class="input-group">
					<span class="input-group-addon" id="addon-breadcrumbs" ><i class="fa fa-list-ul" data-toggle="tooltip" title="<?php echo $text_show.'/'.$text_hide.' '.$text_breadcrumbs; ?>"></i> </span>
				<select name="breadcrumbs" id="input-breadcrumbs" class="form-control" aria-describedby="addon-breadcrumbs">
					<option value="0" <?php echo ($bcrumbs == 0) ? 'selected="selected"' : ""; ?> ><?php echo $text_hide.' '.$text_breadcrumbs ; ?></option>
					<option value="1" <?php echo ($bcrumbs == 1) ? 'selected="selected"' : ""; ?> ><?php echo $text_show.' '.$text_breadcrumbs; ?></option>
				</select>
            </div>
          </div>
            <div class="col-sm-3">
				<div class="input-group">
					<span class="input-group-addon" id="addon-iview" ><i class="fa fa-eye" data-toggle="tooltip" title="<?php echo $help_iview; ?>"></i> </span>
				<select name="iview" id="iview" class="form-control" aria-describedby="addon-iview">
					<option value="list" <?php echo ($iview == 'list') ? 'selected="selected"' : ""; ?> ><?php echo $text_list; ?></option>
					<option value="grid" <?php echo ($iview == 'grid') ? 'selected="selected"' : ""; ?> ><?php echo $text_grid; ?></option>
				</select>
            </div>
          </div>
            <div class="col-sm-3">
				<div class="input-group">
					<span class="input-group-addon" id="addon-iview" ><i class="fa fa-eye" data-toggle="tooltip" title="<?php echo $help_opacity; ?>"></i> </span>
              <select name="opacity" id="input-backdrop" class="form-control">
                <option value="0" <?php echo ($opacity == 0) ? 'selected="selected"' : ""; ?> ><?php echo $text_disabled; ?></option>
                <option value="1" <?php echo ($opacity == 1) ? 'selected="selected"' : ""; ?>><?php echo $text_enabled; ?></option>
              </select>
            </div>
            </div>
            
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-popup"><?php echo $entry_popup; ?></label>
            <div class="col-sm-5">
				<div class="input-group">
					<span class="input-group-addon" id="addon-status" ><i class="fa fa-plug" data-toggle="tooltip" title="<?php echo $help_popup; ?>"></i> </span>
				<select name="popup[status]" id="popup-status" class="form-control" aria-describedby="addon-status">
					<option value="0" <?php echo ($popup['status'] == 0) ? 'selected="selected"' : ""; ?> ><?php echo $text_disabled; ?></option>
					<option value="1" <?php echo ($popup['status'] == 1) ? 'selected="selected"' : ""; ?> ><?php echo $text_enabled; ?></option>
				</select>
            </div>
            </div>
            <div class="col-sm-5">
			  <div class="input-group">
				  <span class="input-group-addon" id="basic-addon1" ><i class="fa fa-css3"></i> </span>
					<input type="text" class="form-control" name="popup[class]" id="popup-popy" value="<?php echo $popup['class'] ; ?>" />
				</div>
				
            </div>
          </div>
          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-view"><?php echo $entry_popup; ?></label>
            <div class="col-sm-5">
				<div class="input-group">
					<span class="input-group-addon" id="addon-position" ><i class="fa fa-arrows" data-toggle="tooltip" title="<?php echo $help_position; ?>"></i> </span>
				<select name="popup[placement]" id="popup-placement" class="form-control" aria-describedby="addon-position">
					<option value="top" <?php echo ($popup['placement'] == "top") ? 'selected="selected"' : ""; ?> ><?php echo $text_top; ?></option>
					<option value="left" <?php echo ($popup['placement'] == "left") ? 'selected="selected"' : ""; ?> ><?php echo $text_left; ?></option>
					<option value="right" <?php echo ($popup['placement'] == "right") ? 'selected="selected"' : ""; ?> ><?php echo $text_right; ?></option>
					<option value="bottom" <?php echo ($popup['placement'] == "bottom") ? 'selected="selected"' : ""; ?> ><?php echo $text_bottom; ?></option>
				</select>
				</div>
            </div>
            <div class="col-sm-5">
				<div class="input-group">
					<span class="input-group-addon" id="addon-position" ><i class="fa fa-play" data-toggle="tooltip" title="<?php echo $help_play; ?>"></i> </span>
				<select name="popup[trigger]" id="trigger" class="form-control">
					<option value="hover" <?php echo ($popup['trigger'] == "hover") ? 'selected="selected"' : ""; ?> ><?php echo $text_hover; ?></option>
					<option value="click" <?php echo ($popup['trigger'] == "click") ? 'selected="selected"' : ""; ?> ><?php echo $text_click; ?></option>
					<option value="focus" <?php echo ($popup['trigger'] == "focus") ? 'selected="selected"' : ""; ?> ><?php echo $text_focus; ?></option>
				</select>
				</div>
            </div>
        </form>
      </div>
    </div>
	<div style="text-align: center;"><a href="http://www.opencart.com/index.php?route=extension/extension&filter_username=qart" target="_blank">Qart - OC Extensions &nbsp;<i class="fa fa-external-link"></i></a></div>
  </div>
</div>
<?php echo $footer; ?>
