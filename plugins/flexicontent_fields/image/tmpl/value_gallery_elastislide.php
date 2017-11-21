<?php  // *** DO NOT EDIT THIS FILE, CREATE A COPY !!

/**
 * (Inline) Gallery layout  --  Elastislide
 *
 * This layout does not support inline_info, pretext, posttext
 *
 * Note: This layout uses a thumbnail list created with -- large -- size thubmnails, these will be then thumbnailed by the JS gallery code
 * Note: This is an inline carousel gallery (Responsive image gallery with togglable thumbnail-strip, plus previewer and description)
 */


// ***
// *** Values loop
// ***

$i = -1;
foreach ($values as $n => $value)
{
	// Include common layout code for preparing values, but you may copy here to customize
	$result = include( JPATH_ROOT . '/plugins/flexicontent_fields/image/tmpl_common/prepare_value_display.php' );
	if ($result === _FC_CONTINUE_) continue;
	if ($result === _FC_BREAK_) break;

	$title_attr = $desc ? $desc : $title;
	$img_legend_custom ='
		<img src="'.JUri::root(true).'/'.$src.'" alt ="'.$alt.'"'.$legend.' class="'.$class.'"
			data-medium="' . JUri::root(true).'/'.$srcm . '" 
			data-large="' . JUri::root(true).'/'.$srcl . '"
			data-description="'.$title_attr.'" itemprop="image"/>
	';
	$group_str = $group_name ? 'rel="['.$group_name.']"' : '';
	$field->{$prop}[] = '
		<li><a href="javascript:;" class="fc_image_thumb">
			'.$img_legend_custom.'
		</a></li>';
}



// ***
// *** Add per field custom JS
// ***

if ( !isset(static::$js_added[$field->id][__FILE__]) )
{
	flexicontent_html::loadFramework('elastislide');

	$uid = 'es_'.$field->name."_fcitem".$item->id;
	$js = file_get_contents(JPATH_SITE.DS.'components'.DS.'com_flexicontent'.DS.'librairies'.DS.'elastislide'.DS.'js'.DS.'gallery_tmpl.js');
	$js = str_replace('unique_gal_id', $uid, $js);

	$slideshow_thumb_size = $field->parameters->get( $PPFX_ . 'slideshow_thumb_size', 'large' );
	$slideshow_auto_play = (int) $field->parameters->get( $PPFX_ . 'slideshow_auto_play', 1 );
	$slideshow_auto_delay = (int) $field->parameters->get( $PPFX_ . 'slideshow_auto_play', 4000 );
	$slideshow_transition = $field->parameters->get( $PPFX_ . 'slideshow_transition', 'cross-fade' );
	$slideshow_easing   = $field->parameters->get( $PPFX_ . 'slideshow_easing', 'swing');
	$slideshow_easing_inout = $field->parameters->get( $PPFX_ . 'slideshow_easing_inout', 'easeOut' );
	$slideshow_speed = (int) $field->parameters->get( $PPFX_ . 'slideshow_speed', 600 );

	$carousel_position = (int) $field->parameters->get( $PPFX_ . 'carousel_position', 1 );
	$carousel_visible = (int) $field->parameters->get( $PPFX_ . 'carousel_visible', 2 );

	$carousel_thumb_size = $field->parameters->get( $PPFX_ . 'carousel_thumb_size', 's' );
	$carousel_thumb_width = (int) $field->parameters->get( 'w_'.$carousel_thumb_size, 120 );
	$carousel_transition = $field->parameters->get( $PPFX_ . 'carousel_transition', 'scroll' );
	$carousel_easing   = $field->parameters->get( $PPFX_ . 'carousel_easing', 'swing');
	$carousel_easing_inout = $field->parameters->get( $PPFX_ . 'carousel_easing_inout', 'easeOut' );
	$carousel_speed = $field->parameters->get( $PPFX_ . 'carousel_speed', 600 );

	if ($js)
	{
		JFactory::getDocument()->addScriptDeclaration(
			'var elastislide_options_'.$uid.' = {
				slideshow_thumb_size: \'' . $slideshow_thumb_size . '\',
				slideshow_auto_play: ' . $slideshow_auto_play . ',
				slideshow_auto_delay: ' . $slideshow_auto_delay . ',
				slideshow_transition: \'' . $slideshow_transition . '\',
				slideshow_easing: \'' . $slideshow_easing . '\',
				slideshow_easing_inout: \'' . $slideshow_easing_inout . '\',
				slideshow_speed: ' . $slideshow_speed . ',

				carousel_position: ' . $carousel_position . ',
				carousel_visible: ' . $carousel_visible . ',

				carousel_thumb_width: ' . $carousel_thumb_width . ',
				carousel_transition: \'' . $carousel_transition . '\',
				carousel_easing: \'' . $carousel_easing . '\',
				carousel_easing_inout: \'' . $carousel_easing_inout . '\',
				carousel_speed: ' . $carousel_speed . '
			};
			' . $js
		);
	}

	JFactory::getDocument()->addCustomTag('
	<script id="img-wrapper-tmpl_'.$uid.'" type="text/x-jquery-tmpl">	
		<div class="rg-image-wrapper">
			{{if itemsCount > 1}}
				<div class="rg-image-nav">
					<a href="javascript:;" class="rg-image-nav-prev">'.JText::_('FLEXI_PREVIOUS').'</a>
					<a href="javascript:;" class="rg-image-nav-next">'.JText::_('FLEXI_NEXT').'</a>
				</div>
			{{/if}}
			<div class="rg-image"></div>
			<div class="rg-loading"></div>
			<div class="rg-caption-wrapper">
				<div class="rg-caption" style="display:none;">
					<p></p>
				</div>
			</div>
		</div>
	</script>
	');

	static::$js_added[$field->id][__FILE__] = true;
}



/**
 * Include common layout code before finalize values
 */

$result = include( JPATH_ROOT . '/plugins/flexicontent_fields/image/tmpl_common/before_values_finalize.php' );
if ($result !== _FC_RETURN_)
{
	// ***
	// *** Add container HTML (if required by current layout) and add value separator (if supported by current layout), then finally apply open/close tags
	// ***

	// Add container HTML
	// (note: we will use large image thumbnail as preview, JS will size them done)
	$uid = 'es_'.$field->name."_fcitem".$item->id;
	$field->{$prop} = '
	<div id="rg-gallery_'.$uid.'" class="rg-gallery" >
		<div class="rg-thumbs">
			<!-- Elastislide Carousel Thumbnail Viewer -->
			<div class="es-carousel-wrapper">
				<div class="es-carousel">
					<ul>
						' . implode('', $field->{$prop}) . '
					</ul>
				</div>
			</div>
			<!-- End Elastislide Carousel Thumbnail Viewer -->
		</div><!-- rg-thumbs -->
	</div><!-- rg-gallery -->
	';

	// Apply open/close tags
	$field->{$prop}  = $opentag . $field->{$prop} . $closetag;
}
