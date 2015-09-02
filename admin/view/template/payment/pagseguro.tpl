<?php echo $header; ?><?php echo $column_left; ?>
<div id="content">
  <div class="page-header">
    <div class="container-fluid">
      <div class="pull-right">
        <button type="submit" form="form-pagseguro" data-toggle="tooltip" title="<?php echo $button_save; ?>" class="btn btn-primary"><i class="fa fa-save"></i></button>
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
      	<form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form-pagseguro" class="form-horizontal">
          <div class="form-group required">
          	<label class="col-sm-2 control-label" for="input-token"><span data-toggle="tooltip" title="<?php echo $help_token; ?>"><?php echo $entry_token; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="pagseguro_token" value="<?php echo $pagseguro_token; ?>" placeholder="<?php echo $entry_token; ?>" id="input-token" class="form-control" />
              <?php if ($error_token) { ?>
              <div class="text-danger"><?php echo $error_token; ?></div>
              <?php } ?>
            </div>
          </div>      
          <div class="form-group required">
          	<label class="col-sm-2 control-label" for="input-email"><span data-toggle="tooltip" title="<?php echo $help_email; ?>"><?php echo $entry_email; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="pagseguro_email" value="<?php echo $pagseguro_email; ?>" placeholder="<?php echo $entry_email; ?>" id="input-email" class="form-control" />
              <?php if ($error_email) { ?>
              <div class="text-danger"><?php echo $error_email; ?></div>
              <?php } ?>
            </div>
          </div>
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-total"><span data-toggle="tooltip" title="<?php echo $help_total; ?>"><?php echo $entry_total; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="pagseguro_total" value="<?php echo $pagseguro_total; ?>" placeholder="<?php echo $entry_total; ?>" id="input-total" class="form-control" />
            </div>
          </div> 
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-posfixo"><span data-toggle="tooltip" title="<?php echo $help_posfixo; ?>"><?php echo $entry_posfixo; ?></span></label>
            <div class="col-sm-10">
              <input type="text" name="pagseguro_posfixo" value="<?php echo $pagseguro_posfixo; ?>" placeholder="<?php echo $entry_posfixo; ?>" id="input-posfixo" class="form-control" />
            </div>
          </div>
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_update_status_alert; ?>"><?php echo $entry_update_status_alert; ?></span></label>
            <div class="col-sm-10">
              <label class="radio-inline">
                <?php if ($pagseguro_update_status_alert) { ?>
                <input type="radio" name="pagseguro_update_status_alert" value="1" checked="checked" />
                <?php echo $text_yes; ?>
                <?php } else { ?>
                <input type="radio" name="pagseguro_update_status_alert" value="1" />
                <?php echo $text_yes; ?>
                <?php } ?>
              </label>
              <label class="radio-inline">
                <?php if (!$pagseguro_update_status_alert) { ?>
                <input type="radio" name="pagseguro_update_status_alert" value="0" checked="checked" />
                <?php echo $text_no; ?>
                <?php } else { ?>
                <input type="radio" name="pagseguro_update_status_alert" value="0" />
                <?php echo $text_no; ?>
                <?php } ?>
              </label>
            </div>
          </div>
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_tipo_frete; ?>"><?php echo $entry_tipo_frete; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_tipo_frete" id="input-tipo-frete" class="form-control">
	            <?php if ($pagseguro_tipo_frete == '0') { ?>
	            <option value="0" selected="selected"><?php echo $text_frete_loja; ?></option>
	            <?php } else { ?>
	            <option value="0"><?php echo $text_frete_loja; ?></option>
	            <?php } ?>
	            <?php if ($pagseguro_tipo_frete == '1') { ?>
	            <option value="1" selected="selected"><?php echo $text_frete_pagseguro_pac; ?></option>
	            <?php } else { ?>
	            <option value="1"><?php echo $text_frete_pagseguro_pac; ?></option>
	            <?php } ?>
	            <?php if ($pagseguro_tipo_frete == '2') { ?>
	            <option value="2" selected="selected"><?php echo $text_frete_pagseguro_sedex; ?></option>
	            <?php } else { ?>
	            <option value="2"><?php echo $text_frete_pagseguro_sedex; ?></option>
	            <?php } ?>
	            <?php if ($pagseguro_tipo_frete == '3') { ?>
	            <option value="3" selected="selected"><?php echo $text_frete_pagseguro_nao_especificado; ?></option>
	            <?php } else { ?>
	            <option value="3"><?php echo $text_frete_pagseguro_nao_especificado; ?></option>
	            <?php } ?>	
              </select>
            </div>
          </div>                    
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_order_aguardando_pagamento; ?>"><?php echo $entry_order_aguardando_pagamento; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_order_aguardando_pagamento" id="input-order-status-aguardando-pagamento" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pagseguro_order_aguardando_pagamento) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_order_analise; ?>"><?php echo $entry_order_analise; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_order_analise" id="input-order-status-analise" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pagseguro_order_analise) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>             
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_order_paga; ?>"><?php echo $entry_order_paga; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_order_paga" id="input-order-status-paga" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pagseguro_order_paga) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_order_disputa; ?>"><?php echo $entry_order_disputa; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_order_disputa" id="input-order-status-disputa" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pagseguro_order_disputa) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div> 
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_order_devolvida; ?>"><?php echo $entry_order_devolvida; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_order_devolvida" id="input-order-status-devolvida" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pagseguro_order_devolvida) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>
          <div class="form-group">
          	<label class="col-sm-2 control-label"><span data-toggle="tooltip" title="<?php echo $help_order_cancelada; ?>"><?php echo $entry_order_cancelada; ?></span></label>
            <div class="col-sm-10">
              <select name="pagseguro_order_cancelada" id="input-order-status-cancelada" class="form-control">
                <?php foreach ($order_statuses as $order_status) { ?>
                <?php if ($order_status['order_status_id'] == $pagseguro_order_cancelada) { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>" selected="selected"><?php echo $order_status['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $order_status['order_status_id']; ?>"><?php echo $order_status['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>                                
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-geo-zone"><?php echo $entry_geo_zone; ?></label>
            <div class="col-sm-10">
              <select name="pagseguro_geo_zone_id" id="input-geo-zone" class="form-control">
                <option value="0"><?php echo $text_all_zones; ?></option>
                <?php foreach ($geo_zones as $geo_zone) { ?>
                <?php if ($geo_zone['geo_zone_id'] == $pagseguro_geo_zone_id) { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>" selected="selected"><?php echo $geo_zone['name']; ?></option>
                <?php } else { ?>
                <option value="<?php echo $geo_zone['geo_zone_id']; ?>"><?php echo $geo_zone['name']; ?></option>
                <?php } ?>
                <?php } ?>
              </select>
            </div>
          </div>          
          <div class="form-group">
            <label class="col-sm-2 control-label" for="input-status"><?php echo $entry_status; ?></label>
            <div class="col-sm-10">
              <select name="pagseguro_status" id="input-status" class="form-control">
                <?php if ($pagseguro_status) { ?>
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
            <label class="col-sm-2 control-label" for="input-sort-order"><?php echo $entry_sort_order; ?></label>
            <div class="col-sm-10">
              <input type="text" name="pagseguro_sort_order" value="<?php echo $pagseguro_sort_order; ?>" placeholder="<?php echo $entry_sort_order; ?>" id="input-sort-order" class="form-control" />
            </div>
          </div>	    
      	</form>
      </div>
    </div>
  </div>
</div>
<?php echo $footer; ?>