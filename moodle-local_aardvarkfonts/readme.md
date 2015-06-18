#Aardvark Fonts
This plugin provides the web fonts, [Font Awesome](http://fontawesome.io) and [Open Sans](https://www.google.com/fonts/specimen/Open+Sans),  that the Moodle theme [Aardvark](https://moodle.org/plugins/pluginversions.php?plugin=theme_aardvark) depends on. The theme currently includes a CDN call to retrieve the fonts. This plugin allows the fonts to be stored locally, preventing a need to make an external network request. This is helpful in reducing load times and if local computers cannot access the wider Internet.

##Installation
1. Install and enable the Aardvark theme
2. Install the plugin
3. Edit `layout/head.php` by commenting out the lines:

```
<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' />
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type='text/css' />
```

You should end up with it looking like:

```
<!--
<link href='//fonts.googleapis.com/css?family=Open+Sans' rel='stylesheet' type='text/css' /> 
<link href="//maxcdn.bootstrapcdn.com/font-awesome/4.2.0/css/font-awesome.min.css" rel="stylesheet" type='text/css' />
-->
```
4. Under `Administration > Site Administration > Appearance > Additional HTML`, in the _Within HEAD_ textbox add:

```
<link href="/moodle/local/aardvarkfonts/vendor/font-awesome/css/font-awesome.min.css" rel="stylesheet" type="text/css" />
<link href="/moodle/local/aardvarkfonts/vendor/google-webfonts-open-sans/css/opensans.css" rel="stylesheet" type="text/css" />
```

##Third Party Licenses
* Font Awesome 4.2.0 by @davegandy - http://fontawesome.io - @fontawesome
	License - http://fontawesome.io/license (Font: SIL OFL 1.1, CSS: MIT License)
* Open Sans by Steve Matteson, Apache License 2.0
