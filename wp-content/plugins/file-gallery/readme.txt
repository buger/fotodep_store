=== File Gallery ===
Contributors: aesqe, azizur
Donate link: http://skyphe.org/donate/
Tags: attachment, attachments, gallery, galleries, template, templates, shortcode, file, files, attach, detach, unattach, copy, media, tags, library, custom, custom fields, custom fields for attachments, attachment custom fields
Requires at least: 3.1
Tested up to: 3.3
Stable tag: 1.7.1

File Gallery extends WordPress media (attachments) capabilities 
by adding a new gallery shortcode handler with templating support 
(and much more).



== Description ==

"File Gallery" extends WordPress' media (attachments) capabilities 
by adding a new gallery shortcode handler with templating support, 
a new interface for attachment handling when editing posts, and much 
more... Here's the full list of features:

**Features:**

1.  multiple galleries per post with custom attachment order
2.  a flexible templating system (PHP, CSS, JS) - choose a different 
    template for each gallery, even within the same post (4 templates 
	included with plugin)
3.  simple, easy to use UI with drag and drop sorting shows attachment 
    thumbnails beneath text editor: everything attachments-related is on 
    the same screen you're editing your post on
4.  fully integrated with the visual editor (tinyMCE) - click on the 
    [gallery] placeholder image and change any option in the File 
	Gallery metabox - changes will be applied instantly
5.  settings page extends the default media settings page
6.  attach copies of items from media library to current post (copies 
    data only, not the file)
7.  copy all attachments from another post
8.  unattach (detach) items from current post
9.  media tags = tag your attachments and then use those tags to choose 
    which attachments you want to display in your gallery or to filter 
	your media library items.
10. custom fields for attachments
11. gallery pagination
12. different background colors for items in media library depending 
    on their status = completely unattached (white), attached to other 
    posts (red), or attached to current post (yellow)
13. compatible with "WordPress Mobile Edition", "Media Tags" and 
    "WPML Multilingual CMS" plugins	
14. basic caching of gallery output and frequent queries (transients)
15. please see the help file for complete list of features :)



== Screenshots ==

1.  File Gallery main box on editing screen
2.  Edit attachment data
3.  Copy all attachments from another post
4.  Deleting attachments that have copies
5.  Build a gallery by choosing media tags
6.  Settings page
7.  Post thumb and number of attachments as extras in this view
8.  Copying attachments from media library
9.  With "WPML Multilingual CMS" plugin istalled, you'll be able to copy 
    all attachments from the original post to translated one in two clicks
10. Custom fields for attachments



== Installation == 

1.	Place the whole 'file-gallery' folder into your WordPress 
	installation folder (usually under 'wp-content\plugins').
2.	Go to WordPress administration -> plugins page and activate 
	"File Gallery" plugin.
3.	You're done - go edit or add a new post to see how it works :)



== FAQ ==

Coming soon.

For additional information, please see the File Gallery help file.
It's included with the installation, but you can also view it online here: 
<a href="http://skyphe.org/wp-content/plugins/file-gallery/help/index.html">http://skyphe.org/wp-content/plugins/file-gallery/help/index.html</a>

**Full list of filters within File Gallery**

file_gallery_templates_folder_abspath
file_gallery_templates_folder_url
file_gallery_content_templates_folder_abspath
file_gallery_content_templates_folder_url
file_gallery_default_template_url
file_gallery_default_template_abspath
file_gallery_default_template_name
file_gallery_crystal_url
file_gallery_admin_css_location
file_gallery_admin_rtl_css_location
file_gallery_post_attachment_count
file_gallery_post_thumb_content
file_gallery_no_post_thumb_content
file_gallery_parse_attachment_data
file_gallery_attachment_copy_title_extension
file_gallery_mime_types
file_is_displayable_image
file_gallery_lightboxes_options
file_gallery_lightbox_classes
file_gallery_approved_attachment_post_statuses
file_gallery_ignored_attachment_post_statuses
file_gallery_lightbox_linkrel
file_gallery_lightbox_linkclass
file_gallery_lightbox_imageclass
file_gallery_non_image_thumb_link
file_gallery_galleryclass
file_gallery_output



== Changelog ==

= 1.7.1 =
* December 15th, 2011
* fixed a nasty overlook concerning wp_rewrite->flush_rules()
  (called too early) - so sorry about that :|

= 1.7 =
* December 13th, 2011
* public release for WordPress 3.3

= 1.7-RC14 =
* December 11th, 2011
* fixed a cut/paste mistake that was preventing 
  'file_gallery_default_template_abspath' filter from 
  working properly - thanks bedex78!
* updated POT file

= 1.7-RC13 =
* December 10th, 2011
* fixed [gallery] 'exclude' parameter
* fixed filtering of File Gallery templates locations; added
  support for storing templates within wp-content folder (in a
  subfolder named 'file-gallery-templates')
* changed the interface a bit

= 1.7-RC12 =
* November 26th, 2011
* fixed bug: on thickbox close, overlay would stay visible and 
  file gallery would get stuck in "loading" loop
* fixed bug: multiple single images insert, first image's caption 
  would stick for all images
* a few UI fixes
* some javascript improvements
* added a new notice on media settings screen
* WordPress 3.3 compatible

= 1.7-RC11 =
* November 16th, 2011
* fixed "Simple" template images stuck on loading animation bug in MSIE
* rearranged the File Gallery metabox a bit, trying to reduce the 
  visual footprint
* a few minor fixes (single images insert)

= 1.7-RC10 =
* October 2nd, 2011
* two minor fixes

= 1.7-RC9 =
* October 1st, 2011
* rtl direction file gallery metabox fixes
* full Media Tags plugin compatibility
* ability to change media_tag taxonomy name and URL slug 
  (WP Admin -> Settings -> Permalinks),
* various media_tag taxonomy fixes
* rel attribute is now always available
* "upload files" button with the file gallery metabox is always 
  visible now

= 1.7-RC8 =
* August 21st, 2011
* when copying attachments, copy custom fields and media tags 
  too [thanks joo-joo]
* disabling attachment custom fields now also affects library display
* fixed some attachment custom fields related javascript bugs

= 1.7-RC7 =
* August 19th, 2011
* ability to sort attachments within the File Gallery metabox by 
  title, date, or menu_order [thanks to alexbarber]
* custom rel attribute value for galleries [thanks to thedarkmist]
* fixed double media tags submenus when Media Tags plugin is 
  active [props alx359]

= 1.7-RC5 = 
* July 31st, 2011
* fixed MSIE tinyMCE content insert position bug
* fixed gallery output to produce a HTML comment instead of an empty
  string, so that the default gallery won't be shown if no attachments
  were found for specified arguments

= 1.7-RC4 =
* July 23rd, 2011
* SSL Admin support
* Better compatibility with plugin checkboxes in media library popup
* other minor fixes

= 1.7-RC3 =
* July 10th, 2011
* WordPress 3.2 compatible
* minor Media Tags plugin compatibility fix

= 1.7 =
* May XX, 2011
* reworked the settings system so it's easier to add new options
* improved lightbox support, more flexibility
* improved pagination
* new gallery option: gallery class
* new metabox option: alternative color scheme 
  (and still working on it)
* ability to copy attachments from WPML translations which aren't
  primary translations
* added the pot file to languages directory
* bugfixes, bugfixes, bugfixes

= 1.6.5.6 =
* August 4th, 2011
* fixed jQuery compatibility with WP < 3.2, sorry about that :|

= 1.6.5.5 = 
* August 1st, 2011
* WP 3.2 (jQuery) compatibility
* internet explorer fixes:
  - correct position in editor when inserting content
  - update of gallery shortcode (instead of deletion) when gallery
    placeholder is clicked
  - fixed File Gallery metabox items editing buttons visibility CSS bug

= 1.6.6-beta =
* January 11th, 2011
* added option to display just the insert buttons 
  for gallery/single images
* better handling of file gallery options
* image thumbnails regeneration (works, but needs a non-mockupy ui :)
* some css fixes for better ie6-7 compliance
* various small fixes all over the place

= 1.6.5.4 =
* January 6th, 2011
* bugfix: left an alert box in javascript, eh...

= 1.6.5.3 =
* January 6th, 2011
* options to hide and toggle display of attachment custom fields
* separated hiding of gallery and single image insert options
* few minor improvements and bugfixes

= 1.6.5.2 =
* December 16th, 2010
* fixed a JS bug where attachment reordering would not work if
  gallery/single image insert options are hidden
* set the option to display single image captions to true by default
* thanks to jardokraka for noticing both issues :)
* a few small bugfixes in javascript and template js dependencies

= 1.6.5.1 =
* December 6th, 2010
* new function file_gallery_overrides() which you can use in 
  your template files before the_content() to modify [gallery] 
  arguments for that post, like this:
  file_gallery_overrides( array('size' => 'medium') )
  (will add it to the help file soon)
* gallery pagination: must use with 'limit' argument;
  will NOT work with paginated posts or pages;
  example: [gallery limit="6" paginate="true"]
  (will add it to the help file soon)
* a few small bugfixes: urlencoded caption for single images; 
  'simple' template no link variant; if link_size is set and 
  link="none", images are still linked

= 1.6.5 =
* November 7th, 2010
* custom fields for attachments
* variable width/height for thumbnails in File Gallery metabox
* fixed the "simple" theme so it does not rely on colorbox being 
  installed and checks if thickbox is available
* some JavaScript improvements
* new [gallery] attribute, 'link_size' - choose which image size 
  thumbnails should be linked to
* bugfix: image align class

= 1.6.4.1 =
* October 12th, 2010
* bugfix: single image alignment class wasn't showing
* updated the translations

= 1.6.4 =
* October 10th 2010
* if there's a "gallery.js" file inside your template folder, it 
  will be automatically enqueued. You can set its JavaScript 
  dependencies by defining a $js_dependencies variable inside your
  template's PHP file - it must be an array, something like this:
  $js_dependencies = array("jquery")
* new template - 'simple' - added to demonstrate inclusion of the 
  JavaScript file (thanks to spiranovich!)
* added captions support for single file insertion
* fixed crystal icons url bug - sorry about that :/

= 1.6.3 = 
* October 3rd, 2010
* moved constants definitions into another function to allow 
  functions.php filtering of most of the constants
* fixed disappeared attachment count
* zero columns option, columns.css is loaded only when needed
* new constants, see help file:
  FILE_GALLERY_CRYSTAL_URL,
  FILE_GALLERY_DEFAULT_TEMPLATE_ABSPATH,
  FILE_GALLERY_DEFAULT_TEMPLATE_URL,
  FILE_GALLERY_DEFAULT_TEMPLATE_NAME
* added gallery id to gallery wrapper element
* "file_gallery_output" filter
* post ID option for shortcode
* post thumbs width on post editing screen is now controlled via
  JavaScript to give more space to other columns on smaller screens

= 1.6.2 =
* September 12th, 2010
* added Hebrew translation and support for RTL languages
  - Maor Barazany, thank you so much for your assistance :)
* some new filters (check the help file)
* small cosmetic changes in code
* added a proper donation link to this readme ;)
* skipped v1.6.1 so it wouldn't confuse people (because of 1.6.0.1)

= 1.6.0.1 =
* August 24th, 2010
* fixed a bug in media settings where custom image size height would 
  get the value of width because of a typo - sorry :/

= 1.6 =
* August 17th, 2010
* added French translation (thanks, Jean-Michel!)
* reverted to default 'load_plugin_textdomain'
* fixed a link 'rel' attribute bug
* modified the default templates a bit (if/else statements for image 
  class)
* minor js changes regarding (un)setting featured image translation

= 1.5.9 =
* August 11th, 2010
* fixed a small javascript bug
* made some changes regarding textdomain loading
* updated the help file
* updated screenshots

= 1.5.8 =
* August 1st, 2010
* implemented 'limit' option in UI
* all the stuff below related to 1.5.8b1 and 1.5.8b2
* new WP tinyMCE version probably in 3.1, will investigate...

= 1.5.8b2 =
* July 25th, 2010
* added mime type support, more info in help file when 1.5.8 leaves the
  testing phase. For now:
  - you can either use the full mime type (like application/pdf) or the 
    shorthand variants (pdf, word, doc, xls, excel, cvs, zip, rar, 7zip)
  - you can extend the mime types array using the 
    'file_gallery_mime_types' filter
* added 'limit' in code, but I just remembered I didn't implement it in 
  UI, so forget about that for now (will anybody use this, anyway?) :D
* minor tweaks and bugfixes, added a few new actions and filters, 
  enhanced the support for lightbox scripts... New help files are 
  coming soon :)
  
= 1.5.8b1 =
* July 20th, 2010
* almost complete tinyMCE integration - click on [gallery] placeholder 
  image and change any option in File Gallery metabox - changes will be 
  automatically  applied :)
  - it's not working in Webkit browsers (safari, chrome...) at the 
    moment because of Webkit problems with 
	tinyMCE.activeEditor.selection.setContent() in tinyMCE versions 
	lower than 3.3.6. If you're an Webkit browser user, you can either 
	upgrade your tinyMCE editor to a newer version, or wait for 
	WordPress 3.0.1 (should be out soon)
  - Internet Explorer loses focus and updating contents doesn't work.
    I'll do my best to fix it as soon as I can.
  - Opera and Firefox seem to be working just fine :)
* couple minor bugfixes

= 1.5.7 =
* July 3rd, 2010
* added option to disable file gallery shortcode handler (if you want  
  to use File Gallery functionality in backend only)
* added options to link a gallery or single images to some external url
* fixed the post thumb -1 (WPRemoveThumbnail nonce) bug

= 1.5.6 =
* June 19th, 2010
* added 'include' and 'exclude' parameters for compatibility with 
  standard WP gallery shortcode function. 'include' is identical to
  'attachment_ids' and will be used as the default parameter name 
  from this version on
* added columns parameter and "Clear File Gallery cache" button to 
  the File Gallery UI

= 1.5.5 =
* June 2nd, 2010
* fixed a bug where image caption for an image without a caption set
  would be from previous image
* added alternate text field in editor for image attachments

= 1.5.4 =
* June 1st, 2010
* squashed some bugs, added forgotten global variables

= 1.5.3 =
* May 31st, 2010
* fixed a bug where attachment class value would be appended to 
  ad infinitum, when inserting multiple single attachments

= 1.5.2 =
* May 30th, 2010
* added image align options for single image inserts
* added option to link images to parent post
* muchly improved file_gallery_list_tags output
* fixed the url to 'crystal' icons for non-image file types
* added support for alt text for images
* support for lightbox-type scripts:
  - choose which link classes trigger auto-enqueueing of 
    scripts and styles (a script should be registered beforehand 
	for this to work)
  - added filters to modify image class, link class, and link rel 
    attribute
* a bunch of bugfixes

= 1.5.1 =
* May 4th, 2010
* added option to filter out duplicate attachments (copies) when
  browsing media library
* post thumbnail is now removed when that attachment is deleted or
  detached
* and finally, the help file is included :)

= 1.5 =
* April 25th, 2010
* fixed _has_copies meta handling when deleting a copy
* copied log writing function from Decategorizer
* some minor improvements
* first release sent to the WordPress plugin repository

= 1.5rc1 =
* April 16th, 2010
* set / unset post thumbnail with one click in the File Gallery box
* when copying all attachments from another post, if current post has 
  no attachments of its own, automatically set post thumb to be the same 
  as for the post we're copying attachments from
* if you have "WPML Multilingual CMS" plugin installed and you're editing
  a post that is a translation, you'll notice a bluish link at the bottom 
  of the "Language" metabox. It allows you to copy all attachments from 
  the original post in just two clicks :)
* some minor improvements

= 1.5b3 =
* April 5th, 2010
* javascript bugfixes: attachment copying, clickable media tags, insert 
  single images into post... happens when one is not doing the debugging 
  part right from the beginning... :|

= 1.5b2 =
* April 4th, 2010
* bugfix: options would be reset on reactivation
* compatibility with custom post types in WordPress 3.0 (set it up on
  media options page, right under "File Gallery" heading)
* added option to delete all options on deactivation (for uninstall)
* check user capabilities before any delete or edit action

= 1.5b =
* March 20th, 2010
* bugfix: attachment data didn't get saved when edited inside File 
  Gallery metabox (a slight javascript oops, sorry about that)
* caching via WordPress transients
* states (shown/hidden) of insert options fieldsets are now saved 
  and preserved in options automatically
* new options:
	- enable/disable insert options fieldsets (displays 
	  attachment list only)
	- enable caching
	- cache non-html output
	- cache expiry time
	- clear cache
	- what additional columns to show on post/page edit screens
* some minor css fixes

= 1.5a =
* February 8th, 2010
* added ability to copy all attachments from another post
* added a list of media tags for each attachment (media library screen).
  this also means you can click on a tag's name and you'll get a list 
  of all attachments using that tag
* attachment edit screen: added default icon for non images (for now)
* more icon types in file gallery metabox :D

= 1.4 =
* January 19th, 2010
* more code optimizations, especially javascript
* added buttons to toggle gallery / single image options
* image zoom now opens fullsize image in a jQuery UI dialog
  (works fine in all browsers except Opera - does not get css position,
  will fix for 1.5)
* fixed theme folder url, custom templates now work (just place them in
  a folder named "file-gallery-templates" within your theme's folder)

= 1.3 =
* massive reorder / rewrite of the code, especially the javascript
  part which now performs much better, even in internet explorer
* complete rethink of the way attachment copying works + added a dialog
  box to warn the user of consequences if an attachment that is marked 
  for deletion has copies, with multiple choices on how to proceed
* some preliminary tinymce integration (click on gallery 
  placeholder image in visual editor and those attachments present in
  that gallery will get checked - currently only works for galleries 
  already present in tinymce editor when opening a post for editing)
* each attachment that has copies gets a new meta key->value pair:
  "_has_copies"->array(ids of attachment copies)

= 1.2 =
* December 30th, 2009
* nonced everything for security reasons
* color differentiation for items in media library
* converted options from variables to constants
* improved performance when copying items from media library
* moved attachment file url field on item edit screen to bottom of 
  the form
* added a "cancel and return" button on item edit screen
* each copied attachment now gets a new meta key->value pair:
  "_is_copy_of"->"original_attachment_id"

= 1.1 =
* December 12th, 2009
* Rewritten a lot of stuff for better WordPress compliance :)



== More info ==

This plugin uses icons from the awesome famfamfam Silk icon set by 
Mark James :)

"Silk" can be found at: http://famfamfam.com/lab/icons/silk/

Plugin settings are integrated into media settings page.

Help file is included, you'll find it in the "help" subfolder :)



== Translation Credits ==

* **Maor Barazany**     - Hebrew  - http://www.maorb.info/
* **Jean-Michel Meyer** - French  - http://www.li-an.fr/wpplugins/
* **Pietro Palli**      - Italian - http://ppal.li/



== Thanks goes out to... ==

* All the translators, and people leaving comments, bug reports and 
  suggestions on the official page and in the WordPress.org forums

  
  
== Plans for the next version? ==

sooner:

- manage media tags for all selected attachments
- audio and video preview on editing screens

later:

- phpdoc all the functions
- more detailed description for file_gallery_shortcode function 
  (what can be used in 'orderby', what do 'columns' mean) for beginner programmers
- yoxview lightbox http://www.yellow-llama.com/yoxview-gallery-wordpress-plugin/

maybe:

- show attachments' Custom Fields in the main page of Media Library (custom column)
- media library sort by file size or picture dimensions

probably not, but who knows:

- watermarking - probably in another plugin...
- when detaching / deleting attachments, just remove them from DOM
  without refreshing the entire file gallery box, but take care of 
  originals and copies


And thank you for reading this :)

aesqe
