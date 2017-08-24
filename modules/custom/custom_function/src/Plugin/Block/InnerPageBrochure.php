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
 * Provides a 'Custom Inner Page Brochure' Block.
 *
 * @Block(
 *   id = "custom_inner_page_brochure",
 *   admin_label = @Translation("Inner Page Brochure"),
 *   category = @Translation("Custom Function")
 * )
 */

class InnerPageBrochure extends BlockBase {

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

	$output = '';
	$current_path = \Drupal::service('path.current')->getPath();
	$path_args = explode('/', $current_path);
//	print_r($path_args);
//	die();

	$path_args[2] = (int) $path_args[2];


	if ($path_args[1] == 'node' && is_int($path_args[2]) && !isset($path_args[3])) {

	    $nd = Node::load($path_args[2]);
	    $type_name = $nd->getType();
	    $node_type = Settings::get('brochure_node', NULL);

	    if (in_array($type_name, $node_type)) {

		if (!$nd->get('field_brochure')->isEmpty()) {
		    $brochure = file_create_url($nd->field_brochure->entity->getFileUri());

		    $output .= '<div class="downloadBrochure"><a href="' . $brochure . '">Download Brochure</a></div>';
		    return $output;
		}

		return $output;
	    }
	}
    }

}
