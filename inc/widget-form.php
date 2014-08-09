<?php
/*
 *  @description: Widget form options in WP-Admin
 *  @since 1.0
 */

?>

<!-- Title -->
<p>
    <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Widget Title', 'gpr'); ?></label>
    <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>"
           name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>"/>
</p>


<!-- Google Maps Autocomplete Label -->
<p>
    <label for="<?php echo $this->get_field_id('autocomplete'); ?>"><?php _e('Location Lookup', 'gpr'); ?>:</label>
    <input class="widefat gpr-autocomplete" id="<?php echo $this->get_field_id('autocomplete'); ?>"
           name="<?php echo $this->get_field_name('autocomplete'); ?>" type="text"
           value="<?php echo(empty($autocomplete) ? '' : $autocomplete) ?>"/>
</p>

<!-- Google Maps Reference Field -->
<div class="set-business" <?php if (empty($location)) {
    echo "style='display:none;'";
} ?>>
    <p>
        <label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Location', 'gpr'); ?>:</label>
        <input class="widefat location" onClick="this.setSelectionRange(0, this.value.length)" readonly
               id="<?php echo $this->get_field_id('location'); ?>"
               name="<?php echo $this->get_field_name('location'); ?>" type="text"
               placeholder="<?php echo(empty($location) ? 'No location set' : ''); ?>"
               value="<?php echo $location; ?>"/>
    </p>

    <p>
        <label for="<?php echo $this->get_field_id('reference'); ?>"><?php _e('Location Reference ID', 'gpr'); ?>
            :</label>
        <input class="widefat reference" onClick="this.setSelectionRange(0, this.value.length)" readonly
               id="<?php echo $this->get_field_id('reference'); ?>"
               name="<?php echo $this->get_field_name('reference'); ?>" type="text"
               placeholder="<?php echo(empty($reference) ? 'No location set' : ''); ?>"
               value="<?php echo $reference; ?>"/>
    </p>
</div>

<h4 class="gpr-widget-toggler"><?php _e('Review Options', 'gpr'); ?>:<span></span></h4>

<div class="review-options toggle-item">


    <!-- Filter Reviews -->
    <p class="pro-field">
        <label for="<?php echo $this->get_field_id('review_filter'); ?>"><?php _e('Minimum Review Rating:', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('Filter negative reviews to prevent them from displaying. Upgrade to Pro version to unlock this functionality.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>

        <select id="<?php echo $this->get_field_id('review_filter'); ?>" class="widefat"
                name="<?php echo $this->get_field_name('review_filter'); ?>" disabled>

            <option value="none" <?php if (empty($review_filter) || $review_filter == 'No filter') {
                echo "selected='selected'";
            } ?>><?php _e('No filter', 'gpr'); ?>
            </option>
            <option value="5" <?php if ($review_filter == '5') {
                echo "selected='selected'";
            } ?>><?php _e('5 Stars', 'gpr'); ?>
            </option>
            <option value="4" <?php if ($review_filter == '4') {
                echo "selected='selected'";
            } ?>><?php _e('4 Stars', 'gpr'); ?>
            </option>
            <option value="3" <?php if ($review_filter == '3') {
                echo "selected='selected'";
            } ?>><?php _e('3 Stars', 'gpr'); ?>
            </option>
            <option value="2" <?php if ($review_filter == '2') {
                echo "selected='selected'";
            } ?>><?php _e('2 Stars', 'gpr'); ?>
            </option>
            <option value="1" <?php if ($review_filter == '1') {
                echo "selected='selected'";
            } ?>><?php _e('1 Star', 'gpr'); ?>
            </option>

        </select>

    </p>

    <!-- Review Limit -->
    <p>
        <label for="<?php echo $this->get_field_id('review_limit'); ?>"><?php _e('Limit Number of Reviews:', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('Limit the number of reviews displayed for this location to a set number. Upgrade to the Pro version for 5 total reviews.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
        <select name="<?php echo $this->get_field_name('review_limit'); ?>" id="<?php echo $this->get_field_id('review_limit'); ?>"
                class="widefat">
            <?php
            $options = array('3', '2', '1');
            foreach ($options as $option) {
                ?>

                <option value="<?php echo $option; ?>"
                        id="<?php echo $option; ?>" <?php if ($review_limit == $option || empty($review_limit) && $option == '4') {
                    echo 'selected="selected"';
                } ?>><?php echo $option; ?></option>

            <?php } ?>
        </select>
    </p>

</div><!-- /.review-options -->

<h4 class="gpr-widget-toggler"><?php _e('Display Options', 'gpr'); ?>:<span></span></h4>

<div class="display-options toggle-item">

    <!-- Widget Theme -->
    <p>
        <label for="<?php echo $this->get_field_id('widget_style'); ?>"><?php _e('Widget Theme'); ?>: <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                         title="<?php _e('Widget themes provide a way ', 'gpr'); ?>"
                         class="tooltip-info" width="16" height="16"/></label>
        <select name="<?php echo $this->get_field_name('widget_style'); ?>" class="widefat profield">
            <?php
            $options = array(
                __('Bare Bones', 'gpr'),
                __('Minimal Light', 'gpr'),
                __('Minimal Dark', 'gpr'),
                __('Shadow Light', 'gpr'),
                __('Shadow Dark', 'gpr')
            );
            //Counter for Option Values
            $counter = 0;

            foreach ($options as $option) {
                echo '<option value="' . $option . '" id="' . $option . '"', $widget_style == $option ? ' selected="selected"' : '', '>', $option, '</option>';
                $counter++;
            }
            ?>
        </select>
    </p>

    <!-- Hide Places Header -->
    <p>
        <input id="<?php echo $this->get_field_id('hide_header'); ?>"
               name="<?php echo $this->get_field_name('hide_header'); ?>" type="checkbox"
               value="1" <?php checked('1', $hide_header); ?> />
        <label for="<?php echo $this->get_field_id('hide_header'); ?>"><?php _e('Hide Business Information', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('Disable the main business information profile image, name, overall rating. Useful for displaying only ratings in the widget.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
    </p>


    <!-- Hide x Rating -->
    <p>
        <input id="<?php echo $this->get_field_id('hide_out_of_rating'); ?>"
               name="<?php echo $this->get_field_name('hide_out_of_rating'); ?>" type="checkbox"
               value="1" <?php checked('1', $hide_out_of_rating); ?> />
        <label
            for="<?php echo $this->get_field_id('hide_out_of_rating'); ?>"><?php _e('Hide "x out of 5 stars" text', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('Hide the text the appears after the star image displaying x out of 5 stars. The text will still be output because it is important for SEO but it will be hidden with CSS.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
    </p>

    <!-- Show Google Image -->
    <p>
        <input id="<?php echo $this->get_field_id('hide_google_image'); ?>"
               name="<?php echo $this->get_field_name('hide_google_image'); ?>" type="checkbox"
               value="1" <?php checked('1', $hide_out_of_rating); ?> />
        <label
            for="<?php echo $this->get_field_id('hide_google_image'); ?>"><?php _e('Hide Google logo', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('Prevent the Google logo from displaying in the reviews widget.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
    </p>


</div>
<!--/.display-options -->

<h4 class="gpr-widget-toggler"><?php _e('Advanced Options:', 'gpr'); ?><span></span></h4>


<div class="advanced-options toggle-item">

    <!-- Transient / Cache -->
    <p>
        <label for="<?php echo $this->get_field_id('cache'); ?>"><?php _e('Cache Data:', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('Caching data will save Google Place data to your database in order to speed up response times and conserve API requests. The suggested settings is 1 Day. ', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>

        <select name="<?php echo $this->get_field_name('cache'); ?>" id="<?php echo $this->get_field_id('cache'); ?>"
                class="widefat">
            <?php  $options = array(__('None', 'gpr'), __('1 Hour', 'gpr'), __('3 Hours', 'gpr'), __('6 Hours', 'gpr'), __('12 Hours', 'gpr'), __('1 Day', 'gpr'), __('2 Days', 'gpr'), __('1 Week', 'gpr'));
            /**
             * Output Cache Options (set 2 Days as default for new widgets)
             */
            foreach ($options as $option) {
                ?>
                <option value="<?php echo $option; ?>"
                        id="<?php echo $option; ?>" <?php if ($cache == $option || empty($cache) && $option == '1 Day') {
                    echo ' selected="selected" ';
                } ?>>
                    <?php echo $option; ?>
                </option>
                <?php $counter++;
            }  ?>
        </select>


    </p>

    <!-- Clear Cache Button -->
    <p class="clearfix">
        <span class="cache-message"></span>
        <a href="#" class="button gpr-clear-cache" title="Clear" data-transient-id-1="gpr_widget_api"
           data-transient-id-2="gpr_widget_options">Clear Cache</a>
        <span class="cache-clearing-loading spinner"></span>
    </p>


    <!-- Disable title output checkbox -->
    <p>
        <input id="<?php echo $this->get_field_id('disable_title_output'); ?>"
               name="<?php echo $this->get_field_name('disable_title_output'); ?>" type="checkbox"
               value="1" <?php checked('1', $title_output); ?>/>
        <label
            for="<?php echo $this->get_field_id('disable_title_output'); ?>"><?php _e('Disable Title Output', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('The title output is content within the \'Widget Title\' field above. Disabling the title output may be useful for some themes.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
    </p>

    <!-- Open Links in New Window -->
    <p>
        <input id="<?php echo $this->get_field_id('target_blank'); ?>"
               name="<?php echo $this->get_field_name('target_blank'); ?>" type="checkbox"
               value="1" <?php checked('1', $target_blank); ?>/>
        <label for="<?php echo $this->get_field_id('target_blank'); ?>"><?php _e('Open Links in New Window', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('This option will add target=\'_blank\' to the widget\'s links. This is useful to keep users on your website.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
    </p>

    <!-- No Follow Links -->
    <p>
        <input id="<?php echo $this->get_field_id('no_follow'); ?>"
               name="<?php echo $this->get_field_name('no_follow'); ?>" type="checkbox"
               value="1" <?php checked('1', $no_follow); ?> />
        <label for="<?php echo $this->get_field_id('no_follow'); ?>"><?php _e('No Follow Links', 'gpr'); ?>
            <img src="<?php echo GPR_PLUGIN_URL . '/assets/images/help.png' ?>"
                 title="<?php _e('This option will add rel=\'nofollow\' to the widget\'s outgoing links. This option may be useful for SEO.', 'gpr'); ?>"
                 class="tooltip-info" width="16" height="16"/></label>
    </p>

</div>
<p class="gpr-widget-footer-links clearfix">
    <span class="google-power"></span>
    <a href="http://wordimpress.com/plugins/google-places-reviews-pro/" target="_blank" class="new-window"><?php _e('Upgrade to Pro', 'gpr'); ?></a> <a href="http://wordimpress.com/docs/google-places-reviews-pro/" target="_blank"  class="new-window"><?php _e('Plugin Documentation', 'gpr'); ?></a>
</p>

