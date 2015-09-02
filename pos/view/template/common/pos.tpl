<?php echo $header; ?>
<script>
    var data_url = '<?php echo $data_url; ?>';
    var status_url = '<?php echo $status_url; ?>';
    var transaction_url = '<?php echo $transaction_url; ?>';
    var refund_url = '<?php echo $refund_url; ?>';
    var add_transaction_url = '<?php echo $add_transaction_url; ?>';
    var token = '<?php echo $token; ?>';
    var config = jQuery.parseJSON('<?php echo $default; ?>');
    var payment = jQuery.parseJSON('<?php echo $payment; ?>');
    var receipt_template = decodeURIComponent(("<?php echo  urlencode(str_replace('<br />','',$receipt_header)); ?>"+'').replace(/\+/g, '%20')).replace(/\\"/g, '"');
    var cashier_name = '<?php echo $cashier; ?>';
    $(document).ready(function(){
        var pos = $(document).openpos();
        ProductCarousel(null);
    });

</script>
<div id="content">
    <?php if (isset($error_message)) { ?>
    <div class="warning"><?php echo $error_message; ?></div>
    <?php } ?>
    <div class="box">
        <div style="margin: 0 auto;">
            <div id="cp">
                <div class="product-pad">
                    <div id="slider">
                        <div class="jcarousel-wrapper">
                            <div class="jcarousel">
                                <ul id="categories">
                                </ul>
                            </div>
                            <a href="#" class="jcarousel-control-prev">&lsaquo;</a>
                            <a href="#" class="jcarousel-control-next">&rsaquo;</a>

                        </div>
                    </div>
                    <div style="clear:both;"></div>
                    <div id="ajaxproducts">
                        <ul>

                        </ul>
                    </div>
                    <div class="btn-con">
                        <button id="pro-control-prev" type="button" class="btn btn-default" style="z-index:10002;">Back</button>
                        <button id="pro-control-next" type="button" class="btn btn-default" style="z-index:10003;">Next</button>
                    </div>
                </div>
                <div class="keyboard-pad" style="display: none;">
                    <ul class="key">

                        <li><button value="1">1</button></li>
                        <li><button value="2">2</button></li>
                        <li><button value="3">3</button></li>
                    </ul>
                    <ul class="key">
                        <li><button value="4">4</button></li>
                        <li><button value="5">5</button></li>
                        <li><button value="6">6</button></li>
                    </ul>
                    <ul class="key">
                        <li><button value="7">7</button></li>
                        <li><button value="8">8</button></li>
                        <li><button value="9">9</button></li>
                    </ul>
                    <ul class="key">
                        <li><button value="0">0</button></li>
                        <li><button id="clr" value="clr">clear</button></li>
                        <li><button id="clra" value="clra">Clear All</button></li>
                        <li><button id="enter" value="enter">Enter</button></li>
                    </ul>
                </div>
            </div>
            <div class="space"></div>

            <div id="leftdiv" class="well-sm">
                <div id="lefttop">
                    <div style="width:75%; float:left;height: 53px;">
                        <form id="barcode_scanner" onSubmit="return false;">
                            <input name="barcode" id="scancode" class="form-control" placeholder="Barcode Scanner" style="margin-bottom: 10px;">
                            <input type="submit" style="display:none;">
                        </form>
                    </div>
                    <div  style="width:20%; float:right;">
                        <button type="button" id="saved_count">0</button>
                    </div>
                </div>
                <div id="cart">
                    <div id="prodiv">
                        <div id="pro-col-header">
                            <div class="del-col">
                                x
                            </div>
                            <div class="product-col">
                                Product
                            </div>
                            <div class="price-col">
                                Price
                            </div>
                            <div class="tax-col">
                                Tax
                            </div>
                            <div class="qty-col">
                                Qty
                            </div>
                            <div class="subtotal-col" style="text-align: right;">
                                Total
                            </div>
                        </div>

                        <div id="product-cart-list">
                            <form id="form_product_cart_list">
                                <table>


                                </table>
                            </form>
                        </div>

                    </div>
                    <div id="totaldiv">
                        <table id="totaltbl" width="100%" class="table table-striped table-condensed totals">
                            <tbody>
                            <tr class="totaltr">
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Items</div>
                                    <div id="item_count" style="width:25%;float:left;"><span id="count">0</span></div>
                                </td>
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Subtotal</div>
                                    <div class="text_right" colspan="2" id="total_value"><span id="total">0</span></div>
                                </td>
                            </tr>
                            <tr class="totaltr">
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Tax<a href="#" id="add_tax" style="color:#FFF; font-size:0.80em"></a></div>
                                    <div class="text_right" id="tax_value">0</div>
                                </td>
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Grand Total<a href="#" id="add_tax" style="color:#FFF; font-size:0.80em"></a></div>
                                    <div class="text_right" id="total_able"><span id="total-payable">0</span></div>
                                </td>
                            </tr>
                            <tr class="totaltr">
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Discount<a href="#" id="add_discount" style="color:#FFF; font-size:0.80em"></a></div>
                                    <div  id="discount_value"><button type="button" id="discount_button">0</button></div>
                                </td>
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Total paid<a href="#" id="add_tax" style="color:#FFF; font-size:0.80em"></a></div>
                                    <div class="text_right" id="total_paid"><input onClick="this.setSelectionRange(0, this.value.length)"  type="text" name="total_paid" value="0" size="15"></div>
                                </td>
                            </tr>
                            <tr class="totaltr">
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Customer<a href="#" id="add_discount" style="color:#FFF; font-size:0.80em"></a></div>
                                    <div  id="customer_value"><button type="button" id="customer_button">Nguyen Van A</button></div>
                                </td>
                                <td>
                                    <div style="width:50%;float:left;"  id="title">Balance<a href="#" id="add_tax" style="color:#FFF; font-size:0.80em"></a></div>
                                    <div class="text_right" id="refund_value"><span id="ts_con">0</span></div>
                                </td>
                            </tr>
                            <tr>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr class="totaltr">
                                <td colspan="2">
                                    <div style="width:20%;float:left;" id="title"><div style="float:left">Comment</div><div id="order_comment" style="color:#FFF; font-size:0.80em"></div></div>
                                    <div id="comment_value"><span>Order by openPOS</span></div>
                                </td>

                            </tr>

                            </tbody>
                        </table>
                    </div>
                </div>
                <div id="buttons">
                    <div id="botbuttons" style="text-align:center;">
                        <button type="button" class="btn btn-danger" id="cancel" style="width:90px;">
                            Cancel                  </button>
                        <button type="button" class="btn btn-info" id="hold" style="width:90px;">
                            Hold                  </button>
                        <button type="button" class="btn btn-success" id="payment" style="margin-right:0; width:180px;">
                            Checkout                  </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<?php echo $footer; ?>