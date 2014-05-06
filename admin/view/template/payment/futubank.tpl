<?php echo $header; ?>
<div id="content">
  <div class="breadcrumb">
    <?php foreach ($breadcrumbs as $breadcrumb) { ?>
    <?php echo $breadcrumb['separator']; ?><a href="<?php echo $breadcrumb['href']; ?>"><?php echo $breadcrumb['text']; ?></a>
    <?php } ?>
  </div>
<?php if ($error_warning) { ?>
<div class="warning"><?php echo $error_warning; ?></div>
<?php } ?>
<div class="box">
  <div class="left"></div>
  <div class="right"></div>
  <div class="heading">
      <h1><img src="view/image/payment.png" alt="" /> <?php echo $heading_title; ?></h1>
      <div class="buttons">
          <a onclick="$('#form').submit();" class="button"><span><?php echo $button_save; ?></span></a>
          <a onclick="location='<?php echo $cancel; ?>';" class="button"><span><?php echo $button_cancel; ?></span></a>
      </div>
  </div>
  <div class="content">
      <form action="<?php echo $action; ?>" method="post" enctype="multipart/form-data" id="form">
      <table class="form">
      	<tr>
            <td width="25%">
                <span class="required">*</span> <?php echo $entry_merchant_id; ?>
            </td>
            <td>
                <input type="text" name="futubank_merchant_id" value="<?php echo $futubank_merchant_id; ?>" /><br />
                <?php if ($error_merchant_id) { ?>
                    <span class="error"><?php echo $error_merchant_id; ?></span>
                <?php } ?>
            </td>
      	</tr>
        <tr>
            <td width="25%">
                <span class="required">*</span> <?php echo $entry_secret_key; ?>
            </td>
            <td>
                <input type="text" name="futubank_secret_key" value="<?php echo $futubank_secret_key; ?>" /><br />
                <?php if ($error_secret_key) { ?>
                    <span class="error"><?php echo $error_secret_key; ?></span>
                <?php } ?>
            </td>
        </tr>
      	<tr>
       		  <td>callback_url:</td>
            <td><?php echo $callback_url; ?></td>
      	</tr>
      	<tr>
            <td>success_url:</td>
            <td><?php echo $success_url; ?></td>
      	</tr>
      	<tr>
      	    <td>fail_url:</td>
            <td><?php echo $fail_url; ?></td>
      	</tr>
        <tr>
            <td>
                <?php echo $entry_mode; ?>
            </td>
            <td>
                <select name="futubank_mode">
                    <option value="test"<?php if ($futubank_mode == 'test') { ?> selected="selected"<?php } ?>><?php echo $entry_mode_test; ?></option>
                    <option value="real"<?php if ($futubank_mode == 'real') { ?> selected="selected"<?php } ?>><?php echo $entry_mode_real; ?></option>
                </select>
            </td>
        </tr>
      	<tr>
            <td><?php echo $entry_order_status; ?></td>
            <td>
                <select name="futubank_order_status_id">
                  <?php foreach ($order_statuses as $order_status) { ?>
                      <option 
                          value="<?php echo $order_status['order_status_id']; ?>" 
                          <?php if ($order_status['order_status_id'] == $futubank_order_status_id) { ?>selected="selected"<?php } ?>
                      ><?php echo $order_status['name']; ?></option>
                  <?php } ?>
              </select>
            </td>
      	</tr>
      	<tr>
        <td><?php echo $entry_geo_zone; ?></td>
            <td>
                <select name="futubank_geo_zone_id">
                    <option value="0"><?php echo $text_all_zones; ?></option>
                    <?php foreach ($geo_zones as $geo_zone) { ?>
                        <option 
                            value="<?php echo $geo_zone['geo_zone_id']; ?>" 
                            <?php if ($geo_zone['geo_zone_id'] == $futubank_geo_zone_id) { ?>selected="selected"<?php } ?>
                        ><?php echo $geo_zone['name']; ?></option>
                    <?php } ?>
                </select>
            </td>
      	</tr>
      	<tr>
            <td>
                <?php echo $entry_status; ?>
            </td>
            <td>
                <select name="futubank_status">
                    <option value="1"<?php if ($futubank_status)  { ?> selected="selected"<?php } ?>><?php echo $text_enabled; ?></option>
                    <option value="0"<?php if (!$futubank_status) { ?> selected="selected"<?php } ?>><?php echo $text_disabled; ?></option>
                </select>
            </td>
      	</tr>
      	<tr>
            <td><?php echo $entry_sort_order; ?></td>
            <td><input type="text" name="futubank_sort_order" value="<?php echo $futubank_sort_order; ?>" size="1" /></td>
        </tr>
        <tr>
            <td></td>
            <td>
                <a href="https://github.com/Futubank/opencart-futupayments">Свежие версии этого модуля на Github</a>
            </td>
        </tr>
      </table>
    </form>
  </div>
</div>
<?php echo $footer; ?>