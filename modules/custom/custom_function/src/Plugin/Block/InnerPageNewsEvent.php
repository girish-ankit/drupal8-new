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
 * Provides a 'Custom Inner Page News Event' Block.
 *
 * @Block(
 *   id = "custom_inner_page_news_event",
 *   admin_label = @Translation("Inner Page News Event"),
 *   category = @Translation("Custom Function")
 * )
 */

class InnerPageNewsEvent extends BlockBase {

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
	    $node_type = Settings::get('news_events_node', NULL);

	    if (in_array($type_name, $node_type)) {

		if (!$nd->get('field_news_and_event')->isEmpty()) {
		    $news_event_node = $nd->get('field_news_and_event')->getValue();

		    $output .= '<div class="aboutLeftSectionHeding">News &amp; Event</div>';
		    $output .= '<div class="aboutLeftSectionBox news">';
		    $output .= '<ul>';
		    foreach ($news_event_node as $val) {
			$nid = $val['target_id'];
			$output .= $this->get_news_evens_tag_data($nid);
		    }


		    $output .= '</ul>';
		    $output .= '</div>';
		    return $output;
		}
	    }
	}
    }

    private function get_news_evens_tag_data($nid) {

	$nd = Node::load($nid);
	$uri = $nd->field_image->entity->getFileUri();
	$url = ImageStyle::load('news_and_even_thumb')->buildUrl($uri);


	$output = '';
	$output .= '<li class="clearfix">';
	$output .= '<span class="img"><img alt="" src="' . $url . '"></span>';
	$output .= '<div class="abt-newsContent">' . \Drupal::l(t($nd->get('title')->value), Url::fromUri('internal:/node/' . $nid));
	$output .= '<p>' . date("M d, Y", strtotime($nd->get('field_publish_date')->value)) . '</p>';
	$output .= '</div>';
	$output .= '</li>';
	return $output;
    }

}
