<?php
/**
 * Vector - Modern version of MonoBook with fresh look and many usability
 * improvements.
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along
 * with this program; if not, write to the Free Software Foundation, Inc.,
 * 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.
 * http://www.gnu.org/copyleft/gpl.html
 *
 * @todo document
 * @file
 * @ingroup Skins
 */

if ( ! defined( 'MEDIAWIKI' ) ):
	die( -1 );
endif;

/**
 * SkinTemplate class for Vector skin
 * @ingroup Skins
 */
class SkinWikiCLF extends SkinTemplate {
	protected static $bodyClasses = array( 'vector-animateLayout' );

	var $skinname = 'wikiclf', $stylename = 'wikiclf',
		$template = 'WikiCLFTemplate', $useHeadElement = true;

	/**
	 * Initializes output page and sets up skin-specific parameters
	 * @param $out OutputPage object to initialize
	 */
	public function initPage( OutputPage $out ) {
		global $wgLocalStylePath;
		
		parent::initPage( $out );
		
		// Append CSS which includes IE only behavior fixes for hover support -
		// this is better than including this in a CSS fille since it doesn't
		// wait for the CSS file to load before fetching the HTC file.
		$min = $this->getRequest()->getFuzzyBool( 'debug' ) ? '' : '.min';
		$out->addHeadItem( 'csshover',
			'<!--[if lt IE 7]><style type="text/css">body{behavior:url("' .
				htmlspecialchars( $wgLocalStylePath ) .
				"/{$this->stylename}/csshover{$min}.htc\")}</style><![endif]-->"
		);
		$out->addHeadItem( 'favicon', '<link rel="shortcut icon" href="http://cdn.ubc.ca/clf/7.0.3/img/favicon.ico">') ;
		$out->addHeadItem( 'favicon-touch-144', '<link rel="apple-touch-icon-precomposed" sizes="144x144" href="http://cdn.ubc.ca/clf/7.0.3/img/apple-touch-icon-144-precomposed.png">') ;
		$out->addHeadItem( 'favicon-touch-114', '<link rel="apple-touch-icon-precomposed" sizes="114x114" href="http://cdn.ubc.ca/clf/7.0.3/img/apple-touch-icon-114-precomposed.png">') ;
		$out->addHeadItem( 'favicon-touch-72', '<link rel="apple-touch-icon-precomposed" sizes="72x72" href="http://cdn.ubc.ca/clf/7.0.3/img/apple-touch-icon-72-precomposed.png">') ;
		$out->addHeadItem( 'favicon-touch-57', '<link rel="apple-touch-icon-precomposed" href="http://cdn.ubc.ca/clf/7.0.3/img/apple-touch-icon-57-precomposed.png">') ;
		
		$out->addModules( 'skins.wikiclf' );
		
		$out->addStyle( 'http://cdn.ubc.ca/clf/7.0.3/css/ubc-clf-full.min.css', 'screen' );
		$out->addStyle( 'common/commonElements.css', 'screen' );
		$out->addStyle( 'common/commonContent.css', 'screen' );
		$out->addStyle( 'common/commonInterface.css', 'screen' );
		$out->addStyle( 'wikiclf/vector.css', 'screen' );
		$out->addStyle( 'wikiclf/inputs.css', 'screen' );
		$out->addStyle( 'wikiclf/wikiclf.css', 'screen' );
		$out->addStyle( 'wikiclf/responsive.css', 'screen' );
	}

	/**
	 * Load skin and user CSS files in the correct order
	 * fixes bug 22916
	 * @param $out OutputPage object
	 */
	function setupSkinUserCss( OutputPage $out ) {
		parent::setupSkinUserCss( $out );
		$out->addModuleStyles( 'skins.wikiclf' );
	}

	/**
	 * Adds classes to the body element.
	 * 
	 * @param $out OutputPage object
	 * @param &$bodyAttrs Array of attributes that will be set on the body element
	 */
	function addToBodyAttributes( $out, &$bodyAttrs ) {
		if ( isset( $bodyAttrs['class'] ) && strlen( $bodyAttrs['class'] ) > 0 ):
			$bodyAttrs['class'] .= ' ' . implode( ' ', static::$bodyClasses );
		else:
			$bodyAttrs['class'] = implode( ' ', static::$bodyClasses );
		endif;
	}
}

/**
 * QuickTemplate class for Vector skin
 * @ingroup Skins
 */
class WikiCLFTemplate extends BaseTemplate {
	
	/**
	 * Outputs the entire contents of the (X)HTML page
	 */
	public function execute() {
		global $wgVectorUseIconWatch;
		
		// Build additional attributes for navigation urls
		$nav = $this->data['content_navigation'];
		
		if ( $wgVectorUseIconWatch ):
			$mode = ( $this->getSkin()->getUser()->isWatched( $this->getSkin()->getRelevantTitle() ) ? 'unwatch' : 'watch' );
			if ( isset( $nav['actions'][$mode] ) ):
				$nav['views'][$mode] = $nav['actions'][$mode];
				$nav['views'][$mode]['class'] = rtrim( 'icon ' . $nav['views'][$mode]['class'], ' ' );
				$nav['views'][$mode]['primary'] = true;
				unset( $nav['actions'][$mode] );
			endif;
		endif;
		
		$xmlID = '';
		foreach ( $nav as $section => $links ):
			foreach ( $links as $key => $link ):
				if ( $section == 'views' && ! ( isset( $link['primary'] ) && $link['primary'] ) ):
					$link['class'] = rtrim( 'collapsible ' . $link['class'], ' ' );
				endif;
				
				$xmlID = ( isset( $link['id'] ) ? $link['id'] : 'ca-' . $xmlID );
				$nav[$section][$key]['attributes'] = ' id="' . Sanitizer::escapeId( $xmlID ) . '"';
				
				if ( $link['class'] ):
					$nav[$section][$key]['attributes'] .= ' class="' . htmlspecialchars( $link['class'] ) . '"';
					unset( $nav[$section][$key]['class'] );
				endif;
				
				if ( isset( $link['tooltiponly'] ) && $link['tooltiponly'] ):
					$nav[$section][$key]['key'] = Linker::tooltip( $xmlID );
				else:
					$nav[$section][$key]['key'] = Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( $xmlID ) );
				endif;
			endforeach;
		endforeach;
		
		$this->data['namespace_urls'] = $nav['namespaces'];
		$this->data['view_urls'] = $nav['views'];
		$this->data['action_urls'] = $nav['actions'];
		$this->data['variant_urls'] = $nav['variants'];

		// Reverse horizontally rendered navigation elements
		if ( $this->data['rtl'] ):
			$this->data['view_urls'] = array_reverse( $this->data['view_urls'] );
			$this->data['namespace_urls'] = array_reverse( $this->data['namespace_urls'] );
			$this->data['personal_urls'] = array_reverse( $this->data['personal_urls'] );
		endif;
		
		// Output HTML Page
		$this->html( 'headelement' );
		?>
		<!-- UBC Global Utility Menu -->
		<?php $this->renderNavigation( 'PERSONAL' ); ?>
		
		<div class="collapse expand" id="ubc7-global-menu">
			<div id="ubc7-search" class="expand">
				<div class="container">
					<div id="ubc7-search-box">
						<form class="form-search" method="get" action="http://www.ubc.ca/search/refine/" role="search">
							<input type="text" name="q" placeholder="Search this website" class="input-xlarge search-query">
							<input type="hidden" name="label" value="Search UBC" />
							<input type="hidden" name="site" value="wiki.ubc.ca" />
							<button type="submit" class="btn">Search</button>
						</form>
					</div>
				</div>
			</div>
			<div class="row-fluid">
				<div id="ubc7-global-header" class="expand">
					<!-- Global Utility Header from CDN -->
				</div>
			</div>
		</div>
		<!-- End of UBC Global Utility Menu -->
		<!-- UBC Header -->
		<header id="ubc7-header" class="row-fluid expand" role="banner">
			<div class="container">
				<div class="span1">
					<div id="ubc7-logo">
						<a href="http://www.ubc.ca" title="The University of British Columbia (UBC)">The University of British Columbia</a>
					</div>
				</div>
				<div class="span2">
					<div id="ubc7-apom">
						<a href="//cdn.ubc.ca/clf/ref/aplaceofmind" title="UBC a place of mind">UBC - A Place of Mind</a>                        
					</div>
				</div>
				<div class="span9" id="ubc7-wordmark-block">
					<div id="ubc7-wordmark">
						<a href="http://www.ubc.ca" title="The University of British Columbia (UBC)">The University of British Columbia</a>
						<!--<span class="ubc7-campus" id="ubc7-vancouver-campus">Vancouver Campus</span>-->
					</div>
					<div id="ubc7-global-utility">
						<button type="button" data-toggle="collapse" data-target="#ubc7-global-menu"><span>UBC Search</span></button>
						<noscript><a id="ubc7-global-utility-no-script" href="http://www.ubc.ca/" title="UBC Search">UBC Search</a></noscript>
					</div>
				</div>
			</div>
		</header>
		<!-- End of UBC Header -->
		
		<div class="wikiclf-content">
			<!-- panel -->
			<div id="mw-panel" class="noprint">
				<!-- logo -->
				<div id="p-logo" class="hidden-phone">
					<a style="background-image: url(<?php $this->text( 'logopath' ) ?>);" href="<?php echo htmlspecialchars( $this->data['nav_urls']['mainpage']['href'] ) ?>" <?php echo Xml::expandAttributes( Linker::tooltipAndAccesskeyAttribs( 'p-logo' ) ) ?>></a>
				</div>
				<!-- /logo -->
				<div id="p-links">
					<?php $this->renderPortals( $this->data['sidebar'] ); ?>
				</div>
			</div>
			<!-- /panel -->
			
			<!-- header -->
			<div id="mw-head" class="noprint">
				<div class="visible-phone pull-right">
					<?php $this->renderNavigation( array( 'SEARCH' ) ); ?>
				</div>
				<div id="mw-head-inner">
					<div id="left-navigation">
						<?php $this->renderNavigation( array( 'NAMESPACES', 'VARIANTS' ) ); ?>
					</div>
					<div id="right-navigation">
						<?php $this->renderNavigation( array( 'VIEWS', 'ACTIONS' ) ); ?>
						<span class="hidden-phone">
							<?php $this->renderNavigation( array( 'SEARCH' ) ); ?>
						</span>
					</div>
				</div>
			</div>
			<!-- /header -->
			
			<!--
			<div id="mw-page-base" class="noprint"></div>
			<div id="mw-head-base" class="noprint"></div>
			-->
			
			<!-- content -->
			<div id="content" class="mw-body">
				<a id="top"></a>
				<div id="mw-js-message" style="display:none;"<?php $this->html( 'userlangattributes' ) ?>></div>
				
				<?php if ( $this->data['sitenotice'] ): ?>
					<!-- sitenotice -->
					<div id="siteNotice"><?php $this->html( 'sitenotice' ) ?></div>
					<!-- /sitenotice -->
				<?php endif; ?>
				
				<!-- firstHeading -->
				<h1 id="firstHeading" class="firstHeading"><span dir="auto"><?php $this->html( 'title' ) ?></span></h1>
				<!-- /firstHeading -->
				
				<!-- bodyContent -->
				<div id="bodyContent">
					<?php if ( $this->data['isarticle'] ): ?>
						<!-- tagline -->
						<div id="siteSub"><?php $this->msg( 'tagline' ) ?></div>
						<!-- /tagline -->
					<?php endif; ?>
					
					<!-- subtitle -->
					<div id="contentSub"<?php $this->html( 'userlangattributes' ) ?>><?php $this->html( 'subtitle' ) ?></div>
					<!-- /subtitle -->
					
					<?php if ( $this->data['undelete'] ): ?>
						<!-- undelete -->
						<div id="contentSub2"><?php $this->html( 'undelete' ) ?></div>
						<!-- /undelete -->
					<?php endif; ?>
					
					<?php if( $this->data['newtalk'] ): ?>
						<!-- newtalk -->
						<div class="usermessage"><?php $this->html( 'newtalk' )  ?></div>
						<!-- /newtalk -->
					<?php endif; ?>
					
					<?php if ( $this->data['showjumplinks'] ): ?>
						<!-- jumpto -->
						<div id="jump-to-nav" class="mw-jump">
							<?php $this->msg( 'jumpto' ) ?>
							<a href="#mw-head"><?php $this->msg( 'jumptonavigation' ) ?></a><?php $this->msg( 'comma-separator' ) ?>
							<a href="#p-search"><?php $this->msg( 'jumptosearch' ) ?></a>
						</div>
						<!-- /jumpto -->
					<?php endif; ?>
					
					<!-- bodycontent -->
					<?php $this->html( 'bodycontent' ) ?>
					<!-- /bodycontent -->
					
					<?php if ( $this->data['printfooter'] ): ?>
						<!-- printfooter -->
						<div class="printfooter"><?php $this->html( 'printfooter' ); ?></div>
						<!-- /printfooter -->
					<?php endif; ?>
					
					<?php if ( $this->data['catlinks'] ): ?>
						<!-- catlinks -->
						<?php $this->html( 'catlinks' ); ?>
						<!-- /catlinks -->
					<?php endif; ?>
					
					<?php if ( $this->data['dataAfterContent'] ): ?>
						<!-- dataAfterContent -->
						<?php $this->html( 'dataAfterContent' ); ?>
						<!-- /dataAfterContent -->
					<?php endif; ?>
					
					<div class="visualClear"></div>
					
					<!-- debughtml -->
					<?php $this->html( 'debughtml' ); ?>
					<!-- /debughtml -->
				</div>
				<!-- /bodyContent -->
			</div>
			<!-- /content -->
			
			<!-- footer -->
			<div id="footer"<?php $this->html( 'userlangattributes' ) ?>>
				<?php
					foreach( $this->getFooterLinks() as $category => $links ):
						?>
						<ul id="footer-<?php echo $category ?>">
							<?php foreach( $links as $link ): ?>
								<li id="footer-<?php echo $category ?>-<?php echo $link ?>"><?php $this->html( $link ) ?></li>
							<?php endforeach; ?>
						</ul>
						<?php
					endforeach;
					
					$footericons = $this->getFooterIcons("icononly");
					if ( count( $footericons ) > 0 ):
						?>
						<ul id="footer-icons" class="noprint">
							<?php foreach ( $footericons as $blockName => $footerIcons ): ?>
								<li id="footer-<?php echo htmlspecialchars( $blockName ); ?>ico">
									<?php
										foreach ( $footerIcons as $icon ):
											echo $this->getSkin()->makeFooterIcon( $icon );
										endforeach;
									?>
								</li>
							<?php endforeach; ?>
						</ul>
						<?php
					endif;
				?>
				<div style="clear: both;"></div>
			</div>
			<!-- /footer -->
		</div>
		
		<footer id="ubc7-footer" class="expand" role="contentinfo">
			<!--
			<div class="row-fluid expand" id="ubc7-global-footer">
				<div class="container">
					<div class="span5" id="ubc7-signature"><a href="http://www.ubc.ca/" title="The University of British Columbia (UBC)">The University of British Columbia</a></div>
					<div class="span7" id="ubc7-footer-menu"></div>
				</div>
			</div>
			-->
			<div class="row-fluid expand" id="ubc7-minimal-footer">
				<div class="container">
					<div class="span12">
						<ul>
							<li><a href="//cdn.ubc.ca/clf/ref/emergency" title="Emergency Procedures">Emergency Procedures</a> <span class="divider">|</span></li>
							<li><a href="//cdn.ubc.ca/clf/ref/terms" title="Terms of Use">Terms of Use</a> <span class="divider">|</span></li>
							<li><a href="//cdn.ubc.ca/clf/ref/copyright" title="UBC Copyright">Copyright</a> <span class="divider">|</span></li>
							<li><a href="//cdn.ubc.ca/clf/ref/accessibility" title="Accessibility">Accessibility</a></li>
						</ul>
					</div>
				</div>
			</div>
		</footer>
		
		<script src="http://cdn.ubc.ca/clf/7.0.3/js/ubc-clf.min.js"></script>
		<script src="<?php echo dirname( __FILE__ )."/wikiclf/wikiclf.js"; ?>"></script>
		<script>
			jQuery(document).ready( function() {
				$('.dropdown-toggle').dropdown();
			} )
		</script>
		
		<?php $this->printTrail(); ?>
		</body>
		</html>
		<?php
	}

	/**
	 * Render a series of portals
	 *
	 * @param $portals array
	 */
	protected function renderPortals( $portals ) {
		// Force the rendering of the following portals
		if ( ! isset( $portals['SEARCH'] ) ):
			$portals['SEARCH'] = true;
		endif;
		
		if ( ! isset( $portals['TOOLBOX'] ) ):
			$portals['TOOLBOX'] = true;
		endif;
		
		if ( ! isset( $portals['LANGUAGES'] ) ):
			$portals['LANGUAGES'] = true;
		endif;
		
		// Render portals
		foreach ( $portals as $name => $content ) {
			if ( $content === false ):
				continue;
			endif;
			
			echo "\n<!-- {$name} -->\n";
			switch ( $name ):
				case 'SEARCH':
					break;
				case 'TOOLBOX':
					$this->renderPortal( 'tb', $this->getToolbox(), 'toolbox', 'SkinTemplateToolboxEnd' );
					break;
				case 'LANGUAGES':
					if ( $this->data['language_urls'] ):
						$this->renderPortal( 'lang', $this->data['language_urls'], 'otherlanguages' );
					endif;
					break;
				default:
					$this->renderPortal( $name, $content );
				break;
			endswitch;
			echo "\n<!-- /{$name} -->\n";
		}
	}

	/**
	 * @param $name string
	 * @param $content array
	 * @param $msg null|string
	 * @param $hook null|string|array
	 */
	protected function renderPortal( $name, $content, $msg = null, $hook = null ) {
		if ( $msg === null ):
			$msg = $name;
		endif;
		
		?>
		<div class="portal" id='<?php echo Sanitizer::escapeId( "p-$name" ) ?>'<?php echo Linker::tooltip( 'p-' . $name ) ?>>
			<h5<?php $this->html( 'userlangattributes' ) ?>><?php $msgObj = wfMessage( $msg ); echo htmlspecialchars( $msgObj->exists() ? $msgObj->text() : $msg ); ?></h5>
			<div class="body">
				<?php
					if ( is_array( $content ) ):
						?>
						<ul>
						<?php
							foreach( $content as $key => $val ):
								echo $this->makeListItem( $key, $val );
							endforeach;
							
							if ( $hook !== null ):
								wfRunHooks( $hook, array( &$this, true ) );
							endif;
						?>
						</ul>
						<?php
					else:
						echo $content; // Allow raw HTML block to be defined by extensions
					endif;
				?>
			</div>
		</div>
		<?php
	}

	/**
	 * Render one or more navigations elements by name, automatically reveresed
	 * when UI is in RTL mode
	 *
	 * @param $elements array
	 */
	protected function renderNavigation( $elements ) {
		global $wgVectorUseSimpleSearch;
		
		// If only one element was given, wrap it in an array, allowing more flexible arguments
		if ( ! is_array( $elements ) ):
			$elements = array( $elements );
		elseif ( $this->data['rtl'] ): // If there's a series of elements, reverse them when in RTL mode
			$elements = array_reverse( $elements );
		endif;
		
		// Render elements
		foreach ( $elements as $name => $element ):
			echo "\n<!-- {$name} -->\n";
			switch ( $element ):
				case 'NAMESPACES':
					?>
					<div id="p-namespaces" class="vectorTabs<?php echo ( count( $this->data['namespace_urls'] ) == 0  ? ' emptyPortlet' : '' ); ?>">
						<h5><?php $this->msg( 'namespaces' ) ?></h5>
						<ul <?php $this->html( 'userlangattributes' ) ?>>
							<?php foreach ( $this->data['namespace_urls'] as $link ): ?>
								<li <?php echo $link['attributes'] ?>>
									<span>
										<a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
											<?php echo htmlspecialchars( $link['text'] ) ?>
										</a>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php
					break;
				case 'VARIANTS':
					?>
					<div id="p-variants" class="vectorMenu<?php if ( count( $this->data['variant_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
						<h4>
						<?php
							foreach ( $this->data['variant_urls'] as $link ):
								if ( stripos( $link['attributes'], 'selected' ) !== false ):
									echo htmlspecialchars( $link['text'] );
								endif;
							endforeach;
						?>
						</h4>
						<h5>
							<span><?php $this->msg( 'variants' ) ?></span>
							<a href="#"></a>
						</h5>
						<div class="menu">
							<ul>
								<?php foreach ( $this->data['variant_urls'] as $link ): ?>
									<li <?php echo $link['attributes'] ?>>
										<a href="<?php echo htmlspecialchars( $link['href'] ) ?>" lang="<?php echo htmlspecialchars( $link['lang'] ) ?>" hreflang="<?php echo htmlspecialchars( $link['hreflang'] ) ?>" <?php echo $link['key'] ?>>
											<?php echo htmlspecialchars( $link['text'] ) ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<?php
					break;
				case 'VIEWS':
					?>
					<div id="p-views" class="vectorTabs<?php echo ( count( $this->data['view_urls'] ) == 0 ? ' emptyPortlet' : '' ); ?>">
						<h5><?php $this->msg('views') ?></h5>
						<ul <?php $this->html('userlangattributes') ?>>
							<?php foreach ( $this->data['view_urls'] as $link ): ?>
								<li <?php echo $link['attributes'] ?>>
									<span>
										<a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
											<?php
												// $link['text'] can be undefined - bug 27764
												if ( array_key_exists( 'text', $link ) ):
													echo array_key_exists( 'img', $link ) ?  '<img src="'.$link['img'].'" alt="'.$link['text'].'" />' : htmlspecialchars( $link['text'] );
												endif;
											?>
										</a>
									</span>
								</li>
							<?php endforeach; ?>
						</ul>
					</div>
					<?php
					break;
				case 'ACTIONS':
					?>
					<div id="p-cactions" class="vectorMenu<?php echo ( count( $this->data['action_urls'] ) == 0 ? ' emptyPortlet' : '' ); ?>">
						<h5>
							<span><?php $this->msg( 'actions' ) ?></span>
							<a href="#"></a>
						</h5>
						<div class="menu">
							<ul<?php $this->html( 'userlangattributes' ) ?>>
								<?php foreach ( $this->data['action_urls'] as $link ): ?>
									<li <?php echo $link['attributes'] ?>>
										<a href="<?php echo htmlspecialchars( $link['href'] ) ?>" <?php echo $link['key'] ?>>
											<?php echo htmlspecialchars( $link['text'] ) ?>
										</a>
									</li>
								<?php endforeach; ?>
							</ul>
						</div>
					</div>
					<?php
					break;
				case 'PERSONAL':
					?>
					<div id="wiki-toolbar" class="<?php if ( count( $this->data['personal_urls'] ) == 0 ) echo ' emptyPortlet'; ?>">
						<ul <?php $this->html( 'userlangattributes' ) ?>>
							<li class="title">
								<a href="http://wiki.ubc.ca">UBC Wiki</a>
							</li>
							<?php
								foreach( $this->getPersonalTools() as $key => $item ):
									echo $this->makeListItem( $key, $item );
								endforeach;
							?>
							<li id="pt-visit">
								<div class="btn-group">
									<a class="dropdown-toggle" data-toggle="dropdown" href="#">
										Visit
										<span class="caret"></span>
									</a>
									<ul class="dropdown-menu pull-right">
										<!-- dropdown menu links -->
										<li><a href="http://blogs.ubc.ca/">Blogs</a></li>
										<li><a href="http://wiki.ubc.ca/">Wiki</a></li>
										<li><a href="http://cms.ubc.ca/">Sites</a></li>
										<li class="divider"></li>
										<li><a href="http://blogs.ubc.ca/groups/">Groups</a></li>
										<li><a href="http://blogs.ubc.ca/forums/">Forums</a></li>
										<li><a href="http://blogs.ubc.ca/members/">People</a></li>
										<li><a href="http://elearning.ubc.ca/">eLearning</a></li>
										<li><a href="http://ipeer.apsc.ubc.ca/">iPeer</a></li>					
										<li><a href="https://webwork.elearning.ubc.ca/webwork2/">WebWorks</a></li>
									</ul>
								</div>
							</li>
						</ul>
						<div style="clear:both"></div>
					</div>
					<?php
					break;
				case 'SEARCH':
					?>
					<div id="p-search">
						<h5 <?php $this->html( 'userlangattributes' ) ?>>
							<label for="searchInput"><?php $this->msg( 'search' ) ?></label>
						</h5>
						<form action="<?php $this->text( 'wgScript' ) ?>" id="searchform" class="form-search">
							<?php
								$simpleSearch = $wgVectorUseSimpleSearch && $this->getSkin()->getUser()->getOption( 'vector-simplesearch' );
							?>
							<div <?php echo ( $simpleSearch ? 'id="simple-search"' : '' ); ?> class="search-wrapper">
								<?php
									if ( $simpleSearch ):
										if ( $this->data['rtl'] ): 
											echo $this->makeSearchButton( 'image', array(
												'id'     => 'searchButton',
												'src'    => $this->getSkin()->getSkinStylePath( 'images/search-rtl.png' ),
												'width'  => '12',
												'height' => '13',
												'class'  => 'rtl'
											) );
										endif; 
										
										echo $this->makeSearchInput( array(
											'id'    => 'searchInput',
										) );
										
										if ( ! $this->data['rtl'] ):
											echo $this->makeSearchButton( 'image', array(
												'id'     => 'searchButton',
												'src'    => $this->getSkin()->getSkinStylePath( 'images/search-ltr.png' ),
												'width'  => '12',
												'height' => '13',
											) );
										endif;
									else:
										echo $this->makeSearchInput( array(
											'id' => 'searchInput'
										) );
										
										echo $this->makeSearchButton( 'go', array(
											'id'    => 'searchGoButton',
											'class' => 'searchButton btn',
										) );
										
										echo $this->makeSearchButton( 'fulltext', array(
											'id'    => 'mw-searchButton',
											'class' => 'searchButton btn btn-primary',
										) );
									endif;
								?>
								<input type='hidden' name="title" value="<?php $this->text( 'searchtitle' ) ?>"/>
							</div>
						</form>
					</div>
					<?php
				break;
			endswitch;
			
			echo "\n<!-- /{$name} -->\n";
		endforeach;
	}
}
