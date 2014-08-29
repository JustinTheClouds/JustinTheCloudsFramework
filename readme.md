# JustinTheClouds Framework

## A new, unique type of Wordpress theme

My goal was to create a base parent theme that makes adding functionality to your child themes as easy as possible. I took a slightly different approach than some other Wordpress parent frameworks out there. You may like it, you may hate it. Let me know!

### Problems I tried to solve

* Support for semantics built in
* Support for microdata
* HTML5 standards
* Try to keep logic out of template files
* Options Framework built in with common options defined
* Some helper methods for displaying common page elements
* Proper use of heading tags
* Auto update support for the framework AND child themes built off of
* Less repetition inside of your function.php files
* Hook support for better plugin creation for the framework
* Ease of use
* Small learning curve

### Public Methods

#### JTCF::openSection($location [, $attributes])

Opens a new HTML tag. This method for be used on common structure based tags such as html, head, header, main, article, aside, footer, navigation. It will automatically apply roles and microdata accordingly when used. It also has a 'JTCF_beforeOpenSection' action and a 'JTCF_afterOpenSection' action to hook into.

### Actions

#### JTCF_beforeOpenSection

#### JTCF_afterOpenSection

### Filters

#### JTCF_runConfiguration

This is the only action/filter that cannot be accessed by use of the initialization 'filters' option. The filter is applied on the 'after_setup_theme' action. This filter is in place for plugin creation. This way plugins can also make changes to the theme configuration. 

**Note:** Child themes should not need to use this filter since they can just alter configurations on initialization.

#### JTCF_openSection

Allows modification of actual HTML tag used as well has the attributes to be applied

#### JTCF_outputMicrodata

This is the single filter that will allow you to alter all of the websites microdata. Three arguments will be passed in with the filtered data. $location, $type, $property. $location is where in the document the microdata is being placed. $type is the type of microdata which can be 'scope', 'meta', or 'itemprop'.

### Custom Shortcodes

JTCF automatically enables shortcodes inside of widgets.

#### [bloginfo option="option-name"]

A quick way to return bloginfo options inside of posts/pages/widgets.

#### [of option="option-name"]

A simple shortcode to retrieve theme options inside of posts and widgets.

#### [address]

A quick way of outputting a full address stamp. This pulls from the theme options > contact tab. Most small businesses need to place their address on their contact page and in their footers. This shortcode comes in handy.

## Changelog

### 1.0.6

* Added [bloginfo], [address], and [of] shortcodes
* Added ajaxurl js variable