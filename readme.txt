=== Plugin Name ===
Contributors: factorypattern
Donate link: http://example.com/
Tags: admin, members, users, paypal, payments, ecommerce
Requires at least: 3.0.1
Tested up to: 3.8
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Dovetail adds basic yet beautiful membership tools to your WordPress website. Use and enjoy.

== Description ==

Dovetail adds basic yet beautiful membership tools to your WordPress website. Our intention with Dovetail was to create a membership plugin which stayed as close to the "WordPress Way" as possible, whilst also being extendable and powerful. Think WooCommerce for membership.

Our idea is that Dovetail should compliment other plugins rather than just blindly copy their functionality (otherwise, why have plugins?) Want customised login screens? Install the excellent ["Theme My Login"](http://wordpress.org/plugins/theme-my-login/). Customised signup? [Gravity Forms](http://www.gravityforms.com/) with the [User Registration](http://www.gravityforms.com/add-ons/user-registration/) add on. And so on.

Right now Dovetail's fairly new, so there aren't any payment gateways. We're working on a PayPal one though with a view to eventually allow others to develop and payment gateway they choose and bolt it on.

Dovetail's been lovingly crafted by [Factory Pattern](http://factorypattern.co.uk) in the UK to service our clients' membership needs; it was forged in the fire of client projects and tested in the often harsh real world. Install it, try it and let us know your ideas; we're all ears.

== Installation ==

1. Upload to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress

== Frequently Asked Questions ==

= I want to theme the login pages. How do I do that? =

Install ["Theme My Login"](http://wordpress.org/plugins/theme-my-login/). It's a great little plugin that does just that and it works very well with Dovetail.

= Great. I'd love to have a customised signup form too. =

We use [Gravity Forms](http://www.gravityforms.com/) with the [User Registration](http://www.gravityforms.com/add-ons/user-registration/) add on. Yes, it's paid for, but it's worth it if you want a great website!

If you're not wild about parting with your cash then Dovetail should work nicely with any other plugin which lets you theme the Wordpress user signup process.

= I need to skip checking whether a user can view a page on pages with a certain type; can I do that? =

Just add "add_filter( "dovetail_skip_content_check", '__return_true', 10 );" (without the speech marks) above where it says "get_header" in the template file.

== Screenshots ==

1. The dashboard lets you know how many members are on board
2. Settings page
3. Adds to the menu editor to allow you to hide menu items based on some predefined rules, or user roles
4. Set up new user levels or edit old ones
5. Editing a user level. You can set the capabilities here too.
6. Restrict access to pages within the page editing screen.

== Changelog ==

= 1.2.3 =

* Added "dovetail_skip_content_check" filter, which allows theme developers to skip authorisation checks in certain page templates if they want to (e.g. add "add_filter( "dovetail_skip_content_check", '__return_true', 10 );" at the top of the template to skip the auth check)

= 1.2.2 =

* Added a "Dovetail" menu to the admin bar with links to the plugin's admin page and the users page

= 1.2.1 =

* Fixed bug which created a redirect loop when page was protected and now protected page had been chosen in the settings
* Changed the "Pages" menu item to "Settings"

= 1.2 =

* Published on the WordPress repository

== Upgrade Notice ==

= 1.2 =
First public release
