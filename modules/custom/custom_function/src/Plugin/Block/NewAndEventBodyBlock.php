<?php

namespace Drupal\custom_function\Plugin\Block;

use Drupal\Core\Block\BlockBase;
use Drupal\Core\Url;
use Drupal\node\Entity\Node;
use Drupal\image\Entity\ImageStyle;

####################################################################################################
# Another new concept to Drupal that we need to use for the block is Annotations.                  #
# In order for Drupal to find your block code, you need to implement a code comment                #
# in a specific way, called an Annotation. An Annotation provides basic details of                 #
# the block such as an id and admin label. The admin label will appear on the block listing page.  #
#####################################################################################################
/**
 * Provides a 'Custom Test Block' Block.
 *
 * @Block(
 *   id = "custom_news_event_body",
 *   admin_label = @Translation("News Event Body"),
 *   category = @Translation("Custom Function")
 * )
 */

class NewAndEventBodyBlock extends BlockBase {

    /**
     * {@inheritdoc}
     */
    public function build() {
	return array(
	    '#markup' => $this->get_news_event_data(),
	);
    }

    private function get_news_event_data() {
	$output = '';
	$nv = array();
	$query = \Drupal::database()->select('node_field_data', 'nfd');
	$query->fields('nfd', ['nid', 'title', 'created']);
	$query->join('node_revision__field_publish_date', 'fpd', 'nfd.nid = fpd.entity_id');
	$query->condition('nfd.type', 'news_event');
	$query->condition('nfd.status', 1);
	//$query->orderBy('nfd.created', 'DESC');
	$query->orderBy('fpd.field_publish_date_value', 'DESC');
	$query->range(0, 10);
	$result = $query->execute();
	$i = 1;
	while ($row = $result->fetchAssoc()) {

	    $nv[$i] = array('nid' => $row['nid'], 'title' => $row['title'], 'created' => $row['created']);
	    $i++;
	}
	$nd_cnt = count($nv);
	$multple_two_cnt = floor($nd_cnt / 2);


	$output .= '<div class="row">';
	$output .= '<div id="myCarousel" class="carousel slide" data-ride="carousel">';

	$output .= '<ol class="carousel-indicators">';

	for ($i = 0; $i < $multple_two_cnt; $i++) {
	    if ($i == 0) {
		$output .= '<li data-slide-to="' . $i . '" data-target="#myCarousel" class="active"></li>';
	    } else {
		$output .= '<li data-slide-to="' . $i . '" data-target="#myCarousel" ></li>';
	    }
	}

	$output .= '</ol>';

	$output .= '<div class="carousel-inner" role="listbox">';
	for ($i = 0; $i < $multple_two_cnt; $i++) {
	    if ($i == 0) {
		$output .= '<div class="item  active">';
	    } else {
		$output .= '<div class="item">';
	    }
	    $id_1 = 2 * $i + 1;
	    $id_2 = 2 * $i + 2;
	    $nid_1 = $nv[$id_1]['nid'];
	    $nid_2 = $nv[$id_2]['nid'];
	    $nd_1 = Node::load($nid_1);
	    $nd_2 = Node::load($nid_2);
	    $uri_1 = $nd_1->field_image->entity->getFileUri();
	    $uri_2 = $nd_2->field_image->entity->getFileUri();
	    $url_1 = ImageStyle::load('news_and_even_thumb')->buildUrl($uri_1);
	    $url_2 = ImageStyle::load('news_and_even_thumb')->buildUrl($uri_2);

	    $output .= '<div class="col-sm-6">';
	    $output .= '<div class="newsBox clearfix">';
	    $output .= '<div class = "newsImg"><img alt = "" class = "img-responsive" src = "' . $url_1 . '"></div>';
	    $output .= '<div class="newsContentSec">';
	    $output .= '<p class="newsDate">' . date("M d, Y", strtotime($nd_1->get('field_publish_date')->value)) . '</p>';
	    $output .= '<p class="newsHeadign">' . \Drupal::l(t($nd_1->get('title')->value), Url::fromUri('internal:/node/' . $nid_1)) . '</p>';
	    $output .= '<div class="newsContent">';
	    $output .= '<p>' . substr(strip_tags($nd_1->get('body')->value), 0, 150) . '</p>';
	    $output .= '</div>';
	    $output .= '</div>';
	    $output .= '</div>';
	    $output .= '</div>';

	    $output .= '<div class="col-sm-6">';
	    $output .= '<div class="newsBox clearfix">';
	    $output .= '<div class = "newsImg"><img alt = "" class = "img-responsive" src = "' . $url_2 . '"></div>';
	    $output .= '<div class="newsContentSec">';
	    $output .= '<p class="newsDate">' . date("M d, Y", strtotime($nd_2->get('field_publish_date')->value)) . '</p>';
	    $output .= '<p class="newsHeadign">' . \Drupal::l(t($nd_2->get('title')->value), Url::fromUri('internal:/node/' . $nid_2)) . '</p>';
	    $output .= '<div class="newsContent">';
	    $output .= '<p>' . substr(strip_tags($nd_2->get('body')->value), 0, 150) . '</p>';
	    $output .= '</div>';
	    $output .= '</div>';
	    $output .= '</div>';
	    $output .= '</div>';
	    $output .= '</div>'; // item
	}
	$output .= '</div>'; // carousel-inner

	$output .= '<a class = "left left-btn" data-slide = "prev" href = "#myCarousel" role = "button"><span aria-hidden = "true"><i aria-hidden = "true" class = "fa fa-angle-left"></i></span></a>';
	$output .= '<a class = "right right-btn" data-slide = "next" href = "#myCarousel" role = "button"><span aria-hidden = "true"><i aria-hidden = "true" class = "fa fa-angle-right"></i></span></a>';

	$output .= '</div>'; // myCarousel
	$output .= '</div>'; // row
	return $output;
    }

}
