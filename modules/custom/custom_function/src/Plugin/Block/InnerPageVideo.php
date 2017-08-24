<?php

namespace Drupal\custom_function\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;
use Drupal\Core\Site\Settings;

####################################################################################################
# Another new concept to Drupal that we need to use for the block is Annotations.                  #
# In order for Drupal to find your block code, you need to implement a code comment                #
# in a specific way, called an Annotation. An Annotation provides basic details of                 #
# the block such as an id and admin label. The admin label will appear on the block listing page.  #
#####################################################################################################
/**
 * Provides a 'Custom Inner Page Video' Block.
 *
 * @Block(
 *   id = "custom_inner_page_video",
 *   admin_label = @Translation("Inner Page Video"),
 *   category = @Translation("Custom Function")
 * )
 */

class InnerPageVideo extends BlockBase {

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
	    $node_type = Settings::get('video_node', NULL);

	    if (in_array($type_name, $node_type)) {

		if (!$nd->get('field_video')->isEmpty()) {
		    $video_node = $nd->get('field_video')->getValue();
		    $nid = $video_node[0]['target_id'];
		    $output .= '<div class="aboutLeftSectionHeding">Request for demo</div>';
		    $output .= '<div class="aboutLeftSectionBox requestDemo">';
		    $output .= '<div id="natg-video">Loading the player...</div>';
		    $output .= $this->get_video_data($nid);
		    $output .= '</div>';
		    return $output;
		}
	    }
	}


	return $output;
    }

    private function get_video_data($nid) {
	$output = '';

	$nd = Node::load($nid);
	$image_url = file_create_url($nd->field_video_image->entity->getFileUri());
	$file_url = file_create_url($nd->field_video_file->entity->getFileUri());

	$output .= '<span id="video-title" title="' . $nd->get('title')->value . '"></span>';
	$output .= '<span id="video-image" title="' . $image_url . '"></span>';
	$output .= '<span id="video-file" title="' . $file_url . '"></span>';
	return $output;
    }

}
