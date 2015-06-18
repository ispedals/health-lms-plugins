#filter_viewerjs

This is a Moodle filter that converts links to PDFs, ODPs, ODTs, and ODSs into an embedded player powered by [ViewerJS](http://viewerjs.org). It uses the model from [moodle-filter_jwplayer](https://github.com/lucisgit/moodle-filter_jwplayer) of creating a Moodle `core_media_player` renderer and using that to change links into an embedded player. 

##Installation

Note that [ViewerJS](http://viewerjs.org) is licensed AGPLv3. To ease the licensing and distribution requirements of this filter, the ViewerJS library must be installed separately, instead of
it being bundled with the filter.

1. Place the files for this filter at `/filter/viewerjs` in your Moodle Installation
1. Download [ViewerJS](https://github.com/kogmbh/ViewerJS/releases) and place the extracted directory at `/filter/viewerjs/lib/viewerjs` in your Moodle installation
3. Using a web browser, go to `/admin` to complete installation

##Usage

1. Create a page activity in a course
2. Add some text
3. Create a link to a PDF
4. When you save and view the page, you should see the PDF displayed in the embedded player

##License
2015 ispedals, GPL 3.0

