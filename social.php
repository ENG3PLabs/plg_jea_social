<?php
/**
 *
 * @package      Joomla
 * @subpackage   JEA
 * @copyright    Copyright (C) 2011 Roberto Segura. All rights reserved.
 * @license      GNU/GPL, see LICENSE.txt
 * Joomla Estate Agency is free software. This version may have been modified pursuant to the
 * GNU General Public License, and as distributed it includes or is derivative
 * of works licensed under the GNU General Public License or other free or open
 * source software licenses.
 */

defined('_JEXEC') or die;

jimport('joomla.event.plugin');

/**
 * Plugin JEA Social
 *
 * @package Joomla
 * @subpackage JEA
 * @since 1.5
 */
class plgJeaSocial extends JPlugin
{

	/**
	 * onBeforeShowDescription method
	 * Called in the default_item.php tpl
	 *
	 * @param stdClass $row
	 */
	public function onBeforeShowDescription($row)
	{
		$position = $this->params->get('position', 1);

		if ($position == 1 || $position == 2)
		{
			$this->showSocialBar($row);
		}
	}

	/**
	 * onAfterShowDescription method
	 * Called in the default_item.php tpl
	 *
	 * @param stdClass $row
	 */
	public function onAfterShowDescription($row)
	{
		$position = $this->params->get('position', 1);

		if ($position == 0 || $position == 2)
		{
			$this->showSocialBar($row);
		}
	}

	protected function showSocialBar($row)
	{
		if (empty($row->id))
		{
			return;
		}

		$like = '';
		$twitter = '';
		$gplus = '';
		$digg = '';
		$linkedin = '';

		// Build the property url
		$uri = JFactory::getURI();
		$row->slug = $row->alias ? ($row->id . ':' . $row->alias) : $row->id;
		$url = $uri->toString(array('scheme', 'host', 'port'))
			 . JRoute::_('index.php?option=com_jea&view=properties&id=' . $row->slug);

		$url2 = urlencode($url);

		// Facebook
		if ($this->params->get('like')) {
			$like = '<div id="fb-root"></div>';
			$document = JFactory::getDocument();
			// Add Javascript
			$document->addScriptDeclaration('
                (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.3";
                fjs.parentNode.insertBefore(js, fjs);
                }(document, \'script\', \'facebook-jssdk\'));
                ');
			$like = '<div data-href=' . $url . ' class="fb-like jeasocial_button jeasocial_facebook" style="float:' .
				$this->params->get('float').'; width:' . $this->params->get('like_width') . 'px;" data-width="' .
				$this->params->get('like_width') . '" data-layout="'. $this->params->get('like_style') . '" data-action="' .
				$this->params->get('like_verb') . '" data-show-faces="false" data-share="' .
				$this->params->get('include_share') . '" data-colorscheme="' . $this->params->get('like_color_scheme') . '">';
			$like .= '</div>';
		}

		// Twitter
		if ($this->params->get('twitter_active'))
		{
			$width = $this->params->get('twitter_style') == 'horizontal' ? 110 : 70;

			$twitter = '<div class="jeasocial_button jeasocial_twitter" style="float:' . $this->params->get('float') . '; width:' .
					$width. 'px;">';

			$twitter .= '<a href="http://twitter.com/share?url=' . $url2 . '" class="twitter-share-button" data-text="' . $row->title .
					 ':" data-count="' . $this->params->get('twitter_style') . '" data-via="' . $this->params->get('twitter_account') .
					 '" data-related="' . $this->params->get('twitter_account2') .
					 '">Tweet</a><script type="text/javascript" src="http://platform.twitter.com/widgets.js"></script>';
			$twitter .= '</div>';
		}

		// Google+
		if ($this->params->get('gplus_active'))
		{
			// Include js callback in head section
			JFactory::getDocument()->addScript('http://apis.google.com/js/plusone.js');

			// Beta: automatic gplus language
			$lang = JFactory::getLanguage();
			$langCode = '';

			if ($langTag = explode('-', $lang->getTag()))
			{
				$langCode = 'window.___gcfg = {lang: "' . $langTag[0] . '"};';
			}

			$gplus = '<div class="jeasocial_button jeasocial_gplus" style="float:' . $this->params->get('float') . '; width:' .
					$this->params->get('gplus_width') . 'px; ">';
			$gplus .= '<div id="plusone-div"></div>';
			$gplus .= '<script type="text/javascript">' . $langCode . 'gapi.plusone.render("plusone-div", {
                           "size": "' . $this->params->get('gplus_style', 'medium') . '",
                           "annotation": "' .$this->params->get('gplus_annotation', 'bubble') . '",
                           "expandTo": "' . $this->params->get('gplus_expandto', 'bottom') . '",
                           "width": "' . $this->params->get('gplus_width', '90') . '",
                           "href": "' . $url2 . '"
                           });
                       </script>';
			$gplus .= '</div>';
		}

		// Digg
		if ($this->params->get('digg_active'))
		{
			$digg = '<div class="jeasocial_button jeasocial_digg" style="float:' . $this->params->get('float') . '; width:90px;">';
			$digg .= '<script type="text/javascript">
                                    (function() {
                                    var s = document.createElement("SCRIPT"), s1 = document.getElementsByTagName("SCRIPT")[0];
                                    s.type = "text/javascript";
                                    s.async = true;
                                    s.src = "http://widgets.digg.com/buttons.js";
                                    s1.parentNode.insertBefore(s, s1);
                                    })();
                      </script>';
			$digg .= '<a class="DiggThisButton ' . $this->params->get('digg_style', 'DiggCompact') . '" href="http://digg.com/submit?url=' . $url2 .
						 '&amp;title=' . $row->title . '"></a>';
			$digg .= '</div>';
		}

		// Linkedin
		if ($this->params->get('linkedin_active'))
		{
			$width= $this->params->get('linkedin_style') == 'right' ? 110 : 75;

			$linkedin = '<div class="jeasocial_button jeasocial_linkedin" style="float:' . $this->params->get('float') . '; width: ' .
					$width. 'px;">';
			$linkedin .= '<script src="http://platform.linkedin.com/in.js" type="text/javascript"></script>
									<script type="IN/Share" data-url="' . $url2 . '" data-counter="' .
					 $this->params->get('linkedin_style') . '"></script>';
			$linkedin .= '</div>';
		}

		// Pinterest
		if ($this->params->get('pint_active'))
		{
			$width= $this->params->get('pint_style') == 'horizontal' ? 110 : 70;

			$pinterest = '<div class="jeasocial_button jeasocial_linkedin" style="float:' . $this->params->get('float') . '; width: ' .
					$width. 'px;">';
			$pinterest .= '<script type="text/javascript" src="//assets.pinterest.com/js/pinit.js"></script>';
			$pinterest .= '<a href="http://pinterest.com/pin/create/button/?url=' . $url2 . '" class="pin-it-button" count-layout="' .
					 $this->params->get('pint_style') . '"><img border="0" src="//assets.pinterest.com/images/PinExt.png" title="Pin It" /></a>';
			$pinterest .= '</div>';
		}

		echo '<div class="jeasocial">' . $twitter . $like . $gplus . $digg . $linkedin . $pinterest . '<div style="clear:both;"></div></div>';
	}
}