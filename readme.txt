=== Tonjoo Theme Options Maker ===
Contributors: Todi.Adiatmo, qutek, Alzea
Tags: theme options, admin, options, framework, generator, theme
Requires at least: 3.0.3
Tested up to: 3.6
Stable tag: trunk
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Theme options framework and generator for WordPress Theme. Available as a plugin or library.
== Description ==

Tonjo Theme Options Maker (TTOM) is a theme options framework and generator for WordPress Theme, it requires at least php 5.3. TTOM enable you to generate theme option easy and fast. You can also integrate TTOM with your newly created themes, so you can focus on theme usability and design. Moreover TTOM supports custom ads so you can add your own ads on theme options page.

**Features :**

* Drag and drop user interface.
* Extensive theme options support : Text Area, Media Upload, Select, Select with Image, Checkbok and many more.
* Input sanitation and validation.
* Theme Integration API.
* Custom ads on options page

**Usage Instruction**

1. Install and activate the plugin.
2. Create options
* Using config file :
    > Copy the sample options file (**tonjoo_options.php**) located on sample-options folder on plugin directory to your active theme directory.
* Or create options directly from admin dashboard :
    > > From WP Admin go to TTOM Options -> Create Options.

If you have any questions,comment,customization request or suggestion please contact us via our <a href="https://forum.tonjoo.com/thread-category/tonjoo-tom/" title="support forum" rel="friend">Tonjoo Support Forum</a>

Plugin guide can be read here : <a href="https://tonjoo.com/addons/tonjoo-tom/#manual" title="TTOM Manual" rel="friend">Tonjoo Theme Options Maker Manual</a>

Theme integration manual : <a href="https://tonjoo.com/tonjoo-tom-theme-integration/" title="TTOM integration manual" rel="friend">TTOM Integration Manual</a>

Find more detail on our official <a href="https://tonjoo.com/addons/tonjoo-tom" title="TTOM" rel="friend">TTOM Page</a>

Please support this plugin by [donate](http://www.tonjoo.com/donate/ "donate") :)

== Installation ==

1. Grap the plugin from from wordpress plugin directory or Upload the tonjoo-tom folder to the /wp-content/plugins/ directory
2. Activate the plugin

== Screenshots ==

1. Create your theme options easily
2. Theme options front-end
3. Theme integration
4. Custom ads on options page

== Changelog ==
**1.0.5**
- Fix some redirect options.php issues 

**1.0.2**
- Used almari ioc framework
- Fix some bug 

**1.0.1** 
- Add feature custom ads
- Fix default value from config file for option type image
- Fix option name not changed after save with ajax
- Omit the default value on shotcode copy to clipboard

**1.0.0**
- Initial Release
