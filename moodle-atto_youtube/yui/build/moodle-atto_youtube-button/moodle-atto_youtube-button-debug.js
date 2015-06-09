YUI.add('moodle-atto_youtube-button', function (Y, NAME) {

var CSS = {
        INPUTEND: 'atto_youtube_endentry',
        INPUTSUBMIT: 'atto_youtube_urlentrysubmit',
        INPUTURL: 'atto_youtube_urlentry',
        INPUTTIME: 'atto_youtube_time',
        INPUTSTART: 'atto_youtube_startentry',
        YOUTUBEPREVIEW: 'atto_youtube_preview',
        YOUTUBEPREVIEWBOX: 'atto_youtube_preview_box'
    },
    SELECTORS = {
        INPUTURL: '.' + CSS.INPUTURL
    },
    COMPONENTNAME = 'atto_youtube',
    TEMPLATE = '' +
    '<form class="atto_form">' +
		'<label for="{{elementid}}_{{CSS.INPUTURL}}">{{get_string "enterurl" component}}</label>' +
		'<input class="fullwidth {{CSS.INPUTURL}}" type="url" id="{{elementid}}_{{CSS.INPUTURL}}" size="32"/>' +
		'<br/>' +
		'<div id="{{elementid}}_{{CSS.INPUTTIME}}" class="{{CSS.INPUTTIME}}">' +
			'<label for="{{elementid}}_{{CSS.INPUTSTART}}">Start Time (mm:ss)</label>' +
			'<input type="text" class="{{CSS.INPUTSTART}} input-mini" id="{{elementid}}_{{CSS.INPUTSTART}}" size="4"/>' +
			'<br/>' +
			'<label for="{{elementid}}_{{CSS.INPUTEND}}">End Time (mm:ss)</label>' +
			'<input type="text" class="{{CSS.INPUTEND}} input-mini" id="{{elementid}}_{{CSS.INPUTEND}}" size="4"/>' +
		'</div>' +
		'<div class="mdl-align">' +
			'<div class="{{CSS.YOUTUBEPREVIEWBOX}}">' +
				'<iframe src="#" class="{{CSS.YOUTUBEPREVIEW}}" frameborder="0" style="display: none;"></iframe>' +
			'</div>' +
			'<button class="{{CSS.INPUTSUBMIT}}" type="submit">{{get_string "createvideo" component}}</button>' +
		'</div>' +
    '</form>',
    IMAGETEMPLATE = '' +
	'<span class="mediaplugin mediaplugin_youtube">' +
	'<iframe src="{{url}}?start={{start}}{{{amp}}}end={{end}}{{{amp}}}modestbranding=1{{{amp}}}rel=0{{{amp}}}showinfo=0" width="400" height="300" allowfullscreen="1" class="youtuberestrict"></iframe>'+
	'</span>';
Y.namespace('M.atto_youtube').Button = Y.Base.create('button', Y.M.editor_atto.EditorPlugin, [], {
    /**
     * A reference to the current selection at the time that the dialogue
     * was opened.
     *
     * @property _currentSelection
     * @type Range
     * @private
     */
    _currentSelection: null,
    /**
     * The most recently selected image.
     *
     * @param _selectedImage
     * @type Node
     * @private
     */
    _selectedvideo: null,
    /**
     * A reference to the currently open form.
     *
     * @param _form
     * @type Node
     * @private
     */
    _form: null,
    /**
     * The dimensions of the raw image before we manipulate it.
     *
     * @param _rawImageDimensions
     * @type Object
     * @private
     */
    _rawVideoProperties: null,
    initializer: function() {
        this.addButton({
			icon: 'e/insert_time',
            callback: this._displayDialogue
        });
        this.editor.delegate('dblclick', this._displayDialogue, 'iframe', this);
        this.editor.delegate('click', this._handleClick, 'iframe', this);
    },
    /**
     * Handle a click on an image.
     *
     * @method _handleClick
     * @param {EventFacade} e
     * @private
     */
    _handleClick: function(e) {
        var video = e.target,
		selection = this.get('host').getSelectionFromNode(video);
        if (this.get('host').getSelection() !== selection) {
            this.get('host').setSelection(selection);
        }
    },
    /**
     * Display the image editing tool.
     *
     * @method _displayDialogue
     * @private
     */
    _displayDialogue: function() {
        // Store the current selection.
        this._currentSelection = this.get('host').getSelection();
        if (this._currentSelection === false) {
            return;
        }

        this._rawVideoProperties = null;
        var dialogue = this.getDialogue({
            headerContent: M.util.get_string('cropvideo', COMPONENTNAME),
            width: '480px',
            focusAfterHide: true,
            focusOnShowSelector: SELECTORS.INPUTURL
        });

        dialogue.set('bodyContent', this._getDialogueContent())
            .show();
    },
    /**
     * Set the inputs for width and height if they are not set, and calculate
     * if the constrain checkbox should be checked or not.
     *
     * @method _loadPreviewImage
     * @param {String} url
     * @private
     */
    _loadPreviewVideo: function(url) {
		var input, currentStart, currentEnd, newurl;
		this._rawVideoProperties = this._parseTimes(url);

		input = this._form.one('.' + CSS.INPUTSTART);
		currentStart = input.get('value');
		if (currentStart === '') {
			input.set('value', this._secondsToHMS(this._rawVideoProperties.start));
			currentStart = this._rawVideoProperties.start;
		}
		else {
			currentStart = this._HMSToseconds(currentStart);
		}
		input = this._form.one('.' + CSS.INPUTEND);
		currentEnd = input.get('value');
		if (currentEnd === '') {
			input.set('value', this._secondsToHMS(this._rawVideoProperties.end));
			currentEnd = this._rawVideoProperties.end;
		}
		else {
			currentEnd = this._HMSToseconds(currentEnd);
		}
		input = this._form.one('.' + CSS.YOUTUBEPREVIEW);
		newurl = this._rawVideoProperties.src+'?modestbranding=1&rel=0&showinfo=0&start='+currentStart+'&end='+currentEnd;
		if (input.src !== newurl){
			input.setAttribute('src', newurl);
			input.setStyles({
				'display': 'inline'
			});
			this.getDialogue().centerDialogue();
		}

    },
    /**
     * Return the dialogue content for the tool, attaching any required
     * events.
     *
     * @method _getDialogueContent
     * @return {Node} The content to place in the dialogue.
     * @private
     */
    _getDialogueContent: function() {
        var template = Y.Handlebars.compile(TEMPLATE),
            content = Y.Node.create(template({
                elementid: this.get('host').get('elementid'),
                CSS: CSS,
                component: COMPONENTNAME
            }));
        this._form = content;
        // Configure the view of the current image.
        this._applyVideoProperties(this._form);
        this._form.one('.' + CSS.INPUTURL).on('blur', this._urlChanged, this);
		this._form.one('.' + CSS.INPUTSTART).on('blur', this._urlChanged, this);
        this._form.one('.' + CSS.INPUTEND).on('blur', this._urlChanged, this, true);
        this._form.one('.' + CSS.INPUTSUBMIT).on('click', this._setImage, this);

        return content;
    },
    /**
     * Applies properties of an existing image to the image dialogue for editing.
     *
     * @method _applyVideoProperties
     * @param {Node} form
     * @private
     */
    _applyVideoProperties: function(form) {
      //update form with start and end from url
        var properties = this._getSelectedVideoProperties(),
            img = form.one('.' + CSS.YOUTUBEPREVIEW);
        if (properties === false) {
            img.setStyle('display', 'none');
            return;
        }
        if (properties.start) {
            form.one('.' + CSS.INPUTSTART).set('value', this._secondsToHMS(properties.start));
        }
        if (properties.end) {
            form.one('.' + CSS.INPUTEND).set('value', this._secondsToHMS(properties.end));
        }
        if (properties.src) {
            form.one('.' + CSS.INPUTURL).set('value', properties.src);
            this._loadPreviewVideo(properties.src);
        }
    },
    /**
     * Gets the properties of the currently selected image.
     *
     * The first image only if multiple images are selected.
     *
     * @method _getSelectedVideoProperties
     * @return {object}
     * @private
     */
    _getSelectedVideoProperties: function() {
        var videos = this.get('host').getSelectedNodes(), url;
        if (videos) {
            videos = videos.filter('.youtuberestrict');
        }
        if (videos && videos.size()) {
            url = videos.item(0).getAttribute('src');
            return this._parseTimes(url);
        }
        // No image selected - clean up.
        this._selectedvideo = null;
        return false;
    },
	_parseTimes: function(url){
		var properties = {
                start: null,
                end: null,
				src:null
		}, params, i;
		if (url.indexOf('youtube.com/embed/')!==-1){
			properties.src = url.split('?')[0];
		}
		if(url.indexOf('?') === -1){ //embed url with no params
			return properties;
		}
		params = url.split('?')[1].split('&');
		for(i = 0; i < params.length; i++){
			if(params[i].indexOf('start') === 0){
				properties.start = params[i].split('=')[1];
			}
			if(params[i].indexOf('end') === 0){
				properties.end = params[i].split('=')[1];
			}
			if(params[i].indexOf('v') === 0){
				properties.src = 'https://www.youtube.com/embed/'+params[i].split('=')[1];
			}
		}
		return properties;
	},
    /**
     * Update the form when the URL was changed. This includes updating the
     * height, width, and image preview.
     *
     * @method _urlChanged
     * @private
     */
    _urlChanged: function() {
        var input = this._form.one('.' + CSS.INPUTURL);
        if (input.get('value') !== '') {
            // Load the preview image.
            this._loadPreviewVideo(input.get('value'));
        }
    },

    /**
     * adds image to page as html (change to creating url and inserting iframe)
     *
     * @method _setImage
     * @param {EventFacade} e
     * @private
     */
    _setImage: function(e) {
        //convert to html
        var form = this._form,
            url = form.one('.' + CSS.INPUTURL).get('value'),
            start = this._HMSToseconds(form.one('.' + CSS.INPUTSTART).get('value')),
            end = this._HMSToseconds(form.one('.' + CSS.INPUTEND).get('value')),
            imagehtml,
            template,
            host = this.get('host');
        e.preventDefault();
        // Focus on the editor in preparation for inserting the image.
        host.focus();
        if (url !== '') {
            if (this._selectedvideo) {
                host.setSelection(host.getSelectionFromNode(this._selectedvideo));
            } else {
                host.setSelection(this._currentSelection);
            }
			url = this._parseTimes(url).src;
            template = Y.Handlebars.compile(IMAGETEMPLATE);
            imagehtml = template({
                url: url,
				start: start,
				end: end,
				amp:'&'
			});
            this.get('host').insertContentAtFocusPoint(imagehtml);
            this.markUpdated();
        }
        this.getDialogue({
            focusAfterHide: null
        }).hide();
    },
	_secondsToHMS: function(seconds){
		var minutes = Math.floor(seconds/60);
		seconds -= minutes * 60;
		if(minutes<10) {
			minutes = "0" + minutes;
		}
		seconds = Math.floor(seconds);
		if(seconds<10) {
			seconds = "0" + seconds;
		}
		return minutes + ':' + seconds;
	},
	_HMSToseconds: function (timestamp) {
		var times = /(\d+?):(\d\d)/.exec(timestamp),
		minutes = parseInt(times[1], 10),
		seconds = parseInt(times[2], 10);
		return (minutes * 60) + seconds;
	}
});


}, '@VERSION@', {"requires": ["moodle-editor_atto-plugin"]});
