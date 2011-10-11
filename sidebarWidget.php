<?php 
class sidebarWidget extends WP_Widget{
	
	/**
	 * Initializes the sidebarWidget class
	 */
	public function sidebarWidget(){
		// Settings
		$widget_ops = array('classname' => 'sidebarWidget', 'description' => __('The widget version of the Pronamic Magento plugin.', 'pronamic-magento-plugin'));

		/* Widget control settings. */
		$control_ops = array('width' => 300, 'height' => 350, 'id_base' => 'pronamic-magento-plugin');

		/* Create the widget. */
		$this->WP_Widget('pronamic-magento-plugin', __('Pronamic Magento Plugin Sidebar Widget', 'pronamic-magento-plugin'), $widget_ops, $control_ops);
	}
	
	/**
	 * The form shown on the admins widget page. Here settings can be changed.
	 * 
	 * @param mixed arrat $instance
	 */
	public function form($instance) {
		// outputs the options form on admin
		$defaults = array('mode' => 'latestproducts', 'pid' => '', 'cat' => '', 'maxproducts' => 2, 'septemp' => 1);
		$instance = wp_parse_args((array) $instance, $defaults); ?>

		<p>
			<label for="<?php echo $this->get_field_id('mode'); ?>"><?php _e('Show mode:', 'pronamic-magento-plugin'); ?></label>
			<select class="fat" id="<?php echo $this->get_field_id('mode'); ?>" name="<?php echo $this->get_field_name('mode'); ?>">
				<option <?php if($instance['mode'] == 'latestproducts') echo 'selected="selected"'; ?> value="latestproducts"><?php _e('Show latest products', 'pronamic-magento-plugin'); ?></option>
				<option <?php if($instance['mode'] == 'custom') echo 'selected="selected"'; ?> value="custom"><?php _e('Show custom list of products', 'pronamic-magento-plugin'); ?></option>
			</select>
		</p>
		
		<?php if($instance['mode'] == 'custom'): ?>

		<p>
			<label for="<?php echo $this->get_field_id('pid'); ?>"><?php _e('Product ID\'s or SKU\'s (Comma separated):', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('pid'); ?>" name="<?php echo $this->get_field_name('pid'); ?>" value="<?php echo $instance['pid']; ?>" style="width:100%;" />
		</p>
		
		<p>
			<label for="<?php echo $this->get_field_id('cat'); ?>"><?php _e('Category name or ID:', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('cat'); ?>" name="<?php echo $this->get_field_name('cat'); ?>" value="<?php echo $instance['cat']; ?>" style="width:100%;" />
		</p>
		
		<?php elseif($instance['mode'] == 'latestproducts'): ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('maxproducts'); ?>"><?php _e('Number of products shown', 'pronamic-magento-plugin'); ?></label>
			<input class="widefat" id="<?php echo $this->get_field_id('maxproducts'); ?>" name="<?php echo $this->get_field_name('maxproducts'); ?>" value="<?php echo $instance['maxproducts']; ?>" style="width:100%" />
		</p>
		
		<?php endif; ?>
		
		<p>
			<label for="<?php echo $this->get_field_id('septemp'); ?>"><?php _e('Use separate template', 'pronamic-magento-plugin'); ?></label>
			<select class="fat" id="<?php echo $this->get_field_id('septemp'); ?>" name="<?php echo $this->get_field_name('septemp'); ?>">
				<option <?php if($instance['septemp']) echo 'selected="selected"'; ?> value="1"><?php _e('Yes', 'pronamic-magento-plugin'); ?></option>
				<option <?php if(!$instance['septemp']) echo 'selected="selected"'; ?> value="0"><?php _e('No', 'pronamic-magento-plugin'); ?></option>
			</select>
		</p>
		
		<?php
	}

	/**
	 * Updates widget's settings. New settings are parsed by the form function
	 * 
	 * @param mixed array $new_instance
	 * @param mixed array $old_instance
	 */
	public function update($new_instance, $old_instance) {
		// processes widget options to be saved
		$instance = $old_instance;

		$instance['mode'] = $new_instance['mode'];
		$instance['pid'] = strip_tags($new_instance['pid']); 
		$instance['cat'] = strip_tags($new_instance['cat']);
		
		if(!is_numeric(strip_tags($new_instance['maxproducts']))) $new_instance['maxproducts'] = 3;
		$instance['maxproducts'] = strip_tags($new_instance['maxproducts']);
		$instance['septemp'] = $new_instance['septemp'];

		return $instance;
	}

	/**
	 * The widget as show to the user.
	 * 
	 * @param mixed array $args
	 * @param mixed array $instance
	 */
	public function widget($args, $instance) {
		error_reporting(E_ALL ^ E_NOTICE);

		// Determine wether the widget or the plugin template should be used.
		$templatemode = null;
		if($instance['septemp']) $templatemode = 'widget';
		
		// Different mode is different outcome
		$content = '';
		if($instance['mode'] == 'latestproducts'){	
			$content .= magento::getProductOutput(array('latest'=>$instance['maxproducts']), 0, $templatemode);
		}elseif($instance['mode'] == 'custom'){
			$content .= magento::getProductOutput(array('pid'=>$instance['pid'], 'cat'=>$instance['cat']), $instance['maxproducts'], $templatemode);
		}		
		
		echo $content;
	}
	
}
?>