<?php

namespace Drupal\custom_function\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\Core\Link;
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
 * Provides a 'Custom Inner Page Video' Block.
 *
 * @Block(
 *   id = "custom_inner_industry_segments",
 *   admin_label = @Translation("Inner Industry Segments"),
 *   category = @Translation("Custom Function")
 * )
 */

class InnerIndustrySegments extends BlockBase {

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
	    $node_type = Settings::get('industry_node', NULL);

	    if (in_array($type_name, $node_type)) {

		$current_uri = 'entity:node/' . $path_args[2];

		$query = \Drupal::database()->select('menu_link_content_data', 'mlcd');
		$query->join('menu_link_content', 'mlc', 'mlcd.id = mlc.id');
		$query->addField('mlc', 'uuid');
		$query->condition('mlcd.link__uri', $current_uri);
		$query->range(0, 1);
		$uuid = $query->execute()->fetchField();
		$parent_uuid = 'menu_link_content:' . $uuid;


		$child_menu = array();
		$query = \Drupal::database()->select('menu_link_content_data', 'mlcd');
		$query->fields('mlcd', ['id', 'title', 'link__uri']);
		$query->condition('mlcd.menu_name', 'main');
		$query->condition('mlcd.enabled', 1);
		$query->condition('mlcd.parent', $parent_uuid);
		$query->orderBy('mlcd.weight', 'ASC');
		$result = $query->execute();
		$i = 0;
		while ($row = $result->fetchAssoc()) {
		    $child_menu[$i] = array('id' => $row['id'], 'title' => $row['title'], 'link' => $row['link__uri']);
		    $i++;
		}

		if ($i > 0) {
		    $output .= '<div class="industrysegments">
				<img class="img-responsive" alt="" src="/themes/natg/images/industrysegments-bg.jpg">
				<div class="industrysegmentsContent">
				<div class="industrysegmentsheading">Industry Segments</div>
				<ul class="clearfix">';

		    foreach ($child_menu as $key => $value) {

			$title = $value['title'];
			$link = $value['link'];
			$path = str_replace('entity:', '/', $link);
			$path_arr = explode('/', $link);
			$child_nid = $path_arr[1];
			$industry_img = $this->get_industry_image($child_nid);

			$alias = \Drupal::service('path.alias_manager')->getAliasByPath($path);

			$output .= '<li><a href="' . $alias . '">' . $industry_img . '<span>' . $title . '</span></a></li>';
		    }

		    $output .= '</ul>
				</div>
				</div>';
		}
	    }
	}

	return $output;
    }

    private function get_industry_image($nid) {
	$nd = Node::load($nid);
	$type_name = $nd->getType();

	if (!$nd->get('field_industry')->isEmpty()) {
	    $banner_uri = $nd->field_industry->entity->getFileUri();
	    $url = ImageStyle::load('industry_segments')->buildUrl($banner_uri);
	    $title = $nd->get('title')->value;
	    $output .= '<img src="' . $url . '" alt="' . $title . '">';
	} else {
	    $field_info = FieldConfig::loadByName('node', $type_name, 'field_industry');
	    $image_uuid = $field_info->getSetting('default_image')['uuid'];
	    // dpm($image_uuid);
	    $image = \Drupal::service('entity.repository')->loadEntityByUuid('file', $image_uuid);
	    $image_uri = $image->getFileUri();
	    $url = ImageStyle::load('industry_segments')->buildUrl($image_uri);
	    $title = $nd->get('title')->value;
	    $output .= '<img src="' . $url . '" alt="' . $title . '">';
	}

	return $output;
    }

}
