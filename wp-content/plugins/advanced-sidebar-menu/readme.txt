=== Plugin Name ===
Contributors: Mat Lipe
Donate link: http://lipeimagination.info/contact/
Tags: menus, sidebar menu, heirchy 
Requires at least: 3.1
Tested up to: 3.3.1
Stable tag: 2.1
Version: 2.1

== Description ==

Creates a widget that can be dragged into a sidebar which automatically generates a menu based on the parent/child relationship 
of the pages. When on a top level page, it displays a menu of the all of the top level pages and a menu of all of the pages that 
are children of the current page. Keeps the sidebar menu clean and usable.

Has the ability to exclude page from the menu.
As of 2.0 it also allows for display of all the child pages always.
You may also select the level of pages to display with this option



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

As of version 1.1 this will automatically generate class names for each level for menu system. 
You can add classes to your theme's style.css file to style it accordingly. 
You may want to use something like margins to set the levels apart.


== Changelog ==

= 2.1 =
* Added default syling.

= 2.0 =
* Brought back the ability to exclude pages with support for excluding single pages from the menu.
* Added the ability to display all levels of child pages always.
* Added the option to select how many levels of pages to display when the "Always Display Child Pages" is selected


= 1.4.5 =
* Added compatibility for sites with non wp_ prefix tables
* Removed All traces of Each menu level if no pages to list
* Removed Error created by some search forms

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


== Screenshots ==
1. The widget Menu as of 2.0.

== Upgrade Notice ==

= 2.0 =
This Version will give you better control over the menu and styling ability.
Added new options and more stable code.

= 1.2 =
This Version will allow you to order the pages in the menu using the page order section of the editor.

= 1.1 =
This version will allow simliar css styling.

