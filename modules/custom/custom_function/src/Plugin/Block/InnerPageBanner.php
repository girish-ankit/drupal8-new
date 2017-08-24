<?php

namespace Drupal\custom_function\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Site\Settings;
use Drupal\field\Entity\FieldConfig;

####################################################################################################
# Another new concept to Drupal that we need to use for the block is Annotations.                  #
# In order for Drupal to find your block code, you need to implement a code comment                #
# in a specific way, called an Annotation. An Annotation provides basic details of                 #
# the block such as an id and admin label. The admin label will appear on the block listing page.  #
#####################################################################################################
/**
 * Provides a 'Custom Inner Page Banner' Block.
 *
 * @Block(
 *   id = "custom_inner_page_banner",
 *   admin_label = @Translation("Inner Page Banner"),
 *   category = @Translation("Custom Function")
 * )
 */

class InnerPageBanner extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
	return array(
	    '#markup' => $this->get_data(),
	);
    }

    private function get_data() {
	$output = '';
	$current_path = \Drupal::service('path.current')->getPath();
	$path_args = explode('/', $current_path);
//	print_r($path_args);
//	die();

	$path_args[2] = (int) $path_args[2];


	if ($path_args[1] == 'node' && is_int($path_args[2]) && !isset($path_args[3])) {

	    $nd = Node::load($path_args[2]);
	    $type_name = $nd->getType();
	    $node_type = Settings::get('banner_node', NULL);

	    if (in_array($type_name, $node_type)) {
		//dpm($nd->field_banner_image);

		if (!$nd->get('field_banner_image')->isEmpty()) {
		    $banner_uri = $nd->field_banner_image->entity->getFileUri();
		    $url = ImageStyle::load('banner_image')->buildUrl($banner_uri);
		    $title = $nd->get('title')->value;
		    $tag_line = $nd->get('field_tag_line')->value;

		    $output .= '<div class="innerBanner">';
		    $output .= '<img src="' . $url . '" alt="' . $title . '">';
		    $output .='<div class="innerBannerContent">';
		    $output .='<p class="innerBannerHeading">' . $title . '</p>';
		    if ($tag_line) {
			$output .='<p>"' . $tag_line . '"</p>';
		    }
		    $output .='<img src="/themes/natg/images/innerDiamond.png" alt="' . $title . '" class="innerDiamond img-responsive">';
		    $output .='</div>';
		    $output .='</div>';
		    return $output;
		} else {
		    $field_info = FieldConfig::loadByName('node', $type_name, 'field_banner_image');
		    $image_uuid = $field_info->getSetting('default_image')['uuid'];
		    // dpm($image_uuid);
		    $image = \Drupal::service('entity.repository')->loadEntityByUuid('file', $image_uuid);
		    $image_uri = $image->getFileUri();
		    $url = ImageStyle::load('banner_image')->buildUrl($image_uri);
		    $title = $nd->get('title')->value;
		    $tag_line = $nd->get('field_tag_line')->value;

		    $output .= '<div class="innerBanner">';
		    $output .= '<img src="' . $url . '" alt="' . $title . '">';
		    $output .='<div class="innerBannerContent">';
		    $output .='<p class="innerBannerHeading">' . $title . '</p>';
		    if ($tag_line) {
			$output .='<p>"' . $tag_line . '"</p>';
		    }
		    $output .='<img src="/themes/natg/images/innerDiamond.png" alt="' . $title . '" class="innerDiamond img-responsive">';
		    $output .='</div>';
		    $output .='</div>';
		}

		return $output;
	    }
	}
    }

}
