# Staff List

Staff List is a Wordpress plugin that (aptly) lists the staff / members of a business or organization on a page using a shortcode.

## Features

* Add any number of staff
* Add name, title, email, phone, and a bio
* Display all staff of just staff from one department

## Usage

In order to add the text to the body of a page you need to use it's shortcode.

`[staff_list]`

You can also call a specific list of staff members by adding the slug of the wanted department name.

For example if you want a list of the Board Members and their slug is "board-members" you would add the following shortcode.

`[staff_list department='board-members']`

### Modifying the Layout

The Staff List plugin uses a template system which is very easy to modify if you are a developer.

Here's an example that you can drop into your theme's functions.php file:
```php
function my_staff_list_template( $tpl ) {

	// remove the phone extension since we don't use it
	$tpl = str_replace("%%PHONE_EX%%", "", $tpl);

	// move the position of the email field beneath the phone field
	$tpl = str_replace("%%EMAIL%%", "", $tpl);
	$tpl = str_replace("%%PHONE%%", "%%PHONE%%%%EMAIL%%", $tpl);

	// returns the modified template
	return $tpl;

}
add_filter( 'staff_list_template', 'my_staff_list_template' );
```