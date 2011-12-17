=== Plugin Name ===
Contributors: Mat Lipe
Donate link: http://lipeimagination.info/contact/
Tags: menus, sidebar menu, heirchy 
Requires at least: 3.1
Tested up to: 3.2.1
Stable tag: 1.4.4
Version: 1.4.4

== Description ==

Creates a widget that can be dragged into a sidebar which automatically generates a menu based on the parent/child relationship of the pages. When on a top level page it displays a menu of the all of the top level pages and a menu of all of the pages that are children of the current page. Keep the sidebar menu clean and usable.
Has a checkbox for including the top level of pages (good for sites that have 3 or more page levels) and a box to exclude pages and children of excluded page.


== Installation ==

This section describes how to install the plugin and get it working.

e.g.

1. Upload the `advanced-sidbebar-menu` folder to the `/wp-content/plugins/` directory
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Drag the Sidebar Menu widget into a sidebar.

== Frequently Asked Questions ==

= Does this support multiple instances? =

Yes.

= Does the menu change for each page you are on? =

Yes. Based on whatever parents and children pages you are on, the menu will change automatically.

= How does this work with styling the page? =

As of version 1.1 this will automatically generate class names for each level for menu system. You can add classes to your theme's style.css file to style it accourdingly. You may want to use something like margins to set the levels apart.

== Screenshots ==

1. The Simple to use widget


== Changelog ==

= 1.4.4 =
* Cleaned up the way the plugin displays
* Added class to match normal widgets
* Removed the <div> completely when no menu present


= 1.4 =
* Removed Menu from pages with no children
* Added a checkbox for including menu on page with no children
* Removed ability to exclude items from menu


= 1.2 =
* Added support for the built in page ordering.

= 1.1 =
* Added support for separate css classes on each level of the menu.

== Upgrade Notice ==

= 1.2 =
This Version will allow you to order the pages in the menu using the page order section of the editor.

= 1.1 =
This version will allow simlier css styling.

