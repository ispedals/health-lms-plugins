#filter_viewerjs

This is a Moodle filter that converts links to PDFs into an embedded player powered by [ViewerJS](http://viewerjs.org). Heavily based on [moodle-filter_jwplayer](https://github.com/lucisgit/moodle-filter_jwplayer)

##Installation

1. Download [ViewerJS](https://github.com/kogmbh/ViewerJS/releases) and place the extracted directory at `/lib/viewerjs` in your Moodle installation
2. Place the files for this filter at `/filter/viewerjs` in your Moodle Installation
3. Using a web browser, go to `/admin` to complete installation

##Usage

1. Create a page
2. Add some text
3. Create a link to the pdf
4. When you save and view the page, you should see the embedded player

##Todo

* Package this correctly for easy installation
* Enable use with other supported document formats (e.g. ODT, ODP, ODS)
* Enable support with using the Moodle built-in embed function instead of having to create a link first
* Work out licensing

